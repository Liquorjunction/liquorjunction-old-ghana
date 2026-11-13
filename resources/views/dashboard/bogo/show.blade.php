@extends('dashboard.layouts.master')
@section('title', 'View Bogo Offer')
@section('content')

<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<style type="text/css">
    .select2-container {
        width: 100% !important;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #ffffff;
        opacity: 1;
    }

    .table tbody>tr>td,
    .table thead>tr>th {

        border-left: 1px solid #dfdfdf !important;

    }

    .table_design {
        padding: 0 !important;
    }

    .table_design th {
        width: 200px !important;
    }
</style>

<div class="padding list-school">
    <div class="box">
        <div class="box-header dker">
            <h3>View Bogo offer</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('promocode') }}">Bogo offer</a> /
                View Bogo offer
            </small>
        </div>
        <input type="hidden" id="product_type" value="{{$Bogo->product_type}}">
        <div class="box nav-active-border b-info">
            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                    <div class="box-body">
                        <form class="table_design">
                            <table class="table table-bordered m-a-0">
                               
                                <tr>
                                    <th>Applicable On</th>
                                    <td>
                                        @php
                                            $types = [1 => 'Brand', 2 => 'Category', 3 => 'Product'];
                                        @endphp

                                        {{ $types[$Bogo->product_type] ?? 'N/A' }}
                                    </td>
                                </tr>

                               <tr class="brand">
                                    <th>Brand</th>
                                    <td>
                                        {{ ucfirst(optional($brand->firstWhere('id', $Bogo->brand_id))->title) ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr class="category">
                                    <th>Category</th>
                                    <td>
                                        {{ ucfirst(optional($categories->firstWhere('id', $Bogo->category_id))->title) ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr class="subcategory">
                                    <th>Sub Category</th>
                                    <td>
                                        {{ ucfirst(optional($subcategories->firstWhere('id', $Bogo->subcategory_id))->title) ?? 'N/A' }}
                                    </td>
                                </tr>

                               <tr class="product">
                                    <th>Product</th>
                                    <td>
                                        {{ ucfirst(optional($product->firstWhere('id', $Bogo->product_id))->product_name) ?? 'N/A' }}
                                    </td>
                                </tr>
                                                                
                                <tr>
                                    <tbody>
                                        <th>Start Date</th>
                                        <td>{{ ($Bogo->start_date ? Carbon\Carbon::parse($Bogo->start_date)->format(env('DATE_FORMAT', 'Y-m-d')) : "-") }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>End Date</th>
                                        <td>
                                            {{ ($Bogo->end_date ? Carbon\Carbon::parse($Bogo->end_date)->format(env('DATE_FORMAT', 'Y-m-d')) : "-") }}
                                        </td>
                                    </tbody>
                                </tr>
                               
                                <tr>
                                    <tbody>
                                        <th>Created Date</th>
                                        <?php $date = \Helper::formatDatetime($Bogo->created_at) . ' ' . \Helper::formatTimeLocal($Bogo->created_at) ?>
                                        <td>{{ ($Bogo->created_at ? Carbon\Carbon::parse($Bogo->created_at)->format(env('DATE_FORMAT', 'Y-m-d')) : "-") }}</td>
                                    <tbody>
                                </tr>
                            </table>
                        </form>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <a href="{{ url()->previous() }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                                    <i class="material-icons">
                                        &#xe5cd;</i> Cancel
                                </a>
                            </div>
                            <div class="col-sm-10"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @push("after-scripts")

    <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>


<script>
    $(document).ready(function() {
        showHide();
    });

    function showHide() {
        var type = $("#product_type").val();
        $('.category').hide();
        $('.product').hide();
        $('.brand').hide();
        $('.subcategory').hide();
        if (type != '' && type != null) {
            if (type == '1') {
                $('.category').hide();
                $('.product').hide();
                $('.brand').show();
                $('.subcategory').hide();
            } else if(type == '2') {
                $('.category').show();
                $('.product').hide();
                $('.brand').hide();
                $('.subcategory').show();
            }else{
                $('.category').hide();
                $('.product').show();
                $('.brand').hide();
                $('.subcategory').hide();
            }
        }
    }
</script>



    @endpush