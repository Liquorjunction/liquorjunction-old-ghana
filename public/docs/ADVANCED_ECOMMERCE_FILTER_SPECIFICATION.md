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
3. [Complete Catalog Facets & Controls Breakdown](#3-complete-catalog-facets--controls-breakdown)
4. [Interdependence Matrix (How Filters Affect Each Other)](#4-interdependence-matrix)
5. [UI / UX Architecture & Layout Wireframe](#5-ui--ux-architecture--layout-wireframe)
6. [Backend Data & Query Architecture (Laravel & MySQL)](#6-backend-data--query-architecture)
7. [Frontend Interaction & State Engine (AJAX + URL Sync)](#7-frontend-interaction--state-engine)
8. [Step-by-Step Implementation Roadmap](#8-step-by-step-implementation-roadmap)
9. [Design System Compliance & Native Styling Rules](#9-design-system-compliance--native-styling-rules)

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
  - `[ ] 🔥 Special Offers (On Offer)` (`product.offer = 1`) - Shows products specifically tagged on promotional offer.
  - `[ ] 🎁 BOGO Deals` (`bogo` table active matching) - Buy 1 Get 1 Free products.
  - `[ ] 📦 In Stock Only` (`pv.available_qty > 0`) - Hides out-of-stock items.
  - `[ ] 🏬 Store Pickup / Express Delivery` (`product.in_store = 1` / `product.in_online = 1`)

### Facet 8: Sort By Controls (Top Right Bar)
* `Featured / Recommended` (Default)
* `Price: Low to High`
* `Price: High to Low`
* `Customer Rating: Highest First`
* `Newest Arrivals`
* `Best Sellers / Popular`

---

## 4. Interdependence Matrix (360° Bidirectional Facet Synchronization)

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

## 5. UI / UX Architecture & Layout Wireframe

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

## 6. Backend Data & Query Architecture

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
    $limit = 18;
    $totalCount = $products->count();
    $productData = $products->paginate($limit);

    // 8. Return JSON for AJAX, or View for Full Page
    if ($request->ajax()) {
        return response()->json([
            'html' => view('frontend.product.ajax_product', compact('productData'))->render(),
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

---

## 7. Frontend Interaction & State Engine

### Live Debounced AJAX + Browser URL Sync
Instead of relying on an "Apply" button or full page reload, the client-side engine uses **Instant Debounced Reactive Updates**:

1. **Checkbox Changes (`change` event):** Instantly triggers AJAX fetch (`300ms debounce`).
2. **Price Slider (`input` event):** Live updates the price display text immediately, then triggers AJAX after user stops sliding (`400ms debounce`).
3. **URL Synchronization (`history.pushState`):**
   - When filters change, the browser URL updates:  
     `/product-list?category=whisky&brands=jameson,glenfiddich&min_price=200&max_price=800&sort=price_asc`
   - **Benefit:** If the user bookmarks the page, refreshes, or sends the link to a friend, the exact filtered list opens!
4. **Skeleton Loading:** Product cards show a sleek animated placeholder shimmer while AJAX fetches, avoiding jarring screen jumps.

---

## 8. Step-by-Step Implementation Roadmap

| Phase | Milestone | Actions & Deliverables |
|---|---|---|
| **Phase 1** | **Query & Controller Engine** | Refactor `ProductController::list` & `filterData` into a unified faceted query service that returns products + real-time facet counts + price bounds. |
| **Phase 2** | **Desktop Sidebar Component** | Create reusable blade partial `resources/views/frontend/product/components/filter-sidebar.blade.php` containing Accordion Categories, Price Slider, Searchable Brands, Variants, Ratings, and Offer toggles. |
| **Phase 3** | **Grid Layout Refactoring** | Update `product-list.blade.php` and `filtered-product-list.blade.php` from `col-12` to `col-lg-3` (Sidebar) + `col-lg-9` (Products Grid), keeping `d-lg-none` offcanvas for mobile. |
| **Phase 4** | **Reactive JS Engine** | Build `faceted-filter.js`: Real-time facet DOM syncing, URL history sync (`pushState`), debounced price slider, active tag removal, and dynamic facet pruning. |
| **Phase 5** | **Header & Navigation polish** | Ensure `/product-list` without category ID opens a full catalog view with all categories available in the sidebar. |
| **Phase 6** | **Quality Verification** | Test responsive breakpoints (Mobile 375px, Tablet 768px, Desktop 1200px+), verify 0-count facet hiding, test browser back/forward URL synchronization. |

---

## 9. Design System Compliance & Native Styling Rules

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
