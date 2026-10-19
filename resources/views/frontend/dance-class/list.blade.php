@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<style type="text/css">
    .center {
        text-align: center;
        margin-top: 200px;
    }
    .no_content{
        font-weight: bold;
        font-size: xx-large;
    }
</style>
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>Classes</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <!--Page Title-->
             <!--Breadcrumb-->
                <div class="breadcrumb_cover">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Classes</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <!--Breadcrumb-->

            <!--Listing Page-->
            <section class="common_padding listing_page">
                <div class="container">
                    <div class="row listing_title_row">
                        <div class="col-12">
                            <div class="section_title">
                                <h2>{{ \Helper::lang_data('classes_subtitle') }}</h2>
                                <p>{{ \Helper::lang_data('classes_subtitle_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="block-filter">
                                <div class="block-title filter-title">
                                    <h4>Filter</h4>
                                </div> 
                                <div class="block-content filter-content">
                                    <div class="listing_left">
                                        <div class="listing_block">
                                                <h4 class="filter_title">Search</h4>
                                                <form role="search" method="get" class="search_form form" action="#" id="search_list_form">
                                                    <input type="search" class="search-field" placeholder="Search...." id="search_dance_class" name="search" autocomplete="off" />
                                                    <input type="submit" class="search_submit" value="" />
                                                </form>
                                        </div>
                                        <div class="listing_block">
                                            <h4 class="filter_title sorting">Sort By</h4>
                                            <select class="form-select" aria-label="Default select example" id="soring_filter" name="soring_filter">
                                                    <option selected>Sort By</option>
                                                    <option value="1">Name - A to Z</option>
                                                    <option value="2">Name - Z to A</option>
                                                    <option value="3">Price - High to Low</option>
                                                    <option value="4">Price - Low to High</option>
                                            </select>
                                        </div>
                                        <div class="listing_block">
                                            <h4 class="filter_title">Category</h4>
                                            @if(isset($dance_category) && !empty($dance_category))
                                            <div class="filter_item_content" id="categoryCheckboxes">
                                                <ul class="item_content_list">
                                                    @foreach($dance_category as $key => $dc)
                                                    <li>
                                                        <input type="hidden" class="removeids_val" name="">
                                                        <input class="form-check-input" type="checkbox" value="" data-id="{{$dc->id}}" id="checkbox_category_filter">
                                                        <label class="text">{{$dc->category_name}}</label>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="listing_block">
                                            <h4 class="filter_title">Price</h4>
                                            <div class="filter_item_content">
                                                <div class="price-slider">
                                                    <input type="text" class="price-range-slider" value="" id="price-range" />
                                                </div>
                                                <div class="price-slider-output">
                                                    <div class="min_box">
                                                        <span>{{isset($setting) ? $setting->currency_symbol : '$'}}</span>
                                                        <input type="text" size="2" class="price-input-from text-right" value="{{isset($min_dance_class_price) ? $min_dance_class_price : '0.0'}}" id="minimum_range"/>
                                                    </div>
                                                    <div class="max_box">
                                                        <span>{{isset($setting) ? $setting->currency_symbol : '$'}}</span>
                                                        <input type="text" size="2" class="price-input-to  text-right" value="{{isset($max_dance_class_price) ? $max_dance_class_price : '0.0'}}" id="maximum_range" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="listing_block">
                                            <h4 class="filter_title">Level</h4>
                                            <div class="filter_item_content" id="levelCheckboxes">
                                                <ul class="item_content_list">
                                                    <input type="hidden" class="removeids_val_level" name="">
                                                    @foreach ($dance_level as $key => $dl)
                                                    <li>
                                                        <input class="form-check-input" type="checkbox" value="{{$dl->id}}" id="Begineer" data-id="{{$dl->id}}">
                                                        <label for="dance_level" class="text">{{$dl->title}}</label>
                                                    </li>
                                                    @endforeach
                                                    <!-- <li>
                                                        <input class="form-check-input" type="checkbox" value="2" id="Intermediate" data-id="2">
                                                        <label for="Intermediate" class="text">Intermediate</label>
                                                    </li>
                                                    <li>
                                                        <input class="form-check-input" type="checkbox" value="3" id="Advance" data-id="3"> 
                                                        <label for="Advance" class="text">Advance</label>
                                                    </li> -->
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="listing_block">
                                            <h4 class="filter_title">Duration</h4>
                                            <div class="filter_item_content" id="durationCheckboxes">
                                                <div class="time-slider">
                                                    <input type="hidden" class="removeids_val_duration" name="">
                                                    <input type="text" class="time-range-slider" value="" />
                                                </div>
                                                <div class="time-slider-output">
                                                    <div class="min_box">
                                                        <input type="text" size="2" class="time-input-from text-right" value="{{isset($min_duration) ? $min_duration : '0'}}" />
                                                    </div>
                                                    <div class="max_box">
                                                        <input type="text" size="2" class="time-input-to  text-right" value="{{isset($max_duration) ? $max_duration : '0'}}" />
                                                    </div>
                                                </div>
                                                <!-- <ul class="item_content_list">
                                                    <input type="hidden" class="removeids_val_duration" name="">
                                                    <li>
                                                        <input class="form-check-input" type="checkbox" value="10 min" id="10min" data-id="10 min">
                                                        <label for="10min" class="text">10 min</label>
                                                    </li>
                                                    <li>
                                                        <input class="form-check-input" type="checkbox" value="20 min" id="20min" data-id="20 min">
                                                        <label for="20min" class="text">20 min</label>
                                                    </li>
                                                    <li>
                                                        <input class="form-check-input" type="checkbox" value="30 min" id="30min" data-id="30 min">
                                                        <label for="30min" class="text">30 min</label>
                                                    </li>
                                                </ul> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="listing_right">
                                <div class="row" id="search_data">
                                    @if(isset($dance_class) && !empty($dance_class))
                                    @if(count($dance_class) > 0)
                                    @foreach($dance_class as $key => $dc)
                                    <div class="col-md-4 col-sm-6">
                                        <?php $dance_category = App\Models\DanceCategory::where('id',$dc->dance_category_id)->first(); ?>
                                        <div class="course_cover_listing">
                                            <?php
                                                    $parameter =[
                                                        'id' =>$dc->class_id,
                                                    ];
                                               //$parameter= Crypt::encrypt($parameter);
                                               $parameter = base64_encode($dc->class_id);
                                            ?>
                                            <div class="list_box">
                                                <div class="list_box_video">
                                                    <a href="{{route('danceclassdetailwithid', ['id' => $parameter]) }}">
                                                        <img class="listing_popular_img" src="{{ asset('uploads/dance_class/images/').'/'.$dc->class_thumbnail_image }}" alt="listing_popular_img">
                                                        <span class="play_icon">
                                                            <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="play_icon">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="course_details">
                                                <ul class="course_type">
                                                    <li class="course_box orange_box"><span>{{$dance_category->category_name}}</span></li>
                                                    <li class="course_box yellow_box"><span>{{$dc->dance_level_title}}</span></li>
                                                    <li class="course_box blue_box"><span>{{$dc->duration}}</span></li>
                                                </ul>
                                                <?php $out = strlen($dc->class_name) > 30 ? substr($dc->class_name,0,30)."..." : $dc->class_name;
                                                ?>
                                                <h3><a href="{{route('danceclassdetailwithid', ['id' => $parameter]) }}">{{$out}}</a></h3>
                                                <ul class="price_nav">
                                                    <?php 
                                                        $class_price = $dc->price;
                                                        $discount = isset($dc->discount) ? $dc->discount : 0;
                                                        $discount_price = ($class_price * $discount) /100;
                                                        $total_price = $class_price - $discount_price;

                                                        $out_1 = strlen($total_price) > 10 ? substr($total_price,0,10)."..." : $total_price;
                                                        $out_2 = strlen($dc->price) > 10 ? substr($dc->price,0,10)."..." : $dc->price;
                                                    ?>
                                                    <li class="main_price">
                                                        <span>{{isset($setting) ? $setting->currency_symbol : '$'}}</span>
                                                        <h4>{{$out_1}}</h4>
                                                    </li>
                                                    <li class="offer_price">
                                                         <h4 class="grey_color">{{isset($setting) ? $setting->currency_symbol : '$'}} {{$out_2}}</h4>
                                                    </li>
                                                </ul>
                                                <?php $user = App\Models\MainUser::where('id',$dc->user_id)->first();
                                                 ?>
                                                <div class="course_author">
                                                    <img src="" alt="author_img" height="20%" width="20%">
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-md-12 col-sm-12 col-lg-12">
                                        <div class="center">
                                            <p class="no_content">No Data Found</p>
                                        </div>    
                                    </div>
                                    @endif
                                    @endif
                                </div>

                                <div class="listing_pagination">
                                    <!-- <ul class="pagination">
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:(void)" aria-label="Previous">
                                                <img class="pagination_left_arrow" src="{{ asset('assets/frontend/images/left_arrow_small.svg') }}" alt="left_arrow_small" />
                                            </a>
                                        </li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">1</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">2</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">3</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">4</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">5</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">6</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">...</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">10</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:(void)" aria-label="Next">
                                                <img class="pagination_right_arrow" src="{{ asset('assets/frontend/images/right_arrow_small.svg') }}" alt="right_arrow_small" />
                                            </a>
                                        </li>
                                    </ul> -->
                                    {{ $dance_class->links('vendor.pagination.custom_pagination') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--Listing Page-->
        </div>
        <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
        <script type="text/javascript">

            function sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                {
                    $.ajax({
                        url:"{{route('danceClasssortingnew')}}",
                        method: 'POST',
                        dataType: 'json',
                        data:{query: query, option: option, category_id: category_id,level: level, min_dance_class_price: min_dance_class_price, max_dance_class_price: max_dance_class_price,min_duration:min_duration, max_duration:max_duration},
                        success:function(data)
                        {
                           // $('#search_data').innerHTML(data);
                           if(data.length > 0)
                           {
                               document.getElementById("search_data").innerHTML = data.data;
                           }
                           else
                           {
                                var html = '<div class="center">';
                                html += '<p class="no_content">No Data Found</p>';
                                html += '</div>';
                               document.getElementById("search_data").innerHTML = html; 
                           }
                           //document.getElementById("search_data").innerHTML = data;
                        }
                    });
                }
            $(function () {
                var $range = $(".price-range-slider"),
                    $inputFrom = $(".price-input-from"),
                    $inputTo = $(".price-input-to"),
                    instance,
                    min = "{{$min_dance_class_price}}",
                    max = "{{$max_dance_class_price}}",
                    from = 0,
                    to = 0;
                    
                $range.ionRangeSlider({
                    skin: "round",
                    type: "double",
                    min: min,
                    max: max,
                    // from: 100,
                    // to: 5000,
                    step: 100,
                    // onStart: updateInputs,
                    onChange: updateInputs,
                });

                instance = $range.data("ionRangeSlider");
                
                function updateInputs (data) {
                    from = data.from;
                    to = data.to;
                    //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','','','',from,to,'','');
                   // load_price_filter_data(from,to);
                    $inputFrom.prop("value", from);
                    $inputTo.prop("value", to); 
                }
                
                $inputFrom.on("input", function () {
                    var val = $(this).prop("value");
                    
                    // validate
                    if (val < min) {
                        val = min;
                    } else if (val > to) {
                        val = to;
                    }
                    
                    instance.update({
                        from: val
                    });
                });
                
                $inputTo.on("input", function () {
                    var val = $(this).prop("value");
                    
                    // validate
                    if (val < from) {
                        val = from;
                    } else if (val > max) {
                        val = max;
                    }
                    
                    instance.update({
                        to: val
                    });
                });

                $(function () {
                  var $range = $(".time-range-slider"),
                      $inputFrom = $(".time-input-from"),
                      $inputTo = $(".time-input-to"),
                      instance,
                      min = "{{$min_duration}}",
                      max = "{{$max_duration}}",
                      from = 0,
                      to = 0;
                      
                  $range.ionRangeSlider({
                      skin: "round",
                      type: "double",
                      min: min,
                      max: max,
                      // from: 0,
                      // to: 50,
                      step: 0.1,
                    //  onStart: updateInputs,
                      onChange: updateInputs
                  });
                  instance = $range.data("ionRangeSlider");
                  
                  function updateInputs (data) {
                      from = data.from;
                      to = data.to;
                      //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                      sorting_all('','','','','','',from,to);
                      //load_duration_filter_data(from,to);
                      $inputFrom.prop("value", from);
                      $inputTo.prop("value", to);   
                  }
                  
                  $inputFrom.on("input", function () {
                      var val = $(this).prop("value");
                      
                      // validate
                      if (val < min) {
                          val = min;
                      } else if (val > to) {
                          val = to;
                      }
                      
                      instance.update({
                          from: val
                      });
                  });
                  
                  $inputTo.on("input", function () {
                      var val = $(this).prop("value");
                      
                      // validate
                      if (val < from) {
                          val = from;
                      } else if (val > max) {
                          val = max;
                      }
                      
                      instance.update({
                          to: val
                      });
                  });
                  
                });

                // function sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                // {
                //     $.ajax({
                //         url:"{{route('danceClasssortingnew')}}",
                //         method: 'POST',
                //         dataType: 'json',
                //         data:{query: query, option: option, category_id: category_id,level: level, min_dance_class_price: min_dance_class_price, max_dance_class_price: max_dance_class_price,min_duration:min_duration, max_duration:max_duration},
                //         success:function(data)
                //         {
                //            // $('#search_data').innerHTML(data);
                //            if(data.length > 0)
                //            {
                //                document.getElementById("search_data").innerHTML = data;
                //            }
                //            else
                //            {
                //                 var html = '<div class="center">';
                //                 html += '<p class="no_content">No Data Found</p>';
                //                 html += '</div>';
                //                document.getElementById("search_data").innerHTML = html; 
                //            }
                //            //document.getElementById("search_data").innerHTML = data;
                //         }
                //     });
                // }

                function load_duration_filter_data(min_duration, max_duration)
                {
                    $.ajax({
                        url:"{{route('danceclasssortingduration')}}",
                        method: 'POST',
                        dataType: 'json',
                        data:{min_duration:min_duration, max_duration:max_duration},
                        success:function(data)
                        {
                           // $('#search_data').innerHTML(data);
                           if(data.length > 0)
                           {
                               document.getElementById("search_data").innerHTML = data;
                           }
                           else
                           {
                                var html = '<div class="center">';
                                html += '<p class="no_content">No Data Found</p>';
                                html += '</div>';
                               document.getElementById("search_data").innerHTML = html; 
                           }
                           //document.getElementById("search_data").innerHTML = data;
                        }
                    });
                }
                

                function load_price_filter_data(min_dance_class_price, max_dance_class_price)
                {
                    $.ajax({
                        url:"{{route('danceclasssortingprice')}}",
                        method: 'POST',
                        dataType: 'json',
                        data:{min_dance_class_price:min_dance_class_price, max_dance_class_price:max_dance_class_price},
                        success:function(data)
                        {
                           // $('#search_data').innerHTML(data);
                           if(data.length > 0)
                           {
                               document.getElementById("search_data").innerHTML = data;
                           }
                           else
                           {
                                var html = '<div class="center">';
                                html += '<p class="no_content">No Data Found</p>';
                                html += '</div>';
                               document.getElementById("search_data").innerHTML = html; 
                           }
                           //document.getElementById("search_data").innerHTML = data;
                        }
                    });
                }
            });
                // var path = "{{ route('autocomplete.dance-class') }}";
                // $('input.typeahead').typeahead({
                //     source: function(query, process) {
                //         return $.get(path, {
                //             query: query
                //         }, function(data) {
                //             console.log(data);
                //             return process(data);
                //         });
                //     },
                // });  

                
        $(document).ready(function () {

            $("#search_list_form").submit(function(e) {
                    e.preventDefault();
            });

            $('#search_dance_class').keypress(function (e) {
                 var key = e.which;
                 if(key == 13)  // the enter key code
                  {
                        event.preventDefault();
                        var query = $(this).val();
                        //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                        sorting_all(query,'','','','','','','');
                       // fetch_data(query);
                  }
            });   

            function fetch_data(query = '') {
                $.ajax({
                    url: "{{route('danceclasssearch')}}",
                    method: 'POST',
                    data: {query: query},
                    dataType: 'json',
                    success: function (data) {
                       // $('tbody').html(data.table_data);
                       if(data.length > 0)
                       {
                           document.getElementById("search_data").innerHTML = data;
                       }
                       else
                       {
                            var html = '<div class="center">';
                            html += '<p class="no_content">No Data Found</p>';
                            html += '</div>';
                           document.getElementById("search_data").innerHTML = html; 
                       }
                      
                    }
                })
            }

            $(document).on('keyup', '#search_dance_class', function () {
                var query = $(this).val();
                //var query = $('#search_dance_class').val();
                var option = $('#soring_filter').val();
                var category_id = $('.removeids_val').val();
                var level = $('.removeids_val_level').val();
                var min_dance_class_price = $(".price-input-from").val();
                var max_dance_class_price = $(".price-input-to").val();
                var min_duration = $('.time-input-from').val();
                var max_duration = $(".time-input-to").val();
                // fetch_data(query);
                $.ajax({
                    url: "{{route('danceClasssortingnew')}}",
                    method: 'POST',
                    data: {
                        query: query, option: option, category_id: category_id,level: level, min_dance_class_price: min_dance_class_price, max_dance_class_price: max_dance_class_price,min_duration:min_duration, max_duration:max_duration
                    },
                    dataType: 'json',
                    success: function(data) {
                       
                       if(data.data.length > 0)
                       {
                           document.getElementById("search_data").innerHTML = data.data;
                       }
                       else
                       {
                            var html = '<div class="center">';
                            html += '<p class="no_content">No Data Found</p>';
                            html += '</div>';
                           document.getElementById("search_data").innerHTML = html; 
                       }
                    }
                })
                
            });

            function fetch_sorting_data(option = '') {
                $.ajax({
                    url: "{{route('danceclasssorting')}}",
                    method: 'POST',
                    data: {option: option},
                    dataType: 'json',
                    success: function (data) {
                       // $('tbody').html(data.table_data);
                       if(data.length > 0)
                       {
                           document.getElementById("search_data").innerHTML = data;
                       }
                       else
                       {
                            var html = '<div class="center">';
                            html += '<p class="no_content">No Data Found</p>';
                            html += '</div>';
                           document.getElementById("search_data").innerHTML = html; 
                       }
                     // document.getElementById("search_data").innerHTML = data;
                    }
                })
            }

            $("select").on('change', function()
            {
                var option = this.value;
                //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                sorting_all('',option,'','','','','','');
                // /fetch_sorting_data(option);
            });

            function fetch_category_sorting_data(category_id = '') {
                $.ajax({
                    url: "{{route('danceclasssortingcategory')}}",
                    method: 'POST',
                    data: {category_id: category_id},
                    dataType: 'json',
                    success: function (data) {
                       // $('tbody').html(data.table_data);
                       if(data.length > 0)
                       {
                           document.getElementById("search_data").innerHTML = data;
                       }
                       else
                       {
                            var html = '<div class="center">';
                            html += '<p class="no_content">No Data Found</p>';
                            html += '</div>';
                           document.getElementById("search_data").innerHTML = html; 
                       }
                      //document.getElementById("search_data").innerHTML = data;
                    }
                })
            }

            $('#categoryCheckboxes').on("change", ":checkbox", function () {
                if (this.checked) {
                    var option = $(this).attr('data-id');
                    //var allVals = [];
                   // var commType = $(this).val();
                    var ids = $('.removeids_val').val();
                   // var multiadd = option.split(',');
                  // multiadd.push(option);

                   if (ids) {
                    var multiadd = ids.split(',');
                    multiadd.push(option);



                    $('.removeids_val').val(multiadd);
                    console.log(multiadd);
                    //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','',multiadd,'','','','','');
                    //fetch_category_sorting_data(multiadd);
                   // $(this).val('1');
                    }else{
                    var multiadd = [];
                    multiadd.push(option);
                    $('.removeids_val').val(multiadd);
                    //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','',multiadd,'','','','','');
                    //fetch_category_sorting_data(multiadd);
                    }

                    //allVals.push(option);

                   // var join_selected_values = allVals.join(",");

                    //console.log(multiadd);
                } else {
                   var option = $(this).attr('data-id');
                    //var allVals = [];
                   // var commType = $(this).val();
                    var ids = $('.removeids_val').val();
                   // var multiadd = option.split(',');
                  // multiadd.push(option);

                   if (ids) {
                    var multiadd = ids.split(',');
                    for( var i = 0; i < multiadd.length; i++){ 
    
                        if ( multiadd[i] === option) { 
                    
                            multiadd.splice(i, 1); 
                        }
                    
                    }
                   // multiadd.pop(option);



                    $('.removeids_val').val(multiadd);
                    console.log(multiadd);
                    //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','',multiadd,'','','','','');
                    //fetch_category_sorting_data(multiadd);
                    //$(this).val('0');
                    }else{
                    var multiadd = [];
                    for( var i = 0; i < multiadd.length; i++){ 
    
                        if ( multiadd[i] === option) { 
                    
                            multiadd.splice(i, 1); 
                        }
                    
                    }
                    //multiadd.pop(option);
                    $('.removeids_val').val(multiadd);
                    //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','',multiadd,'','','','','');
                    //fetch_category_sorting_data(multiadd);
                    }
                }
            });

            function fetch_level_sorting_data(level = '') {
                $.ajax({
                    url: "{{route('danceclasssortinglevel')}}",
                    method: 'POST',
                    data: {level: level},
                    dataType: 'json',
                    success: function (data) {
                       // $('tbody').html(data.table_data);
                       if(data.length > 0)
                       {
                           document.getElementById("search_data").innerHTML = data;
                       }
                       else
                       {
                            var html = '<div class="center">';
                            html += '<p class="no_content">No Data Found</p>';
                            html += '</div>';
                           document.getElementById("search_data").innerHTML = html; 
                       }
                     // document.getElementById("search_data").innerHTML = data;
                    }
                })
            }

            $('#levelCheckboxes').on("change", ":checkbox", function () {
                if (this.checked) {
                    var option = $(this).attr('data-id');

                    //var allVals = [];
                   // var commType = $(this).val();
                    var ids = $('.removeids_val_level').val();
                   // var multiadd = option.split(',');
                  // multiadd.push(option);

                   if (ids) {
                    var multiadd = ids.split(',');
                    multiadd.push(option);



                    $('.removeids_val_level').val(multiadd);
                    console.log(multiadd);
                    //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','','',multiadd,'','','','');
                    //fetch_level_sorting_data(multiadd);
                    //$(this).val('1');
                    }else{
                    var multiadd = [];
                    multiadd.push(option);
                    $('.removeids_val_level').val(multiadd);
                     //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','','',multiadd,'','','','');
                    // /fetch_level_sorting_data(multiadd);
                    }

                    //allVals.push(option);

                   // var join_selected_values = allVals.join(",");

                    //console.log(multiadd);
                } else {
                   var option = $(this).attr('data-id');
                   
                    //var allVals = [];
                   // var commType = $(this).val();
                    var ids = $('.removeids_val_level').val();
                   // var multiadd = option.split(',');
                  // multiadd.push(option);

                   if (ids) {
                    var multiadd = ids.split(',');
                    for( var i = 0; i < multiadd.length; i++){ 
    
                        if ( multiadd[i] === option) { 
                    
                            multiadd.splice(i, 1); 
                        }
                    
                    }
                   // multiadd.pop(option);



                    $('.removeids_val_level').val(multiadd);
                    console.log(multiadd);
                     //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','','',multiadd,'','','','');
                   // fetch_level_sorting_data(multiadd);
                   // $(this).val('0');
                    }else{
                    var multiadd = [];
                    for( var i = 0; i < multiadd.length; i++){ 
    
                        if ( multiadd[i] === option) { 
                    
                            multiadd.splice(i, 1); 
                        }
                    
                    }
                   // multiadd.pop(option);
                    $('.removeids_val_level').val(multiadd);
                     //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
                    sorting_all('','','',multiadd,'','','','');
                   // fetch_level_sorting_data(multiadd);
                    }
                }
            });

            // function fetch_duration_sorting_data(duration = '') {
            //     $.ajax({
            //         url: "{{route('danceclasssortingduration')}}",
            //         method: 'POST',
            //         data: {duration: duration},
            //         dataType: 'json',
            //         success: function (data) {
            //            // $('tbody').html(data.table_data);
            //            if(data.length > 0)
            //            {
            //                document.getElementById("search_data").innerHTML = data;
            //            }
            //            else
            //            {
            //                 var html = '<div class="center">';
            //                 html += '<p class="no_content">No Data Found</p>';
            //                 html += '</div>';
            //                document.getElementById("search_data").innerHTML = html; 
            //            }
            //          // document.getElementById("search_data").innerHTML = data;
            //         }
            //     })
            // }

            // $('#durationCheckboxes').on("change", ":checkbox", function () {
            //     if (this.checked) {
            //         var option = $(this).attr('data-id');

            //         //var allVals = [];
            //        // var commType = $(this).val();
            //         var ids = $('.removeids_val_duration').val();
            //        // var multiadd = option.split(',');
            //       // multiadd.push(option);

            //        if (ids) {
            //         var multiadd = ids.split(',');
            //         multiadd.push(option);



            //         $('.removeids_val_duration').val(multiadd);
            //         console.log(multiadd);
            //         fetch_duration_sorting_data(multiadd);
            //         //$(this).val('1');
            //         }else{
            //         var multiadd = [];
            //         multiadd.push(option);
            //         $('.removeids_val_duration').val(multiadd);
            //         fetch_duration_sorting_data(multiadd);
            //         }

            //         //allVals.push(option);

            //        // var join_selected_values = allVals.join(",");

            //         //console.log(multiadd);
            //     } else {
            //        var option = $(this).attr('data-id');
            //         //var allVals = [];
            //        // var commType = $(this).val();
            //         var ids = $('.removeids_val_duration').val();
            //        // var multiadd = option.split(',');
            //       // multiadd.push(option);

            //        if (ids) {
            //         var multiadd = ids.split(',');
            //         for( var i = 0; i < multiadd.length; i++){ 
    
            //             if ( multiadd[i] === option) { 
                    
            //                 multiadd.splice(i, 1); 
            //             }
                    
            //         }
            //         //multiadd.pop(option);



            //         $('.removeids_val_duration').val(multiadd);
            //         console.log(multiadd);
            //         fetch_duration_sorting_data(multiadd);
            //         //$(this).val('0');
            //         }else{
            //         var multiadd = [];
            //         for( var i = 0; i < multiadd.length; i++){ 
    
            //             if ( multiadd[i] === option) { 
                    
            //                 multiadd.splice(i, 1); 
            //             }
                    
            //         }
            //         //multiadd.pop(option);
            //         $('.removeids_val_duration').val(multiadd);
            //         fetch_duration_sorting_data(multiadd);
            //         }
            //     }
            // });

           var input = document.getElementById("search_dance_class");
           input.addEventListener("keydown", function (e) {
            if (e.key === "Enter") { 
            var query = $(this).val();
             //sorting_all(query,option,category_id,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration)
            sorting_all(query,'','','','','','','');
              //fetch_data(query);
            }
          });
        });

     
    </script>    
@endsection
