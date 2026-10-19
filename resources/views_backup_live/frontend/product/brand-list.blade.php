@extends('frontend.layouts.app')
@section('title', 'Product List')
@section('content')
@include('sweetalert::alert')

<style>
    .bogo {
        /* position: absolute; */
        top: 5px;
        right: 14px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding:4px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
        transition: all 0.3s ease-in-out;
        transform: scale(1);
    }

        /* .bogo:hover {
        transform: scale(1.05);
    } */

    .offer {
        /* position: absolute; */
        top: 5px;
        right: 14px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding:4px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
        transition: all 0.3s ease-in-out;
        transform: scale(1);
    }
/* 
    .offer:hover {
        transform: scale(1.05);
    } */


    
      .swal-custom-confirm {
            background: #fbb516 !important; 
            color: black !important;
            border: 1px solid #fbb516 !important;
        }

        .swal-custom-confirm:focus {
            outline: none !important; 
            /* box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.5) !important;  */
            box-shadow: none !important; 
        }

        .swal2-icon.swal2-success {
            border-color: black !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border: 4px solid #fbb516 !important;
        }

        .swal2-icon.swal2-success .swal2-success-line-tip,
        .swal2-icon.swal2-success .swal2-success-line-long {
            background-color: #fbb516 !important;
        }
</style>

<div class="loader" id="loader"></div>

{{-- product Listing --}}
<section class="product-listing mt-5">
    <div class="container">
        <div class="row" id = "product_list">
            <div class="col-12">
                <div class="short-by">
                    <input type="hidden" name="keyword" id="keyword" value="{{@$keyword}}">
                    <input type="hidden" name="brand_id" id="brand_id" value="{{@$brand_id}}">
                    <p class="mb-0">{{ Helper::language('all_products') }} ( <span
                            id="total_page_count">{{ @$total_product_count ?: '0' }}</span> Items )</p>
                    <div class="short-by-block">
                        <form action="">
                            <select name="" id="sort-by">
                                <option value="">{{ Helper::language('sort_by') }}</option>
                                <option value="1">Newest</option>
                                <option value="2">{{ Helper::language('high_to_low') }}</option>
                                <option value="3">{{ Helper::language('low_to_high') }}</option>
                                <option value="4">Popular</option>
                            </select>
                        </form>
                        <button type="button" class="solid-button backdrop" data-bs-toggle="offcanvas"
                            data-bs-target="#filteroffcanvas" aria-controls="offcanvasRight">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.5 8.61971H12.641C12.8656 9.54023 13.6863 10.2248 14.6621 10.2248C15.6378 10.2248 16.4585 9.54023 16.6831 8.61971H19.5C19.7761 8.61971 20 8.39257 20 8.11243C20 7.83229 19.7761 7.60515 19.5 7.60515H16.6831C16.4585 6.68463 15.6378 6 14.662 6C13.6863 6 12.8655 6.68463 12.641 7.60515H4.5C4.22387 7.60515 4 7.83229 4 8.11243C4 8.39257 4.22387 8.61971 4.5 8.61971ZM14.6621 7.01455C15.2587 7.01455 15.7442 7.50706 15.7442 8.1124C15.7442 8.71777 15.2587 9.21027 14.6621 9.21027C14.0654 9.21027 13.5799 8.71777 13.5799 8.1124C13.5799 7.50706 14.0654 7.01455 14.6621 7.01455ZM4.5 13.0073H7.31694C7.5415 13.9278 8.36222 14.6124 9.33797 14.6124C10.3137 14.6124 11.1344 13.9278 11.359 13.0073H19.5C19.7761 13.0073 20 12.7801 20 12.5C20 12.2199 19.7761 11.9927 19.5 11.9927H11.359C11.1344 11.0722 10.3137 10.3876 9.33794 10.3876C8.36219 10.3876 7.54147 11.0722 7.31691 11.9927H4.5C4.22387 11.9927 4 12.2199 4 12.5C4 12.7801 4.22384 13.0073 4.5 13.0073ZM9.33794 11.4021C9.93462 11.4021 10.4201 11.8946 10.4201 12.5C10.4201 13.1053 9.93462 13.5978 9.33794 13.5978C8.74125 13.5978 8.25581 13.1053 8.25581 12.5C8.25581 11.8946 8.74125 11.4021 9.33794 11.4021ZM19.5 16.3803H16.6831C16.4585 15.4598 15.6378 14.7751 14.662 14.7751C13.6863 14.7751 12.8656 15.4598 12.641 16.3803H4.5C4.22387 16.3803 4 16.6074 4 16.8876C4 17.1677 4.22387 17.3948 4.5 17.3948H12.641C12.8656 18.3154 13.6863 19 14.6621 19C15.6378 19 16.4585 18.3154 16.6831 17.3948H19.5C19.7761 17.3948 20 17.1677 20 16.8876C20 16.6074 19.7762 16.3803 19.5 16.3803ZM14.6621 17.9854C14.0654 17.9854 13.5799 17.4929 13.5799 16.8876C13.5799 16.2822 14.0654 15.7897 14.6621 15.7897C15.2587 15.7897 15.7442 16.2822 15.7442 16.8876C15.7442 17.4929 15.2587 17.9854 14.6621 17.9854Z"
                                    fill="#242424" />
                            </svg>{{ Helper::language('filter') }}
                        </button>
                    </div>
                </div>
                <div class="row product-listing-row" id="table_data">
                    @include('frontend.product.ajax_brand')
                </div>
                <div class="row">
                    <div class="offset-md-5 col-md-7">
                        <div class="show-more">
                            <button
                                @if ($total_product_count != 0 && $total_product_count > 16) style="display:block;" @else style="display:none;" @endif
                                class="solid-button show-more load-more-data">{{ Helper::language('show_more') }}</button>
                            @if ($total_product_count != 0)
                                <p id="showing">{{ Helper::language('showing') }}
                                    <span id="p-current-count">{{ $showing_product_count ?: 0 }} </span> of <span
                                        id="p-total-count">{{ $total_product_count }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- End Product Listing -->

<!-- Service -->
@include('frontend.service.service')
<!-- End Service -->

<!-- Newsletter -->
@include('frontend.newsletter.newsletter')
<!-- End Newsletter -->

<div class="offcanvas offcanvas-end filteroffcanvas" tabindex="-1" id="filteroffcanvas"
aria-labelledby="offcanvasRightLabel" style="display: flex; flex-direction: column; height: 100%;">
<div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ Helper::language('filter') }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body" style="flex-grow: 1; overflow-y: auto;padding-bottom:10px;">

    {{-- //changes 1 --}}
    {{-- <div class="card-header"> --}}

        <div class="wrapper">
            <div class="price-title">
                <p class="text-sm mb-0 black-text">{{ Helper::language('price') }}</p>
            </div>
        
            <div class="values">
                <span id="range1">
                    {{ $product_min_price->min_price . Helper::Settings('currency_symbol') ?: '0' }}
                </span>
                <span> &dash; </span>
                <span id="range2">
                    {{ $product_max_price->max_price . Helper::Settings('currency_symbol') ?: '0' }}
                </span>
            </div>
        
            <div class="container-range">
                <div class="slider-track"></div>
        
                <!-- Sliders together -->
                <input type="range"
                    min="{{ $product_min_price->min_price ?: '0' }}"
                    max="{{ $product_max_price->max_price ?: '0' }}"
                    value="{{ $product_min_price->min_price ?: '0' }}"
                    id="slider-1"
                    oninput="slideOne()">
        
                <input type="range"
                    min="{{ $product_min_price->min_price ?: '0' }}"
                    max="{{ $product_max_price->max_price ?: '0' }}"
                    value="{{ $product_max_price->max_price ?: '0' }}"
                    id="slider-2"
                    oninput="slideTwo()">
            </div>
        
            <!-- ✅ Visible input boxes BELOW sliders -->
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <input type="number" name="min_price" id="min-price" value="{{ $product_min_price->min_price }}" placeholder="Min">
                <input type="number" name="max_price" id="max-price" value="{{ $product_max_price->max_price }}" placeholder="Max">
            </div>
        </div>
        
        <div class="filter-listing" style="padding: 0px 0px">
            <span class="filter-title custom-border m-3 border-2" style="background-color: #f1f1f1; padding-left:10px ; padding-right:10px;  
           border: 10px solid  #e4e2e2;">{{ Helper::language('category') }}</span>
            <ul class="filter-list p-3" style="background-color:white; margin:15px;">
                @foreach ($categories as $category)
                    <li>
                        <div class="check-group">
                            <input type="checkbox"
                                class="form-check-input product_categories  border border-dark"
                                name="product_categories[]"
                                value="{{ $category->id }}"
                                id="category_id_{{ $category->id }}">
                            <label for="category_id_{{ $category->id }}" class="form-check-label">
                                {{ session('language') == 1 ? $category->title : $category->title_fr }}
                            </label>
                        </div>
        
                        @if ($category->subcategory && $category->subcategory->count())
                            <ul class="sub-filter-list">
                                @foreach ($category->subcategory as $sub)
                                    <li style="padding-left: 12px;">
                                        <div class="check-group">
                                            <input type="checkbox"
                                                class="form-check-input subcategories border border-dark"
                                                name="subcategories[]"
                                                value="{{ $sub->id }}"
                                                id="sub_category_{{ $category->id }}_{{ $sub->id }}"
                                                data-parent="{{ $category->id }}">
                                            <label for="sub_category_{{ $category->id }}_{{ $sub->id }}" class="form-check-label">
                                                {{ session('language') == 1 ? $sub->title : $sub->title_fr }}
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        {{-- </div> --}}


        @if ($brandData && count($brandData) > 0)
             <div class="">
                <span class="filter-title m-3 p4 border-2" style="background-color: #f1f1f1 ; padding-left:10px ; padding-right:10px ;
                border: 10px solid #e4e2e2;">{{ Helper::language('brand') }}</span>
                <div class="filter-list p-3" style="margin:15px">
                    <input type="text" id="brand-search"
                        placeholder="Search" onkeyup="filterBrands()"
                        class="form-control form-control-solid mb-3">
                    <ul id="brand-list">
                        @foreach ($brandData as $brand_result)
                            @php
                                if (session::get('language') == 1) {
                                    $brand_title = $brand_result->title;
                                } else {
                                    $brand_title = $brand_result->title_fr;
                                }
                            @endphp
                           <li>
                            <div class="check-group">
                                <input class="form-check-input brands border border-dark" name="brands[]" type="checkbox"
                                    id="brands_{{ $brand_result->id }}" value="{{ $brand_result->id }}">
                                <label class="form-check-label"
                                    for="brands_{{ $brand_result->id }}">{{ $brand_title }}</label>
                            </div>
                        </li>
                        
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card-footer" style="padding: 31px;">
    <div class="filter-button">
        <div class="filter-button-group">
            <a href="javascript:void(0);" onclick="location.reload();" 
               id="clear-filter"
               style="background-color: #fff; color: #000; border: 0.5px solid #000; 
                      padding: 11px 40px; font-size: 16px; line-height: 24px;
                      cursor: pointer; text-align: center; text-decoration: none;
                      border-radius: 0; display: inline-flex; align-items: center;
                      justify-content: center; text-transform: uppercase; 
                      transition: all 0.4s ease;">
               {{ Helper::language('clear') }}
            </a>
 
            <a href="javascript:void(0);" id="apply-filter" 
               class="solid-button" data-bs-dismiss="offcanvas" 
               aria-label="Close">
               {{ Helper::language('apply_filter') }}
            </a>
        </div>
    </div>
 </div>
 </div>


<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>

<script>      
    const clearButton = document.getElementById('clear-filter');

    clearButton.addEventListener('mouseenter', () => {
        clearButton.style.backgroundColor = '#fbb516';
        clearButton.style.color = '#242424';             
        clearButton.style.border = '2px solid #fbb516';  
    });

    clearButton.addEventListener('mouseleave', () => {
        clearButton.style.backgroundColor = '#fff';     
        clearButton.style.color = '#000';                
        clearButton.style.border = '2px solid #000';   
    });
</script>

<script type="text/javascript">
        function brandsFilterIds() {
            var brand_ids = [];
            $.each($("input[name='brands[]']:checked"), function() {
                brand_ids.push($(this).val());
            });
            return brand_ids;
        }

        function categoryFilterIds() {
            var category_ids = [];
            $.each($("input[name='product_categories[]']:checked"), function() {
                category_ids.push($(this).val());
            });
            return category_ids;
        }

        function subCategoryFilterIds() {
            var subcategory_ids = [];
            $.each($("input[name='subcategories[]']:checked"), function() {
                subcategory_ids.push($(this).val());
            });
            return subcategory_ids;
        }

        function filterBrands() {
            const searchInput = document.getElementById('brand-search').value.toLowerCase();
            const brandList = document.getElementById('brand-list');
            const brands = brandList.getElementsByTagName('li');

            for (let i = 0; i < brands.length; i++) {
                const label = brands[i].querySelector('.form-check-label').textContent.toLowerCase();

                if (label.includes(searchInput)) {
                    brands[i].style.display = ''; 
                } else {
                    brands[i].style.display = 'none'; 
                }
            }
        }

        $(document).ready(function() {
            $("#apply-filter, #sort-by").click(function() {
                getProductData();
            });

            $(".load-more-data").click(function() {
                $(".product_counts").remove();
                var product_last_id = $("#last-id").val();
                var sort_by = $("#sort-by").val();
                var brand_ids = brandsFilterIds();
                var category_ids = categoryFilterIds();
                var subcategory_ids = subCategoryFilterIds();
                var min_price = $("#min-price").val();
                var max_price = $("#max-price").val();
                var current_page_count = $("#p-current-count").text();
                var keyword = $("#keyword").val();
                var myBrand = $("#brand_id").val();

                $.ajax({
                    url: "{{ route('loadbrandlist') }}?sort_by=" + sort_by + "&brand_ids=" +
                        brand_ids + "&category_ids=" + category_ids + "&subcategory_ids=" +
                        subcategory_ids + "&min_price=" + min_price + "&max_price=" + max_price +
                        '&product_last_id=' + product_last_id + "&current_page_count=" +
                        current_page_count+"&keyword=" +
                        keyword + "&myBrand=" + myBrand,
                    beforeSend: function() {
                        $(".loader").fadeIn();
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(data) {
                        $('.loader').css("visibility", "hidden");
                        $('#appen-html' + product_last_id).after(data);
                        $("#last-id").remove();
                
                    }
                }).done(function() {
                    var current_count = $(".load_products_ids").length;
                    $("#p-current-count").text(current_count);
                    var pro_total_count = $("#p-total-count").text();
                    
                    if (pro_total_count == current_count) {
                        $('.load-more-data').hide();
                    }
                    else {
                        $('.load-more-data').show(); 
                    }
                });
            });
        })


        function getProductData() {
            var brand_ids = brandsFilterIds();  
            var category_ids = categoryFilterIds(); 
            var subcategory_ids = subCategoryFilterIds();  
            var min_price = $("#min-price").val(); 
            var max_price = $("#max-price").val();  
            var sort_by = $("#sort-by").val();  
            var keyword = $("#keyword").val();
            var myBrand = $("#brand_id").val();

            // remove old data for load
            $("#p-current-count").text(0); 
            $(".load-more-data").show();

            $.ajax({
                url: "{{ route('loadbrandlist') }}", 
                data: {
                    sort_by: sort_by,
                    brand_ids: brand_ids,
                    category_ids: category_ids,
                    subcategory_ids: subcategory_ids,
                    min_price: min_price,
                    max_price: max_price,
                    keyword: keyword,
                    myBrand: myBrand,
                    current_page_count: 0
                },
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(data) {
                    $('.loader').css("visibility", "hidden");
                    $('#table_data').html(data);  
                    productCountData(); 

                }
            });
        }

        function productCountData() {
            $("#p-current-count").text($("#proFcount").val());
            var pro_tcount = $("#proTcount").val();
            if (pro_tcount == 0) {
                $("#showing").hide();
                pro_tcount = 0
            } else {
                $("#showing").show();
            }
            $("#p-total-count").text($("#proTcount").val());
            $("#total_page_count").text(pro_tcount);

             // Reset the visibility of the "Load More" button based on the new count
            var current_count = $(".load_products_ids").length;
            if (pro_tcount > current_count) {
                $(".load-more-data").show();
            } else {
                $(".load-more-data").hide();
            }
        }

        function productFav(product_id, status) {
            var status = status;
            var user_id = $("#user_id").val();
            if (user_id) {
                action_url = "{{ route('productfav') }}";
            } else {
                var url = "{{ route('websitelogin') }}";
                window.location.href = url;
            }
            var csrf = "{{ csrf_token() }}";
            $.ajax({
                url: action_url,
                data: {
                    'user_id': user_id,
                    'product_id': product_id,
                    'status': status
                },
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                type: "POST",

                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(response) {
                    $('.loader').css("visibility", "visible");
                    if (user_id != '') {
                        location.reload();
                    } else {
                        location.href = "{{ route('websitelogin') }}";
                    }
                },
            });
        }

        var currency_type = "{{ Helper::Settings('currency_symbol') }}";
          
        $(document).ready(function () {
            // When a category is (un)checked, update its subcategories
            $('.product_categories').on('change', function () {
                const categoryId = $(this).attr('value'); // FIXED: .val() replaced
                const isChecked = $(this).is(':checked');

                $('.subcategories[data-parent="' + categoryId + '"]').prop('checked', isChecked);
                getProductData();
            });

            //  When a subcategory is (un)checked, update its parent category
            $('.subcategories').on('change', function () {
                const categoryId = $(this).data('parent');
                const subCheckboxes = $('.subcategories[data-parent="' + categoryId + '"]');
                const allChecked = subCheckboxes.length === subCheckboxes.filter(':checked').length;

                $('#category_id_' + categoryId).prop('checked', allChecked);
                getProductData();
            });

            //  On page load, sync parent checkboxes
            function checkCategorySelection() {
                $('.product_categories').each(function () {
                    const categoryId = $(this).attr('value'); // FIXED here too
                    const subCheckboxes = $('.subcategories[data-parent="' + categoryId + '"]');
                    const allChecked = subCheckboxes.length > 0 && subCheckboxes.length === subCheckboxes.filter(':checked').length;

                    $(this).prop('checked', allChecked);
                });
            }

            checkCategorySelection();

        })
</script>


<script type="text/javascript">
        function checkCategorySelection() {
            $('.product_categories').each(function () {
                const categoryId = $(this).val();
                const subCheckboxes = $("input.subcategories[data-parent='" + categoryId + "']");
                const checkedSubs = subCheckboxes.filter(":checked");

                // If all subcategories are checked => check parent
                if (subCheckboxes.length > 0 && subCheckboxes.length === checkedSubs.length) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
        }

        // Trigger when subcategory changes
        $(document).on('change', '.subcategories', function () {
            checkCategorySelection();
        });

        // Run on page load
        $(document).ready(function () {
            checkCategorySelection();
        });
</script>

 <script>
    const sliderOne = document.getElementById("slider-1");
    const sliderTwo = document.getElementById("slider-2");
    const displayValOne = document.getElementById("range1");
    const displayValTwo = document.getElementById("range2");
    const minPriceInput = document.getElementById("min-price");
    const maxPriceInput = document.getElementById("max-price");
    const currency = "{{ Helper::Settings('currency_symbol') ?: 'GH₵' }}";

     const applyFilterBtn = document.getElementById("apply-filter");

    const minGap = 1; 
    const sliderTrack = document.querySelector(".slider-track");
    const sliderMaxValue = parseFloat(sliderTwo.max);
    const sliderMinValue = parseFloat(sliderOne.min);

    function ensureNonNegative(value) {
        return value < 0 ? 0 : value; 
    }

    function validatePriceRange(minVal, maxVal) {
        const isInvalid =
            minVal === null || maxVal === null ||
            isNaN(minVal) || isNaN(maxVal) ||
            maxVal <= minVal;

        applyFilterBtn.disabled = isInvalid;
        applyFilterBtn.style.opacity = isInvalid ? "0.6" : "1";
        applyFilterBtn.style.pointerEvents = isInvalid ? "none" : "auto";

        // Optionally disable sliders if invalid
        sliderOne.disabled = isInvalid;
        sliderTwo.disabled = isInvalid;
    }

    function updateSlider() {
        const minVal = parseFloat(sliderOne.value);
        const maxVal = parseFloat(sliderTwo.value);

        displayValOne.textContent = minVal.toFixed(2) + currency;
        displayValTwo.textContent = maxVal.toFixed(2) + currency;

        minPriceInput.value = minVal;
        maxPriceInput.value = maxVal;

        fillColor();
        validatePriceRange(minVal, maxVal);
    }


    function fillColor() {
    let percent1 = (sliderOne.value / sliderMaxValue) * 100;
    let percent2 = (sliderTwo.value / sliderMaxValue) * 100;
    sliderTrack.style.background = `linear-gradient(to right, #d3d3d3 ${percent1}% , #FFA500 ${percent1}% , #FFA500 ${percent2}%, #d3d3d3 ${percent2}%)`;
    }

    function slideOne() {
        if (parseFloat(sliderTwo.value) - parseFloat(sliderOne.value) <= minGap) {
            sliderOne.value = parseFloat(sliderTwo.value) - minGap;
        }
        updateSlider();
    }

    function slideTwo() {
        if (parseFloat(sliderTwo.value) - parseFloat(sliderOne.value) <= minGap) {
            sliderTwo.value = parseFloat(sliderOne.value) + minGap;
        }
        updateSlider();
    }

    // Initialize on page load
    window.addEventListener("DOMContentLoaded", () => {
        updateSlider();

        minPriceInput.addEventListener("input", () => {
            const rawMinVal = minPriceInput.value;
            const rawMaxVal = maxPriceInput.value;

            const minVal = rawMinVal === "" ? null : parseFloat(rawMinVal);
            const maxVal = rawMaxVal === "" ? null : parseFloat(rawMaxVal);

            validatePriceRange(minVal, maxVal);

            if (minVal !== null && !isNaN(minVal)) {
                let safeMin = Math.max(minVal, sliderMinValue);
                if (maxVal !== null && safeMin > maxVal - minGap) return;

                sliderOne.value = safeMin;
                updateSlider();
            }
        });

        maxPriceInput.addEventListener("input", () => {
            const rawMinVal = minPriceInput.value;
            const rawMaxVal = maxPriceInput.value;

            const minVal = rawMinVal === "" ? null : parseFloat(rawMinVal);
            const maxVal = rawMaxVal === "" ? null : parseFloat(rawMaxVal);

            validatePriceRange(minVal, maxVal);

            if (maxVal !== null && !isNaN(maxVal)) {
                let safeMax = Math.min(maxVal, sliderMaxValue);
                if (minVal !== null && safeMax < minVal + minGap) return;

                sliderTwo.value = safeMax;
                updateSlider();
            }
        });

    });
</script>

<script>
    // add to cart
    $(document).on('click', '.add-bucket', function(e) {
            e.preventDefault();

            var $this = $(this);
            $this.hide();

            var product_id = $this.data('product-id');
            var variantId = $this.data('variant-id');
            var bogo_status = $this.data('bogo_status');
            var offer_status = $this.data('offer_status');
            var quantity = 1;
            var action_url = "{{ route('productcartadd') }}";
            var csrf = "{{ csrf_token() }}";
            var currentCount = $(".cart-item-total-count").html();

            var added_product_message = "{{\Helper::language('product_added_to_cart_successfully')}}";
            var success_message = "{{\Helper::language('success')}}";

            $.ajax({
                url: action_url,
                data: {
                    'product_id': product_id,
                    'quantity': quantity,
                    'variantId': variantId,
                    'bogo_status':bogo_status,
                    'offer_status':offer_status
                },
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                type: "POST",
                beforeSend: function() {
                     $(".loader").fadeIn().css("visibility", "visible");
                },
                success: function(response) {
                    $('.loader').css("visibility", "hidden");
                    $('#cart-url').removeAttr("onclick").attr('href', '{{ route('cart') }}');
                    $(".cart-item-total-count").html(response.cart_count);
                    if (response.success == "true") {
                        Swal.fire({
                            icon: "success",
                            title: success_message,
                            text: added_product_message,
                            customClass: {
                                confirmButton: 'swal-custom-confirm'
                            }
                        });
                    }
                },
            });
        });
</script>



@endsection