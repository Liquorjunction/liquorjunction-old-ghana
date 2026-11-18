<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Brand;
use App\Models\ProductVariants;
use App\Models\Setting;
use App\Models\Offers;
use App\Models\Bogo;
use App\Models\FavoriteProduct;
use Carbon\Carbon;
use DB;
use Helper;

class CatalogController extends Controller
{
    protected $cachedSpecialOfferProductIds = null;

    /**
     * Faceted Products Catalog API (Exact 1:1 Mirror of Web Frontend)
     * Route: GET /api/v1/product-catalog or POST /api/v1/product-catalog
     */
    public function index(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $currencySymbol = Helper::Settings('currency_symbol') ?: 'GH₵';

        // 1. Pagination parameters
        $limit = intval($request->get('limit', $request->get('per_page', 16)));
        if ($limit <= 0 || $limit > 100) {
            $limit = 16;
        }

        if ($request->has('offset')) {
            $offset = max(0, intval($request->get('offset')));
            $page = intval(floor($offset / $limit)) + 1;
        } else {
            $page = max(1, intval($request->get('page', 1)));
            $offset = ($page - 1) * $limit;
        }

        // 2. Global metadata cached for 5 minutes (same as frontend)
        $meta = Cache::remember('catalog_global_meta_v6', 300, function () {
            $categories = Categories::with(['subcategory' => function ($q) {
                $q->where('status', 1)->orderBy('title', 'ASC');
            }])
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();

            $brands = Brand::where('status', 1)->orderBy('title', 'ASC')->get();

            // Dynamic price bounds
            $priceBounds = DB::table('product as p')
                ->join('product_variants as pv', 'p.id', '=', 'pv.product_id')
                ->where('p.status', 1)
                ->where('pv.status', 1)
                ->where('pv.variant_price', '>', 0)
                ->selectRaw('MIN(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as min_price')
                ->selectRaw('MAX(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as max_price')
                ->first();

            $minPrice = floor($priceBounds->min_price ?? 0);
            $maxPrice = ceil($priceBounds->max_price ?? 2000);
            if ($minPrice >= $maxPrice) {
                $maxPrice = $minPrice + 500;
            }

            // Sizes sorted ascending by physical volume in ML
            $rawSizes = DB::table('product_variants as pv')
                ->join('product as p', 'pv.product_id', '=', 'p.id')
                ->leftJoin('uofs as u', 'pv.variant_uof', '=', 'u.id')
                ->where('p.status', 1)
                ->where('pv.status', 1)
                ->whereNotNull('pv.variant_size')
                ->where('pv.variant_size', '>', 0)
                ->select(
                    DB::raw('CAST(pv.variant_size AS CHAR) as variant_size'),
                    DB::raw('MAX(u.title) as unit'),
                    DB::raw('CASE WHEN MAX(u.title) = "L" OR (MAX(u.title) IS NULL AND pv.variant_size <= 5) THEN pv.variant_size * 1000 ELSE pv.variant_size END as volume_ml')
                )
                ->groupBy('pv.variant_size')
                ->orderBy('volume_ml', 'ASC')
                ->get();

            $sizes = [];
            $sizeUnitMap = [];
            foreach ($rawSizes as $row) {
                $sz = (string)$row->variant_size;
                $unit = !empty($row->unit) ? strtoupper(trim($row->unit)) : (floatval($sz) <= 5 ? 'L' : 'ML');
                $sizes[] = $sz;
                $sizeUnitMap[$sz] = $unit;
            }

            return [
                'categories' => $categories,
                'brands' => $brands,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sizes' => $sizes,
                'size_units' => $sizeUnitMap,
            ];
        });

        $catalogMinPrice = $meta['min_price'];
        $catalogMaxPrice = $meta['max_price'];
        $allCategories = $meta['categories'];
        $allBrands = $meta['brands'];
        $allSizes = $meta['sizes'];
        $sizeUnitMap = $meta['size_units'] ?? [];

        // 3. Parse Incoming Filter Parameters (Identical to Frontend)
        $filters = $this->parseFilters($request, $catalogMinPrice, $catalogMaxPrice);

        // 4. Build Filtered Query Pipeline
        $queryResult = $this->buildFilteredQuery($filters);
        $productsQuery = $queryResult['query'];

        $total_product_count = (clone $productsQuery)->count('p.id');

        // Pluck matching product IDs for current page
        $pagedIds = (clone $productsQuery)->skip($offset)->take($limit)->pluck('p.id')->toArray();

        // 5. Load Product Cards matching Frontend layout
        $productCards = $this->loadProductCardsForIds($pagedIds, $filters['sort_by'] ?? 'newest');

        // 6. User authentication check for wishlist / cart
        $userData = null;
        if ($request->filled('uniqid')) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->where('status', 1)->first();
        }

        // 7. Attach Promotions & Calculate Exact Final Price & Badges
        $this->attachPromotionsAndVariants($productCards, $today, $userData);

        // 8. Dynamic Facets Calculation ("Filter bhi Filter ho" - Dynamic Counting)
        $facets = $this->calculateDynamicFacets($filters);
        $dynamicPriceBounds = $this->calculatePriceBounds($filters, $catalogMinPrice, $catalogMaxPrice);

        // 9. Active Filter Pills (Dynamic chips for top bar slider)
        $activePills = $this->generateActivePills($filters, $allCategories, $allBrands, $catalogMinPrice, $catalogMaxPrice, $sizeUnitMap);

        // 10. Build Structured Filter Hierarchy with embedded Live Counts
        $structuredFilters = $this->buildStructuredFilterTree(
            $allCategories,
            $allBrands,
            $allSizes,
            $sizeUnitMap,
            $facets,
            $dynamicPriceBounds,
            $currencySymbol
        );

        $totalPages = $total_product_count > 0 ? (int)ceil($total_product_count / $limit) : 0;
        $showingCount = min($offset + count($productCards), $total_product_count);

        return response()->json([
            'code' => '1',
            'message' => 'success',
            'result' => [
                'pagination' => [
                    'total_products' => $total_product_count,
                    'showing_products' => $showingCount,
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'per_page' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + count($productCards)) < $total_product_count,
                ],
                'currency' => $currencySymbol,
                'price_bounds' => [
                    'min_price' => floor($dynamicPriceBounds->min_price ?? $catalogMinPrice),
                    'max_price' => ceil($dynamicPriceBounds->max_price ?? $catalogMaxPrice),
                ],
                'active_pills' => $activePills,
                'active_filters' => [
                    'category_ids' => $filters['category_ids'],
                    'subcategory_ids' => $filters['subcategory_ids'],
                    'brand_ids' => $filters['brand_ids'],
                    'sizes' => $filters['sizes'],
                    'min_price' => $filters['min_price'],
                    'max_price' => $filters['max_price'],
                    'ratings' => $filters['ratings'],
                    'bestseller' => $filters['bestseller'],
                    'offer' => $filters['offer'],
                    'bogo' => $filters['bogo'],
                    'in_stock' => $filters['in_stock'],
                    'keyword' => $filters['keyword'],
                    'sort_by' => $filters['sort_by'],
                ],
                'filters' => $structuredFilters,
                'facets' => $facets,
                'products' => $productCards,
            ]
        ], 200);
    }

    /**
     * Dedicated Filter Metadata API with Live Dynamic Counts
     * Route: GET /api/v1/catalog-filters
     */
    public function filters(Request $request)
    {
        $currencySymbol = Helper::Settings('currency_symbol') ?: 'GH₵';

        // 1. Initial cached global metadata
        $meta = Cache::remember('catalog_global_meta_v6', 300, function () {
            $categories = Categories::with(['subcategory' => function ($q) {
                $q->where('status', 1)->orderBy('title', 'ASC');
            }])
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();

            $brands = Brand::where('status', 1)->orderBy('title', 'ASC')->get();

            $priceBounds = DB::table('product as p')
                ->join('product_variants as pv', 'p.id', '=', 'pv.product_id')
                ->where('p.status', 1)
                ->where('pv.status', 1)
                ->where('pv.variant_price', '>', 0)
                ->selectRaw('MIN(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as min_price')
                ->selectRaw('MAX(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as max_price')
                ->first();

            $minPrice = floor($priceBounds->min_price ?? 0);
            $maxPrice = ceil($priceBounds->max_price ?? 2000);
            if ($minPrice >= $maxPrice) {
                $maxPrice = $minPrice + 500;
            }

            $rawSizes = DB::table('product_variants as pv')
                ->join('product as p', 'pv.product_id', '=', 'p.id')
                ->leftJoin('uofs as u', 'pv.variant_uof', '=', 'u.id')
                ->where('p.status', 1)
                ->where('pv.status', 1)
                ->whereNotNull('pv.variant_size')
                ->where('pv.variant_size', '>', 0)
                ->select(
                    DB::raw('CAST(pv.variant_size AS CHAR) as variant_size'),
                    DB::raw('MAX(u.title) as unit'),
                    DB::raw('CASE WHEN MAX(u.title) = "L" OR (MAX(u.title) IS NULL AND pv.variant_size <= 5) THEN pv.variant_size * 1000 ELSE pv.variant_size END as volume_ml')
                )
                ->groupBy('pv.variant_size')
                ->orderBy('volume_ml', 'ASC')
                ->get();

            $sizes = [];
            $sizeUnitMap = [];
            foreach ($rawSizes as $row) {
                $sz = (string)$row->variant_size;
                $unit = !empty($row->unit) ? strtoupper(trim($row->unit)) : (floatval($sz) <= 5 ? 'L' : 'ML');
                $sizes[] = $sz;
                $sizeUnitMap[$sz] = $unit;
            }

            return [
                'categories' => $categories,
                'brands' => $brands,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sizes' => $sizes,
                'size_units' => $sizeUnitMap,
            ];
        });

        $catalogMinPrice = $meta['min_price'];
        $catalogMaxPrice = $meta['max_price'];
        $allCategories = $meta['categories'];
        $allBrands = $meta['brands'];
        $allSizes = $meta['sizes'];
        $sizeUnitMap = $meta['size_units'] ?? [];

        // 2. Parse current active filters if opening drawer with active state
        $filters = $this->parseFilters($request, $catalogMinPrice, $catalogMaxPrice);

        // 3. Dynamic Facets calculation ("Filter me Filter ki counting")
        $facets = $this->calculateDynamicFacets($filters);
        $dynamicPriceBounds = $this->calculatePriceBounds($filters, $catalogMinPrice, $catalogMaxPrice);

        // 4. Build Structured Filter Hierarchy with embedded counts
        $structuredFilters = $this->buildStructuredFilterTree(
            $allCategories,
            $allBrands,
            $allSizes,
            $sizeUnitMap,
            $facets,
            $dynamicPriceBounds,
            $currencySymbol
        );

        return response()->json([
            'code' => '1',
            'message' => 'success',
            'result' => array_merge($structuredFilters, [
                'facets' => $facets,
            ])
        ], 200);
    }

    /**
     * Build Structured Filter Hierarchy with embedded Live Counts
     */
    protected function buildStructuredFilterTree($allCategories, $allBrands, $allSizes, $sizeUnitMap, $facets, $dynamicPriceBounds, $currencySymbol)
    {
        $catCounts = $facets['categories'] ?? [];
        $subcatCounts = $facets['subcategories'] ?? [];
        $brandCounts = $facets['brands'] ?? [];
        $sizeCounts = $facets['sizes'] ?? [];
        $ratingCounts = $facets['ratings'] ?? [];
        $promoCounts = $facets['promotions'] ?? [];

        // 1. Categories & Subcategories with counts
        $categoriesList = $allCategories->map(function ($cat) use ($catCounts, $subcatCounts) {
            $cid = $cat->id;
            $catCount = $catCounts[$cid] ?? 0;

            $subcategories = $cat->subcategory->map(function ($sub) use ($subcatCounts) {
                $sid = $sub->id;
                $subCount = $subcatCounts[$sid] ?? 0;

                return [
                    'id' => (string)$sub->id,
                    'category_id' => (string)$sub->category_id,
                    'title' => (string)$sub->title,
                    'product_count' => (int)$subCount,
                ];
            });

            return [
                'id' => (string)$cat->id,
                'title' => (string)$cat->title,
                'image' => !empty($cat->image) ? asset('uploads/category/' . $cat->image) : '',
                'product_count' => (int)$catCount,
                'subcategories' => $subcategories,
            ];
        });

        // 2. Brands with counts
        $brandsList = $allBrands->map(function ($b) use ($brandCounts) {
            $bid = $b->id;
            $bCount = $brandCounts[$bid] ?? 0;

            return [
                'id' => (string)$b->id,
                'title' => (string)$b->title,
                'image' => !empty($b->image) ? asset('uploads/brand/' . $b->image) : '',
                'product_count' => (int)$bCount,
            ];
        });

        // 3. Bottle Sizes (in volume-ascending order) with counts
        $sizesList = [];
        foreach ($allSizes as $size) {
            $szCount = $sizeCounts[$size] ?? 0;
            $szUnit = $sizeUnitMap[$size] ?? (floatval($size) <= 5 ? 'L' : 'ML');

            $sizesList[] = [
                'size' => (string)$size,
                'unit' => $szUnit,
                'label' => $size . ' ' . $szUnit,
                'product_count' => (int)$szCount,
            ];
        }

        // 4. Customer Ratings (5 Star to 1 Star) with counts
        $ratingsList = [
            ['rating' => '5', 'label' => '5 Star', 'stars' => 5, 'product_count' => (int)($ratingCounts['5'] ?? 0)],
            ['rating' => '4', 'label' => '4 Star', 'stars' => 4, 'product_count' => (int)($ratingCounts['4'] ?? 0)],
            ['rating' => '3', 'label' => '3 Star', 'stars' => 3, 'product_count' => (int)($ratingCounts['3'] ?? 0)],
            ['rating' => '2', 'label' => '2 Star', 'stars' => 2, 'product_count' => (int)($ratingCounts['2'] ?? 0)],
            ['rating' => '1', 'label' => '1 Star', 'stars' => 1, 'product_count' => (int)($ratingCounts['1'] ?? 0)],
        ];

        // 5. Deals & Availability (Promotions) with counts
        $promotionsList = [
            ['id' => 'bestseller', 'label' => 'Best Sellers', 'product_count' => (int)($promoCounts['bestsellers'] ?? 0)],
            ['id' => 'offer', 'label' => 'Special Offers', 'product_count' => (int)($promoCounts['offers'] ?? 0)],
            ['id' => 'in_stock', 'label' => 'In Stock Only', 'product_count' => (int)($promoCounts['in_stock'] ?? 0)],
        ];

        // 6. Price Range
        $priceRange = [
            'min_price' => floor($dynamicPriceBounds->min_price ?? 0),
            'max_price' => ceil($dynamicPriceBounds->max_price ?? 2000),
            'currency_symbol' => $currencySymbol,
        ];

        return [
            'categories' => $categoriesList,
            'brands' => $brandsList,
            'sizes' => $sizesList,
            'ratings' => $ratingsList,
            'promotions' => $promotionsList,
            'price_range' => $priceRange,
        ];
    }

    /**
     * Parse and sanitize request parameters (Full alignment with Frontend)
     */
    protected function parseFilters(Request $request, $catalogMinPrice = 0, $catalogMaxPrice = 50000)
    {
        $rawCats = $this->normalizeArray(
            $request->get('category_ids',
            $request->get('categories',
            $request->get('category_id',
            $request->get('category',
            $request->get('id')))))
        );

        $rawSubcats = $this->normalizeArray(
            $request->get('subcategory_ids',
            $request->get('subcategories',
            $request->get('subcategory_id',
            $request->get('subcategory',
            $request->get('sid')))))
        );

        $rawBrands = $this->normalizeArray(
            $request->get('brand_ids',
            $request->get('brands',
            $request->get('brand_id',
            $request->get('brand',
            $request->get('bid')))))
        );

        $sizes = array_values(array_unique($this->normalizeArray($request->get('sizes', $request->get('size')))));

        // Decode IDs (handles both plain integers and hashed IDs)
        $category_ids = array_values(array_unique(array_filter(array_map([Helper::class, 'decodeFilterId'], $rawCats))));
        $subcategory_ids = array_values(array_unique(array_filter(array_map([Helper::class, 'decodeFilterId'], $rawSubcats))));
        $brand_ids = array_values(array_unique(array_filter(array_map([Helper::class, 'decodeFilterId'], $rawBrands))));

        if (!empty($subcategory_ids) && empty($category_ids)) {
            $category_ids = SubCategories::whereIn('id', $subcategory_ids)->pluck('category_id')->unique()->filter()->values()->toArray();
        }

        $reqMin = $request->filled('min_price') && is_numeric($request->min_price) ? floatval($request->min_price) : null;
        $reqMax = $request->filled('max_price') && is_numeric($request->max_price) ? floatval($request->max_price) : null;

        $min_price = ($reqMin !== null && $reqMin > $catalogMinPrice) ? $reqMin : null;
        $max_price = ($reqMax !== null && $reqMax < $catalogMaxPrice) ? $reqMax : null;

        $rawRatings = $this->normalizeArray($request->get('ratings', $request->get('rating')));
        $ratings = array_values(array_unique(array_filter(array_map('intval', $rawRatings), function ($r) {
            return $r >= 1 && $r <= 5;
        })));
        $rating = !empty($ratings) ? $ratings[0] : null;

        $bestseller = $request->boolean('bestseller') || $request->get('bestseller') == '1';
        $offer = $request->boolean('offer') || $request->get('offer') == '1';
        $bogo = $request->boolean('bogo') || $request->get('bogo') == '1';
        $in_stock = $request->boolean('in_stock') || $request->get('in_stock') == '1';

        $sort_by = $request->get('sort_by', 'newest');
        $keyword = trim($request->get('keyword', $request->get('search', $request->get('q', ''))));

        return compact(
            'category_ids',
            'subcategory_ids',
            'brand_ids',
            'sizes',
            'min_price',
            'max_price',
            'ratings',
            'rating',
            'bestseller',
            'offer',
            'bogo',
            'in_stock',
            'sort_by',
            'keyword'
        );
    }

    /**
     * Normalize arrays from query string / comma separated
     */
    protected function normalizeArray($val)
    {
        if (empty($val)) return [];
        if (is_array($val)) {
            return array_values(array_filter(array_map('trim', $val)));
        }
        return array_values(array_filter(explode(',', $val)));
    }

    /**
     * Build the filtered master product query (Exact 1:1 Mirror of Web Frontend)
     */
    protected function buildFilteredQuery(array $filters, array $exclude = [])
    {
        $query = DB::table('product as p')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->where('c.status', '=', 1)
            ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
            ->where('sc.status', '=', 1)
            ->join('brand as b', 'p.brand_id', '=', 'b.id')
            ->where('b.status', '=', 1)
            ->where('p.status', '=', 1);

        $hasPriceFilter = !in_array('price', $exclude) && ($filters['min_price'] !== null || $filters['max_price'] !== null);
        $hasSizeFilter = !in_array('size', $exclude) && !empty($filters['sizes']);
        $hasStockFilter = !in_array('promotion', $exclude) && $filters['in_stock'];
        $hasPriceSort = in_array($filters['sort_by'] ?? '', ['2', 'price_desc', 'high_to_low', '3', 'price_asc', 'low_to_high']);

        if ($hasPriceFilter || $hasSizeFilter || $hasStockFilter || $hasPriceSort) {
            $firstVariant = DB::table('product_variants as fv')
                ->join(
                    DB::raw('(SELECT product_id, MIN(id) as min_vid FROM product_variants WHERE status = 1 GROUP BY product_id) as fv_min'),
                    'fv.id', '=', 'fv_min.min_vid'
                )
                ->select('fv.product_id', 'fv.variant_price', 'fv.variant_size', 'fv.available_qty');

            $query->joinSub($firstVariant, 'fv_base', 'fv_base.product_id', '=', 'p.id');

            if ($hasPriceFilter) {
                if ($filters['min_price'] !== null && $filters['max_price'] !== null) {
                    $query->whereBetween('fv_base.variant_price', [$filters['min_price'], $filters['max_price']]);
                } elseif ($filters['min_price'] !== null) {
                    $query->where('fv_base.variant_price', '>=', $filters['min_price']);
                } elseif ($filters['max_price'] !== null) {
                    $query->where('fv_base.variant_price', '<=', $filters['max_price']);
                }
            }

            if ($hasSizeFilter) {
                $query->whereExists(function ($subQ) use ($filters) {
                    $subQ->select(DB::raw(1))
                        ->from('product_variants as pv_sz')
                        ->whereColumn('pv_sz.product_id', 'p.id')
                        ->where('pv_sz.status', 1)
                        ->whereIn('pv_sz.variant_size', $filters['sizes']);
                });
            }

            if ($hasStockFilter) {
                $query->where('fv_base.available_qty', '>', 0);
            }
        }

        // Apply Search Keyword (searches product_name, brand title, and category title)
        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('p.product_name', 'LIKE', '%' . $kw . '%')
                  ->orWhere('b.title', 'LIKE', '%' . $kw . '%')
                  ->orWhere('c.title', 'LIKE', '%' . $kw . '%');
            });
        }

        // Apply Categories & Subcategories
        if (!in_array('category', $exclude)) {
            $catIds = $filters['category_ids'];
            $subcatIds = $filters['subcategory_ids'];

            if (!empty($catIds) && empty($subcatIds)) {
                $query->whereIn('p.category_id', $catIds);
            } elseif (!empty($subcatIds)) {
                $subcatCategories = SubCategories::whereIn('id', $subcatIds)->pluck('category_id')->unique()->toArray();
                $catsWithoutSubcats = array_values(array_diff($catIds, $subcatCategories));

                $query->where(function ($q) use ($catsWithoutSubcats, $subcatIds) {
                    if (!empty($catsWithoutSubcats)) {
                        $q->whereIn('p.category_id', $catsWithoutSubcats);
                    }
                    if (!empty($subcatIds)) {
                        if (!empty($catsWithoutSubcats)) {
                            $q->orWhereIn('p.subcategory_id', $subcatIds);
                        } else {
                            $q->whereIn('p.subcategory_id', $subcatIds);
                        }
                    }
                });
            }
        }

        // Apply Brands
        if (!in_array('brand', $exclude) && !empty($filters['brand_ids'])) {
            $query->whereIn('p.brand_id', $filters['brand_ids']);
        }

        // Apply Ratings
        if (!in_array('rating', $exclude)) {
            $selectedRatings = !empty($filters['ratings']) ? (array)$filters['ratings'] : (!empty($filters['rating']) ? [(int)$filters['rating']] : []);
            if (!empty($selectedRatings)) {
                $query->where(function ($q) use ($selectedRatings) {
                    foreach ($selectedRatings as $r) {
                        $r = intval($r);
                        if ($r === 5) {
                            $q->orWhere('p.average_rating', '>=', 4.5);
                        } elseif ($r === 4) {
                            $q->orWhere(function ($sub) {
                                $sub->where('p.average_rating', '>=', 3.5)
                                    ->where('p.average_rating', '<', 4.5);
                            });
                        } elseif ($r === 3) {
                            $q->orWhere(function ($sub) {
                                $sub->where('p.average_rating', '>=', 2.5)
                                    ->where('p.average_rating', '<', 3.5);
                            });
                        } elseif ($r === 2) {
                            $q->orWhere(function ($sub) {
                                $sub->where('p.average_rating', '>=', 1.5)
                                    ->where('p.average_rating', '<', 2.5);
                            });
                        } elseif ($r === 1) {
                            $q->orWhere(function ($sub) {
                                $sub->where('p.average_rating', '>=', 0.5)
                                    ->where('p.average_rating', '<', 1.5);
                            });
                        }
                    }
                });
            }
        }

        // Apply Promotions
        if (!in_array('promotion', $exclude)) {
            $hasBestseller = !empty($filters['bestseller']);
            $hasOffer = !empty($filters['offer']);
            $hasBogo = !empty($filters['bogo']);

            $promoProductIds = [];
            if ($hasOffer) {
                $promoProductIds = array_merge($promoProductIds, $this->getActiveSpecialOfferProductIds());
            }
            if ($hasBogo) {
                $promoProductIds = array_merge($promoProductIds, $this->getActiveBogoProductIds());
            }
            $promoProductIds = array_values(array_unique($promoProductIds));

            if ($hasBestseller && ($hasOffer || $hasBogo)) {
                $query->where(function ($q) use ($promoProductIds) {
                    $q->where('p.is_product_bestseller', 1)
                      ->orWhereIn('p.id', !empty($promoProductIds) ? $promoProductIds : [-1]);
                });
            } elseif ($hasBestseller) {
                $query->where('p.is_product_bestseller', 1);
            } elseif ($hasOffer || $hasBogo) {
                $query->whereIn('p.id', !empty($promoProductIds) ? $promoProductIds : [-1]);
            }
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'newest';
        switch ($sortBy) {
            case '2':
            case 'price_desc':
            case 'high_to_low':
                $query->orderBy('fv_base.variant_price', 'DESC');
                break;
            case '3':
            case 'price_asc':
            case 'low_to_high':
                $query->orderBy('fv_base.variant_price', 'ASC');
                break;
            case '4':
            case 'popular':
            case 'popularity':
                $query->leftJoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
                      ->orderByRaw('COALESCE(mvp.count, 0) DESC')->orderBy('p.id', 'DESC');
                break;
            case 'rating':
                $query->orderBy('p.average_rating', 'DESC');
                break;
            case '1':
            case 'newest':
            default:
                $query->orderBy('p.id', 'DESC');
                break;
        }

        return ['query' => $query];
    }

    /**
     * Load Full Product Cards for Displayed IDs
     */
    protected function loadProductCardsForIds(array $pagedIds, $sortBy = 'newest')
    {
        if (empty($pagedIds)) {
            return [];
        }

        $idList = implode(',', array_map('intval', $pagedIds));
        $currencySymbol = Helper::Settings('currency_symbol') ?: 'GH₵';

        $firstVariant = DB::table('product_variants as fv')
            ->join(
                DB::raw('(SELECT product_id, MIN(id) as min_vid FROM product_variants WHERE status = 1 and product_id IN (' . $idList . ') GROUP BY product_id) as fv_min'),
                'fv.id', '=', 'fv_min.min_vid'
            )
            ->select('fv.id', 'fv.product_id', 'fv.variant_price', 'fv.variant_discounted_price', 'fv.variant_size', 'fv.variant_uof', 'fv.available_qty');

        $firstImage = DB::table('product_image as pi')
            ->join(
                DB::raw('(SELECT product_id, MIN(id) as min_img_id FROM product_image WHERE status = 1 and product_id IN (' . $idList . ') GROUP BY product_id) as pi_min'),
                'pi.id', '=', 'pi_min.min_img_id'
            )
            ->select('pi.product_id', 'pi.image');

        $query = DB::table('product as p')
            ->whereIn('p.id', $pagedIds)
            ->leftJoinSub($firstImage, 'first_image', 'first_image.product_id', '=', 'p.id')
            ->leftJoinSub($firstVariant, 'first_variant', 'first_variant.product_id', '=', 'p.id')
            ->leftJoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
            ->leftJoin('brand as b', 'p.brand_id', '=', 'b.id')
            ->select(
                'p.id as id',
                'p.product_name',
                'p.category_id',
                'p.subcategory_id',
                'p.brand_id',
                'p.average_rating',
                'p.is_product_bestseller',
                'c.title as category_title',
                'sc.title as subcategory_title',
                'b.title as brand_title',
                'first_image.image as product_image',
                'first_variant.id as variant_id',
                'first_variant.variant_price',
                'first_variant.variant_discounted_price',
                'first_variant.variant_size',
                'first_variant.variant_uof',
                'first_variant.available_qty'
            );

        $orderSql = 'FIELD(p.id, ' . $idList . ')';
        $rows = $query->orderByRaw($orderSql)->get();

        // Load all variants for displayed products so app can switch sizes
        $allVariantsGroup = DB::table('product_variants as pv')
            ->leftJoin('uofs as u', 'pv.variant_uof', '=', 'u.id')
            ->whereIn('pv.product_id', $pagedIds)
            ->where('pv.status', 1)
            ->select('pv.*', 'u.title as unit_title')
            ->orderBy('pv.id', 'ASC')
            ->get()
            ->groupBy('product_id');

        $cards = [];
        foreach ($rows as $row) {
            $unit = Helper::getUnitById($row->variant_uof);
            $formatted_unit = !empty($unit) ? strtoupper(trim($unit)) : (floatval($row->variant_size) <= 5 ? 'L' : 'ML');
            $sizeStr = !empty($row->variant_size) ? $row->variant_size . ' ' . $formatted_unit : '';

            $imagePath = !empty($row->product_image) && file_exists(public_path('uploads/product/' . $row->product_image))
                ? asset('uploads/product/' . $row->product_image)
                : asset('assets/frontend/images/image-not-avilable.png');

            $originalPrice = floatval($row->variant_price ?? 0);
            $discountPrice = (!empty($row->variant_discounted_price) && $row->variant_discounted_price > 0)
                ? floatval($row->variant_discounted_price)
                : $originalPrice;

            // Variants sub-array
            $variantsSub = [];
            $productVariants = $allVariantsGroup->get($row->id, collect([]));
            foreach ($productVariants as $v) {
                $vUnit = !empty($v->unit_title) ? strtoupper(trim($v->unit_title)) : (floatval($v->variant_size) <= 5 ? 'L' : 'ML');
                $vOrig = floatval($v->variant_price ?? 0);
                $vDisc = (!empty($v->variant_discounted_price) && $v->variant_discounted_price > 0) ? floatval($v->variant_discounted_price) : $vOrig;

                $variantsSub[] = [
                    'variant_id' => (string)$v->id,
                    'variant_size' => (string)$v->variant_size,
                    'variant_unit' => $vUnit,
                    'size_label' => $v->variant_size . ' ' . $vUnit,
                    'original_price' => number_format($vOrig, 2, '.', ''),
                    'discount_price' => $vDisc < $vOrig ? number_format($vDisc, 2, '.', '') : null,
                    'final_price' => number_format($vDisc, 2, '.', ''),
                    'available_qty' => (int)($v->available_qty ?? 0),
                    'in_stock' => (int)($v->available_qty ?? 0) > 0,
                ];
            }

            $cards[] = [
                'product_id' => (string)$row->id,
                'product_title' => (string)($row->product_name ?? ''),
                'product_image' => $imagePath,
                'category_id' => (string)$row->category_id,
                'category_title' => (string)($row->category_title ?? ''),
                'subcategory_id' => (string)$row->subcategory_id,
                'subcategory_title' => (string)($row->subcategory_title ?? ''),
                'brand_id' => (string)$row->brand_id,
                'brand_title' => (string)($row->brand_title ?? ''),
                'average_rating' => (string)($row->average_rating ?? '0'),
                'variant_id' => (string)($row->variant_id ?? ''),
                'variant_size' => (string)($row->variant_size ?? ''),
                'variant_unit' => $formatted_unit,
                'product_size' => $sizeStr,
                'original_price' => number_format($originalPrice, 2, '.', ''),
                'final_price' => number_format($discountPrice, 2, '.', ''),
                'sell_price' => number_format($discountPrice, 2, '.', ''),
                'has_discount' => $discountPrice < $originalPrice,
                'currency' => $currencySymbol,
                'available_qty' => (int)($row->available_qty ?? 0),
                'in_stock' => (int)($row->available_qty ?? 0) > 0,
                'is_bestseller' => (bool)$row->is_product_bestseller,
                'is_favourite' => false,
                'in_cart' => false,
                'offer_status' => false,
                'offer_type' => null,
                'discount_amount' => '0',
                'offer_label' => '',
                'bogo_status' => false,
                'bogo_label' => '',
                'all_variants' => $variantsSub,
            ];
        }

        return $cards;
    }

    /**
     * Attach Promotions, Wishlist & Cart state with Exact Frontend Price Calculations
     */
    protected function attachPromotionsAndVariants(array &$products, $today, $userData = null)
    {
        if (empty($products)) return;

        $pids = array_map(function ($p) { return intval($p['product_id']); }, $products);
        $currencySymbol = Helper::Settings('currency_symbol') ?: 'GH₵';

        // Check active BOGOs
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        // Check active Offers
        $activeOffers = Offers::where('status', 1)
            ->whereDate('expiry_date', '>=', $today)
            ->get();

        // User Favorites & Cart
        $favPids = [];
        $cartPids = [];
        if ($userData) {
            $favPids = FavoriteProduct::where('user_id', $userData->id)
                ->whereIn('product_id', $pids)
                ->where('status', 1)
                ->pluck('product_id')
                ->toArray();

            $cartPids = DB::table('cart')
                ->where('user_id', $userData->id)
                ->whereIn('product_id', $pids)
                ->where('status', 1)
                ->pluck('product_id')
                ->toArray();
        }

        foreach ($products as &$p) {
            $pid = intval($p['product_id']);
            $cid = intval($p['category_id']);
            $scid = intval($p['subcategory_id']);
            $bid = intval($p['brand_id']);

            $p['is_favourite'] = in_array($pid, $favPids);
            $p['in_cart'] = in_array($pid, $cartPids);

            // BOGO Check
            $isBogo = false;
            foreach ($activeBogos as $bogo) {
                if ($bogo->product_id && $bogo->product_id == $pid) { $isBogo = true; break; }
                if ($bogo->subcategory_id && $bogo->subcategory_id == $scid) { $isBogo = true; break; }
                if ($bogo->category_id && empty($bogo->subcategory_id) && $bogo->category_id == $cid) { $isBogo = true; break; }
                if ($bogo->brand_id && $bogo->brand_id == $bid) { $isBogo = true; break; }
            }

            if ($isBogo) {
                $p['bogo_status'] = true;
                $p['bogo_label'] = 'BOGO';
            }

            // Special Offer Check
            $matchedOffer = null;
            foreach ($activeOffers as $offer) {
                if ($offer->product_id && $offer->product_id == $pid) { $matchedOffer = $offer; break; }
                if ($offer->subcategory_id && $offer->subcategory_id == $scid) { $matchedOffer = $offer; break; }
                if ($offer->category_id && empty($offer->subcategory_id) && $offer->category_id == $cid) { $matchedOffer = $offer; break; }
                if ($offer->brand_id && $offer->brand_id == $bid) { $matchedOffer = $offer; break; }
            }

            if ($matchedOffer) {
                $p['offer_status'] = true;
                $p['offer_type'] = $matchedOffer->offer_type;
                $p['discount_amount'] = (string)$matchedOffer->dis_amount;
                $formattedDiscount = rtrim(rtrim(number_format($matchedOffer->dis_amount, 2, '.', ''), '0'), '.');
                if ($matchedOffer->offer_type == 'flat') {
                    $p['offer_label'] = 'Flat ' . $formattedDiscount . ' ' . $currencySymbol . ' Off';
                } elseif ($matchedOffer->offer_type == 'percentage') {
                    $p['offer_label'] = $formattedDiscount . '% Off';
                }

                // EXACT Frontend Price Calculation (matches product_grid.blade.php lines 18-24)
                // Offer discount only applies if NOT in BOGO
                if (!$isBogo) {
                    $orig = floatval($p['original_price']);
                    $calculatedFinal = $orig;
                    if ($matchedOffer->offer_type === 'flat') {
                        $calculatedFinal = max(0, $orig - floatval($matchedOffer->dis_amount));
                    } elseif ($matchedOffer->offer_type === 'percentage') {
                        $calculatedFinal = max(0, $orig - ($orig * floatval($matchedOffer->dis_amount) / 100));
                    }
                    $p['final_price'] = number_format($calculatedFinal, 2, '.', '');
                    $p['sell_price'] = number_format($calculatedFinal, 2, '.', '');
                    $p['has_discount'] = $calculatedFinal < $orig;
                }
            }
        }
    }

    /**
     * Calculate dynamic facet counts ("Filter bhi Filter ho" - Exact Frontend Algorithm)
     */
    protected function calculateDynamicFacets(array $filters)
    {
        // 1. Categories Facets (relative to other filters, excluding category)
        $catBase = $this->buildFilteredQuery($filters, ['category'])['query'];
        $rawCatCounts = (clone $catBase)
            ->select('p.category_id', DB::raw('COUNT(*) as count'))
            ->groupBy('p.category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        $categoryCounts = [];
        foreach ($rawCatCounts as $cid => $cnt) {
            $categoryCounts[$cid] = (int)$cnt;
            $hash = Helper::encodeFilterId($cid);
            $categoryCounts[$hash] = (int)$cnt;
        }

        // Subcategory counts
        $rawSubcatCounts = (clone $catBase)
            ->select('p.subcategory_id', DB::raw('COUNT(*) as count'))
            ->groupBy('p.subcategory_id')
            ->pluck('count', 'subcategory_id')
            ->toArray();

        $subcatCounts = [];
        foreach ($rawSubcatCounts as $sid => $cnt) {
            $subcatCounts[$sid] = (int)$cnt;
            $hash = Helper::encodeFilterId($sid);
            $subcatCounts[$hash] = (int)$cnt;
        }

        // 2. Brand Facets (relative to other filters, excluding brand)
        $brandBase = $this->buildFilteredQuery($filters, ['brand'])['query'];
        $rawBrandCounts = (clone $brandBase)
            ->select('p.brand_id', DB::raw('COUNT(*) as count'))
            ->groupBy('p.brand_id')
            ->pluck('count', 'brand_id')
            ->toArray();

        $brandCounts = [];
        foreach ($rawBrandCounts as $bid => $cnt) {
            $brandCounts[$bid] = (int)$cnt;
            $hash = Helper::encodeFilterId($bid);
            $brandCounts[$hash] = (int)$cnt;
        }

        // 3. Size Facets (relative to other filters, excluding size)
        $sizeBase = $this->buildFilteredQuery($filters, ['size'])['query'];
        $matchingPids = (clone $sizeBase)->pluck('p.id')->toArray();
        $sizeCounts = [];
        if (!empty($matchingPids)) {
            $rawSizeCounts = DB::table('product_variants as pv')
                ->whereIn('pv.product_id', $matchingPids)
                ->where('pv.status', 1)
                ->whereNotNull('pv.variant_size')
                ->where('pv.variant_size', '>', 0)
                ->select(DB::raw('CAST(pv.variant_size AS CHAR) as variant_size'), DB::raw('COUNT(DISTINCT pv.product_id) as count'))
                ->groupBy('pv.variant_size')
                ->get();

            foreach ($rawSizeCounts as $rsc) {
                $sizeCounts[(string)$rsc->variant_size] = intval($rsc->count);
            }
        }

        // 4. Rating Facets (relative to current filters, excluding rating)
        $ratingBase = $this->buildFilteredQuery($filters, ['rating'])['query'];
        $ratingRow = (clone $ratingBase)
            ->reorder()
            ->selectRaw('
                COALESCE(SUM(CASE WHEN p.average_rating >= 4.5 THEN 1 ELSE 0 END), 0) as r5,
                COALESCE(SUM(CASE WHEN p.average_rating >= 3.5 AND p.average_rating < 4.5 THEN 1 ELSE 0 END), 0) as r4,
                COALESCE(SUM(CASE WHEN p.average_rating >= 2.5 AND p.average_rating < 3.5 THEN 1 ELSE 0 END), 0) as r3,
                COALESCE(SUM(CASE WHEN p.average_rating >= 1.5 AND p.average_rating < 2.5 THEN 1 ELSE 0 END), 0) as r2,
                COALESCE(SUM(CASE WHEN p.average_rating >= 0.5 AND p.average_rating < 1.5 THEN 1 ELSE 0 END), 0) as r1
            ')
            ->first();

        // 5. Promotion Facets (relative to current filters, excluding promotion)
        $promoBase = $this->buildFilteredQuery($filters, ['promotion'])['query'];
        $specialOfferIds = $this->getActiveSpecialOfferProductIds();
        $specialOfferIdList = !empty($specialOfferIds) ? implode(',', $specialOfferIds) : '0';

        $promoRow = (clone $promoBase)
            ->reorder()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN p.is_product_bestseller = 1 THEN 1 ELSE 0 END), 0) as bestsellers,
                COALESCE(SUM(CASE WHEN p.id IN ({$specialOfferIdList}) THEN 1 ELSE 0 END), 0) as offers
            ")
            ->first();

        // In-stock count
        $inStockCount = 0;
        $promoPids = (clone $promoBase)->pluck('p.id')->toArray();
        if (!empty($promoPids)) {
            $inStockCount = DB::table('product_variants as pv')
                ->whereIn('pv.product_id', $promoPids)
                ->where('pv.status', 1)
                ->where('pv.available_qty', '>', 0)
                ->distinct('pv.product_id')
                ->count('pv.product_id');
        }

        return [
            'categories' => $categoryCounts,
            'subcategories' => $subcatCounts,
            'brands' => $brandCounts,
            'sizes' => $sizeCounts,
            'ratings' => [
                '5' => intval($ratingRow->r5 ?? 0),
                '4' => intval($ratingRow->r4 ?? 0),
                '3' => intval($ratingRow->r3 ?? 0),
                '2' => intval($ratingRow->r2 ?? 0),
                '1' => intval($ratingRow->r1 ?? 0),
            ],
            'promotions' => [
                'bestsellers' => intval($promoRow->bestsellers ?? 0),
                'offers' => intval($promoRow->offers ?? 0),
                'in_stock' => intval($inStockCount),
            ],
        ];
    }

    /**
     * Calculate dynamic min/max price bounds for current selection
     */
    protected function calculatePriceBounds(array $filters, $defaultMin = 0, $defaultMax = 2000)
    {
        $priceBase = $this->buildFilteredQuery($filters, ['price'])['query'];
        $pids = (clone $priceBase)->pluck('p.id')->toArray();

        if (empty($pids)) {
            return (object)[
                'min_price' => $defaultMin,
                'max_price' => $defaultMax,
            ];
        }

        $bounds = DB::table('product_variants as pv')
            ->whereIn('pv.product_id', $pids)
            ->where('pv.status', 1)
            ->where('pv.variant_price', '>', 0)
            ->selectRaw('MIN(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as min_price')
            ->selectRaw('MAX(COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price)) as max_price')
            ->first();

        return (object)[
            'min_price' => floor($bounds->min_price ?? $defaultMin),
            'max_price' => ceil($bounds->max_price ?? $defaultMax),
        ];
    }

    /**
     * Generate Active Filter Pills
     */
    protected function generateActivePills(array $filters, $allCategories, $allBrands, $catalogMinPrice, $catalogMaxPrice, array $sizeUnitMap = [])
    {
        $pills = [];

        // Search Keyword
        if (!empty($filters['keyword'])) {
            $pills[] = [
                'type' => 'keyword',
                'id' => 'keyword',
                'label' => 'Search: "' . $filters['keyword'] . '"',
            ];
        }

        // Categories
        if (!empty($filters['category_ids'])) {
            $catsMap = $allCategories ? $allCategories->keyBy('id') : collect([]);
            foreach ($filters['category_ids'] as $cid) {
                $catObj = $catsMap->get($cid);
                $name = $catObj ? $catObj->title : (Categories::where('id', $cid)->value('title') ?: ('Category #' . $cid));
                $pills[] = [
                    'type' => 'category',
                    'id' => Helper::encodeFilterId($cid),
                    'raw_id' => (string)$cid,
                    'label' => 'Category: ' . $name,
                ];
            }
        }

        // Subcategories
        if (!empty($filters['subcategory_ids'])) {
            foreach ($filters['subcategory_ids'] as $sid) {
                $subObj = SubCategories::find($sid);
                $name = $subObj ? $subObj->title : ('Subcategory #' . $sid);
                $pills[] = [
                    'type' => 'subcategory',
                    'id' => Helper::encodeFilterId($sid),
                    'raw_id' => (string)$sid,
                    'label' => 'Subcategory: ' . $name,
                ];
            }
        }

        // Brands
        if (!empty($filters['brand_ids'])) {
            $brandsMap = $allBrands ? $allBrands->keyBy('id') : collect([]);
            foreach ($filters['brand_ids'] as $bid) {
                $brandObj = $brandsMap->get($bid);
                if (!$brandObj) {
                    $brandObj = Brand::find($bid);
                }
                if ($brandObj) {
                    $pills[] = [
                        'type' => 'brand',
                        'id' => Helper::encodeFilterId($bid),
                        'raw_id' => (string)$bid,
                        'label' => 'Brand: ' . $brandObj->title,
                    ];
                }
            }
        }

        // Sizes
        if (!empty($filters['sizes'])) {
            foreach ($filters['sizes'] as $sz) {
                $unit = $sizeUnitMap[$sz] ?? (floatval($sz) <= 5 ? 'L' : 'ML');
                $pills[] = [
                    'type' => 'size',
                    'id' => $sz,
                    'raw_id' => $sz,
                    'label' => 'Size: ' . $sz . ' ' . $unit,
                ];
            }
        }

        // Price
        $hasPriceFilter = ($filters['min_price'] !== null && $filters['min_price'] > $catalogMinPrice)
                       || ($filters['max_price'] !== null && $filters['max_price'] < $catalogMaxPrice);

        if ($hasPriceFilter) {
            $curr = Helper::Settings('currency_symbol') ?: 'GH₵';
            $minVal = $filters['min_price'] !== null ? $filters['min_price'] : $catalogMinPrice;
            $maxVal = $filters['max_price'] !== null ? $filters['max_price'] : $catalogMaxPrice;
            $pills[] = [
                'type' => 'price',
                'id' => 'price',
                'label' => 'Price: ' . $curr . number_format($minVal, 0) . ' - ' . $curr . number_format($maxVal, 0),
            ];
        }

        // Ratings
        $selectedRatings = !empty($filters['ratings']) ? (array)$filters['ratings'] : (!empty($filters['rating']) ? [(int)$filters['rating']] : []);
        if (!empty($selectedRatings)) {
            foreach ($selectedRatings as $r) {
                $pills[] = [
                    'type' => 'rating',
                    'id' => strval($r),
                    'label' => $r . '★ Rating',
                ];
            }
        }

        // Bestseller
        if ($filters['bestseller']) {
            $pills[] = ['type' => 'bestseller', 'id' => 'bestseller', 'label' => 'Best Sellers'];
        }

        // Offer
        if ($filters['offer']) {
            $pills[] = ['type' => 'offer', 'id' => 'offer', 'label' => 'Special Offers'];
        }

        // In Stock
        if ($filters['in_stock']) {
            $pills[] = ['type' => 'in_stock', 'id' => 'in_stock', 'label' => 'In Stock Only'];
        }

        return $pills;
    }

    /**
     * Active Special Offers product IDs (All active Offers & BOGOs)
     */
    protected function getActiveSpecialOfferProductIds()
    {
        if ($this->cachedSpecialOfferProductIds !== null) {
            return $this->cachedSpecialOfferProductIds;
        }

        $today = Carbon::now()->toDateString();
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        $activeOffers = Offers::where('status', 1)
            ->whereDate('expiry_date', '>=', $today)
            ->get();

        if ($activeBogos->isEmpty() && $activeOffers->isEmpty()) {
            return $this->cachedSpecialOfferProductIds = [];
        }

        $matchingProductIds = [];

        foreach ($activeBogos as $bogo) {
            $q = DB::table('product as p')->where('p.status', 1);
            if (!empty($bogo->product_id)) {
                $q->where('p.id', $bogo->product_id);
            } elseif (!empty($bogo->subcategory_id)) {
                if (!empty($bogo->category_id)) {
                    $q->where('p.category_id', $bogo->category_id);
                }
                $q->where('p.subcategory_id', $bogo->subcategory_id);
            } elseif (!empty($bogo->category_id)) {
                $q->where('p.category_id', $bogo->category_id);
            } elseif (!empty($bogo->brand_id)) {
                $q->where('p.brand_id', $bogo->brand_id);
            }
            $pids = $q->pluck('p.id')->toArray();
            if (!empty($pids)) {
                $matchingProductIds = array_merge($matchingProductIds, $pids);
            }
        }

        foreach ($activeOffers as $offer) {
            $q = DB::table('product as p')->where('p.status', 1);
            if (!empty($offer->product_id)) {
                $q->where('p.id', $offer->product_id);
            } elseif (!empty($offer->subcategory_id)) {
                if (!empty($offer->category_id)) {
                    $q->where('p.category_id', $offer->category_id);
                }
                $q->where('p.subcategory_id', $offer->subcategory_id);
            } elseif (!empty($offer->category_id)) {
                $q->where('p.category_id', $offer->category_id);
            } elseif (!empty($offer->brand_id)) {
                $q->where('p.brand_id', $offer->brand_id);
            }
            $pids = $q->pluck('p.id')->toArray();
            if (!empty($pids)) {
                $matchingProductIds = array_merge($matchingProductIds, $pids);
            }
        }

        return $this->cachedSpecialOfferProductIds = array_values(array_unique(array_map('intval', $matchingProductIds)));
    }

    /**
     * Active BOGO product IDs
     */
    protected function getActiveBogoProductIds()
    {
        $today = Carbon::now()->toDateString();
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        if ($activeBogos->isEmpty()) return [];

        $matchingProductIds = [];
        foreach ($activeBogos as $bogo) {
            $q = DB::table('product as p')->where('p.status', 1);
            if (!empty($bogo->product_id)) {
                $q->where('p.id', $bogo->product_id);
            } elseif (!empty($bogo->subcategory_id)) {
                if (!empty($bogo->category_id)) {
                    $q->where('p.category_id', $bogo->category_id);
                }
                $q->where('p.subcategory_id', $bogo->subcategory_id);
            } elseif (!empty($bogo->category_id)) {
                $q->where('p.category_id', $bogo->category_id);
            } elseif (!empty($bogo->brand_id)) {
                $q->where('p.brand_id', $bogo->brand_id);
            }
            $pids = $q->pluck('p.id')->toArray();
            if (!empty($pids)) {
                $matchingProductIds = array_merge($matchingProductIds, $pids);
            }
        }

        return array_values(array_unique(array_map('intval', $matchingProductIds)));
    }
}
