@extends('dashboard.layouts.master')
@section('title', 'Banner')
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
            word-wrap: break-word;

        }

        .table tbody>tr>td {
            word-wrap: break-word;
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
                <h3>{{ __('backend.view') }} Banner
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / <a
                        href="{{ route('banner') }}">{{ __('backend.banner_management') }}</a> / View Banner
                    <!-- <a>Banner</a> -->
                </small>
            </div>

            <?php
            // dd($banner->type);
            if ($banner->type == 1) {
                $type = 'Category';
            } elseif ($banner->type == 2) {
                $type = 'Product';
            } 
            elseif ($banner->type == 0) {
                $type = 'Brand';
            }
            else {
                $type = 'Custom URL';
            }
            ?>
            <div class="box nav-active-border b-info">

                <div class="tab-content clear b-t">
                    <div class="tab-pane active" id="tab_details">
                        {{ Form::open(['route' => ['banner'], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data']) }}
                        <div class="box-body">
                            <table class="table table-bordered m-a-0">
                                <tr>
                                    <tbody>
                                        <th>Title [EN]</th>
                                        <td style="width: 75%">{{ isset($banner->title) ? $banner->title : '' }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Title [FR]</th>
                                        <td>{{ isset($banner->title_fr) ? $banner->title_fr : '' }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Description [EN]</th>
                                        <td>{{ $banner->description }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Description [FR]</th>
                                        <td>{{ $banner->description_fr }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Type</th>
                                        <td>{{ $type }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Banner Type</th>
                                        <td>
                                            @php
                                                if($banner->offer == 1){
                                                    echo 'Offer';
                                                }elseif($banner->highlight == 1){
                                                    echo "Highlight";
                                                }else{
                                                    echo "Main banner";
                                                }

                                            @endphp
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Image</th>
                                        <td>
                                            @if (isset($banner->photo) && $banner->photo != '')
                                                <a href="{{ asset('uploads/banners/' . $banner->photo) }}" target="_blank">

                                                    <img id="image"
                                                        src="{{ asset('uploads/banners/') . '/' . $banner->photo }}"
                                                        class="thumbnail" width="100px" height="100px" />
                                                </a>
                                            @else
                                                <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px"
                                                    height="100px">
                                            @endif
                                        </td>
                                    </tbody>
                                </tr>
                                {{-- <tr>
                                    <tbody>
                                        <th>Offer</th>
                                        <td>{{ $banner->offer == 1 ? 'Yes' : 'No' }}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Highlight</th>
                                        <td>{{ $banner->highlight ? 'Yes' : 'No' }}</td>
                                    </tbody>
                                </tr> --}}
                            </table>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2">
                            <a href="{{ route('banner') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                                <i class="material-icons">
                                    &#xe5cd;</i> Cancel
                            </a>
                        </div>
                        <div class="col-sm-10"></div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('after-scripts')
        <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script type="text/javascript">
            $(".tab-content :input").prop("disabled", true);
        </script>
    @endpush
