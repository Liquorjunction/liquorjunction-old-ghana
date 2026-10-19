@extends('dashboard.layouts.master')
@section('title', __('backend.quote'))
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
            <h3>{{ __('backend.view') }} {{ __('backend.quote') }}
                </h3>
            <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Quote
                    <!-- <a>Banner</a> -->
                </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('quote')}}">
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
                                        <th>{!! __('backend.create_user') !!}</th>
                                        <td>{{@$quoteData->create_user_first_name}} {{@$quoteData->create__user_last_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.assign_user') !!}</th>
                                        @if($quoteData->assign_user_id == "")
                                        <td>Not Assign</td>

                                        @else

                                        <td>{{@$quoteData->create_user_assign_first_name}} {{@$quoteData->create__user_assign_last_name}}</td>
                                        @endif
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.category_name') !!}</th>
                                        <td>{{$quoteData->category_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.time_frame') !!}</th>
                                        <td>{{$quoteData->time_frame_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.material_category') !!}</th>
                                        <td>{{$quoteData->material_category_name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.post_code') !!}</th>
                                        <td>{{$quoteData->post_code}}</td>
                                    </tbody>
                                </tr>
                                
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.description') !!}</th>
                                        <td>{{$quoteData->description}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.quote_image') !!}</th>
                                        <td><a href="{{ asset('uploads/quote/').'/'.$quoteData->quote_image }}" target="_blank"><img src="{{ asset('uploads/quote/').'/'.$quoteData->quote_image }}" alt="Category Image" height="100" width="100"></a></td>
                                    </tbody>
                                </tr>

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