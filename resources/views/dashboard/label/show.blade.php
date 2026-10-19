@extends('dashboard.layouts.master')
@section('title', __('backend.label'))
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
                <h3>{{ __('backend.view') }} {{ __('backend.label') }}
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('label') }}">Labels</a> / View label
                </small>
            </div>
            {{-- <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('label')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div> --}}

            <div class="box nav-active-border b-info">

                <div class="tab-content clear b-t">
                    <div class="tab-pane active" id="tab_details">
                        <div class="box-body">
                            {{ Form::open(['class' => 'table_design']) }}

                            <table class="table table-bordered m-a-0">

                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.labelKey') !!}</th>
                                        <td>{{ $label->label_name }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.labelValue') !!} [EN]</th>
                                        <td>{{ $label->label_value }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>{!! __('backend.labelValue') !!} [FR]</th>
                                        <td>{{ $label->label_value_fr }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Label Type</th>
                                        <td>{{ ($label->label_type == 0) ? "Mobile" : "Web"}}</td>
                                    </tbody>
                                </tr>
                            </table>
                            {{ Form::close() }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <a href="{{ url()->previous() }}" class="btn btn-default m-t" style="margin-left: 17px">
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
    @push('after-scripts')
        <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
        <!-- <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
            async defer></script> -->
        <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
            async defer></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script type="text/javascript">
            $(".tab-content :input").prop("disabled", true);
        </script>
    @endpush
