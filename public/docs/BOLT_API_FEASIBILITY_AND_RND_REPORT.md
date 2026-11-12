# Bolt Send (Parcel Delivery API) — Comprehensive R&D, Feasibility & Advanced Architecture Report for Liquor Delivery

---

## 1. Executive Summary & R&D Verdict

### 1.1 Objective
Research and evaluate the **Bolt Ride Booker / Bolt Send API** (`https://ridebooker.bolt.eu/api-docs`) to determine whether and how it can be integrated into the **LiquorJunction** platform (`admin.new.liquorjunctionghana.com`) for on-demand **unaccompanied parcel and liquor delivery** within a single designated country (**Ghana: Accra, Kumasi, Takoradi**), covering the complete lifecycle from **Order Placement to Final Delivery & Financial Reconciliation**.

### 1.2 Feasibility Verdict: **FULLY FEASIBLE VIA "BOLT SEND" (`vehicle_category: "delivery"`)**
| Evaluation Metric | Status | Details |
| :--- | :---: | :--- |
| **Official Parcel Mode (Bolt Send)** | ✅ **Supported** | Bolt officially operates **Bolt Send** in Ghana (Accra, Kumasi, etc.) as an unaccompanied parcel delivery category. In the API schema, it is identified by `vehicle_category: "delivery"` or `"motorbike"`. |
| **No Passenger Requirement** | ✅ **Guaranteed** | Under Bolt Send, **no passenger travels in the vehicle**. The vehicle is dispatched solely to transport the sealed package from the store to the recipient. |
| **Single-Package Security** | ✅ **Guaranteed** | "One Trip = One Package": Bolt delivery drivers carry strictly one order per trip, preventing package mix-ups. |
| **Single-Country Operation** | ✅ **Supported** | All bookings are coordinate-based (`lat`, `lng`). Restricting operations to Ghana is enforced by geofencing store and customer destination coordinates within Ghana's borders. |
| **Customer Pinup & Admin Online Dispatch** | ✅ **Optimized Workflow** | Customers pin their exact gate on Google Maps during checkout (saving lat/lng with **zero Bolt API calls at checkout**). In Admin, a condition gate ensures only verified **Online Paid orders** unlock the Bolt Send dispatch modal. |
| **Geocoding & Location Selection** | 🔴 **Mandatory Requirement** | Bolt API **does not** accept text-only addresses. It strictly requires decimal coordinates (`lat`, `lng`). A Google Maps location picker / Geocoding integration on the address/checkout page is **100% mandatory**. |
| **Webhook Database Audit Logging** | 🔴 **Essential for Production** | Every incoming webhook must be logged into a persistent `bolt_webhook_logs` table for auditability, debugging, and dispute resolution ("Kab, Kya, Kese Call Hua"). |
| **Package Handover Model** | ⚠️ **Curbside Model** | Bolt Send operates on a **curbside basis**: Store fulfillment staff meets driver outside at vehicle to hand over box; recipient meets driver at street/gate upon arrival. |
| **Centralized Billing** | ✅ **Centralized** | Billed directly to the business account (`payment_method: "business"`), eliminating cash transactions between driver and store. |

---

## 2. End-to-End Advanced Lifecycle: From Order to Delivery

An enterprise-grade delivery system requires more than just calling an API; it requires handling packing gates, fare lock expirations, curbside handovers, customer notifications, and edge-case exceptions.

```
+-------------------------------------------------------------------------------------------------------------------------+
|                                    COMPLETE ADVANCED BOLT SEND DELIVERY LIFECYCLE                                       |
|                                                                                                                         |
| [ STAGE 1: CUSTOMER MAP PINNING & ORDER PAYMENT (NO BOLT API CALL) ]                                                    |
| Customer enters address -> Pins exact gate on Google Map -> lat/lng saved in user_address and snapshotted on order ->  |
| Standard platform delivery fee calculated -> Customer completes payment (e.g. MojoPay Online). Zero Bolt API overhead.  |
|                                                                                                                         |
| [ STAGE 2: STORE PACKING GATE (CRUCIAL PRE-DISPATCH) ]                                                                  |
| Order confirmed -> Store warehouse receives pick-list -> Staff packs bottles in shockproof sealed box (Max 15 kg) ->    |
| Staff marks "Packing Completed" in Admin. (Driver is NEVER called before bottles are boxed!).                          |
|                                                                                                                         |
| [ STAGE 3: ADMIN ONLINE PAYMENT CHECK & BOLT DISPATCH ]                                                                 |
| Admin checks condition: order_type == 1 (Delivery) AND payment_type == 1 (Online Paid). Button unlocks.                 |
| Admin clicks "Send Package via Bolt Send" -> Modal loads Store (Pickup) & Customer Pin (Dropoff) coordinates ->        |
| API calls Bolt /rides/estimations for live quote -> Admin confirms -> POST /rides/create books courier & saves ride_id. |
|                                                                                                                         |
| [ STAGE 4: STORE CURBSIDE HANDOVER ]                                                                                    |
| Bolt assigns nearby courier -> Webhook 'ARRIVED_AT_CLIENT' triggers audio alert on store dashboard ->                   |
| Fulfillment runner walks outside to vehicle -> Hands over box #LJ-1042 to driver -> Driver places in boot/top-box.      |
|                                                                                                                         |
| [ STAGE 5: IN-TRANSIT & LIVE CUSTOMER TRACKING ]                                                                        |
| Webhook 'DRIVING_WITH_CLIENT' fires -> bolt_webhook_logs records entry -> Automated SMS sent to customer with          |
| live GPS tracking link: "Your LiquorJunction order is on the way! Track driver here: https://tracking.bolt.eu/..."      |
|                                                                                                                         |
| [ STAGE 6: DESTINATION HANDOVER & AGE VERIFICATION ]                                                                    |
| Driver arrives outside customer gate -> Driver calls recipient Kwame Mensah (+233241234567) ->                          |
| Recipient meets driver at curb -> Shows valid 18+ ID -> Receives sealed carton.                                         |
|                                                                                                                         |
| [ STAGE 7: COMPLETION, INVOICING & RECONCILIATION ]                                                                     |
| Webhook 'COMPLETED' fires -> Order marked 'Delivered' -> API calls GET /rides/receipt -> Final charge saved in DB ->    |
| Automated financial reconciliation compares collected customer shipping fee vs actual Bolt corporate invoice.          |
+-------------------------------------------------------------------------------------------------------------------------+
```

---

## 3. Advanced Edge Cases & Exception Handling

In real-world Ghana operations, delivery workflows encounter edge cases that the software must automatically handle:

### 3.1 Fare Lock Expiration (`fare_id` expired)
* **Problem**: Bolt's `fare_id` generated during estimation typically expires after 2 to 5 minutes to prevent stale prices during surge pricing.
* **Solution**: When the admin clicks "Dispatch", the backend checks the timestamp of the quote. If older than 2 minutes, it silently requests a fresh `fare_id` before calling `/rides/create`, avoiding `EXPIRED_FARE_ID` errors.

### 3.2 No Driver Available (`NO_DRIVERS_AVAILABLE` or search timeout)
* **Problem**: During heavy rain or peak traffic hours in Accra, no nearby Bolt Send rider may be immediately available.
* **Solution**: 
  1. The system attempts 3 automated retries spaced 90 seconds apart.
  2. If still unassigned after 5 minutes, an alert appears on the Admin Order View: *"Bolt couriers currently busy in your area. [Retry Bolt Send] or [Assign In-House Store Rider]"*.

### 3.3 Driver Cancellation in Transit
* **Problem**: A driver accepts the trip, then cancels due to a vehicle breakdown or flat tyre.
* **Solution**: Bolt's webhook will transition state back to `SEARCHING` or send `CANCELLED` with a reason. The webhook controller detects this, alerts the store manager, and triggers an automated re-dispatch so the package is not delayed.

### 3.4 Unreachable Recipient at Destination
* **Problem**: Driver arrives outside the customer's gate in Accra, but the recipient does not answer the phone.
* **Solution**: Bolt drivers wait 5 minutes at the destination curb. If unanswered, the driver contacts support to return the parcel to the store. The webhook records this state, and the order status is updated to `delivery_unreachable` so customer care can intervene immediately.

### 3.5 Manual Cancellation & Fee Check
* **Problem**: A customer cancels their order after the store has already booked a Bolt Send courier.
* **Solution**: Before executing `/rides/cancel`, the system calls `GET /rides/cancellation_fee`. If a cancellation fee applies (e.g. 5.00 GHS because the driver was already en route for > 2 minutes), the system prompts the admin with the fee amount before confirming cancellation.

---

## 4. Bolt Send Package Specifications & Limitations

* **Maximum Weight**: **15 kg** (Easily accommodates standard cartons of 6 to 12 wine, champagne, or spirit bottles).
* **Maximum Dimensions**: **65 × 55 × 40 cm** (Must fit comfortably in a car boot, backseat, or courier motorbike top-box).
* **Handover Protocol**: **Curbside handover**. Neither sender nor driver performs inside door-to-door transit; packages are exchanged directly at the vehicle outside.
* **Packaging Standard**: Glass bottles must be bubble-wrapped, boxed, and securely taped with tamper-evident tape before driver arrival.

---

## 5. Geocoding & Location Selection Strategy (Mandatory)

### 5.1 Technical Requirement
Bolt endpoints (`POST /reseller/v2/rides/estimations` and `POST /reseller/v1/rides/create`) strictly require exact coordinates:
```json
"stops": [
  { "lat": 5.6037, "lng": -0.1870 },
  { "lat": 5.6320, "lng": -0.1654 }
]
```
If coordinates are missing or inaccurate:
1. Bolt API immediately returns `400 Bad Request / Validation Error`.
2. Bolt drivers navigating in Accra cannot locate unstructured addresses (e.g., *"behind the shell station near East Legon"*).

### 5.2 3-Layer Geocoding Architecture
1. **Google Places Autocomplete**: Customer searches verified street/landmark addresses in Ghana.
2. **Interactive Map Pin Picker**: Customer drags a red marker on Google Map directly to their building entrance, or clicks *"Use My GPS"*.
3. **Automated Backend Fallback**: If an existing address lacks coordinates, the backend `GeocodingService` calls Google Geocoding API to resolve the text address before dispatching to Bolt.

---

## 6. Complete API Endpoints Reference for Bolt Send

### 6.1 Schema Proof: `VehicleCategory` Enum
From the official OpenAPI specification (`open-api.json`), the vehicle category enum explicitly defines:
```json
"RideBookerApi.VehicleCategory": {
  "type": "string",
  "enum": [
    "delivery",        <-- Official Bolt Send category
    "motorbike",       <-- Motorbike Courier
    "standard",
    "comfort",
    "economy",
    "xl"
  ]
}
```

---

### 6.2 Step 1: Request Fare & ETA Estimation for Bolt Send
* **Endpoint**: `POST https://node.bolt.eu/ride-booker-api/reseller/v2/rides/estimations`
* **Headers**:
  * `Authorization: Bearer <access_token>`
  * `Content-Type: application/json`

#### Request Body
```json
{
  "stops": [
    { "lat": 5.6037, "lng": -0.1870 },
    { "lat": 5.6320, "lng": -0.1654 }
  ],
  "payment_methods": ["business"],
  "language": "en"
}
```

#### Response Body (Extracting `delivery` / `Bolt Send`)
```json
{
  "categories": [
    {
      "category_id": 14,
      "vehicle_category": "delivery",
      "name": "Bolt Send",
      "description": "Fast and reliable parcel delivery",
      "eta": 180,
      "estimates": [
        {
          "fare_id": "fare_gh_send_99281aef10",
          "payment_method": "business",
          "price": {
            "amount": 22.00,
            "currency": "GHS"
          },
          "price_breakdown": [
            { "name": "Base fare", "value": "5.00 GHS" },
            { "name": "Distance fare", "value": "13.00 GHS" },
            { "name": "Booking fee", "value": "4.00 GHS" }
          ]
        }
      ]
    }
  ]
}
```

---

### 6.3 Step 2: Create & Dispatch Bolt Send Ride
Pass the `fare_id` corresponding to the `delivery` category.

* **Endpoint**: `POST https://node.bolt.eu/ride-booker-api/reseller/v1/rides/create`

#### Request Body
```json
{
  "fare_id": "fare_gh_send_99281aef10",
  "note_to_driver": "BOLT SEND PARCEL: Order #LJ-1042. Sealed box handover at store counter. Recipient must be 18+.",
  "user": {
    "name": "Kwame Mensah",
    "phone": "+233241234567"
  },
  "stops": [
    {
      "lat": 5.6037,
      "lng": -0.1870,
      "address": "Liquor Junction Store, Osu, Accra"
    },
    {
      "lat": 5.6320,
      "lng": -0.1654,
      "address": "Airport Residential Area, Accra"
    }
  ]
}
```

#### Response Body
```json
{
  "ride_id": 10928374
}
```

---

### 6.4 Step 3: Track Live Parcel Details & Driver Info
* **Endpoint**: `GET https://node.bolt.eu/ride-booker-api/reseller/v1/rides/details?ride_id=10928374`

#### Response Body
```json
{
  "ride_id": 10928374,
  "state": "DRIVER_ON_ROUTE_TO_CLIENT",
  "eta_to_client": 180,
  "eta_to_destination": 720,
  "tracking_url": "https://tracking.bolt.eu/ride/abc123xyz",
  "vehicle": {
    "driver_name": "Samuel Boateng",
    "driver_phone": "+233201234567",
    "driver_picture": "https://images.bolt.eu/drivers/photo123.jpg",
    "car_model": "Toyota Vitz / Motorbike",
    "car_reg_number": "GE-4921-21"
  },
  "stops": [
    { "lat": 5.6037, "lng": -0.1870 },
    { "lat": 5.6320, "lng": -0.1654 }
  ],
  "user": {
    "name": "Kwame Mensah",
    "phone": "+233241234567"
  },
  "payment_method": "business",
  "created": "2026-10-28T10:00:00.000Z"
}
```

---

## 7. Webhooks & Persistent Database Audit Logging

### 7.1 Database Table: `bolt_webhook_logs`
Every incoming webhook is permanently recorded before and after processing for complete operational traceability.

```sql
CREATE TABLE `bolt_webhook_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id` VARCHAR(191) NULL INDEX,
  `event_type` VARCHAR(100) NULL INDEX,
  `ride_id` VARCHAR(100) NULL INDEX,
  `state` VARCHAR(50) NULL INDEX,
  `headers` JSON NULL,
  `payload` JSON NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `status` ENUM('RECEIVED', 'PROCESSED', 'FAILED', 'UNAUTHORIZED', 'IGNORED') DEFAULT 'RECEIVED',
  `response_code` INT DEFAULT 200,
  `response_body` JSON NULL,
  `error_message` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 7.2 Bolt Send State Mapping for Liquor Delivery
| Bolt Ride State | Meaning in Bolt Send Context | LiquorJunction Order Status | System Action |
| :--- | :--- | :--- | :--- |
| `SEARCHING` | Locating nearby Bolt Send driver/courier in Accra | `Processing / Dispatching` | Display searching spinner; log entry in DB |
| `DRIVER_ON_ROUTE_TO_CLIENT` | Driver driving to liquor store to collect parcel | `Driver Assigned` | Alert store fulfillment to bring box to curb; store driver details |
| `ARRIVED_AT_CLIENT` | Driver arrived outside store | `At Store / Pickup` | Store team hands sealed box to driver |
| `DRIVING_WITH_CLIENT` | Parcel picked up, driver en route to customer | `Out for Delivery` | Send customer SMS with live Bolt `tracking_url` |
| `COMPLETED` | Recipient received parcel at destination gate | `Delivered` | Mark delivered, send confirmation email, store receipt |
| `CANCELLED` | Trip cancelled | `Delivery Failed` | Notify store manager to re-dispatch |

---

## 8. Summary & Next Steps
With the advanced end-to-end functionality integrated into the architecture:
1. Implement the dynamic checkout estimation and packing gate in the Laravel code.
2. Review the detailed code in [BOLT_API_LARAVEL_IMPLEMENTATION_GUIDE.md](file:///d:/xampp/htdocs/liquorjunction/docs/BOLT_API_LARAVEL_IMPLEMENTATION_GUIDE.md).
