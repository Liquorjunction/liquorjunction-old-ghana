@extends('dashboard.layouts.master') @section('title', 'Product Rating | Admin Panel') @section('content') @include('sweetalert::alert') <style type="text/css">
    .pointer_button {
        pointer-events: none !important;
        height: 20px !important;
        width: 82px !important;
    }
    .clear_button {
        margin-top: 30px;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<div class="loader" id="loader"></div>
<div class="padding website-label">
    <div class="success_message" style="margin-bottom: 10px;"></div>
    <div id="success_file_popup" style="margin-bottom: 10px;"></div>
    <div class="box">
        <div class="box-header dker">
            <h3>{{ __('backend.productRating') }}</h3>
            <small>
                <a href="{{ route('product') }}">{{ __('backend.dashboard') }} /<span>{{ __('backend.product_management') }}</span>/ <span></a> @if(isset($productInfo->product_name))  {{ ucfirst($productInfo->product_name) }} @endif  
                ( Avg. Rating - {{$productInfo->average_rating}} )
            </small>
        </div>
        <div class="box-tool">
            
            <li class="nav-item inline">
                    <a class="btn btn-fw primary" href="{{ route('product') }}">
                        &nbsp; {{ __('backend.backproduct') }}
                    </a>
                </li>
        </div>
        {{ Form::open(['route' => 'requestproductUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}
        <div class="table-responsive">
            <table class="table table-bordered m-a-0" id="label">
                <thead class="dker">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('backend.CustomerName') }}</th>
                        <th>{{ __('backend.rating') }}</th>
                        <th>{{ __('backend.review') }}</th>
                        <th>{{ __('backend.reviewDate') }}</th>
                    </tr>
                </thead>
                <tbody id="bannerTable"></tbody>
            </table>
        </div>      
        <footer class="dker p-a">
            <div class="row">
                <div class="col-sm-3 hidden-xs">
                </div>
                <div class="col-sm-6 text-right text-center-xs"></div>
            </div>
        </footer>
        {{ Form::close() }}
    </div>
</div> 
@endsection 
@push('after-scripts') 
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        load_data();
        function load_data() {

            var action_url = "{!! route('productRating.anyData') !!} ";
            
            console.log(action_url);
            
            var dataTable = $('#label').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                columnDefs: [{
                    'bSortable': false
                }],
                ajax: {
                    url: action_url,
                    type: 'POST',
                    data: function(d) {
                        
                        return $.extend({}, d, {
                        product_id:"{{$productInfo->id}}",

                        });
                    }
                },
                columns: [
                {
                    name: 'id',
                    data: 'id',
                    visible : false
                    
                },
                {
                    name: 'customer_name',
                    data: 'customer_name',
                    
                },
                {
                    data: 'rating',
                    name: 'rating',
                }, 
                {
                    data: 'review',
                    name: 'review',
                }, 
                {
                    data: 'review_date',
                    name: 'review_date',
                }],
                order: ['0', 'DESC']
            });
        }
    });
    $(document).ready(function() {
        if ($('.no-sort').hasClass('sorting_disabled')) {
            $('.no-sort').removeClass('sorting_asc')
        }
    });
    $("#filter_btn").click(function() {
        $("#filter_div").slideToggle();
    });
    $("#find_q").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#doctorTypeTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script> 
@endpush