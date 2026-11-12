# Liquor Junction — End-to-End Order Flow Documentation
## (Product Add to Cart से लेकर Order Delivered तक का सम्पूर्ण कोड-फ़्लो)

> **Note:** यह डॉक्यूमेंटेशन केवल और केवल **Liquor Junction** प्रोजेक्ट के वास्तविक कोड बेस (Controllers, Models, Routes, Services, Migrations, Views) को लाइन-दर-लाइन ट्रेस करके तैयार किया गया है।

---

## 📑 विषय-सूची (Table of Contents)
1. [सिस्टम आर्किटेक्चर और डेटाबेस संरचना](#1-सिस्टम-आर्किटेक्चर-और-डेटाबेस-संरचना)
2. [चरण 1: Product Add to Cart & Buy Now Flow](#2-चरण-1-product-add-to-cart--buy-now-flow)
3. [चरण 2: Cart Management & Calculations](#3-चरण-2-cart-management--calculations)
4. [चरण 3: Checkout Page Flow (`/checkout`)](#4-चरण-3-checkout-page-flow-checkout)
5. [चरण 4: Order Placement & Database Persistence (`storePlaceOrder`)](#5-चरण-4-order-placement--database-persistence-storeplaceorder)
6. [चरण 5: Payment Processing (MojoPay Gateway vs Cash On Delivery)](#6-चरण-5-payment-processing-mojopay-gateway-vs-cash-on-delivery)
7. [चरण 6: Order Confirmation & Communication Alerts](#7-चरण-6-order-confirmation--communication-alerts)
8. [चरण 7: Admin Dashboard Order Lifecycle Management](#8-चरण-7-admin-dashboard-order-lifecycle-management)
9. [चरण 8: अंतिम चरण — Order Delivered (Status 3)](#9-चरण-8-अंतिम-चरण--order-delivered-status-3)
10. [चरण 9: Order Cancellation & Inventory Restock](#10-चरण-9-order-cancellation--inventory-restock)
11. [चरण 10: Customer Post-Order Experience & Order Tracking](#11-चरण-10-customer-post-order-experience--order-tracking)
12. [मास्टर स्टेटस और टाइप संदर्भ तालिका](#12-मास्टर-स्टेटस-और-टाइप-संदर्भ-तालिका)

---

## 1. सिस्टम आर्किटेक्चर और डेटाबेस संरचना

### 1.1 विज़ुअल कोड फ़्लो आरेख (Mermaid Diagram)

```mermaid
flowchart TD
    A["User Product Page (Web/App)"] -->|POST /product-addtocart| B["ProductController@productCartAdd"]
    B -->|Guest User| B1["Session::put('cart_info')"]
    B -->|Auth User| B2["DB Table: cart (status=1)"]
    
    B1 & B2 -->|GET /checkout| C["CheckoutController@Checkout"]
    C -->|Fetch| C1["UserAddress & UserBillAddress"]
    C -->|Calculate| C2["Store Distance (Haversine Formula)"]
    C -->|Apply| C3["Promocode & Loyalty Points"]
    
    C -->|POST /store-checkout| D["CheckoutController@storePlaceOrder"]
    D -->|Validate| D1["Stock, Price, Delivery Fee, Tax, Promocode"]
    D -->|Generate IDs| D2["Order ID (LQOID...) & Trans ID (LQTC...)"]
    D -->|Save Records| D3["Tables: order, order_detail, order_info, order_tracking, transactions"]
    
    D -->|Payment Method Check| E{Payment Method}
    
    %% MojoPay Flow
    E -->|1: MojoPay Online| F["MojoPay::createCheckoutSession"]
    F -->|Redirect User| F1["MojoPay Hosted Checkout Page"]
    F1 -->|Webhook/Return| F2["MojoPayController@webhook / @returned"]
    F2 -->|Success| F3["OnlineOrderFulfillment@markPaid"]
    F3 -->|Lock & Update| F4["Deduct ProductVariants Available Stock, Set Transaction & Order Status=1"]
    
    %% COD Flow
    E -->|3: Cash On Delivery| G["Direct COD Processing"]
    G -->|Immediate Stock Update| G1["Deduct ProductVariants Available Stock"]
    G -->|Broadcast Event| G2["NewOrderPlaced (Websocket Alert)"]
    
    F4 & G2 --> H["Send Order Confirmation Email (Template 17) & Push Notifications"]
    H --> I["Clear Cart (cart.status=2) & Flush Sessions"]
    
    %% Admin Lifecycle
    I --> J["Admin Dashboard (/adminorder)"]
    J -->|POST /adminorder/update-order-status| K["Dashboard\\OrderController@updateOrderStatus"]
    
    K -->|Status 1 -> 2| L["Accepted"]
    L -->|Status 2 -> 6 (Home Delivery)| M["Out for Delivery (Email Template 22)"]
    L -->|Status 2 -> 5 (Store Pickup)| N["Ready to Pick Up (Email Template 21)"]
    
    M & N -->|Status -> 3| O["DELIVERED (Final Status)"]
    
    %% Delivered Operations
    O --> P1["Generate DomPDF Tax Invoice (NHIL, GETFund, VAT)"]
    O --> P2["Save to storage/app/public/uploads/invoices/order_{id}.pdf"]
    O --> P3["Activate Loyalty Points (status=1 credit & debit)"]
    O --> P4["Send FCM Push Notification (Delivered)"]
    O --> P5["Send Email with Attached PDF Invoice (Template 16)"]
```

### 1.2 प्रमुख डेटाबेस टेबल्स (Key Database Tables)

| टेबल का नाम | मुख्य कार्य |
| :--- | :--- |
| `cart` | यूजर के एक्टिव कार्ट आइटम्स को स्टोर करती है (`status=1` एक्टिव, `status=2` आर्काइव्ड/ऑर्डर बन चुका)। |
| `product` / `product_variants` | प्रोडक्ट कैटलॉग, साइज, यूनिट, `variant_price`, `variant_discounted_price`, `available_qty`, `sold_qty`। |
| `offers` / `bogo` | एक्टिव डिस्काउंट ऑफर्स और Buy-One-Get-One (BOGO) नियम। |
| `order` | मुख्य आर्डर रिकॉर्ड (`order_id`, `user_id`, `supplier_id`, `order_status`, `status`, `total_amount`, `payable_amount` आदि)। |
| `order_detail` | आर्डर के भीतर हर प्रोडक्ट वेरिएंट का लाइन आइटम (क्वांटिटी, मूल कीमत, ऑफर कीमत, BOGO फ्लैग)। |
| `order_info` | ग्राहक की व्यक्तिगत जानकारी, पता, फोन, कूपन नाम, डिलीवरी चार्ज, स्टोर पिकअप का पता, `order_from`। |
| `order_tracking` | आर्डर की लाइव ट्रैकिंग हिस्ट्री और स्टेटस टाइमलाइन। |
| `transactions` | भुगतान का रिकॉर्ड (`trans_no`, `payment_type`, `payment_status`, `amount`, `status`)। |
| `loyalty` / `loyalty_points` | रिवार्ड/लॉयल्टी पॉइंट्स की सेटिंग्स और यूज़र पॉइंट्स लेजर (`credit` / `debit`, `status=2` पेंडिंग, `status=1` अप्रूव्ड)। |
| `promocode` / `coupon_user` | प्रोमो कोड डिटेल्स और यूज़र यूसेज हिस्ट्री। |
| `payment_logs` | ऑनलाइन पेमेंट गेटवे (MojoPay) की प्रत्येक रिक्वेस्ट और रिस्पॉन्स का ऑडिट लॉग। |
| `notification` / `admin_notifications` | कस्टमर और एडमिन के लिए इन-ऐप नोटिफिकेशन्स। |

---

## 2. चरण 1: Product Add to Cart & Buy Now Flow

### 2.1 फ़ाइल और मेथड
* **Web Controller:** `App\Http\Controllers\Frontend\ProductController@productCartAdd`
* **Route:** `POST /product-addtocart` (`name: productcartadd`)
* **Mobile API Controller:** `App\Http\Controllers\Api\V1\CartController@cartManager`
* **Route:** `POST /api/v1/cart-manager`

### 2.2 कोड फ्लो विवरण
1. **इनपुट पार्सिंग:**
   * `$product_id = base64_decode($request->product_id);`
   * `$variant_id = $request->variantId;`
   * `$quantity = (int) $request->quantity;`
   * `$bogo_status = (int) $request->bogo_status;`
   * `$offer_status = (int) $request->offer_status;`
2. **सक्रिय ऑफर का मूल्यांकन (`Offers` Model):**
   * कोड चेक करता है: `Offers::where('status', 1)->whereDate('expiry_date', '>=', today)->first();`
   * यदि ऑफर सक्रिय है, तो `offer_type` (flat / percentage) और `dis_amount` प्राप्त किया जाता है।
3. **यूज़र की लॉगिन स्थिति:**
   * **Guest User (`Auth::guard('user')->user() == ""`):**
     * डेटा Laravel सेशन `cart_info` में एरे के रूप में रखा जाता है:
       ```php
       $new_item = [
           'quantity' => $quantity,
           'is_bogo' => $bogo_status,
           'offer_type' => $currentOfferType,
           'discount_amount' => $currentOfferAmount,
           'is_offer' => $offer_status
       ];
       // $cart_info = [$product_id => [$variant_id => $new_item]]
       Session::put('cart_info', $result);
       ```
   * **Logged-in User (`Auth::guard('user')->user()`):**
     * `ProductVariants` से कीमत और डिस्काउंटेड कीमत जांची जाती है।
     * अगर यह आइटम पहले से टेबल `cart` में मौजूद है (`user_id`, `product_id`, `product_variant_id`, `status=1`), तो रिकॉर्ड `update` होता है।
     * अगर नया आइटम है, तो `Cart` मॉडल का नया रिकॉर्ड `insert` होता है:
       ```php
       $cart = new Cart();
       $cart->uniqid = uniqid();
       $cart->product_id = $product_id;
       $cart->product_variant_id = $variant_id;
       $cart->user_id = $user_id;
       $cart->product_price = $variant->variant_price;
       $cart->offer_price = $variant->variant_discounted_price;
       $cart->quantity = $quantity;
       $cart->total_price = $total_price;
       $cart->is_bogo = $bogo_status;
       $cart->is_offer = $offer_status;
       $cart->offer_type = $currentOfferType;
       $cart->discount_amount = $currentOfferAmount;
       $cart->order_type = 1;
       $cart->status = 1; // Active cart item
       $cart->save();
       ```
     * रेस्पॉन्स में कुल कार्ट काउंट लौटाया जाता है:
       `response()->json(['success' => 'true', 'cart_count' => $totalCount]);`

### 2.3 Buy Now Session Logic
* **Controller:** `App\Http\Controllers\Frontend\CartController@buyNowSession`
* **Route:** `POST /buy-now-session`
* यह यूज़र के मुख्य कार्ट को प्रभावित किए बिना एक अलग सेशन में तत्काल खरीदारी का डेटा रखता है:
  ```php
  Session::forget('checkout_in_progress');
  Session::forget('buy_now_info');
  $data = [
      'product_id' => $request->product_id,
      'quantity' => $request->quantity,
      'variantId' => $request->variantId,
      'is_bogo' => $bogo_status,
      'offer_type' => $currentOfferType,
      'discount_amount' => $currentOfferAmount,
      'is_offer' => $offer_status,
  ];
  Session::put('buy_now_info', $data);
  ```

---

## 3. चरण 2: Cart Management & Calculations

### 3.1 फ़ाइल और मेथड
* **Controller:** `App\Http\Controllers\Frontend\CartController`
* **Methods:**
  * `Cart(Request $request)` (GET `/cart`) — कार्ट पेज डिस्प्ले।
  * `productCartIncrement(Request $request)` (POST `/cart-increment`) — क्वांटिटी अपडेट।
  * `productCartRemove(Request $request)` (POST `/cart-remove`) — आइटम हटाना।

### 3.2 कोड में गणना का नियम
1. **स्टॉक उपलब्धता चेक:**
   * `ProductVariants::where('id', $variant_id)->first()->available_qty < $quantity` होने पर रिक्वेस्ट अमान्य कर दी जाती है।
2. **डायनामिक डिस्काउंट गणना:**
   * **Flat Discount:** `$discount_price = max(0, $org_price - ($discount_amount * $qty));`
   * **Percentage Discount:** `$discount_price = max(0, $org_price - ($org_price * $discount_amount / 100));`
   * **Variant Discounted Price:** यदि कोई अलग ऑफर न हो, लेकिन वेरिएंट की डिस्काउंटेड कीमत मौजूद हो: `$discount_price = $variant_discounted_price * $qty;`
3. **टैक्स और अंतिम कुल योग:**
   * `$final_amount = $total_products_price - $total_discount_price;`
   * `$tax_amount = round((Helper::Settings('tax') / 100) * $final_amount, 2);`
   * `$total_amount = $final_amount + $tax_amount;`
   * यह नया ब्रेकडाउन AJAX रिस्पॉन्स में फ्रंटएंड को भेजा जाता है।

---

## 4. चरण 3: Checkout Page Flow (`/checkout`)

### 4.1 फ़ाइल और मेथड
* **Controller:** `App\Http\Controllers\Frontend\CheckoutController@Checkout`
* **Route:** `GET|POST /checkout` (`name: checkout`)

### 4.2 चेकआउट पेज पर डेटा लोडिंग:
1. **Authentication Check:**
   * यदि `$this->user_id == ''`, तो सेशन में `checkout_value = 1` सेट करके कस्टमर को सीधे `/login` पर रीडायरेक्ट कर दिया जाता है।
2. **Buy Now बनाम सामान्य कार्ट चयन:**
   * यदि `is_buy_now == 1` और `Session::has('buy_now_info')` है, तो केवल वही विशिष्ट वेरिएंट लोड होता है।
   * सामान्य कार्ट होने पर DB `cart` टेबल से `user_id` और `status = 1` वाले सभी प्रोडक्ट्स लोड होते हैं और उनकी ताज़ा कीमतें सिंक की जाती हैं।
3. **डिलीवरी और बिलिंग पते:**
   * शिपिंग एड्रेस: `UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1]])->get()`
   * डिफ़ॉल्ट बिलिंग एड्रेस: `UserBillAddress::with([...])->where([['user_id', $user_id], ['status', 1], ['default', 1]])->get()`
4. **स्टोर पिकअप दूरी गणना (Haversine Formula):**
   * यदि ब्राउज़र से ग्राहक का अक्षांश (`current_lat`) और देशांतर (`current_long`) प्राप्त होता है, तो Haversine फॉर्मूले से स्टोर्स की दूरी किलोमीटर में निकाली जाती है:
     ```sql
     (6371 * acos(cos(radians(current_lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(current_long)) + sin(radians(current_lat)) * sin(radians(latitude)))) <= map_distance
     ```
5. **उपलब्ध लॉयल्टी पॉइंट्स लोड करना:**
   * केवल वे पॉइंट्स लोड किए जाते हैं जिनका ऑर्डर सफलतापूर्वक डिलीवर हो चुका है (`order_status = 3`):
     ```php
     LoyaltyPoints::where('user_id', $user_id)
         ->where('status', 1)
         ->whereIn('order_id', function ($sub) {
             $sub->select('order_id')->from('order')->where('order_status', 3);
         })
     ```
   * कुल अंक = `Total Credits - Total Debits`।
6. **AJAX सहायक सेवाएं:**
   * `getUserAreaTax`: चुने गए पते के एरिया के अनुसार डिलीवरी फीस या मुफ़्त डिलीवरी की सीमा जांचता है।
   * `applyCoupon`: कूपन कोड की वैधता और डिस्काउंट प्रतिशत सत्यापित करता है।
   * `applyReward`: लॉयल्टी पॉइंट्स को मुद्रा छूट में बदलता है।

---

## 5. चरण 4: Order Placement & Database Persistence (`storePlaceOrder`)

### 5.1 फ़ाइल और मेथड
* **Web Controller:** `App\Http\Controllers\Frontend\CheckoutController@storePlaceOrder`
* **Route:** `POST /store-checkout` (`name: storeCheckout`)
* **API Controller:** `App\Http\Controllers\Api\V1\CheckoutController@placeOrder`

### 5.2 डेटाबेस में ऑर्डर निर्माण के 10 मुख्य चरण:

1. **सख्त सत्यापन (Tampering Checks):**
   * कूपन कोड और डिस्काउंट प्रतिशत डेटाबेस से मेल खाना चाहिए।
   * एरिया की डिलीवरी फीस का मिलान होना चाहिए।
   * एडमिन सेटिंग्स का टैक्स प्रतिशत मैच होना चाहिए।
   * हर वेरिएंट का स्टॉक (`available_qty >= requested_qty`) होना अनिवार्य है।
   * उत्पाद की कीमत में किसी भी प्रकार का क्लाइंट-साइड बदलाव होने पर ऑर्डर रुक जाता है।
2. **Order ID और Transaction ID जनरेशन:**
   * `geneateOrderId()`: डेटाबेस के अंतिम ऑर्डर से सीरियल नंबर लेकर `LQOID000001` जनरेट करता है।
   * `geneateTranscationId()`: `Transactions` टेबल से लेकर `LQTC000001` जनरेट करता है।
3. **`order` टेबल में मुख्य रिकॉर्ड निर्माण:**
   ```php
   $order = new Order();
   $order->uniqid = uniqid();
   $order->user_id = $user_id;
   $order->supplier_id = $store_id; // (यदि स्टोर पिकअप है)
   $order->order_id = $this->geneateOrderId();
   $order->delivery_address = $delivery_address;
   $order->billing_address = $billing_address;
   $order->isSameAddress = $isSameAddress ? 1 : 0;
   $order->order_type = $purchase_type; // 1 = Delivery, 2 = Store Pickup
   $order->order_date = date('Y-m-d H:i:s');
   $order->order_time = date('Y-m-d H:i:s');
   $order->order_status = 1; // Pending
   $order->status = ($payment_method == 1) ? 2 : 1; // 2=Initiated/Unpaid, 1=Active
   $order->recipientName = $giftRecipient;
   $order->giftMessage = $giftMessage;
   $order->delivery_instructions = $user_address_data->delivery_instructions;
   $order->delivery_options = $user_address_data->delivery_options;
   $order->save();
   ```
4. **`order_tracking` टेबल:**
   * नया ट्रैकिंग रिकॉर्ड बनता है: `order_id = $order->id`, `order_status = 1`, `status = 1`।
5. **`order_info` टेबल:**
   * ग्राहक का नाम, मोबाइल नंबर, ईमेल, देश, क्षेत्र, पिकअप स्टोर का पता, कूपन का नाम, डिलीवरी चार्ज और रिवार्ड राशि सुरक्षित की जाती है।
6. **`order_detail` टेबल (लाइन आइटम्स):**
   * कार्ट के प्रत्येक वेरिएंट के लिए:
     * `quantity`: सामान्य या BOGO होने पर दोगुनी।
     * `product_original_amount`: वेरिएंट की मूल कीमत।
     * `product_total_amount`: डिस्काउंटेड/ऑफर कीमत।
     * `is_bogo`: BOGO फ्लैग (1 या 0)।
     * `status = 1`।
7. **अंतिम योग (Payable Amount Calculation):**
   ```
   Grand Total = (Product SubTotal) 
               - (Cart Discount) 
               - (Coupon Discount) 
               - (Reward Points Discount) 
               + (Gift Card Amount) 
               + (Tax) 
               + (Area Delivery Fee)
   ```
   * यह राशि `order->payable_amount` और `order->total_amount` में सेव होती है।
8. **कूपन और लॉयल्टी रिकॉर्ड्स:**
   * कूपन उपयोग: `coupon_user` टेबल में `usage_count = 1` इंसर्ट होता है।
   * रिवार्ड्स उपयोग (खर्च किए गए अंक): `loyalty_points` टेबल में `type = 'debit'`, `status = 2` (पेंडिंग) दर्ज होता है।
   * नए अर्जित अंक (Earned Loyalty Points): `loyalty_points` में `type = 'credit'`, `status = 2` (पेंडिंग) दर्ज होता है।
9. **`transactions` टेबल:**
   * `trans_no = $this->geneateTranscationId()`
   * `payment_type = $payment_method` (1 = MojoPay, 3 = COD)
   * `payment_status = ($payment_method == 1) ? "" : '1'`
   * `amount = $grand_total_amount`
   * `status = ($payment_method == 1) ? 2 : 1`
   * `transactions->save()`

---

## 6. चरण 5: Payment Processing (MojoPay Gateway vs Cash On Delivery)

ऑर्डर बनने के बाद कोड भुगतान विधि के अनुसार दो अलग-अलग पाथ पर आगे बढ़ता है:

### 6.1 Path A: Online Payment via MojoPay Gateway (`payment_method == 1`)
1. **चेकआउट सेशन निर्माण:**
   * `MojoPay::createCheckoutSession($grand_total_amount, $order->id, 'Order ' . $order->order_id)` को कॉल किया जाता है।
   * सेशन ID को रिमेम्बर किया जाता है और `PaymentLog::record(...)` में `checkout_started` (pending) स्थिति लॉग होती है।
   * ग्राहक को MojoPay द्वारा दिए गए गेटवे यूआरएल (`$session['url']`) पर रीडायरेक्ट करने के लिए JSON रेस्पॉन्स लौटाया जाता है।
2. **वेबहुक और रिटर्न कॉलबैक:**
   * **Webhook Route:** `POST /mojopay/webhook` ➔ `MojoPayController@webhook`
     * `MojoPay-Signature` हेडर से पे-लोड की प्रामाणिकता जांची जाती है।
     * अगर इवेंट `checkout_session.succeeded` है:
       * `OnlineOrderFulfillment::markPaid($order)` कॉल होता है।
       * `PaymentLog` में `webhook -> success` रिकॉर्ड होता है।
     * अगर इवेंट `failed`, `cancelled` या `expired` है:
       * `OnlineOrderFulfillment::markFailed($order)` कॉल होता है।
   * **Return Route:** `GET /mojopay/return/{order}` ➔ `MojoPayController@returned`
     * सेशन की स्थिति सीधे MojoPay API से जांची जाती है।
     * सफलता होने पर `OnlineOrderFulfillment::markPaid` कॉल होकर धन्यवाद पेज लोड होता है।
3. **`OnlineOrderFulfillment::markPaid` की आंतरिक कार्यप्रणाली (Atomic Database Transaction):**
   * ऑर्डर और ट्रांजैक्शन रो को लॉक (`lockForUpdate`) किया जाता है।
   * `order->status = 1` सेट होता है।
   * `transactions->status = 1` और `transactions->payment_status = '1'` सेट होता है।
   * **इन्वेंटरी कटौती (Stock Deduction):**
     * `ProductVariants` में `available_qty = available_qty - quantity`
     * `ProductVariants` में `sold_qty = sold_qty + quantity`
     * `order->is_stock_updated = 1` सेट होता है।
   * इन-ऐप और एडमिन नोटिफिकेशन्स जनरेट होते हैं।
   * `sendOrderConfirmationEmail` से ईमेल भेजा जाता है।
   * यूज़र का कार्ट क्लियर किया जाता है: `Cart::where('user_id', $userId)->update(['status' => 2])`।

### 6.2 Path B: Cash On Delivery (COD - `payment_method == 3`)
1. **तत्काल इन्वेंटरी कटौती:**
   * `storePlaceOrder` के लूप में तुरंत स्टॉक अपडेट होता है:
     ```php
     ProductVariants::where('id', $result->id)->update([
         'available_qty' => $result->available_qty - $cart_qty,
         'sold_qty' => $result->sold_qty + $cart_qty
     ]);
     ```
2. **Websocket लाइव एडमिन अलर्ट:**
   * एडमिन डैशबोर्ड पर रियल-टाइम साउंड/पॉपअप अलर्ट के लिए `broadcast(new NewOrderPlaced($data))` इवेंट भेजा जाता है।
3. **नोटिफिकेशन्स और ईमेल:**
   * इन-ऐप नोटिफिकेशन (`Notification`) और एडमिन नोटिफिकेशन (`AdminNotifications`) इंसर्ट होते हैं।
   * `sendOrderConfirmationEmail($user_id, $order)` तुरंत ट्रिगर होता है।
4. **कार्ट खाली करना:**
   * `Cart::where('user_id', $user_id)->update(['status' => 2])`।
   * चेकआउट और बाय-नाउ के सभी सेशन हटा दिए जाते हैं।
5. **सफलता पेज:**
   * कस्टमर को सीधे थैंक्यू पेज `/thankyou/{id}/{earnedpoints}` पर भेज दिया जाता है।

---

## 7. चरण 6: Order Confirmation & Communication Alerts

ऑर्डर सफलतापूर्वक दर्ज होने पर `sendOrderConfirmationEmail` मेथड निष्पादित होता है:

* **फ़ाइल:** `App\Http\Controllers\Frontend\CheckoutController@sendOrderConfirmationEmail`
* **ईमेल 1: कस्टमर को:**
  * टेम्पलेट ID: `17` (Place Order)
  * व्यू: `emails.orderstatuschanged`
  * इसमें ऑर्डर नंबर, डिलीवरी का पता, स्टोर पिकअप विवरण और आर्डर की स्थिति शामिल होती है।
* **ईमेल 2: एडमिन को (`info@liquorjunctionghana.com`):**
  * सब्जेक्ट: `New Order Received`
  * इसमें प्रत्येक आइटम का विस्तृत विवरण, मात्रा, BOGO स्टेटस, डिलीवरी चार्ज, तथा घाना टैक्स ब्रेकडाउन (**NHIL, GETFund, VAT**) भेजा जाता है।

---

## 8. चरण 7: Admin Dashboard Order Lifecycle Management

एडमिन पैनल पर ऑर्डर की स्थिति बदलने की पूरी प्रक्रिया:

* **Controller:** `App\Http\Controllers\Dashboard\OrderController@updateOrderStatus`
* **Route:** `POST /adminorder/update-order-status` (`name: adminorder.updateOrderStatus`)

### स्टेटस ट्रांजिशन फ्लो (Status Transitions):

```
[Status 1: Pending] (Order Placed)
       │
       ▼ (Admin clicks Accept)
[Status 2: Accepted] (Store starts packing)
       │
       ├─────────────────────────────────┐
       ▼ (Home Delivery: order_type = 1) ▼ (Store Pickup: order_type = 2)
[Status 6: Out for Delivery]      [Status 5: Ready to Pick Up]
(Email Template 22 sent)          (Email Template 21 sent)
       │                                 │
       └────────────────┬────────────────┘
                        ▼ (Customer receives items)
              [Status 3: DELIVERED]
```

1. **Status 1 (Pending) ➔ Status 2 (Accepted):**
   * एडमिन ऑर्डर स्वीकार करता है और यदि आवश्यक हो तो इंटरनल नोट (`note`) जोड़ता है।
2. **Status 2 ➔ Status 5 (Ready to Pick Up) [केवल स्टोर पिकअप ऑर्डर्स के लिए]:**
   * इन-ऐप नोटिफिकेशन: *"Online Order ready to pick up"*।
   * ईमेल टेम्पलेट ID `21` ग्राहक को भेजा जाता है।
3. **Status 2 ➔ Status 6 (Out for Delivery) [केवल होम डिलीवरी ऑर्डर्स के लिए]:**
   * इन-ऐप नोटिफिकेशन: *"Online Order out for delivery"*।
   * ईमेल टेम्पलेट ID `22` ग्राहक को भेजा जाता है।

---

## 9. चरण 8: अंतिम चरण — Order Delivered (Status 3)

जब एडमिन या स्टोर एजेंट आर्डर को **Delivered (Status ID: 3)** पर सेट करता है, तो बैकएंड पर निम्नलिखित 5 महत्वपूर्ण कार्य निष्पादित होते हैं:

### 1. टैक्स कैलकुलेशन और DomPDF इनवॉइस जेनरेशन:
[`Dashboard\OrderController.php`](file:///d:/xampp/htdocs/liquorjunction/app/Http/Controllers/Dashboard/OrderController.php#L261-L318) में:
* BOGO डिस्काउंट, कूपन, कार्ट डिस्काउंट और रिवार्ड्स घटाकर SubTotal निकाला जाता है।
* आधिकारिक टैक्स मास्टर्स के आधार पर घाना टैक्स दरें जोड़ी जाती हैं:
  * `nhil = (taxes[1]->tax_value / 100) * subTotal`
  * `getFund = (taxes[2]->tax_value / 100) * subTotal`
  * `vat = (taxes[3]->tax_value / 100) * subTotal`
* **Barryvdh/DomPDF** इंजन द्वारा PDF तैयार की जाती है:
  ```php
  $pdf = Pdf::loadView('dashboard.layouts.orders', compact('orderData', 'orderDetails', 'settings', 'nhil', 'getFund', 'vat', 'subTotal', 'bogoDiscount', 'totalAmount'));
  $pdfFilePath = storage_path('app/public/uploads/invoices/order_' . $order_id . '.pdf');
  file_put_contents($pdfFilePath, $pdf->output());
  ```

### 2. लॉयल्टी पॉइंट्स का स्थायी एक्टिवेशन:
* जब आर्डर बना था, तब अंक `status = 2` (Pending) थे।
* **Delivered होते ही:**
  * **Earned Points (Credit):**
    ```php
    LoyaltyPoints::where('user_id', $orderData->user_id)
        ->where('id', $orderData->loyalty_credit_point_id)
        ->where('type', 'credit')
        ->update(['status' => 1]);
    ```
    *(अब ये पॉइंट्स ग्राहक के वॉलेट में एक्टिव हो जाते हैं और वह इन्हें अगले आर्डर पर इस्तेमाल कर सकता है)*
  * **Spent Points (Debit):**
    ```php
    LoyaltyPoints::where('user_id', $orderData->user_id)
        ->where('id', $orderData->loyalty_debit_point_id)
        ->where('type', 'debit')
        ->update(['status' => 1]);
    ```
    *(खर्च किए गए अंकों की कटौती स्थायी रूप से स्वीकृत हो जाती है)*

### 3. Firebase Push Notification (FCM):
* यदि कस्टमर का `device_token` उपलब्ध है, तो Firebase क्लाउड मैसेजिंग से ऐप पर पुश नोटिफिकेशन जाता है:
  ```php
  $title = "Delivered";
  $message = "Online Order delivered";
  \Helper::sendNotification($device_token, $title, $message, $order->id, $order_number);
  ```

### 4. इन-ऐप डेटाबेस नोटिफिकेशन:
* `notification` टेबल में नया रिकॉर्ड बनता है: `order_id = $order->id`, `title = "Delivered"`, `message = "Online Order delivered"`, `is_read = 0`।

### 5. ईमेल विथ इनवॉइस अटैचमेंट:
* ईमेल टेम्पलेट ID `16` ("Online Order delivered") लोड किया जाता है।
* ग्राहक के ईमेल पर सिस्टम जनरेटेड PDF इनवॉइस को अटैच करके भेजा जाता है:
  ```php
  Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data, $emaildetail, $pdfFilePath, $status) {
      $message->to($data['sendemail'], 'Liquor')->subject($emaildetail->subject);
      $message->from($data['from_email'], $emaildetail->title);
      if ($status == 3 && file_exists($pdfFilePath)) {
          $message->attach($pdfFilePath);
      }
  });
  ```

---

## 10. चरण 9: Order Cancellation & Inventory Restock

यदि ऑर्डर डिलीवर होने से पहले रद्द होता है (`status = 4`):

### 10.1 एडमिन द्वारा कैंसिलेशन:
* **Method:** `Dashboard\OrderController@updateStockOnCancelOrder($order_id)`
* कोड आर्डर के सभी `order_detail` आइटम्स को लूप करता है:
  ```php
  $variant_info->sold_qty -= $order_quantity;
  $variant_info->available_qty += $order_quantity;
  $variant_info->save();
  ```
* कौन रद्द कर रहा है उसका डेटा सेव होता है: `order->cancelled_by` (Admin/Country Admin), `order->cancelled_user = auth()->id()`।
* कैंसिलेशन ईमेल टेम्पलेट ID `20` ग्राहक को भेजा जाता है।

### 10.2 कस्टमर द्वारा कैंसिलेशन:
* **Controller:** `App\Http\Controllers\Frontend\OrderController@cancelOrder`
* **नियम:** ग्राहक केवल तब ही ऑर्डर रद्द कर सकता है जब तक उसका `order_status == 1` (Pending) हो।
* यदि स्टेटस 2, 5, या 6 हो चुका है, तो सिस्टम एरर देता है: *"This order cannot be cancelled"*।

---

## 11. चरण 10: Customer Post-Order Experience & Order Tracking

### 11.1 वेब पोर्टल पर:
* **ऑर्डर लिस्टिंग:** `GET /my-order` ➔ `Frontend\OrderController@index`
* **ऑर्डर डिटेल्स:** `GET /my-order-details/{id}` ➔ `Frontend\OrderController@myOrderDetails`
* **लाइव इनवॉइस डाउनलोड:** `GET /print-my-order/{id}` ➔ `Frontend\OrderController@printMyOrder` (ब्राउज़र में सीधे DomPDF स्ट्रीम होती है)।

### 11.2 मोबाइल ऐप REST API:
* **ऑर्डर लिस्ट:** `GET /api/v1/order-list` ➔ `Api\V1\UserController@orderList`
* **ऑर्डर ट्रैकिंग:** `GET /api/v1/track-order` ➔ `Api\V1\UserController@trackOrder`
  * यह एंडपॉइंट `order_id` लेकर `order_status` टेबल से स्टेटस का बहुभाषी नाम (`name` या `name_fr`) रिटर्न करता है।

---

## 12. मास्टर स्टेटस और टाइप संदर्भ तालिका

### 12.1 Order Status Codes (`order.order_status`)
| ID | Status Name | अर्थ और उपयोग |
| :---: | :--- | :--- |
| **1** | **Pending** | नया आर्डर बना है; भुगतान हो चुका है या COD है; स्टोर की स्वीकृति की प्रतीक्षा है। |
| **2** | **Accepted** | स्टोर ने आर्डर स्वीकार कर लिया है और पैकिंग शुरू हो चुकी है। |
| **3** | **Delivered** | अंतिम स्थिति — आर्डर ग्राहक तक पहुंच गया, इनवॉइस PDF बन गई, रिवार्ड पॉइंट्स वॉलेट में क्रेडिट हो गए। |
| **4** | **Cancelled** | आर्डर रद्द हो चुका है; स्टॉक वापस `ProductVariants` में जोड़ दिया गया है। |
| **5** | **Ready to Pick Up** | स्टोर पिकअप आर्डर (`order_type = 2`) तैयार है; ग्राहक स्टोर से ले सकता है। |
| **6** | **Out for Delivery** | होम डिलीवरी आर्डर (`order_type = 1`) डिलीवरी एजेंट के साथ निकल चुका है। |

### 12.2 Order Types (`order.order_type`)
| Type ID | Type Name | विवरण |
| :---: | :--- | :--- |
| **1** | **Online / Home Delivery** | ग्राहक के पते पर डिलीवर किया जाता है (डिलीवरी फीस लागू)। |
| **2** | **Cash On Delivery / Store Pickup** | स्टोर से ग्राहक स्वयं पिकअप करता है (कोई डिलीवरी फीस नहीं)। |
| **3** | **Purchase Order** | व्होलसेलर (Wholesaler / B2B) ऑर्डर। |

### 12.3 Payment Methods (`transactions.payment_type`)
| Payment ID | Name | कार्यप्रणाली |
| :---: | :--- | :--- |
| **1** | **MojoPay (Online Gateway)** | ऑनलाइन गेटवे रीडायरेक्शन; वेबहुक / रिटर्न कॉलबैक आने पर `OnlineOrderFulfillment::markPaid` द्वारा स्टॉक डिडक्शन और कन्फर्मेशन। |
| **3** | **Cash On Delivery (COD)** | आर्डर बनते ही तुरंत स्टॉक डिडक्शन; रीयल-टाइम एडमिन ब्रॉडकास्ट और कन्फर्मेशन। |

---
*दस्तावेज़ पूर्ण हुआ — स्रोत: संपूर्ण प्रोजेक्ट कोडबेस विश्लेषण।*
