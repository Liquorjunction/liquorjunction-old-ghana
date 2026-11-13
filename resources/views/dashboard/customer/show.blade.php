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
            <h3>{{ __('backend.view') }} {{ __('backend.customer') }} </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / <a href="{{ route('customer') }}">{{ __('backend.customer_managment') }}</a> / <span>{{ __('backend.view') }} {{ __('backend.customer') }}</span>
            </small>
        </div>
        <!-- <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('customer')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div> -->

        <div class="box nav-active-border b-info">

            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                     {{Form::open()}}
                    <div class="box-body">
                            <table class="table table-bordered m-a-0">                                
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.customer_name') !!}</th>
                                        <td style="width: 75%;">{{@Str::ucfirst($customerData->first_name)}} {{@$customerData->last_name}}</td>
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
                                        <td>
                                            @if($customerData->phonecode)
                                                +{{ $customerData->phonecode }} {{ $customerData->phone }}
                                            @else
                                                {{ $customerData->phone }}
                                            @endif
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.customer_join_date') !!}</th>
                                        @php
                                        $date = \Helper::converttimeTozone($customerData->created_at);
                                        @endphp
                                        <td>{{ @$date ? Carbon\Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-"}}</td>
                                    </tbody>
                                </tr>
                                <!-- <tr>
                                    <tbody>
                                        <th>{!! __('backend.Profile') !!}</th>
                                        <td>@if (!empty($customerData->profile)) 
                                            <a href="{{ asset('uploads/customer/' . $customerData->profile) }}" target="_blank">
                                                    <img width="100px" width="100px" src="{{ asset('uploads/customer/'.$customerData->profile) }}"  />
                                             </a>
                                            @else
                                            <img width="100px" width="100px" src="{{asset('uploads/contacts/profile.jpg')}}"/>
                                            @endIf
                                        </td>
                                    </tbody>
                                </tr> -->
                                <!-- <tr>
                                    <tbody>
                                        <th>{!! __('backend.zip_code') !!}</th>
                                       <td>{{@$customerData->post_code}}</td>
                                    </tbody>
                                </tr> -->
                                <!-- <tr>
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
                                </tr> -->
                                
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
  
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>
    @endpush