@extends('wholesaler.layouts.master')
@section('title', 'Store | Wholesaler Panel')
@push("after-styles")
<style type="text/css">
    #blah {
    height: 50% !important;
    width: 25% !important;
}

#blah1 {
    height: 50% !important;
    width: 25% !important;
}
</style>
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <style type="text/css">
        .error {
            color: red;
            margin-left: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="padding edit-package">
        <div class="box">
            <div class="box-header dker">
                
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.topicShow') }} {{ __('backend.storeAddress') }}
                </h3>
                <small>
                    <a href="{{ route('adminwholesalerHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('wholesalerstore') }}">{{ __('backend.storeAddress') }}</a>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('cms') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
               <!-- <form class="cmxform" id="productForm" method="post" action="" autocomplete="off"> -->
                {{Form::open(['route'=>['wholesalerstore.update',@$StoreDetails->id],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data', 'id' => 'labelForm' ])}}

                <div class="personal_informations">
                    <!-- <h3>{!!  __('backend.cms') !!}</h3>
                    <br>
                    <br> -->
                   
           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.country') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="country" id="country" class="form-control"  placeholder="Country" value="{{@$StoreDetails->country}}" readonly>
                            
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.state') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="state" id="state" class="form-control"  placeholder="State" value="{{@$StoreDetails->state}}" readonly>
                           
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.zip_code') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="zip_code" id="zip_code" class="form-control"  placeholder="Zip Code" value="{{@$StoreDetails->zip_code}}" readonly>
                           
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.street_address') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="street_address" id="street_address" class="form-control"  placeholder="Street Address" value="{{@$StoreDetails->street_address}}" readonly>
                            
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.address') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="address" id="address" class="form-control"  placeholder="Street Address" value="{{@$StoreDetails->address}}" readonly>
                           
                        </div>
                    </div>
                    <div class="form-group row">
                       
                        <div class="col-sm-9">
                            <div id="myMapaaa">

                                                        </div>
                        </div>
                    </div>

                     <div class="form-group row weekplanid" id="border" style="border: 1px solid; border-radius: 21px;">
                        <br>
                        <label class="col-sm-2 form-control-label" id="pastekey">Store Time </label>
                        <div class="col-sm-10">
                            <div class="multi-group">
                                 @foreach($StoreTimingWeek as $time)
                                 <div class="form-group row">
                                    <label class="col-sm-2 form-control-label">{{@$time->name}}</label>
                                    @if($time->start_time == "00:00:00")
                                     <div class="col-sm-4">
                                        <label>Opening Time</label>
                                        <input type="text" class="form-control" name="monday_opening_time" id="monday_opening_time" value="Close" readonly>
                                    </div>
                                    @else

                                    <div class="col-sm-4">
                                        <label>Opening Time</label>
                                        <input type="text" class="form-control" name="monday_opening_time" id="monday_opening_time" value="{{date('h:i A', strtotime($time->start_time));}}" readonly>
                                    </div>
                                    @endif

                                    @if($time->end_time == "00:00:00")
                                     <div class="col-sm-4">
                                        <label>Close Time</label>
                                        <input type="text" class="form-control" name="monday_close_time" id="monday_close_time" value="Close" readonly>
                                    </div>
                                    @else
                                     <div class="col-sm-4">
                                        <label>Close Time</label>
                                        <input type="text" class="form-control" name="monday_close_time" id="monday_close_time" value="{{date('h:i A', strtotime($time->end_time));}}" readonly>
                                    </div>
                                    @endif
                                   
                                    
                                 </div>
                                 @endforeach
                                 
                        </div>
                    </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="col-sm-10 pl-0">
                            
                            <a href="{{ route('wholesalerstore')}}" class="btn btn-default m-t">
                                <i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}
                            </a>
                    </div>
                </div>

</form>
                <!-- {{Form::close()}} -->
            </div>
        </div>
    </div>
@endsection


@push('after-scripts')
    

@endpush

