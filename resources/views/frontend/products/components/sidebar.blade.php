@php
    $currencySymbol = Helper::Settings('currency_symbol') ?: 'GH₵';
    $selectedCategories = $filters['category_ids'] ?? [];
    $selectedSubcategories = $filters['subcategory_ids'] ?? [];
    $selectedBrands = $filters['brand_ids'] ?? [];
    $selectedSizes = $filters['sizes'] ?? [];
    $selectedRatings = !empty($filters['ratings']) ? (array)$filters['ratings'] : (!empty($filters['rating']) ? [(int)$filters['rating']] : []);
    $selectedRating = !empty($selectedRatings) ? $selectedRatings[0] : null;
    $selectedBestseller = !empty($filters['bestseller']);
    $selectedOffer = !empty($filters['offer']);
    $selectedInStock = !empty($filters['in_stock']);

    $catCounts = $facets['categories'] ?? [];
    $subcatCounts = $facets['subcategories'] ?? [];
    $brandCounts = $facets['brands'] ?? [];
    $sizeCounts = $facets['sizes'] ?? [];
    $ratingCounts = $facets['ratings'] ?? [];
    $promoCounts = $facets['promotions'] ?? [];

    // Pre-sort items so selected filters always appear at the top
    $sortedCategories = $allCategories->sortByDesc(function ($cat) use ($selectedCategories) {
        $catHash = Helper::encodeFilterId($cat->id);
        return (in_array($cat->id, $selectedCategories) || in_array($catHash, $selectedCategories)) ? 1 : 0;
    });

    $sortedBrands = (!empty($allBrands) && count($allBrands) > 0) ? $allBrands->sortByDesc(function ($b) use ($selectedBrands) {
        $brandHash = Helper::encodeFilterId($b->id);
        return (in_array($b->id, $selectedBrands) || in_array($brandHash, $selectedBrands)) ? 1 : 0;
    }) : collect();

    $sortedSizes = (!empty($allSizes) && count($allSizes) > 0) ? collect($allSizes)->sortByDesc(function ($sz) use ($selectedSizes) {
        return in_array($sz, $selectedSizes) ? 1 : 0;
    }) : collect();
@endphp

<div class="faceted-filter-widget">
    {{-- Filter Widget Header --}}
    <div class="widget-head d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M7 12H17M10 18H14" stroke="#242424" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
            <span class="widget-title">Filters</span>
        </div>
        <a href="javascript:void(0);" onclick="resetAllFacetedFilters();" class="reset-link">
            Reset All
        </a>
    </div>

    {{-- SECTION 1: PRICE RANGE --}}
    <div class="facet-group facet-group-price">
        <div class="facet-header d-flex justify-content-between align-items-center" onclick="toggleFacetSection(this);">
            <span class="facet-title">Price</span>
            <i class="fa-solid fa-chevron-down facet-arrow"></i>
        </div>
        <div class="facet-body">
            {{-- Current Selected Range Display --}}
            <div class="price-display-tag d-flex justify-content-between align-items-center">
                <span class="price-val price-display-min" id="price-display-min">{{ $currencySymbol }}{{ number_format($filters['min_price'] ?? $catalogMinPrice, 2) }}</span>
                <span class="price-sep">to</span>
                <span class="price-val price-display-max" id="price-display-max">{{ $currencySymbol }}{{ number_format($filters['max_price'] ?? $catalogMaxPrice, 2) }}</span>
            </div>

            {{-- Dual Range Slider Bar --}}
            <div class="range-slider-box">
                <div class="range-track-bg"></div>
                <div class="range-track-active range-highlight-track" id="range-highlight-track"></div>
                <input type="range"
                       id="slider-min"
                       class="slider-min-input"
                       min="{{ $catalogMinPrice }}"
                       max="{{ $catalogMaxPrice }}"
                       value="{{ $filters['min_price'] ?? $catalogMinPrice }}"
                       step="1"
                       oninput="onMinSliderMove(this);">
                <input type="range"
                       id="slider-max"
                       class="slider-max-input"
                       min="{{ $catalogMinPrice }}"
                       max="{{ $catalogMaxPrice }}"
                       value="{{ $filters['max_price'] ?? $catalogMaxPrice }}"
                       step="1"
                       oninput="onMaxSliderMove(this);">
            </div>

            {{-- Direct Numeric Input Fields --}}
            <div class="price-inputs-row d-flex gap-2">
                <div class="price-input-col flex-fill">
                    <label class="price-input-label">Min</label>
                    <div class="input-with-sym">
                        <span>{{ $currencySymbol }}</span>
                        <input type="number" 
                               id="num-input-min"
                               class="num-input-min price-num-input" 
                               value="{{ $filters['min_price'] ?? $catalogMinPrice }}" 
                               min="0"
                               step="any"
                               onkeydown="if(['-', '+', 'e', 'E'].includes(event.key)) event.preventDefault();"
                               oninput="if(this.value < 0) this.value = 0;"
                               onchange="onPriceInputChange(this);">
                    </div>
                </div>
                <div class="price-input-col flex-fill">
                    <label class="price-input-label">Max</label>
                    <div class="input-with-sym">
                        <span>{{ $currencySymbol }}</span>
                        <input type="number" 
                               id="num-input-max"
                               class="num-input-max price-num-input" 
                               value="{{ $filters['max_price'] ?? $catalogMaxPrice }}" 
                               min="0"
                               step="any"
                               onkeydown="if(['-', '+', 'e', 'E'].includes(event.key)) event.preventDefault();"
                               oninput="if(this.value < 0) this.value = 0;"
                               onchange="onPriceInputChange(this);">
                    </div>
                </div>
            </div>

            {{-- Quick Price Pills --}}
            <div class="quick-chips-row">
                <button type="button" class="quick-chip" onclick="setQuickPrice({{ $catalogMinPrice }}, {{ min(50, $catalogMaxPrice) }});">Under 50</button>
                <button type="button" class="quick-chip" onclick="setQuickPrice(50, 150);">50 - 150</button>
                <button type="button" class="quick-chip" onclick="setQuickPrice(150, 500);">150 - 500</button>
                <button type="button" class="quick-chip" onclick="setQuickPrice(500, {{ $catalogMaxPrice }});">500+</button>
            </div>
        </div>
    </div>

    {{-- SECTION 2: CATEGORIES --}}
    @php
        $hasAnyCategory = $sortedCategories->contains(function ($c) use ($catCounts, $selectedCategories) {
            $cHash = Helper::encodeFilterId($c->id);
            $cnt = $catCounts[$cHash] ?? $catCounts[$c->id] ?? 0;
            $isChk = in_array($c->id, $selectedCategories) || in_array($cHash, $selectedCategories);
            return $isChk || $cnt > 0;
        });
    @endphp
    <div class="facet-group facet-group-categories {{ !$hasAnyCategory ? 'd-none' : '' }}">
        <div class="facet-header d-flex justify-content-between align-items-center" onclick="toggleFacetSection(this);">
            <span class="facet-title">Category</span>
            <i class="fa-solid fa-chevron-down facet-arrow"></i>
        </div>
        <div class="facet-body">
            <ul class="facet-list facet-cat-list">
                @foreach ($sortedCategories as $category)
                    @php
                        $catHash = Helper::encodeFilterId($category->id);
                        $cCount = $catCounts[$catHash] ?? $catCounts[$category->id] ?? 0;
                        $isCatChecked = in_array($category->id, $selectedCategories) || in_array($catHash, $selectedCategories);
                        $hasSub = $category->subcategory && $category->subcategory->count() > 0;
                        $sortedSubcategories = $hasSub ? $category->subcategory->sortByDesc(function ($sub) use ($selectedSubcategories) {
                            $subHash = Helper::encodeFilterId($sub->id);
                            return (in_array($sub->id, $selectedSubcategories) || in_array($subHash, $selectedSubcategories)) ? 1 : 0;
                        }) : collect();
                        $hasAnySubAvailable = $hasSub && $sortedSubcategories->contains(function ($sub) use ($subcatCounts, $selectedSubcategories) {
                            $subHash = Helper::encodeFilterId($sub->id);
                            $cnt = $subcatCounts[$subHash] ?? $subcatCounts[$sub->id] ?? 0;
                            $isChk = in_array($sub->id, $selectedSubcategories) || in_array($subHash, $selectedSubcategories);
                            return $isChk || $cnt > 0;
                        });
                        $hasAnyCheckedSub = $hasSub && $sortedSubcategories->contains(function ($sub) use ($selectedSubcategories) {
                            $subHash = Helper::encodeFilterId($sub->id);
                            return in_array($sub->id, $selectedSubcategories) || in_array($subHash, $selectedSubcategories);
                        });
                        $isSubListOpen = ($isCatChecked || $hasAnyCheckedSub) && $hasAnySubAvailable;
                    @endphp
                    <li class="facet-item facet-cat-item {{ $cCount == 0 && !$isCatChecked ? 'zero-count' : '' }}" 
                        data-id="{{ $catHash }}">
                        <div class="d-flex justify-content-between align-items-center facet-row-inner">
                            <label class="custom-check-wrap">
                                <input type="checkbox"
                                       class="filter-cat-cb"
                                       name="categories[]"
                                       value="{{ $catHash }}"
                                       id="cat_{{ $catHash }}"
                                       {{ $isCatChecked ? 'checked' : '' }}
                                       {{ $cCount == 0 && !$isCatChecked ? 'disabled' : '' }}>
                                <span class="check-box-ui"></span>
                                <span class="check-label">{{ $category->title }}</span>
                            </label>

                            <div class="d-flex align-items-center gap-2">
                                <span class="count-pill" id="cat-count-{{ $catHash }}">{{ $cCount }}</span>
                                @if ($hasSub)
                                    <span class="sub-toggle-btn {{ !$hasAnySubAvailable ? 'd-none' : '' }}" onclick="toggleSubcatList(event, this);" title="View subcategories">
                                        <i class="fa-solid {{ $isSubListOpen ? 'fa-angle-up' : 'fa-angle-down' }}"></i>
                                    </span>
                                @else
                                    <span class="sub-toggle-btn-placeholder"></span>
                                @endif
                            </div>
                        </div>

                        {{-- Subcategory child list (All available subcategories always shown) --}}
                        @if ($hasSub)
                            <div class="facet-sub-wrap {{ $isSubListOpen ? 'show' : '' }} {{ !$hasAnySubAvailable ? 'd-none' : '' }}">
                                <ul class="facet-sub-list {{ $isSubListOpen ? 'show' : '' }}" data-parent="{{ $catHash }}">
                                    @foreach ($sortedSubcategories as $sub)
                                        @php
                                            $subHash = Helper::encodeFilterId($sub->id);
                                            $sCount = $subcatCounts[$subHash] ?? $subcatCounts[$sub->id] ?? 0;
                                            $isSubChecked = in_array($sub->id, $selectedSubcategories) || in_array($subHash, $selectedSubcategories);
                                        @endphp
                                        <li class="facet-sub-item"
                                            data-id="{{ $subHash }}"
                                            data-parent="{{ $catHash }}">
                                            <div class="d-flex justify-content-between align-items-center facet-row-inner">
                                                <label class="custom-check-wrap">
                                                    <input type="checkbox"
                                                           class="filter-subcat-cb"
                                                           name="subcategories[]"
                                                           value="{{ $subHash }}"
                                                           id="sub_{{ $subHash }}"
                                                           data-parent="{{ $catHash }}"
                                                           {{ $isSubChecked ? 'checked' : '' }}>
                                                    <span class="check-box-ui"></span>
                                                    <span class="check-label sub-label">{{ $sub->title }}</span>
                                                </label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="count-pill sub-pill" id="subcat-count-{{ $subHash }}">{{ $sCount }}</span>
                                                    <span class="sub-toggle-btn-placeholder"></span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($category->subcategory->count() > 4)
                                    <div class="see-more-wrap sub-see-more-wrap">
                                        <button type="button" class="btn-see-more sub-see-more-btn" data-parent="{{ $catHash }}" data-state="collapsed">
                                            <span class="see-more-text">+ See {{ $category->subcategory->count() - 4 }} more</span>
                                            <i class="fa-solid fa-angle-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
            @if ($allCategories->count() > 4)
                <div class="see-more-wrap">
                    <button type="button" class="btn-see-more cat-see-more-btn" data-target="cat" data-state="collapsed">
                        <span class="see-more-text">+ See {{ $allCategories->count() - 4 }} more</span>
                        <i class="fa-solid fa-angle-down ms-1"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- SECTION 3: BRANDS --}}
    @if (!empty($allBrands) && count($allBrands) > 0)
        @php
            $hasAnyBrand = collect($sortedBrands)->contains(function ($b) use ($brandCounts, $selectedBrands) {
                $bHash = Helper::encodeFilterId($b->id);
                $cnt = $brandCounts[$bHash] ?? $brandCounts[$b->id] ?? 0;
                $isChk = in_array($b->id, $selectedBrands) || in_array($bHash, $selectedBrands);
                return $isChk || $cnt > 0;
            });
        @endphp
        <div class="facet-group facet-group-brands {{ !$hasAnyBrand ? 'd-none' : '' }}">
            <div class="facet-header d-flex justify-content-between align-items-center" onclick="toggleFacetSection(this);">
                <span class="facet-title">Brand</span>
                <i class="fa-solid fa-chevron-down facet-arrow"></i>
            </div>
            <div class="facet-body">
                {{-- Instant Brand Search Input --}}
                <div class="brand-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" 
                           id="brand-search-field" 
                           class="brand-search-input"
                           placeholder="Search brands..." 
                           onkeyup="liveBrandFilter(this.value);">
                </div>

                <ul class="facet-chips-wrap brand-scroll-list" id="brand-list-container">
                    <li id="no-brands-msg" class="text-muted text-center py-2 w-100" style="display: none; font-size: 12px;">No matching brands</li>
                    @foreach ($sortedBrands as $brand)
                        @php
                            $brandHash = Helper::encodeFilterId($brand->id);
                            $bCount = $brandCounts[$brandHash] ?? $brandCounts[$brand->id] ?? 0;
                            $isBrandChecked = in_array($brand->id, $selectedBrands) || in_array($brandHash, $selectedBrands);
                        @endphp
                        <li class="facet-chip-item facet-brand-item {{ $bCount == 0 && !$isBrandChecked ? 'zero-count' : '' }}" 
                            data-id="{{ $brandHash }}"
                            data-name="{{ strtolower($brand->title) }}">
                            <label class="chip-filter-btn {{ $isBrandChecked ? 'active' : '' }}">
                                <input type="checkbox"
                                       class="filter-brand-cb"
                                       name="brands[]"
                                       value="{{ $brandHash }}"
                                       id="brand_{{ $brandHash }}"
                                       {{ $isBrandChecked ? 'checked' : '' }}
                                       {{ $bCount == 0 && !$isBrandChecked ? 'disabled' : '' }}>
                                <span class="chip-title brand-title-text">{{ $brand->title }}</span>
                                <span class="chip-count" id="brand-count-{{ $brandHash }}">{{ $bCount }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                @if (count($sortedBrands) > 8)
                    <div class="see-more-wrap">
                        <button type="button" class="btn-see-more brand-see-more-btn" data-target="brand" data-state="collapsed">
                            <span class="see-more-text">+ See {{ count($sortedBrands) - 8 }} more</span>
                            <i class="fa-solid fa-angle-down ms-1"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- SECTION 4: BOTTLE SIZE / VOLUME --}}
    @if (!empty($allSizes) && count($allSizes) > 0)
        @php
            $hasAnySize = collect($sortedSizes)->contains(function ($sz) use ($sizeCounts, $selectedSizes) {
                $cnt = $sizeCounts[$sz] ?? 0;
                $isChk = in_array($sz, $selectedSizes);
                return $isChk || $cnt > 0;
            });
        @endphp
        <div class="facet-group facet-group-sizes {{ !$hasAnySize ? 'd-none' : '' }}">
            <div class="facet-header d-flex justify-content-between align-items-center" onclick="toggleFacetSection(this);">
                <span class="facet-title">Bottle Size</span>
                <i class="fa-solid fa-chevron-down facet-arrow"></i>
            </div>
            <div class="facet-body">
                <ul class="facet-chips-wrap facet-size-list" id="size-list-container">
                    <li id="no-sizes-msg" class="text-muted text-center py-2 w-100" style="display: none; font-size: 12px;">No matching sizes</li>
                    @foreach ($sortedSizes as $size)
                        @php
                            $szCount = $sizeCounts[$size] ?? 0;
                            $isSizeChecked = in_array($size, $selectedSizes);
                        @endphp
                        <li class="facet-chip-item facet-size-item {{ $szCount == 0 && !$isSizeChecked ? 'zero-count' : '' }}" 
                            data-size="{{ $size }}">
                            <label class="chip-filter-btn {{ $isSizeChecked ? 'active' : '' }}">
                                <input type="checkbox"
                                       class="filter-size-cb"
                                       name="sizes[]"
                                       value="{{ $size }}"
                                       id="size_{{ $size }}"
                                       {{ $isSizeChecked ? 'checked' : '' }}
                                       {{ $szCount == 0 && !$isSizeChecked ? 'disabled' : '' }}>
                                <span class="chip-title">{{ $size }} ml</span>
                                <span class="chip-count" id="size-count-{{ $size }}">{{ $szCount }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                @if (count($sortedSizes) > 8)
                    <div class="see-more-wrap">
                        <button type="button" class="btn-see-more size-see-more-btn" data-target="size" data-state="collapsed">
                            <span class="see-more-text">+ See {{ count($sortedSizes) - 8 }} more</span>
                            <i class="fa-solid fa-angle-down ms-1"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- SECTION 5: CUSTOMER RATING --}}
    @php
        $r5 = $ratingCounts['5'] ?? 0;
        $r4 = $ratingCounts['4'] ?? 0;
        $r3 = $ratingCounts['3'] ?? 0;
        $r2 = $ratingCounts['2'] ?? 0;
        $r1 = $ratingCounts['1'] ?? 0;
        $hasAnyRating = $r5 > 0 || $r4 > 0 || $r3 > 0 || $r2 > 0 || $r1 > 0 || !empty(request('rating')) || !empty(request('ratings')) || !empty($selectedRatings);
        $ratingsData = [
            5 => ['gold' => 5, 'muted' => 0, 'count' => $r5, 'label' => '5 Star'],
            4 => ['gold' => 4, 'muted' => 1, 'count' => $r4, 'label' => '4 Star'],
            3 => ['gold' => 3, 'muted' => 2, 'count' => $r3, 'label' => '3 Star'],
            2 => ['gold' => 2, 'muted' => 3, 'count' => $r2, 'label' => '2 Star'],
            1 => ['gold' => 1, 'muted' => 4, 'count' => $r1, 'label' => '1 Star'],
        ];
    @endphp
    <div class="facet-group facet-group-ratings {{ !$hasAnyRating ? 'd-none' : '' }}">
        <div class="facet-header d-flex justify-content-between align-items-center" onclick="toggleFacetSection(this);">
            <span class="facet-title">Customer Rating</span>
            <i class="fa-solid fa-chevron-down facet-arrow"></i>
        </div>
        <div class="facet-body">
            <ul class="facet-list">
                @foreach ($ratingsData as $starVal => $starInfo)
                    <li class="facet-item facet-rating-item {{ $loop->first ? '' : 'mt-1' }}" data-rating="{{ $starVal }}">
                        <div class="d-flex justify-content-between align-items-center facet-row-inner">
                            <label class="custom-check-wrap">
                                <input type="checkbox"
                                       class="filter-rating-cb"
                                       name="ratings[]"
                                       value="{{ $starVal }}"
                                       {{ in_array($starVal, $selectedRatings) ? 'checked' : '' }}>
                                <span class="check-box-ui"></span>
                                <span class="check-label stars-label">
                                    <span class="stars-group">
                                        @for ($i = 0; $i < $starInfo['gold']; $i++)
                                            <i class="fa-solid fa-star star-filled"></i>
                                        @endfor
                                        @for ($j = 0; $j < $starInfo['muted']; $j++)
                                            <i class="fa-regular fa-star star-empty"></i>
                                        @endfor
                                    </span>
                                    <span class="stars-text">{{ $starInfo['label'] }}</span>
                                </span>
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="count-pill rating-count-pill" data-rating="{{ $starVal }}" id="rating-count-{{ $starVal }}">{{ $starInfo['count'] }}</span>
                                <span class="sub-toggle-btn-placeholder"></span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- SECTION 6: PROMOTIONS & DEALS --}}
    @php
        $bsCount = $promoCounts['bestsellers'] ?? 0;
        $offCount = $promoCounts['offers'] ?? 0;
        $stCount = $promoCounts['in_stock'] ?? 0;
        $hasAnyPromo = $bsCount > 0 || $offCount > 0 || $stCount > 0 || !empty(request('bestseller')) || !empty(request('offer')) || !empty(request('in_stock'));
    @endphp
    <div class="facet-group facet-group-promos border-bottom-0 {{ !$hasAnyPromo ? 'd-none' : '' }}">
        <div class="facet-header d-flex justify-content-between align-items-center" onclick="toggleFacetSection(this);">
            <span class="facet-title">Deals & Availability</span>
            <i class="fa-solid fa-chevron-down facet-arrow"></i>
        </div>
        <div class="facet-body">
            @php
                $bsCount = $promoCounts['bestsellers'] ?? 0;
                $offCount = $promoCounts['offers'] ?? 0;
                $stCount = $promoCounts['in_stock'] ?? 0;
            @endphp
            <ul class="facet-list">
                <li class="facet-item facet-promo-item" id="promo-row-bestseller">
                    <div class="d-flex justify-content-between align-items-center facet-row-inner">
                        <label class="custom-check-wrap">
                            <input type="checkbox"
                                   class="filter-bestseller-cb"
                                   name="bestseller"
                                   value="1"
                                   id="promo_bestseller"
                                   {{ $selectedBestseller ? 'checked' : '' }}>
                            <span class="check-box-ui"></span>
                            <span class="check-label d-flex align-items-center gap-1">
                                <i class="fa-solid fa-award text-warning"></i> Best Sellers
                            </span>
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="count-pill" id="promo-count-bestseller">{{ $bsCount }}</span>
                            <span class="sub-toggle-btn-placeholder"></span>
                        </div>
                    </div>
                </li>
                <li class="facet-item facet-promo-item mt-1" id="promo-row-offer">
                    <div class="d-flex justify-content-between align-items-center facet-row-inner">
                        <label class="custom-check-wrap">
                            <input type="checkbox"
                                   class="filter-offer-cb"
                                   name="offer"
                                   value="1"
                                   id="promo_offer"
                                   {{ $selectedOffer ? 'checked' : '' }}>
                            <span class="check-box-ui"></span>
                            <span class="check-label d-flex align-items-center gap-1">
                                <i class="fa-solid fa-fire text-danger"></i> Special Offers
                            </span>
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="count-pill" id="promo-count-offer">{{ $offCount }}</span>
                            <span class="sub-toggle-btn-placeholder"></span>
                        </div>
                    </div>
                </li>
                <li class="facet-item facet-promo-item mt-1" id="promo-row-instock">
                    <div class="d-flex justify-content-between align-items-center facet-row-inner">
                        <label class="custom-check-wrap">
                            <input type="checkbox"
                                   class="filter-instock-cb"
                                   name="in_stock"
                                   value="1"
                                   id="promo_instock"
                                   {{ $selectedInStock ? 'checked' : '' }}>
                            <span class="check-box-ui"></span>
                            <span class="check-label d-flex align-items-center gap-1">
                                <i class="fa-solid fa-box text-success"></i> In Stock Only
                            </span>
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="count-pill" id="promo-count-instock">{{ $stCount }}</span>
                            <span class="sub-toggle-btn-placeholder"></span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
