@extends('dashboard.layouts.master')
@section('title', 'Transaction')
@push('after-styles')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
@endpush
@section('content')
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3>Transaction
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('transaction.index') }}">Transaction</a> /
                    <span>View Transaction</span>
                </small>
            </div>
            <div class="box-body">
                {{ Form::open(['id' => 'categoryForm']) }}
                <div class="personal_informations">



                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Transaction ID :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ ucfirst($transactions->transaction_id) }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Transaction Date :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ ucfirst(Helper::converttimeTozone($transactions->created_at)) }}</label>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Customer Name :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ ucfirst($transactions->user_name) }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Service Provider Name :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ ucfirst($transactions->provider_name) }}</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Activity Name :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ urldecode($transactions->item_name) }}</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Amount :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ $setting->currency . ' ' . number_format($transactions->trans_amount, 2) }}</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Payment Status :</label>
                        <div class="col-sm-9 form-control-label">
                            <label>{{ ucfirst($transactions->status) }}</label>
                        </div>
                    </div>
                    <div class="form-group row ">
                        <div class="col-sm-2">
                            <a href="{{ route('transaction.index') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                                <i class="material-icons">&#xe31b;</i> Back
                            </a>
                        </div>
                    </div>

                </div>
               
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/summernote/dist/summernote.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



    <script>
        // update progress bar
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                $('progress').attr({
                    value: e.loaded,
                    max: e.total
                });
                // reset progress on complete
                if (e.loaded == e.total) {
                    $('progress').attr('value', '0.0');
                }
            }
        }
    </script>
@endpush
