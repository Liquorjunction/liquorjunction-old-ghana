@extends('dashboard.layouts.master')
@section('title', 'Newsletter | Admin Panel')
@section('content')
@include('sweetalert::alert')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<style>
    .model {
        z-index: 1050 !important;
    }

    .model-backdrop {
        z-index: 1040 !important;
    }
</style>
<div class="loader" id="loader"></div>
<div class="padding website-label">
    <div class="success_message" style="margin-bottom: 10px;"></div>
    <div id="success_file_popup" style="margin-bottom: 10px;"></div>
    <div class="box">

        <div class="box-header dker">
            <h3>NewsLetter</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <span>NewsLetter</span>
            </small>
        </div>



        {{ Form::open(['route' => 'emailUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}

        <div class="table-responsive">
            <table class="table table-bordered m-a-0" id="label">
                <thead class="dker">
                    <tr>
                        <!-- <th class="width20 dker no-sort">
                    <label class="ui-check m-a-0">
                        <input id="checkAll" type="checkbox"><i></i>
                    </label>
                </th> -->
                        <!-- <th>Id</th> -->
                        <th id="email">Email</th>
                        <th id="Date">Date</th>
                        {{-- <th>{{ __('backend.status') }}</th> --}}
                        {{-- <th id="option_width">{{ __('backend.options') }}</th>--}}
                    </tr>
                </thead>
                <tbody id="bannerTable">
                </tbody>
            </table>
        </div>

        {{ Form::close() }}
    </div>
</div>


{{ Form::close() }}
</div>

</div>
</div>

@endsection
@push('after-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    function isNumberKey(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        load_data();

        function load_data() {
            var action_url = "{!! route('news.anyData') !!} ";

            $('#label').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                columnDefs: [{
                    'bSortable': false,
                }],
                ajax: {
                    url: action_url,
                    type: 'POST',
                    data: {

                    }
                },
                columns: [
                    
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                 
                ],
                order: ['0', 'DESC']
            });
        }

    });


    $(document).ready(function() {
        if ($('.no-sort').hasClass('sorting_disabled')) {
            $('.no-sort').removeClass('sorting_asc')
        }
    });


    $("#submit_show_msg").click(function() {
        var numberOfChecked = $('input:checkbox:checked').length;
        if (numberOfChecked == '') {
            alert("Please select row.");
            return false;
        }
    });


    $("#checkAll").click(function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
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