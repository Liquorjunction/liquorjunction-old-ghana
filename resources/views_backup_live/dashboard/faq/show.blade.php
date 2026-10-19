@extends('dashboard.layouts.master')
@section('title', __("FAQ's"))
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
            <h3>{{ __('backend.view') }} FAQ's</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <a href="{{ route('faq') }}">FAQ's</a> /
                View FAQ's
            </small>
        </div>
        {{-- <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('faq')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div> --}}
        <div class="box nav-active-border b-info">
            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                    <div class="box-body">
                        <form class="table_design">
                            <table class="table table-bordered m-a-0">
                                <tr>
                                    <tbody>
                                        <th>Question [EN]</th>
                                        <td>{{isset($Faq->question_name)? $Faq->question_name : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Question [FR]</th>
                                        <td>{{isset($Faq->question_name_fr)? $Faq->question_name_fr : ''}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Answer [EN]</th>
                                        <td>{{isset($Faq->answer)? $Faq->answer : ''}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Answer [FR] </th>
                                        <td>{{isset($Faq->answer_fr)? $Faq->answer_fr : ''}}</td>
                                    </tbody>
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
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
async defer></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>
    @endpush