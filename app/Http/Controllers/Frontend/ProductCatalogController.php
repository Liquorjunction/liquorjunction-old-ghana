<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Brand;
use App\Models\ProductVariants;
use App\Models\Uofs;
use App\Models\Bogo;
use App\Models\Offers;
use App\Models\Setting;
use Carbon\Carbon;
use DB;
use Helper;
use Session;

class ProductCatalogController extends Controller
{
    protected $setting;
    protected $user_id;
    protected $cachedSpecialOfferProductIds = null;

    public function __construct()
    {
        // Avoid database queries in constructor
    }

    /**
     * Display the Faceted Products Listing Page
     */
    public function index(Request $request)
    {
        $this->setting = Setting::find(1);
        $this->user_id = auth()->guard('user')->check() ? auth()->guard('user')->id() : null;

        $user_id = $this->user_id;
        $setting = $this->setting;
        $today = Carbon::now()->toDateString();

        // 1. Static Initial Metadata Cached for 5 minutes
        $meta = Cache::remember('catalog_global_meta_v4', 300, function () {
            $categories = Categories::with(['subcategory' => function ($q) {
                $q->where('status', 1)->orderBy('title', 'ASC');
            }])
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();

            $brands = Brand::where('status', 1)->orderBy('title', 'ASC')->get();

            // Truly dynamic price bounds from active variants
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

            // Available distinct sizes
            $sizes = DB::table('product_variants as pv')
                ->join('product as p', 'pv.product_id', '=', 'p.id')
                ->where('p.status', 1)
                ->where('pv.status', 1)
                ->whereNotNull('pv.variant_size')
                ->where('pv.variant_size', '>', 0)
                ->select('pv.variant_size')
                ->distinct()
                ->orderBy('pv.variant_size', 'ASC')
                ->pluck('variant_size')
                ->toArray();

            return [
                'categories' => $categories,
                'brands' => $brands,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sizes' => $sizes,
            ];
        });

        $allCategories = $meta['categories'];
        $allBrands = $meta['brands'];
        $catalogMinPrice = $meta['min_price'];
        $catalogMaxPrice = $meta['max_price'];
        $allSizes = $meta['sizes'];

        // 2. Parse Incoming Filter Parameters (with Safe Hashed ID Decoding)
        $filters = $this->parseFilters($request, $catalogMinPrice, $catalogMaxPrice);

        // 3. Build Filtered Query Pipeline
        $queryResult = $this->buildFilteredQuery($filters);
        $productsQuery = $queryResult['query'];

        // Total count of matching products
        $total_product_count = (clone $productsQuery)->count('p.id');

        // Pagination limit
        $limit = 16;
        $offset = intval($request->get('offset', 0));
        
        // Pluck 16 matching product IDs (ultra-fast indexed scan)
        $pagedIds = (clone $productsQuery)->skip($offset)->take($limit)->pluck('p.id')->toArray();
        $productData = $this->loadProductCardsForIds($pagedIds, $filters['sort_by'] ?? 'newest');

        // Showing count
        $showing_product_count = min($offset + count($productData), $total_product_count);

        // 4. Attach BOGO & Offer status via Bulk Eager-Loading (Zero N+1)
        $this->attachPromotionsAndVariants($productData, $today);

        // 5. Dynamic Facet Calculations & Price Bounds (skip on append pagination for instant load)
        $isAppend = $request->boolean('append', false);
        $facets = null;
        $dynamicPriceBounds = null;

        if (!$isAppend) {
            $facets = $this->calculateDynamicFacets($filters);
            $dynamicPriceBounds = $this->calculatePriceBounds($filters, $catalogMinPrice, $catalogMaxPrice);
        }

        // 6. Active Filter Pills Structure (Dynamic, only when filters actually active)
        $active_pills = $this->generateActivePills($filters, $allCategories, $allBrands, $catalogMinPrice, $catalogMaxPrice);

        // 7. Handle AJAX request (Faceted re-render or Load More)
        if ($request->ajax()) {
            $html = view('frontend.products.partials.product_grid', compact(
                'productData', 'user_id', 'setting'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'total_product_count' => $total_product_count,
                'showing_product_count' => $showing_product_count,
                'has_more' => ($offset + count($productData)) < $total_product_count,
                'facets' => $facets,
                'price_bounds' => [
                    'min' => floor($dynamicPriceBounds->min_price ?? $catalogMinPrice),
                    'max' => ceil($dynamicPriceBounds->max_price ?? $catalogMaxPrice),
                ],
                'active_pills' => $active_pills,
                'is_append' => $isAppend,
            ]);
        }

        // 8. Full Page View
        return view('frontend.products.index', compact(
            'productData',
            'allCategories',
            'allBrands',
            'allSizes',
            'catalogMinPrice',
            'catalogMaxPrice',
            'dynamicPriceBounds',
            'facets',
            'active_pills',
            'filters',
            'total_product_count',
            'showing_product_count',
            'user_id',
            'setting'
        ));
    }

    /**
     * Parse and sanitize request parameters (with Secure ID Hashing / Decoding)
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
        if (empty($rawCats) && $request->route('id')) {
            $rawCats = [$request->route('id')];
        }

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

        // Decode hashed IDs to integer database IDs safely with array_unique
        $category_ids = array_values(array_unique(array_filter(array_map([Helper::class, 'decodeFilterId'], $rawCats))));
        $subcategory_ids = array_values(array_unique(array_filter(array_map([Helper::class, 'decodeFilterId'], $rawSubcats))));
        $brand_ids = array_values(array_unique(array_filter(array_map([Helper::class, 'decodeFilterId'], $rawBrands))));

        // If subcategories are selected without explicit categories, discover their parent categories for sidebar open state
        if (!empty($subcategory_ids) && empty($category_ids)) {
            $category_ids = SubCategories::whereIn('id', $subcategory_ids)->pluck('category_id')->unique()->filter()->values()->toArray();
        }

        $reqMin = $request->filled('min_price') && is_numeric($request->min_price) ? floatval($request->min_price) : null;
        $reqMax = $request->filled('max_price') && is_numeric($request->max_price) ? floatval($request->max_price) : null;

        // Only register price filter if user adjusted beyond the catalog extremes
        $min_price = ($reqMin !== null && $reqMin > $catalogMinPrice) ? $reqMin : null;
        $max_price = ($reqMax !== null && $reqMax < $catalogMaxPrice) ? $reqMax : null;

        $rawRatings = $this->normalizeArray($request->get('ratings', $request->get('rating')));
        $ratings = array_values(array_unique(array_filter(array_map('intval', $rawRatings), function($r) {
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
     * Build the filtered master product query (Ultra-Fast Indexed Query)
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

        // Check if price / size / in_stock / price sort requires joining product_variants
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

        // Apply Search Keyword
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
                // When subcategories are selected, narrow down to those specific subcategories
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

        // Apply Ratings (Multi-select)
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

            if ($hasBestseller && $hasOffer) {
                $specialOfferIds = $this->getActiveSpecialOfferProductIds();
                $query->where(function ($q) use ($specialOfferIds) {
                    $q->where('p.is_product_bestseller', 1)
                      ->orWhereIn('p.id', !empty($specialOfferIds) ? $specialOfferIds : [-1]);
                });
            } elseif ($hasBestseller) {
                $query->where('p.is_product_bestseller', 1);
            } elseif ($hasOffer) {
                $specialOfferIds = $this->getActiveSpecialOfferProductIds();
                $query->whereIn('p.id', !empty($specialOfferIds) ? $specialOfferIds : [-1]);
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
     * Load full product cards for only the 16 displayed IDs (Sub-millisecond execution)
     */
    protected function loadProductCardsForIds(array $pagedIds, $sortBy = 'newest')
    {
        if (empty($pagedIds)) {
            return collect();
        }

        $idList = implode(',', array_map('intval', $pagedIds));

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
            ->select(
                'first_variant.id',
                'p.id as product_id',
                'p.average_rating',
                'p.product_name',
                'first_image.image',
                'first_variant.variant_price',
                'first_variant.variant_discounted_price',
                'first_variant.variant_size',
                'first_variant.variant_uof',
                'first_variant.available_qty',
                DB::raw('COALESCE(NULLIF(first_variant.variant_discounted_price, ""), first_variant.variant_price) AS final_price'),
                'mvp.count',
                'p.category_id',
                'p.subcategory_id',
                'p.brand_id',
                'p.is_product_bestseller',
                'first_variant.variant_price as product_price',
                'first_variant.variant_size as product_size',
                'first_variant.variant_uof as product_uof'
            );

        // Preserve pagedIds order
        $query->orderByRaw('FIELD(p.id, ' . $idList . ')');

        return $query->get();
    }

    /**
     * Calculate dynamic facet counts for auto-pruning & live badges (Lightweight & Instant with Hashed Keys)
     */
    protected function calculateDynamicFacets(array $filters)
    {
        // 1. Category Facets (relative to other filters, ignoring category filter)
        $catBase = $this->buildFilteredQuery($filters, ['category'])['query'];
        $rawCatCounts = (clone $catBase)
            ->select('p.category_id', DB::raw('COUNT(*) as count'))
            ->groupBy('p.category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        $categoryCounts = [];
        foreach ($rawCatCounts as $cid => $cnt) {
            $hash = Helper::encodeFilterId($cid);
            $categoryCounts[$hash] = $cnt;
            $categoryCounts[$cid] = $cnt;
        }

        // Subcategory counts
        $rawSubcatCounts = (clone $catBase)
            ->select('p.subcategory_id', DB::raw('COUNT(*) as count'))
            ->groupBy('p.subcategory_id')
            ->pluck('count', 'subcategory_id')
            ->toArray();

        $subcatCounts = [];
        foreach ($rawSubcatCounts as $sid => $cnt) {
            $hash = Helper::encodeFilterId($sid);
            $subcatCounts[$hash] = $cnt;
            $subcatCounts[$sid] = $cnt;
        }

        // 2. Brand Facets (relative to other filters, ignoring brand filter)
        $brandBase = $this->buildFilteredQuery($filters, ['brand'])['query'];
        $rawBrandCounts = (clone $brandBase)
            ->select('p.brand_id', DB::raw('COUNT(*) as count'))
            ->groupBy('p.brand_id')
            ->pluck('count', 'brand_id')
            ->toArray();

        $brandCounts = [];
        foreach ($rawBrandCounts as $bid => $cnt) {
            $hash = Helper::encodeFilterId($bid);
            $brandCounts[$hash] = $cnt;
            $brandCounts[$bid] = $cnt;
        }

        // 3. Size Facets
        $sizeBase = $this->buildFilteredQuery($filters, ['size'])['query'];
        $matchingPids = (clone $sizeBase)->pluck('p.id')->toArray();
        $sizeCounts = [];
        if (!empty($matchingPids)) {
            $sizeCounts = DB::table('product_variants as pv')
                ->whereIn('pv.product_id', $matchingPids)
                ->where('pv.status', 1)
                ->whereNotNull('pv.variant_size')
                ->where('pv.variant_size', '>', 0)
                ->select('pv.variant_size', DB::raw('COUNT(DISTINCT pv.product_id) as count'))
                ->groupBy('pv.variant_size')
                ->pluck('count', 'variant_size')
                ->toArray();
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

        // In-stock count (relative to current filters, excluding promotion)
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
     * Get IDs of all products currently covered by active Special Offers (offers table) and BOGOs (bogo table)
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
     * Bulk Eager-load Bogo & Offers & Favorites (Zero N+1)
     */
    protected function attachPromotionsAndVariants($productData, $today)
    {
        if ($productData->isEmpty()) return;

        // 1. Bulk Active BOGOs
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        // 2. Bulk Active Offers
        $activeOffers = Offers::where('status', 1)
            ->whereDate('expiry_date', '>=', $today)
            ->get();

        foreach ($productData as $product) {
            $pid = $product->product_id;
            $cid = $product->category_id;
            $sid = $product->subcategory_id;
            $bid = $product->brand_id;

            // Check BOGO
            $isBogo = false;
            foreach ($activeBogos as $bogo) {
                if ($bogo->product_id && $bogo->product_id == $pid) { $isBogo = true; break; }
                if ($bogo->subcategory_id && $bogo->subcategory_id == $sid) { $isBogo = true; break; }
                if ($bogo->category_id && empty($bogo->subcategory_id) && $bogo->category_id == $cid) { $isBogo = true; break; }
                if ($bogo->brand_id && $bogo->brand_id == $bid) { $isBogo = true; break; }
            }
            $product->bogo_status = $isBogo;

            // Check Offer
            $matchedOffer = null;
            foreach ($activeOffers as $offer) {
                if ($offer->product_id && $offer->product_id == $pid) { $matchedOffer = $offer; break; }
                if ($offer->subcategory_id && $offer->subcategory_id == $sid) { $matchedOffer = $offer; break; }
                if ($offer->category_id && empty($offer->subcategory_id) && $offer->category_id == $cid) { $matchedOffer = $offer; break; }
                if ($offer->brand_id && $offer->brand_id == $bid) { $matchedOffer = $offer; break; }
            }

            if ($matchedOffer) {
                $product->offer_status = true;
                $product->offer_type = $matchedOffer->offer_type;
                $product->discount_amount = $matchedOffer->dis_amount;
            } else {
                $product->offer_status = false;
                $product->offer_type = null;
                $product->discount_amount = 0;
            }
        }
    }

    /**
     * Generate Active Filter Pills array (with Hashed IDs)
     */
    protected function generateActivePills(array $filters, $allCategories = null, $allBrands = null, $catalogMinPrice = 0, $catalogMaxPrice = 50000)
    {
        $pills = [];

        // Keyword pill
        if (!empty($filters['keyword'])) {
            $pills[] = [
                'type' => 'keyword',
                'id' => 'keyword',
                'label' => 'Search: "' . $filters['keyword'] . '"',
            ];
        }

        // Category pills (with hashed ID)
        if (!empty($filters['category_ids'])) {
            $catsMap = $allCategories ? $allCategories->keyBy('id') : collect([]);
            foreach ($filters['category_ids'] as $cid) {
                $name = $catsMap->get($cid)->title ?? 'Category #' . $cid;
                $pills[] = [
                    'type' => 'category',
                    'id' => Helper::encodeFilterId($cid),
                    'label' => 'Category: ' . $name,
                ];
            }
        }

        // Subcategory pills (with hashed ID)
        if (!empty($filters['subcategory_ids'])) {
            $subcats = SubCategories::whereIn('id', $filters['subcategory_ids'])->get()->keyBy('id');
            foreach ($filters['subcategory_ids'] as $sid) {
                $name = $subcats->get($sid)->title ?? 'Subcategory #' . $sid;
                $pills[] = [
                    'type' => 'subcategory',
                    'id' => Helper::encodeFilterId($sid),
                    'label' => 'Subcategory: ' . $name,
                ];
            }
        }

        // Brand pills (with hashed ID)
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
                        'label' => 'Brand: ' . $brandObj->title,
                    ];
                }
            }
        }

        // Size pills
        if (!empty($filters['sizes'])) {
            foreach ($filters['sizes'] as $sz) {
                $pills[] = [
                    'type' => 'size',
                    'id' => $sz,
                    'label' => 'Size: ' . $sz . ' ml',
                ];
            }
        }

        // Price pill (ONLY show if user actually adjusted the price slider away from bounds)
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

        // Rating pills (multi-select)
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
}
