# 🛒 Advanced E-Commerce Faceted Filter System Specification
**Project:** Liquor Junction Ghana (`liquorjunction`)  
**Target:** Frontend Product Listing & Catalog Browsing (`/product-list`, `/filter-product-list`, `/special`, `/brand-list`)  
**Document Status:** Complete Architecture & Technical Design Specification  
**Author:** AI Technical Architecture Pair Programmer  
**Location:** Root Directory (`ADVANCED_ECOMMERCE_FILTER_SPECIFICATION.md`)

---

## 📑 Table of Contents
1. [Executive Summary & Concept](#1-executive-summary--concept)
2. [The Core Mechanism: Dynamic Dependent Facets (Faceted Search)](#2-the-core-mechanism-dynamic-dependent-facets)
3. [Complete Catalog Facets & Controls Breakdown (Existing & Advanced Filters)](#3-complete-catalog-facets--controls-breakdown)
4. [Admin Product Add/Edit Audit & Missing Attributes Gap Analysis](#4-admin-product-addedit-audit--missing-attributes-gap-analysis)
5. [Interdependence Matrix (360° Bidirectional Facet Synchronization)](#5-interdependence-matrix-360-bidirectional-facet-synchronization)
6. [UI / UX Architecture & Layout Wireframe](#6-ui--ux-architecture--layout-wireframe)
7. [Backend Data & Query Architecture (Laravel & MySQL)](#7-backend-data--query-architecture)
8. [Frontend Interaction & State Engine (AJAX + URL Sync)](#8-frontend-interaction--state-engine)
9. [Step-by-Step Implementation Roadmap](#9-step-by-step-implementation-roadmap)
10. [Design System Compliance & Native Styling Rules](#10-design-system-compliance--native-styling-rules)

---

## 1. Executive Summary & Concept

In standard e-commerce websites (Amazon, Shopify Plus, Myntra, ASOS), users rarely navigate by static pages. Instead, they use a **dynamic faceted filtering system**.

### The Problem with Static Filters:
* Static filters show all options regardless of context (e.g., showing *Vodka* brands when browsing *Wine*, or showing a Price Slider up to GH₵5,000 when the selected category only has items up to GH₵120).
* Selecting contradictory filters leads to the dreaded **"0 Products Found" dead-end**, frustrating shoppers and causing abandoned sessions.

### The Solution: Dynamic Faceted Navigation:
Every time any filter changes (Category, Price, Brand, Size, Rating, or Availability), the application **recalculates the available facets in real-time**:
1. **Adaptive Bounds:** The Price slider dynamically shifts its minimum and maximum to match only the currently matching products.
2. **Auto-Pruning / Dimming:** Brands, Categories, or Sizes that have `0` available products in the current price/category context either disappear or become disabled.
3. **Live Counter Badges:** Every checkbox shows the exact count of products it will yield (e.g., `Glenfiddich (12)`, `Jameson (8)`, `Jack Daniel's (0)`).
4. **Desktop Sticky Sidebar + Mobile Bottom Drawer:** True 2-column e-commerce layout on desktop and clean offcanvas on mobile.

---

## 2. The Core Mechanism: Dynamic Dependent Facets

```
                   ┌───────────────────────────────────────────────┐
                   │             USER APPLIES A FILTER             │
                   │   (e.g., Selects Category: "Whisky",         │
                   │    or sets Price: GH₵ 200 - GH₵ 800)          │
                   └──────────────────────┬────────────────────────┘
                                          │
                                          ▼
                   ┌───────────────────────────────────────────────┐
                   │           UNIFIED FACET QUERY ENGINE          │
                   │    Filters master inventory based on state     │
                   └──────────────────────┬────────────────────────┘
                                          │
               ┌──────────────────────────┴──────────────────────────┐
               ▼                                                     ▼
┌──────────────────────────────┐                      ┌──────────────────────────────┐
│       MATCHING PRODUCTS      │                      │      RECALCULATED FACETS     │
├──────────────────────────────┤                      ├──────────────────────────────┤
│ • Product Grid HTML rendered │                      │ • New Min / Max Price bounds │
│ • Active Filter Pills updated│                      │ • Relevant Brands + Counts   │
│ • Sort order preserved       │                      │ • Relevant Sizes + Units     │
│ • Pagination count updated   │                      │ • Zero-count items pruned    │
└──────────────────────────────┘                      └──────────────────────────────┘
```

---

## 3. Complete Catalog Facets & Controls Breakdown

### Facet 1: Active Filter Pills (Chips Bar)
* Placed right above the product grid.
* Shows every active filter tag with an `(X)` remove button:
  - `Category: Single Malt [✕]`
  - `Brand: Macallan [✕]`
  - `Price: GH₵ 200 - 800 [✕]`
  - `Rating: 4★ & above [✕]`
* Includes a **"Clear All"** link that resets all filters in one click.

### Facet 2: Category & Subcategory Tree
* **Hierarchy:** Parent Category (e.g., *Whisky*) with expandable subcategories (*Single Malt*, *Blended*, *Bourbon*, *Irish*).
* **Live Counts:** Displays count next to each category: `Whisky (48)`, `Wine (32)`, `Beer (14)`.
* **Dynamic Pruning:** If a price filter (e.g., GH₵ 2,000+) has no Beers, *Beer (0)* is hidden or greyed out.

### Facet 3: Dynamic Adaptive Price Range
* **Dual Range Slider:** Left slider (Min) and Right slider (Max).
* **Numeric Direct Inputs:** Two editable input fields (`Min: GH₵ [   ]` and `Max: GH₵ [   ]`) for precision entry.
* **Dynamic Range Limits:** If the user selects *Beer*, the slider automatically sets `min: 15` and `max: 180`. If the user selects *Luxury Champagne*, it shifts to `min: 250` and `max: 4500`.
* **Quick Price Buckets (One-click shortcuts):**
  - `[ ] Under GH₵ 50`
  - `[ ] GH₵ 50 - GH₵ 150`
  - `[ ] GH₵ 150 - GH₵ 500`
  - `[ ] Above GH₵ 500`

### Facet 4: Brands (with Instant Search)
* **Search within Brands:** An embedded search box (`Search Brands...`) to quickly filter long brand lists.
* **Live Product Counts:** `Jameson (14)`, `Chivas Regal (9)`, `Johnnie Walker (22)`.
* **Automatic Brand Context:** When *Vodka* is chosen, only Vodka brands are shown (*Absolut*, *Smirnoff*, *Grey Goose*).

### Facet 5: Bottle Volume & Pack Units (Variants)
* **Bottle Sizes (ml / L):**
  - `[ ] 330 ml` | `[ ] 500 ml` | `[ ] 750 ml` | `[ ] 1 Litre` | `[ ] 1.75 L`
* **Packaging Unit (`uofs`):**
  - `[ ] Single Bottle` | `[ ] Pack of 6 (Can)` | `[ ] Pack of 12` | `[ ] Carton`

### Facet 6: Customer Ratings & Reviews
* Visual star ratings with product counts:
  - `[ ] ★★★★☆ & Up (34)`
  - `[ ] ★★★☆☆ & Up (58)`
  - `[ ] ★★☆☆☆ & Up (71)`

### Facet 7: Promotions & Availability Toggles
* Special ecommerce badges mapped directly to database columns:
  - `[ ] ⭐ Bestsellers` (`product.is_product_bestseller = 1`) - Shows products curated and flagged as top sellers.
  - `[ ] 🔥 Special Offers (On Offer)` (`product.offer = 1` or active in `offers` table) - Shows products specifically tagged on promotional offer.
  - `[ ] 🎁 BOGO Deals` (`bogo` table active matching) - Buy 1 Get 1 Free products.
  - `[ ] 📦 In Stock Only` (`pv.available_qty > 0`) - Hides out-of-stock items.
  - `[ ] 🏬 Store Pickup Available` (`product.in_store = 1`)
  - `[ ] 🚚 Express Online Delivery` (`product.in_online = 1`)

### Facet 8: Alcohol by Volume (ABV Strength Bands)
* Fundamental filter for all liquor and beverage retail:
  - `[ ] 🚫 Alcohol-Free / 0.0% ABV` (Non-alcoholic beers, spirits, mocktails)
  - `[ ] 🍺 Low Alcohol (< 5% ABV)` (Ciders, light beers, coolers)
  - `[ ] 🍷 Medium Alcohol (5% – 15% ABV)` (Wines, sparkling wines, craft ales)
  - `[ ] 🍸 High / Fortified (15% – 30% ABV)` (Port, Sherry, Liqueurs, Aperitifs, Soju)
  - `[ ] 🥃 Full Strength Spirits (35% – 50%+ ABV)` (Whisky, Rum, Gin, Vodka, Tequila, Brandy)

### Facet 9: Country of Origin & Wine/Whisky Region
* Leverages existing `countries` & `regions` tables in database:
  - **Countries:** Scotland, France, Italy, Ireland, United States, Ghana, Mexico, South Africa, Japan, Spain.
  - **Regions:** Speyside, Islay, Highlands, Bordeaux, Champagne, Tuscany, Kentucky, Tequila (Jalisco), Western Cape.

### Facet 10: Flavour / Taste Profile & Aroma Notes
* Essential for helping shoppers choose by taste preference:
  - `[ ] Peaty & Smoky` (e.g. Islay single malts, mezcal)
  - `[ ] Sweet & Fruity` (e.g. fruit liqueurs, sweet wines, blended scotch)
  - `[ ] Crisp & Refreshing` (e.g. sauvignon blanc, pilsners, dry gins)
  - `[ ] Rich, Woody & Oaky` (e.g. aged bourbons, barrel-aged rums, cabernet sauvignon)
  - `[ ] Floral & Botanical` (e.g. artisanal gins, vermouths)
  - `[ ] Spiced & Warm` (e.g. spiced rums, spiced whiskeys)

### Facet 11: Vintage / Age Statement
* Premium classification for whiskeys, cognacs, and fine wines:
  - `[ ] Non-Vintage / No Age Statement (NAS)`
  - `[ ] 10 - 12 Years Old`
  - `[ ] 15 - 18 Years Old`
  - `[ ] 21+ Years Old`
  - `[ ] Vintage Year Selection` (e.g. 2016, 2018, 2020)

### Facet 12: Popularity & Most Viewed
* Integrates directly with `most_viewed_product` table (`mvp.count`) and existing session tracking (`most_view`):
  - `[ ] Trending / Most Viewed Items` (products with top views)

### Facet 13: Store Location / Wholesaler Outlet
* Integrates with `store_details` and `product.supplier_id`:
  - Filter products available at specific Liquor Junction branches / physical outlets (e.g. Accra Mall branch, Kumasi branch, Osu outlet).

### Facet 14: Dietary & Lifestyle Tags
* Modern beverage preferences:
  - `[ ] Vegan Certified`
  - `[ ] Organic / Bio`
  - `[ ] Gluten-Free`
  - `[ ] Zero Sugar / Low Calorie`

### Facet 15: Sort By Controls (Top Right Bar)
* `Featured / Recommended` (Default)
* `Price: Low to High`
* `Price: High to Low`
* `Customer Rating: Highest First`
* `Newest Arrivals`
* `Best Sellers / Popular`
* `Biggest Discount / Savings`

---

## 4. Admin Product Add/Edit Audit & Missing Attributes Gap Analysis

An end-to-end audit was conducted across the Admin Dashboard controllers and views:
- `resources/views/dashboard/product/create.blade.php`
- `resources/views/dashboard/product/edit.blade.php`
- `app/Http/Controllers/Dashboard/ProductController.php`
- `app/Models/Product.php` & `app/Models/ProductVariants.php`

### 4.1 What Currently Exists on the Add/Edit Form
| Field Name | Type | Stored In | Description |
|---|---|---|---|
| `product_name` | Text Input | `product.product_name` | Product Title |
| `brand_id` | Select Dropdown | `product.brand_id` | Foreign key to `brand` table |
| `category_id` | Select Dropdown | `product.category_id` | Foreign key to `categories` table |
| `subcategory_id` | AJAX Dependent Select | `product.subcategory_id` | Foreign key to `sub_categories` table |
| `property_images[]` | File Upload (Multiple, max 5) | `product_image` table | Image gallery |
| `video` | File Upload (MP4, max 20MB) | `product.video` | Showcase video |
| `prod_variant.*.variant_uof` | Select Dropdown | `product_variants.variant_uof` | Unit of measure (`uofs` table) |
| `prod_variant.*.variant_size` | Number Input | `product_variants.variant_size` | Size numeric value (e.g. 750) |
| `prod_variant.*.variant_price` | Number Input | `product_variants.variant_price` | Original retail price |
| `prod_variant.*.variant_qty` | Number Input | `product_variants.variant_qty` & `available_qty` | Inventory quantity |
| `short_description` | Textarea (max 150 chars) | `product.short_description` | Short excerpt |
| `page_content` | Summernote WYSIWYG | `product.description` | Detailed description |
| `is_product_bestseller` | Checkbox (0 / 1) | `product.is_product_bestseller` | Curated bestseller flag |
| `offer` | Checkbox (0 / 1) | `product.offer` | Special offer badge flag |

---

### 4.2 Critical Gaps & Missing Features in Product Add/Edit

#### 🔴 Gap 1: Variant Discounted Price is Commented Out in HTML
* In both `create.blade.php` (lines 266-278) and `edit.blade.php` (lines 321-333), the input `<input name="prod_variant[][variant_discounted_price]">` is commented out (`{{-- ... --}}`).
* **Consequence:** The admin has no UI to provide variant-specific sale prices. In the database, `product_variants.variant_discounted_price` already exists!
* **Fix:** Re-enable the discounted price field in the variant repeater with validation ensuring `variant_discounted_price < variant_price`.

#### 🔴 Gap 2: Fulfilment Toggles (`in_store` & `in_online`) Missing from Form
* The `product` table and model `$fillable` already define `in_store` and `in_online` (and backend controllers reference them).
* **Consequence:** There are no checkboxes on the Add/Edit form for "Available for In-Store Pickup" and "Available for Online Delivery".
* **Fix:** Add two toggle checkboxes in the product form:
  - `[x] Available for Online Express Delivery` (`in_online`)
  - `[x] Available for In-Store Pickup` (`in_store`)

#### 🔴 Gap 3: Missing Liquor-Specific Attributes (ABV, Country, Region, Flavour, Age)
Liquor e-commerce relies heavily on specialized product parameters that are currently absent from the form and table:
1. **Alcohol by Volume (`abv`):** e.g., `40.0%`, `12.5%`, `0.0%`. Crucial for strength filters.
2. **Country of Origin (`country_id`):** The `countries` table already exists in the project (`Country.php` model), but is not connected to products.
3. **Region / Appellation (`region_id`):** The `regions` table already exists (`Region.php` model), but is not linked to products.
4. **Flavour / Taste Profile (`taste_profile`):** Tags or multi-select for Sweet, Smoky, Peaty, Fruity, Crisp, Oaky, Dry, etc.
5. **Vintage / Age Statement (`vintage_year` / `age_statement`):** Age number (e.g. 12, 18) or harvest year.
6. **Container Type (`container_type`):** Glass Bottle, Aluminum Can, Cask/Keg, Gift Box Set.
7. **Dietary / Lifestyle Tags (`dietary_tags`):** Vegan, Gluten-Free, Organic, Sugar-Free.

#### 🔴 Gap 4: Missing Low-Stock Alert Threshold
* Currently only `variant_qty` exists. There is no `min_stock_alert` threshold to notify admin when stock drops below 5 bottles, or to trigger "Only 3 left in stock!" urgency badges on the frontend.

#### 🔴 Gap 5: Physical Barcode / EAN / UPC Field
* `sku` is currently auto-generated from product name. However, physical liquor retail uses scanned EAN-13 / UPC barcodes for inventory and point-of-sale alignment.

---

### 4.3 Database Schema Migration Blueprint for Missing Attributes

To support both advanced product filtering and complete admin catalog management, create a new Laravel migration:

```php
// database/migrations/xxxx_xx_xx_add_liquor_attributes_to_product_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            // Liquor Specific Characteristics
            $table->decimal('abv', 5, 2)->nullable()->after('brand_id')->comment('Alcohol By Volume % e.g. 40.00');
            $table->unsignedBigInteger('country_id')->nullable()->after('abv')->index();
            $table->unsignedBigInteger('region_id')->nullable()->after('country_id')->index();
            $table->string('taste_profile', 255)->nullable()->after('region_id')->comment('Comma-separated: smoky, peaty, fruity');
            $table->integer('age_statement')->nullable()->after('taste_profile')->comment('Years aged, e.g. 12, 18');
            $table->year('vintage_year')->nullable()->after('age_statement')->comment('Wine vintage year e.g. 2018');
            $table->string('container_type', 50)->nullable()->after('vintage_year')->default('Bottle')->comment('Bottle, Can, Keg, Gift Set');
            $table->string('dietary_tags', 255)->nullable()->after('container_type')->comment('Comma-separated: vegan, gluten_free, organic');
            
            // Operational & Retail Enhancements
            $table->string('barcode', 50)->nullable()->after('sku')->index();
            $table->integer('min_stock_alert')->default(5)->after('product_qty');
            $table->boolean('is_featured')->default(0)->after('is_product_bestseller')->index();
            $table->boolean('is_new_arrival')->default(0)->after('is_featured')->index();
        });
    }

    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn([
                'abv', 'country_id', 'region_id', 'taste_profile',
                'age_statement', 'vintage_year', 'container_type',
                'dietary_tags', 'barcode', 'min_stock_alert',
                'is_featured', 'is_new_arrival'
            ]);
        });
    }
};
```

---

## 5. Interdependence Matrix (360° Bidirectional Facet Synchronization)

> ⚡ **CRITICAL RULE:** Applying ANY filter must update NOT ONLY the product list, but **THE FILTER BAR ITSELF**!
> If an option in Category, Subcategory, Brand, Size, Bestseller, or Offer has `0` products under the current selection, **it must be automatically removed from the filter bar** (or disabled/hidden). The customer must **NEVER** encounter a "0 Products Found" dead-end.

| Filter Triggered | What happens to Categories & Subcategories? | What happens to Price (Min / Max)? | What happens to Brands? | What happens to Variants (Size / Unit)? | What happens to Bestsellers & Offers? |
|---|---|---|---|---|---|
| **Category / Subcategory Selected** | Non-matching subcategories vanish; parent remains active. | Slider bounds clamp to the exact min/max prices of this category. | Only brands producing items in this category remain visible. | Only bottle sizes available in this category appear. | Bestseller / Offer options update to reflect only items in this category. (If 0, they hide). |
| **Price Range Changed** | Categories/subcategories with NO products in this price range **are removed** from the list. | Slider holds chosen range; bounds adjust to valid products. | Brands with NO products in this price range **disappear**. | Sizes with NO items in this price range **disappear**. | If no Bestseller or Offer products exist in this price range, their checkboxes **disappear**. |
| **Brand Selected** | Categories narrow to only categories this brand produces. | Slider min/max clamps to this brand's lowest and highest prices. | Selected brand checked; other brands show their relative count. | Sizes narrow to this brand's portfolio. | Bestseller / Offer flags update for this brand only. |
| **Bestsellers Toggled** | Categories with no bestsellers disappear. | Slider adapts to bestseller price extremes. | Brands with no bestsellers disappear. | Sizes adapt to bestseller items only. | Bestseller remains checked; Offer updates relative to bestsellers. |
| **Is Offer Toggled** | Categories with no active offers disappear. | Slider bounds adapt to active offer prices. | Brands with no offers disappear. | Sizes adapt to items with active offers. | Offer remains checked; Bestseller updates relative to offers. |
| **Bottle Size / Variant Selected** | Categories without this size disappear. | Price adapts to products with this size. | Brands without this size disappear. | Selected size checked; other sizes update counts. | Bestseller & Offer update for this size. |
| **Clear All Clicked** | All categories & subcategories reappear with full catalog counts. | Slider bounds reset to global catalog min/max. | All brands reappear with full counts. | All sizes reappear. | All promo checkboxes reappear with global counts. |

---

### 🔄 How the Filter Bar Re-Renders in Real-Time (Technical Mechanics)

Each AJAX response returns both:
1. `html`: The updated product cards grid (`#table_data`).
2. `facets`: The fresh facet counts, active bounds, and available IDs:
```json
{
  "total_count": 28,
  "price_bounds": { "min": 85, "max": 650 },
  "facets": {
    "categories": [
      { "id": 4, "title": "Whisky", "count": 28, "subcategories": [ { "id": 12, "title": "Single Malt", "count": 18 }, { "id": 14, "title": "Blended", "count": 10 } ] }
    ],
    "brands": [
      { "id": 19, "title": "Glenfiddich", "count": 9 },
      { "id": 22, "title": "Macallan", "count": 7 },
      { "id": 31, "title": "Jameson", "count": 12 }
    ],
    "sizes": [
      { "size": "750", "unit": "ml", "count": 20 },
      { "size": "1000", "unit": "ml", "count": 8 }
    ],
    "promotions": {
      "bestsellers_count": 6,
      "offers_count": 4,
      "bogo_count": 0
    }
  }
}
```

#### DOM Auto-Pruning Routine (Client-Side):
When the AJAX response arrives, the JavaScript engine performs an **instant DOM synchronization**:
* **Categories / Subcategories:** Any item whose ID is not in `facets.categories` or has `count == 0` is smoothly faded out / removed (`display: none;`).
* **Brands:** Any brand checkbox not in `facets.brands` is hidden. Matching brands update their count badges: `Jameson (12)`.
* **Price Slider:** If the current slider bounds exceed `price_bounds.min` or `price_bounds.max`, the slider limits and tooltips automatically clamp to the new valid numbers.
* **Bestseller & Offer Checkboxes:**
  - If `promotions.bestsellers_count === 0`, the Bestseller checkbox and label are **hidden** (`display: none;`).
  - If `promotions.offers_count === 0`, the Special Offers checkbox is **hidden**.
  - If `promotions.bogo_count === 0`, the BOGO checkbox is **hidden**.
* **Variants (Sizes & Units):** Sizes not in `facets.sizes` are hidden.

---

## 6. UI / UX Architecture & Layout Wireframe

### Desktop View (Width >= 992px)
```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ TOP HEADER & CATEGORY NAVIGATION BAR                                                   │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ Breadcrumbs: Home / Catalog / Whisky / Single Malt                                     │
├─────────────────────────┬──────────────────────────────────────────────────────────────┤
│ 🏷️ LEFT SIDEBAR FILTER  │ 📦 PRODUCT CATALOG AREA                                      │
│ (Sticky position: top)  │                                                              │
│                         │ Active Pills: [Whisky ✕] [GH₵ 200-800 ✕]   [Clear All]       │
│ ┌─────────────────────┐ │ Showing 1-16 of 42 Items                     Sort: [Price: Low ▼]│
│ │ ✕ Active Filters    │ ├──────────────────────────────────────────────────────────────┤
│ ├─────────────────────┤ │ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐           │
│ │ 📂 Categories       │ │ │ [Image]      │ │ [Image]      │ │ [Image]      │           │
│ │  ▶ Whisky (42)      │ │ │ Jameson      │ │ Glenfiddich  │ │ Chivas Regal │           │
│ │  ▷ Wine (18)        │ │ │ 750 ml       │ │ 1 Litre      │ │ 750 ml       │           │
│ │  ▷ Beer (0) [greyed]│ │ │ GH₵ 220      │ │ GH₵ 580      │ │ GH₵ 310      │           │
│ ├─────────────────────┤ │ │ [Add to Cart]│ │ [Add to Cart]│ │ [Add to Cart]│           │
│ │ 💰 Price Range      │ │ └──────────────┘ └──────────────┘ └──────────────┘           │
│ │  ●────────────●     │ │ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐           │
│ │  Min: 200  Max: 800 │ │ │ [Image]      │ │ [Image]      │ │ [Image]      │           │
│ ├─────────────────────┤ │ │ ...          │ │ ...          │ │ ...          │           │
│ │ 🏷️ Brands           │ │ └──────────────┘ └──────────────┘ └──────────────┘           │
│ │  [🔍 Search Brand ] │ │                                                              │
│ │  ☑ Glenfiddich (12) │ │ ┌──────────────────────────────────────────────────────────┐ │
│ │  ☑ Macallan (8)     │ │ │               [ LOAD MORE PRODUCTS (16/42) ]             │ │
│ │  ☐ Bushmills (4)    │ │ └──────────────────────────────────────────────────────────┘ │
│ ├─────────────────────┤ └──────────────────────────────────────────────────────────────┘
│ │ 📏 Bottle Size      │                                                                │
│ │  ☐ 750ml | ☐ 1L     │                                                                │
│ ├─────────────────────┤                                                                │
│ │ ⭐ Rating           │                                                                │
│ │  ☐ ★★★★☆ & Up       │                                                                │
│ └─────────────────────┘                                                                │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### Mobile & Tablet View (Width < 992px)
* Left sidebar is hidden (`d-none d-lg-block`).
* A sticky bottom bar or top filter button appears: `[ ⚡ Filter & Sort (3 Active) ]`.
* Tapping opens the Offcanvas drawer from the bottom/right, featuring the exact same responsive facets.

---

## 7. Backend Data & Query Architecture

### Unified Controller Method: `ProductController@facetedFilter`
The server handles both initial page load and AJAX requests through a single high-performance query pipeline:

```php
public function getFacetedProducts(Request $request)
{
    // 1. Base Active Products Query
    $baseQuery = Product::query()
        ->where('status', 1)
        ->whereHas('get_product_images', fn($q) => $q->where('status', 1))
        ->whereHas('get_category', fn($q) => $q->where('status', 1));

    // 2. Clone for Facet Calculations (Before applying self-narrowing constraints)
    // Dynamic Price Range for current category/brand selection:
    $priceStats = (clone $baseQuery)
        ->join('product_variants as pv', 'product.id', '=', 'pv.product_id')
        ->selectRaw('MIN(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as min_price')
        ->selectRaw('MAX(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as max_price')
        ->first();

    // 3. Dynamic Brands with live product counts:
    $availableBrands = Brand::where('status', 1)
        ->withCount(['products' => function($q) use ($request) {
            // Applies category & price filters to brand count
            $this->applyScopeFilters($q, $request, ['brand']);
        }])
        ->having('products_count', '>', 0)
        ->orderBy('title', 'asc')
        ->get();

    // 4. Dynamic Categories with live product counts:
    $availableCategories = Categories::where('status', 1)
        ->withCount(['products' => function($q) use ($request) {
            $this->applyScopeFilters($q, $request, ['category']);
        }])
        ->having('products_count', '>', 0)
        ->get();

    // 5. Apply All Filters to Product Grid Output:
    $products = (clone $baseQuery);
    $this->applyScopeFilters($products, $request);

    // 6. Sorting:
    $this->applySorting($products, $request->get('sort_by', 'featured'));

    // 7. Pagination / Limit:
    $limit = 16;
    $totalCount = $products->count();
    $productData = $products->paginate($limit);

    // 8. Bulk Eager-Loading (Zero N+1 Query Overhead):
    $productIds = $productData->pluck('id')->toArray();
    $activeBogos = $this->getBulkBogos($productIds);
    $activeOffers = $this->getBulkOffers($productIds);
    $userFavorites = auth()->guard('user')->check() 
        ? FavoriteProduct::where('user_id', auth()->guard('user')->id())->whereIn('product_id', $productIds)->where('status', 1)->pluck('product_id')->toArray() 
        : [];

    // 9. Return JSON for AJAX, or View for Full Page
    if ($request->ajax()) {
        return response()->json([
            'html' => view('frontend.product.ajax_filter_product', compact('productData', 'activeBogos', 'activeOffers', 'userFavorites'))->render(),
            'total_count' => $totalCount,
            'price_bounds' => [
                'min' => floor($priceStats->min_price ?? 0),
                'max' => ceil($priceStats->max_price ?? 1000)
            ],
            'facets' => [
                'brands' => $availableBrands,
                'categories' => $availableCategories,
            ],
            'active_filters' => $this->extractActiveFilters($request)
        ]);
    }

    return view('frontend.product.product-list', compact(
        'productData', 'availableBrands', 'availableCategories', 'priceStats', 'totalCount'
    ));
}
```

### ⚡ Critical Query Optimization & Database Indexing Rules
To guarantee sub-100ms faceted responses even with thousands of catalog items:
1. **Eliminate N+1 Loop Queries:** The current `ajax_filter_product.blade.php` executes individual `Helper::userFavoriteProduct()`, `Bogo::where()`, and `Offers::where()` queries inside `@foreach ($productData)`. In the new engine, all Bogos, Offers, and User Favorites **MUST be pre-fetched in bulk** using `whereIn('product_id', $productIds)`.
2. **Compound Database Indexes:**
   ```sql
   ALTER TABLE `product` ADD INDEX `idx_prod_cat_status` (`category_id`, `status`);
   ALTER TABLE `product` ADD INDEX `idx_prod_subcat_status` (`subcategory_id`, `status`);
   ALTER TABLE `product` ADD INDEX `idx_prod_brand_status` (`brand_id`, `status`);
   ALTER TABLE `product` ADD INDEX `idx_prod_bestseller` (`is_product_bestseller`, `status`);
   ALTER TABLE `product_variants` ADD INDEX `idx_variant_prod_avail` (`product_id`, `status`, `available_qty`);
   ```

---

## 8. Frontend Interaction & State Engine

### Live Debounced AJAX + Browser URL Sync
Instead of relying on an "Apply" button or full page reload, the client-side engine uses **Instant Debounced Reactive Updates**:

1. **Checkbox Changes (`change` event):** Instantly triggers AJAX fetch (`300ms debounce`).
2. **Price Slider (`input` event):** Live updates the price display text immediately, then triggers AJAX after user stops sliding (`400ms debounce`).
3. **URL Synchronization (`history.pushState`):**
   - When filters change, the browser URL updates:  
     `/product-list?category=whisky&brands=jameson,glenfiddich&min_price=200&max_price=800&sort=price_asc`
   - **Benefit:** If the user bookmarks the page, refreshes, or sends the link to a friend, the exact filtered list opens!
4. **Skeleton Loading:** Product cards show a sleek animated placeholder shimmer while AJAX fetches, avoiding jarring screen jumps.

### 🛡️ Production Edge-Case Handling Rules

#### 1. Pagination & "Show More" State Management:
* **Filter Change Reset:** Whenever ANY filter (category, brand, price, sort) changes, `current_page_count` MUST reset to `0`, and the grid container `#table_data` must be **replaced** (`.html(response.html)`), NOT appended.
* **Show More Click:** Clicking "Show More" leaves active filters intact, increments `current_page_count` by 16, and **appends** (`.append(response.html)`) the new cards to `#table_data`.
* **Counter Badge Sync:** `#p-current-count` and `#p-total-count` update dynamically with each page increment.

#### 2. Search Keyword Coexistence (`?search=...`):
* When shoppers search from the top global search bar (e.g., `?search=vodka`), the keyword is retained in the filter state engine.
* The keyword renders as an active pill: `Search: "vodka" [✕]`.
* Clicking `[✕]` on the search pill clears the search query while preserving any other selected filters (e.g. Price or Brand).

#### 3. Delegated Event Handlers (No Broken Cart / Wishlist Actions):
* Re-rendering `#table_data` via AJAX destroys previous DOM nodes.
* **Rule:** All interactive click events on product cards MUST use jQuery event delegation on `document`:
  ```javascript
  // Delegated Add-to-Cart
  $(document).on('click', '.add-bucket', function(e) {
      e.preventDefault();
      const productId = $(this).data('product-id');
      const variantId = $(this).data('variant-id');
      addToCartAjax(productId, variantId);
  });

  // Delegated Wishlist / Favorite
  $(document).on('change', '.favs-icon', function() {
      const productId = $(this).data('product-id');
      const isFav = $(this).is(':checked') ? 1 : 0;
      productFav(productId, isFav);
  });
  ```

#### 4. Mobile Drawer UX ("View Results" Sticky Action):
* On mobile screens (< 992px), users should not have the offcanvas drawer snap shut on every single checkbox tap.
* A sticky bottom action bar sits at the bottom of `#filteroffcanvas`:
  - `[ Clear All ]` (White outline button)
  - `[ View 18 Products ]` (Solid gold button)
* Tapping checkboxes live-updates the count inside `View (N) Products` in the background, and tapping "View Products" closes the drawer to reveal the refreshed catalog.

#### 5. Graceful Empty State (Zero Results Fallback):
* If a customer manually tampers with URL parameters or chooses contradictory filters, avoid displaying an ugly broken layout.
* Render a branded empty state with:
  - 🍷 Friendly graphic: *"We couldn't find any products matching your exact filters."*
  - CTA Button: `[ Reset All Filters ]`
  - Carousel: *"Here are some of our all-time Bestsellers instead:"* (renders 4 top bestseller cards).

---

## 9. Step-by-Step Implementation Roadmap

| Phase | Milestone | Actions & Deliverables |
|---|---|---|
| **Phase 1** | **Database & Admin Add/Edit Enhancement** | Add migration for missing liquor attributes (`abv`, `country_id`, `region_id`, `taste_profile`, `age_statement`, `vintage_year`, `dietary_tags`, `in_store`, `in_online`), re-enable `variant_discounted_price` in product add/edit forms. |
| **Phase 2** | **Query & Controller Engine** | Refactor `ProductController::list` & `filterData` into a unified faceted query service that returns products + real-time facet counts + price bounds + new liquor facets. |
| **Phase 3** | **Desktop Sidebar Component** | Create reusable blade partial `resources/views/frontend/product/components/filter-sidebar.blade.php` containing Accordion Categories, Price Slider, Searchable Brands, ABV strength bands, Country/Region, Variants, Ratings, and Offer toggles. |
| **Phase 4** | **Grid Layout Refactoring** | Update `product-list.blade.php` and `filtered-product-list.blade.php` from `col-12` to `col-lg-3` (Sidebar) + `col-lg-9` (Products Grid), keeping `d-lg-none` offcanvas for mobile. |
| **Phase 5** | **Reactive JS Engine** | Build `faceted-filter.js`: Real-time facet DOM syncing, URL history sync (`pushState`), debounced price slider, active tag removal, and dynamic facet pruning. |
| **Phase 6** | **Header & Navigation polish** | Ensure `/product-list` without category ID opens a full catalog view with all categories available in the sidebar. |
| **Phase 7** | **Quality Verification** | Test responsive breakpoints (Mobile 375px, Tablet 768px, Desktop 1200px+), verify 0-count facet hiding, test browser back/forward URL synchronization. |

---

## 10. Design System Compliance & Native Styling Rules

To guarantee that the new filter perfectly matches Liquor Junction's existing UI and looks 100% native (not like an alien plugin), the implementation strictly follows the existing styling guidelines extracted directly from `style.css` and `head.blade.php`:

### 🎨 Brand Color Palette
* **Primary Gold / Yellow:** `#fbb516` (Buttons, active hover borders, badges, slider highlights)
* **Charcoal Black / Text:** `#242424` (Headings, primary labels, hover buttons)
* **Secondary Text / Muted:** `#858584` / `#757575` (Product units, secondary subtitles, facet counts)
* **Light Greys & Borders:** `#f1f1f1` (Card background, dividers), `#e4e2e2` (Facet borders)
* **Pure White:** `#ffffff` (Card background, container fills)

### 🔤 Typography & Fonts
* **Font Family:** `'Outfit', sans-serif` (Strictly loaded via Google Fonts `wght@400;500;600;700`)
* **Filter Headings:** `font-size: 15px; font-weight: 600; line-height: 22px; color: #242424;`
* **Filter Options & Labels:** `font-size: 14px; font-weight: 400; color: #242424; line-height: 21px;`
* **Facet Count Badges:** `font-size: 12px; font-weight: 500; color: #858584;`

### 🧩 Native Reusable Classes to Preserve
* **Collapsible Filter Section:** `.filter-listing` and `.filter-title` with the native rotating chevron icon (`icon_chevron.svg`)
* **Checkbox & Label Styling:** `.check-group`, `.form-check-input`, `.form-check-label`
* **Brand Yellow Buttons:** `.solid-button` (`background: #fbb516; color: #242424; font-weight: 700;`)
* **White Outline Buttons:** `.clear-button` (`border: 1px solid #242424; color: #242424;`)
* **Product Card Grid:** `.bs-box`, `.bs-image`, `.bs-content`, `.price-wrapper`, `.sell-price`, `.original-price`
* **Discount / Promo Badges:** `.bogo` and `.offer` (`border-radius: 28px; background: #fbb516; font-weight: 700;`)

### 🚫 Rules Against Design Degradation
* **No Third-Party CSS Frameworks:** No jQuery UI Theme / Tailwind / extra bloat. Everything runs on the website's native **Bootstrap 5.x** + `style.css`.
* **Zero Layout Shifts (CLS):** Clean CSS transition animations (`transition: all 0.3s ease;`) for filter accordion collapse/expand and responsive grid resizing.
* **Sticky Sidebar Behavior:** Left sidebar uses `position: sticky; top: 120px; z-index: 10;` so it stays comfortably in view as the customer scrolls down through product results.

---

> 💡 **Next Step:** Whenever you are ready to begin, review this specification and let us know which phase to start executing first!
