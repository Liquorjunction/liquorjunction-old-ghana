@extends('dashboard.layouts.master')
@section('title', __('backend.customer'))
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
            <h3>{{ __('backend.view') }} {{ __('backend.customer') }}
                </h3>
            <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Customer
                    <!-- <a>Banner</a> -->
                </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('technicianpending')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="box nav-active-border b-info">

            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                     {{Form::open(['route'=>['label'],'method'=>'POST', 'files' => true,'enctype' => 'multipart/form-data' ])}}
                    <div class="box-body">


                            <table class="table table-bordered m-a-0">
                                
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.customer_name') !!}</th>
                                        <td>{{@$customerData->first_name}} {{@$customerData->last_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.customer_email') !!}</th>
                                        <td>{{@$customerData->email}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.customer_phone') !!}</th>
                                        <td>{{@$customerData->phone}}</td>
                                    </tbody>
                                </tr>
                                
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.street_address') !!}</th>
                                        <td>{{@$customerData->street_address}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.zip_code') !!}</th>
                                        <td>{{@$customerData->post_code}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.city') !!}</th>
                                        <td>{{@$customerData->city}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.state') !!}</th>
                                        <td>{{@$customerData->states}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.country') !!}</th>
                                        <td>{{@$customerData->country}}</td>
                                    </tbody>
                                </tr>
                                @if($customerData->is_technician == 1)
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.category_technician') !!}</th>
                                        <td>{{@$customerData->category_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.business_name') !!}</th>
                                        <td>{{@$customerData->store_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.abn_no') !!}</th>
                                        <td>{{@$customerData->abn_number}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.id_proof') !!}</th>
                                        <td><a href="{{ asset('uploads/idproof/').'/'.$customerData->id_proof }}" target="_blank"><img src="{{ asset('assets/dashboard/images/pdf_icon.webp')}}" alt="ID Proof" height="100" width="100"></a></td>
                                    </tbody>
                                </tr>
                                @endif

                            </table>
                    </div>
                    {{Form::close()}}
                </div>

                <div class="form-group row">
                            <div class="col-sm-2">
                                <a href="{{ url()->previous() }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                                    <i class="material-icons">
                                        &#xe5cd;</i> {!! __('backend.cancel') !!}
                                </a>
                            </div>
                            <div class="col-sm-10">

                            </div>
                </div>
            </div>

        </div>
    </div>
    @endsection
    @push("after-scripts")

    <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
async defer></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>
    @endpush