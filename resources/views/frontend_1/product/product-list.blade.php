@extends('frontend.layouts.app')
@section('title', 'Product List')
@section('content')
    @include('sweetalert::alert')
    <div class="loader" id="loader"></div>
    <?php
    if (session::get('language') == 1) {
        $category_title = $categoryDetails->title;
    } else {
        $category_title = $categoryDetails->title_fr ? $categoryDetails->title_fr : $categoryDetails->title;
    }
    ?>
    <!-- Title Banner -->
    @if ($categoryDetails->photo)

        <section class="title-banner"
            style="background-image: url({{ asset($categoryDetails->photo ? 'uploads/categoryback/' . $categoryDetails->photo : 'assets/frontend/images/default-background.jpg') }}); background-repeat: no-repeat; background-size: cover;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @if (!empty($subCategoryDetails))
                            @php
                                if (session::get('language') == 1) {
                                    $sub_title = $subCategoryDetails->title;
                                } else {
                                    $sub_title = $subCategoryDetails->title_fr
                                        ? $subCategoryDetails->title_fr
                                        : $subCategoryDetails->title;
                                }
                            @endphp
                            <h1 class="mb-0">{{ $sub_title }}</h1>
                        @else
                            <a href="{{ $categoryDetails->url }}" style="text-decoration: none; color: inherit;">
                                <h1 class="mb-0">{{ $category_title }}</h1>
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </section>


    @endif
    <!-- End Title Banner -->

    <div class="bread-crumb-block">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{ Helper::language('home') }}</a>
                    </li>
                    {{-- <li class="breadcrumb-item active" aria-current="page">{{Helper::language('product_list')}}</li> --}}
                    <li class="breadcrumb-item active" aria-current="page">
                        <a
                            @if (!empty($subCategoryDetails)) href="{{ route('productlist', ['id' => Helper::encodeUrl($categoryDetails->id)]) }}" @endif>
                            {{ $category_title }}
                        </a>
                    </li>
                    @if (!empty($subCategoryDetails))
                        <li class="breadcrumb-item active" aria-current="page">{{ @$sub_title }}</li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
    <!-- Product Listing -->
    <section class="product-listing">
        <div class="container">
            <div class="row" id = "product_list">
                <div class="col-12">
                    <div class="short-by">
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
                        @include('frontend.product.ajax_product')
                    </div>
                    <div class="row">
                        <div class="offset-md-5 col-md-7">
                            <div class="show-more">
                                <button
                                    @if ($total_product_count != 0 && $total_product_count > 16) style="display:block;" @else style="display:none;" @endif
                                    class="solid-button show-more load-more-data">{{ Helper::language('show_more') }}</button>
                                @if ($total_product_count != 0)
                                    <p id="showing">{{ Helper::language('showing') }}
                                        {{-- <span id="first_page_count"></span> to --}}
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
        aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ Helper::language('filter') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="filter-listing-main">
                @if ($categories && count($categories) > 0)
                    <div class="filter-listing">
                        <span class="filter-title">{{ Helper::language('category') }}</span>
                        <ul class="filter-list">
                            @foreach ($categories as $key => $categoty_result)
                                @php
                                    if (session::get('language') == 1) {
                                        $category_title = $categoty_result->title;
                                    } else {
                                        $category_title = $categoty_result->title_fr;
                                    }
                                    $page_category_id = base64_decode(\Request::segment(2));
                                    $page_sid = base64_decode(\Request::get('sid'));
                                @endphp
                                <li>
                                    <div class="check-group">
                                        <input class="form-check-input product_categories" name="product_categories[]"
                                            type="checkbox" value="{{ $categoty_result->id }}"
                                            @if ($page_sid == '' && $page_category_id == $categoty_result->id) {{ 'checked' }} @endif
                                            id="category_id_{{ $key }}">
                                        <label class="form-check-label"
                                            for="category_id_{{ $key }}">{{ $category_title }}</label>
                                    </div>
                                    @if ($categoty_result->subcategory && $categoty_result->subcategory != null)
                                        <ul class="sub-filter-list"
                                            @if (count($categoty_result->subcategory) == 1) style="overflow-y: inherit" @endif>
                                            @foreach ($categoty_result->subcategory as $key1 => $sub_result)
                                                @php
                                                    if (session::get('language') == 1) {
                                                        $subcategory_title = $sub_result->title;
                                                    } else {
                                                        $subcategory_title = $sub_result->title_fr;
                                                    }

                                                @endphp
                                                <li>
                                                    <div class="check-group">
                                                        <input class="form-check-input subcategories" type="checkbox"
                                                            value="{{ $sub_result->id }}"
                                                            id="sub_categories_{{ $key1 }}"
                                                            @if ($page_sid == $sub_result->id) {{ 'checked' }} @endif
                                                            name="subcategories[]">
                                                        <label class="form-check-label"
                                                            for="sub_categories_{{ $key1 }}">{{ $subcategory_title }}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="wrapper">
                    <div class="price-title">
                        <p class="text-sm mb-0 black-text">{{ Helper::language('price') }}</p>
                        {{-- <a href="#" class="link-button">{{ Helper::language('clear') }}</a> --}}
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
                        <input type="range" min="{{ $product_min_price->min_price ?: '0' }}"
                            max="{{ $product_max_price->max_price ?: '0' }}"
                            value="{{ $product_min_price->min_price ?: '0' }}" id="slider-1" oninput="slideOne()">
                        <input type="hidden" name="min_price" id="min-price" value="">

                        <input type="range" min="{{ $product_min_price->min_price ?: '0' }}"
                            max="{{ $product_max_price->max_price ?: '0' }}"
                            value="{{ $product_max_price->max_price ?: '0' }}" id="slider-2" oninput="slideTwo()">
                        <input type="hidden" name="max_price" id="max-price" value="">
                    </div>
                </div>
                @if ($brandData && count($brandData) > 0)
                     <div class="filter-listing">
                        <span class="filter-title">{{ Helper::language('brand') }}</span>
                        <div class="filter-list">
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
                                            <input class="form-check-input brands" name="brands[]" type="checkbox"
                                                value="{{ $brand_result->id }}">
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

            <div class="filter-button">
                <div class="filter-button-group">
                    <a href="javascript:void(0)" onclick="location.reload();"
                        class="solid-button clear-button">{{ Helper::language('clear') }}</a>
                    <a href="javascript:void(0)" id="apply-filter" class="solid-button " data-bs-dismiss="offcanvas"
                        aria-label="Close">{{ Helper::language('apply_filter') }}</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
    <script type="text/javascript">
        /*---This function is using for fetching brands filtter ids---*/
        function brandsFilterIds() {
            var brand_ids = [];
            $.each($("input[name='brands[]']:checked"), function() {
                brand_ids.push($(this).val());
            });
            return brand_ids;
        }
        /*---This function is using for fetching  categories filtter ids---*/
        function categoryFilterIds() {
            var category_ids = [];
            $.each($("input[name='product_categories[]']:checked"), function() {
                category_ids.push($(this).val());
            });
            return category_ids;
        }
        /*---This function is using for fetching  sub-categories filtter ids---*/
        function subCategoryFilterIds() {
            var subcategory_ids = [];
            $.each($("input[name='subcategories[]']:checked"), function() {
                subcategory_ids.push($(this).val());
            });
            return subcategory_ids;
        }
        /*--filter brands--*/
        function filterBrands() {
            const searchInput = document.getElementById('brand-search').value.toLowerCase();

            const brandList = document.getElementById('brand-list');
            const brands = brandList.getElementsByTagName('li');

            // Loop through each brand list item
            for (let i = 0; i < brands.length; i++) {
                // Get the text content of the label inside the list item and convert it to lowercase
                const label = brands[i].querySelector('.form-check-label').textContent.toLowerCase();

                // Check if the label includes the search input
                if (label.includes(searchInput)) {
                    brands[i].style.display = ''; // Show matching brands
                } else {
                    brands[i].style.display = 'none'; // Hide non-matching brands
                }
            }
        }

        $(document).ready(function() {
            // $('#sort-by').on("change", function(e) {
            // //     var sort_by = e.target.value;
            // //     var productIds = [];
            // //     $('.load_products_ids').each(function() {
            // //         productIds.push($(this).data('id'));
            // //     });
            // //     var last_id = $("#last-id").val();
            // //     $.ajax({
            // //         url: "{{ route('productSortByList') }}?sort_by=" + sort_by + "&productIds=" +
            // //             productIds+'&last_id='+last_id,
            // //         beforeSend: function() {
            // //             $(".loader").fadeIn();
            // //             $('.loader').css("visibility", "visible");
            // //         },
            // //         success: function(data) {
            // //             $('.loader').css("visibility", "hidden");
            // //             $('#table_data').html(data);

            // //             //productCountData()
            // //         }
            // //     }).done(function(){
            // //         //load product count
            // //         var current_count = $(".load_products_ids").length;
            // //         $("#p-current-count").text(current_count);
            // //     });

            //     var brand_ids = brandsFilterIds();
            //     var category_ids = categoryFilterIds();
            //     var subcategory_ids = subCategoryFilterIds();
            //     var min_price = $("#min-price").val();
            //     var max_price = $("#max-price").val();
            //     var sort_by = $("#sort-by").val();
            //     $.ajax({
            //         url: "{{ route('productFilterData') }}?sort_by=" + sort_by + "&brand_ids=" + brand_ids +
            //             "&category_ids=" + category_ids + "&subcategory_ids=" + subcategory_ids + "&min_price=" +
            //             min_price + "&max_price=" + max_price,
            //         beforeSend: function() {
            //             $(".loader").fadeIn();
            //             $('.loader').css("visibility", "visible");
            //         },
            //         success: function(data) {
            //             $('.loader').css("visibility", "hidden");
            //             $('#table_data').html(data);
            //             productCountData();
            //             var product_listig_count = $('.product-listing-col').length;
            //             var pro_total_count = $("#proFcount").val();
            //             if (pro_total_count == 0) {
            //                 $('.show-more').hide();
            //             }else{
            //                 $('.show-more').show();
            //             }
            //             if (pro_total_count >= 16) {
            //                 $('.load-more-data').show();
            //             }else{
            //                 $('.load-more-data').hide();
            //             }                    
            //             $("#sort-by").prop("selectedIndex", 0);
            //         }
            //     });
            // });

            // $(".product_categories, .brands, .subcategories").click(function() {
            //     getProductData();
            // });

            $("#apply-filter, #sort-by").click(function() {
                getProductData();
                // $("#filteroffcanvas").removeClass('show').css("visibility","hidden").removeAttr('role','');

            });

            //var page = 1;  
            //const productList = document.getElementById('product_list');
            $(".load-more-data").click(function() {
                $(".product_counts").remove();
                var product_last_id = $("#last-id").val();
                var sort_by = $("#sort-by").val();
                var brand_ids = brandsFilterIds();
                // alert(brand_ids);
                var category_ids = categoryFilterIds();
                // alert(category_ids);
                var subcategory_ids = subCategoryFilterIds();
                // alert(subcategory_ids);
                var min_price = $("#min-price").val();
                var max_price = $("#max-price").val();
                var current_page_count = $("#p-current-count").text();
                $.ajax({
                    url: "{{ route('productFilterData') }}?sort_by=" + sort_by + "&brand_ids=" +
                        brand_ids + "&category_ids=" + category_ids + "&subcategory_ids=" +
                        subcategory_ids + "&min_price=" + min_price + "&max_price=" + max_price +
                        '&product_last_id=' + product_last_id + "&current_page_count=" +
                        current_page_count,
                    beforeSend: function() {
                        $(".loader").fadeIn();
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(data) {
                        $('.loader').css("visibility", "hidden");
                        $('#appen-html' + product_last_id).after(data);
                        $("#last-id").remove();
                        // var pro_total_count = "{{ $total_product_count ?: '' }}";
                        // var product_listig_count = $('.product-listing-col').length;
                        // if (pro_total_count == product_listig_count) {
                        //     $('.load-more-data').hide();
                        //     // console.log(productCountData());
                        // }

                    }
                }).done(function() {
                    //load product count
                    var current_count = $(".load_products_ids").length;
                    $("#p-current-count").text(current_count);
                    var pro_total_count = $("#p-total-count").text();
                    if (pro_total_count == current_count) {
                        $('.load-more-data').hide();
                        // console.log(productCountData());
                    }
                });
            });
        });

        /*---This function is using for fetching product records based on differnt filtter value---*/
        function getProductData() {
            var brand_ids = brandsFilterIds();
            var category_ids = categoryFilterIds();
            var subcategory_ids = subCategoryFilterIds();
            var min_price = $("#min-price").val();
            var max_price = $("#max-price").val();
            var sort_by = $("#sort-by").val();
            $.ajax({
                url: "{{ route('productFilterData') }}?sort_by=" + sort_by + "&brand_ids=" + brand_ids +
                    "&category_ids=" + category_ids + "&subcategory_ids=" + subcategory_ids + "&min_price=" +
                    min_price + "&max_price=" + max_price,
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(data) {
                    $('.loader').css("visibility", "hidden");
                    $('#table_data').html(data);
                    productCountData();
                    var product_listig_count = $('.product-listing-col').length;
                    var pro_total_count = $("#proFcount").val();
                    if (pro_total_count == 0) {
                        $('.show-more').hide();
                    } else {
                        $('.show-more').show();
                    }
                    if (pro_total_count >= 16) {
                        $('.load-more-data').show();
                    } else {
                        $('.load-more-data').hide();
                    }
                    //  $("#sort-by").prop("selectedIndex", 0);
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
                    // return false;
                    if (user_id != '') {
                        location.reload();
                    } else {
                        location.href = "{{ route('websitelogin') }}";
                    }
                },
            });
        }

        var currency_type = "{{ Helper::Settings('currency_symbol') }}";
    </script>

@endsection
