@extends('dashboard.layouts.master')
@section('title', 'Category | Admin Panel')
@section('content')
    @include('sweetalert::alert')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="loader" id="loader"></div>
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">

            <div class="box-header dker">
                <h3>{{ __('backend.category_managment') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a>/
                    <span>{{ __('backend.category_managment') }}</span>
                </small>
            </div>
            @php
                $check_creation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'create');
            @endphp
            @if (isset($check_creation_permission) && $check_creation_permission == true)
                <div class="box-tool">
                    <ul class="nav">

                        <li class="nav-item inline">
                            <a class="btn btn-fw primary" data-toggle="modal" data-target="#myModal" data-backdrop="static"
                                data-keyboard="false">
                                <i class="material-icons">&#xe02e;</i>
                                &nbsp; {{ __('backend.NewCategory') }}
                            </a>
                        </li>

                    </ul>
                </div>
            @endif

            {{ Form::open(['route' => 'categoryUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}
            @php
                $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'update');
                $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'delete');
                $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'read');
            @endphp
            @if (
                (isset($check_updation_permission) && $check_updation_permission == true) ||
                    (isset($check_deletion_permission) && $check_deletion_permission == true))
                <div class="bulk-action">
                    <div></div>
                    <div>
                        <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                            <option value="no">{{ __('backend.bulkAction') }}</option>
                            @if (isset($check_updation_permission) && $check_updation_permission == true)
                                <option value="1">{{ __('backend.activeSelected') }}</option>
                                <option value="0">{{ __('backend.blockSelected') }}</option>
                            @endif
                            @if (isset($check_deletion_permission) && $check_deletion_permission == true)
                                <option value="2">{{ __('backend.deleteSelected') }}</option>
                            @endif
                        </select>
                        <button type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
                    </div>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered m-a-0" id="label">
                    <thead class="dker">
                        <tr>
                            <th class="width20 dker no-sort">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>{{ __('backend.categoryName') }}</th>
                            <th>{{ __('backend.status') }}</th>
                            <th id="option_width">{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bannerTable"></tbody>
                </table>
            </div>
            <footer class="dker p-a">
                <div class="row">
                    <div class="col-sm-3 hidden-xs">
                        <!-- .modal -->
                        <div id="m-all" class="modal fade" data-backdrop="true">
                            <div class="modal-dialog" id="animate">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                                    </div>
                                    <div class="modal-body text-center p-lg">
                                        <p>
                                            {{ __('backend.confirmationDeleteMsg') }}
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn dark-white p-x-md"
                                            data-dismiss="modal">{{ __('backend.no') }}</button>
                                        <button type="submit" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-right text-center-xs">
                    </div>
                </div>
            </footer>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('backend.AddCategory') }}</h4>
                </div>
                <form class="cmxform" id="categoryForm" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">{!! __('Title [EN]') !!}<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title" id="title" class="form-control"
                                    onkeypress="return isNumberKey(event)" placeholder="Title [EN]"
                                    value="{{ old('title') }}">
                                <span style="color: red;display: none;" id="errorMsgtitle" class='validate'></span>

                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">{!! __('Title [FR]') !!}<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title_fr" id="title_fr" class="form-control"
                                    onkeypress="return isNumberKey(event)" placeholder="Title [FR]"
                                    value="{{ old('title_fr') }}">

                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Image<span class="valid_field">*</span></label>
                            <div class="col-sm-10">
                                <img id="blah" height="100" width="100"
                                    src="{{ asset('assets/dashboard/images/no_image_found.jpg') }}" alt="your image"
                                    style="width:100px !important; height:100px !important; margin-bottom: 10px; margin-left: 10px;" />
                                <input class="form-control" type="file" name="imagefile" id="imagefile" multiple>
                                <span style="color:red; display: none;" id="errorMsg" class='validate'></span>
                                <div>
                                    <small>
                                        <i class="material-icons">&#xe8fd;</i>
                                        Choose image .png, .jpg, .jpeg files only.
                                    </small>
                                    <br>
                                    <small>
                                        <i class="material-icons">&#xe8fd;</i>
                                        Recommended size 120(Width) x 180(Height).
                                    </small>
                                </div>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Background Image<span
                                    class="valid_field"></span></label>
                            <div class="col-sm-10">
                                <div class="mb-1">
                                    <img id="blah" height="100" width="100"
                                        src="{{ asset('assets/dashboard/images/no_image_found.jpg') }}" alt="your image"
                                        style="width:100px !important; height:100px !important; margin-bottom: 10px;" />
                                </div>
                                <input type="file" name="photo" id="bannerimage" class="form-control"
                                    accept="image/png, image/jpeg">
                                <div class="help-block with-errors" style="color: red;"></div>
                                @if ($errors->has('photo'))
                                    <span class="help-block">
                                        <span style="color: red;" class='validate'>{{ $errors->first('photo') }}</span>
                                    </span>
                                @endif
                                <div>
                                    <small>
                                        <i class="material-icons">&#xe8fd;</i>
                                        Choose image .png, .jpg, .jpeg files only.
                                    </small>
                                    <br>
                                    <small>
                                        <i class="material-icons">&#xe8fd;</i>
                                        Recommended size 1440(Width) x 560(Height).
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">URL<span class="valid_field"></span></label>
                            <div class="col-sm-10">
                                <input type="text" name="url" id="url" class="form-control"
                                     placeholder="URL"
                                    value="{{ old('url') }}">
                                <span style="color: red;display: none;" id="errorMsgtitle" class='validate'></span>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="submit" class="btn btn-default btn btn-primary"><i
                                class="material-icons">&#xe31b;</i> Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                class="material-icons">&#xe5cd;</i> Close</button>
                    </div>
                    {{ Form::close() }}
            </div>
        </div>
    </div>


    <div class="modal fade" id="editCategory" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content add-category-data">
            </div>
        </div>
    </div>

    <div class="modal fade" id="showCategory" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content show-category-data">
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
    <script>
        imagefile.onchange = evt => {
            const [file] = imagefile.files
            fileName = document.querySelector('#imagefile').value;
            extension = fileName.split('.').pop();
            document.querySelector('.output').textContent = extension;
            if (file) {
                console.log(file);
                blah.src = URL.createObjectURL(file)
            }
        }

        $(document).ready(function(e) {

            $.validator.addMethod("customUrl", function(value, element) {
                return this.optional(element) ||
                    /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/.test(value);
                // This is a simple regex for URLs. Adjust it according to your specific needs.
            }, "Please enter a valid URL.");

            var test = $("#categoryForm").validate({
                rules: {
                    imagefile: {
                        required: true,
                        extension: "jpg|jpeg|png|gif",
                    },
                    title: {
                        required: true,
                        maxlength: 30,

                    },
                    title_fr: {
                        required: true,
                        maxlength: 30,
                    },
                    url: {
                        // customUrl: true,
                    }
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    imagefile: {
                        required: "Image field is required.",
                        extension: 'image file type must be jpeg,png,jpg.',
                    },
                    title: {
                        required: "Title field is required.",
                        maxlength: "Title field cannot exceed {0} characters.",

                    },
                    title_fr: {
                        required: "Title Fr field is required.",
                        maxlength: "Title Fr field cannot exceed {0} characters."
                    },
                    url: {
                        customUrl: "Please enter a valid URL."
                    }
                },
                submitHandler: function() {
                    var form_data = new FormData($('#categoryForm')[0]);
                    action_url = "{{ route('category.store') }}";
                    var csrf = "{{ csrf_token() }}";
                    $.ajax({
                        url: action_url,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        processData: false,
                        contentType: false,
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            // $(".loader").fadeIn();
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            // console.log(data)
                            // return false;
                            // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                            // return false;
                            if (data.success) {
                                // $('.loader').css("visibility", "visible");
                                window.location.href = "{{ route('category') }}";
                                // location.reload();
                            }
                        },
                        error: function(errors) {
                            // alert(errors);
                            $('.loader').css("visibility", "hidden");
                            var erroJson = JSON.parse(errors.responseText);
                            // console.log(erroJson.title[0]);
                            for (var err in erroJson) {
                                console.log(erroJson);
                                for (var errstr of erroJson[err])
                                    $("span#errorMessage").css("display", "block");
                                $("span#errorMsg").css("display", "block");
                                $("span#errorMsgtitle").css("display", "block");


                                $("span#errorMsg").html(erroJson.imagefile);
                                $("span#errorMsgtitle").html(erroJson.title);

                            }
                        }
                    });
                }
            });
            $('#myModal').on('hidden.bs.modal', function() {
                test.resetForm();
                $('#myModal form')[0].reset();
            })
        });
        $(document).on('click', '.edit-category', function(e) {
            $("#editCategory").modal({
                backdrop: false
            });
            var category_id = $(this).attr('data-id');
            $(document).find('#editCategory').find(".add-category-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('category.edit') }}",
                type: 'POST',
                data: 'category_id=' + category_id,
                success: function(response) {
                    // console.log(response);
                    // return false;
                    $(document).find('#editCategory').find(".add-category-data").append(response.html);
                    $('.selectpicker').selectpicker();
                    $(document).find('#editCategory').modal('show');
                },
                error: function(response) {

                    alert(response.responseText);
                }
            });
        });

        $(document).on('click', '.show-category', function(e) {
            $("#showCategory").modal({
                backdrop: false
            });
            var category_id = $(this).attr('data-id');
            $(document).find('#showCategory').find(".show-category-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('category.show') }}",
                type: 'POST',
                data: 'category_id=' + category_id,
                success: function(response) {
                    $(document).find('#showCategory').find(".show-category-data").append(response.html);
                    $(document).find('#showCategory').modal('show');
                },
                error: function(response) {
                    alert(response.responseText);
                }
            });
        });

        $(document).on('click', '.status_active', function(e) {
            var category_id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
            swal({
                    title: "Deactive",
                    text: "Are you sure you want to deactive this category?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#BF0A30",
                    confirmButtonText: "Deactive",
                    closeOnConfirm: false
                },
                function() {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('category.status_active') }}",
                        type: 'POST',
                        data: 'category_id=' + category_id,
                        beforeSend: function() {
                            // $(".loader").fadeIn();
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(response) {
                            $('.loader').css("visibility", "visible");
                            window.location.href = "{{ route('category') }}";
                        },
                        error: function(response) {
                            alert(response.responseText);
                        }
                    });
                }
            );
        });

        $(document).on('click', '.status_inactive', function(e) {
            var category_id = $(this).attr('data-id');
            // $(document).find('#editCustomer').find(".add-customer-data").empty();
            swal({
                    title: "Active",
                    text: "Are you sure you want to active this category?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#002868",
                    confirmButtonText: "Active",
                    closeOnConfirm: false
                },
                function() {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('category.status_inactive') }}",
                        type: 'POST',
                        data: 'category_id=' + category_id,
                        beforeSend: function() {
                            // $(".loader").fadeIn();
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(response) {
                            $('.loader').css("visibility", "visible");
                            // return false;
                            window.location.href = "{{ route('category') }}";
                        },
                        error: function(response) {
                            alert(response.responseText);
                        }
                    });
                }
            );
        });
    </script>
    <script type="text/javascript">
        document.getElementById('bannerimage').onchange = function(evt) {
            const file = evt.target.files[0];
            if (!file) return;

            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            const maxSize = 2 * 1024 * 1024; // 2 MB

            if (!allowedExtensions.exec(file.name)) {
                this.value = ''; // Reset the input
                document.querySelector('.help-block.with-errors').innerHTML =
                    'Please upload only .png , .jpg , .jpeg only.';
                return;
            }

            if (file.size > maxSize) {
                this.value = ''; // Reset the input
                document.querySelector('.help-block.with-errors').innerHTML = 'File upload size is not more than 2 MB.';
                return;
            }

            document.querySelector('.help-block.with-errors').innerHTML = ''; // Clear error messages
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
            };

            reader.readAsDataURL(file);
        }
    </script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        var show = "";
        var sort = "";
        var check = "";
    </script>
    <?php
        if(isset($check_updation_permission) && $check_updation_permission==true || isset($check_deletion_permission) && $check_deletion_permission==true ) {
    ?>
    <script>
        var show = true;
        var check = true;
        var sort = [0, 2, 3];
    </script>
    <?php } else if($check_view_permission==true ) {
    ?>
    <script>
        var show = true;
        var check = false;
        var sort = [0, 2, 3];
    </script>
    <?php }else { ?>
    <script>
        var show = false;
        var check = false;
        var sort = [0, 2];
    </script>
    <?php }?>
    <script type="text/javascript">
        function isNumberKey(evt) {
            //var e = evt || window.event;
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32) {
                return false;
            }

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

                var action_url = "{!! route('category.anyData') !!} ";

                $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': sort
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: {

                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            orderable: false,
                            searchable: false,
                            visible: check

                        },
                        {
                            data: 'category_name',
                            name: 'category_name',
                        },
                        {
                            data: 'status',
                            name: 'status',
                        },
                        {
                            data: 'options',
                            orderable: false,
                            searchable: false,
                            visible: show
                        }
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

        $(document).on('submit', '#updateAll', function(e) {
            e.preventDefault();
            var allVals = [];
            var check = false;

            var select_row = "{{ __('backend.select_row') }}";
            var select_status = "{{ __('backend.select_status') }}";

            var type = $(document).find('#action').val();
            if (type == 'no') {
                $(document).find('#alert_confirm').modal('show');
                $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_status);

            } else {
                $(".has-value:checked").each(function() {
                    var idvalue = $(this).attr('data-id');
                    if (typeof idvalue === "undefined") {

                    } else {
                        allVals.push(idvalue);
                    }
                });

                if (allVals.length <= 0) {
                    $(document).find('#alert_confirm').modal('show');
                    $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_row);
                } else {
                    var msg = "";
                    if (type == 0) {
                        msg = "Are you sure you want to deactive this category?";
                    } else if (type == 1) {
                        msg = "Are you sure you want to active this category?";
                    } else {
                        msg = "Are you sure you want to delete this category?";
                    }

                    $(document).find('#default_confirm').modal('show');
                    $(document).find('#default_confirm').find('.dynamic_message').text(msg);
                    var join_selected_values = allVals.join(",");
                    $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
                    $(document).find('#default_confirm').find('.checkbox_type').val(type);

                }

            }
        });
        $(document).on('click', '.delete-school', function(e) {
            e.preventDefault();
            var package_id = $(this).attr('data-id');
            var allVals = [];
            allVals.push(package_id);
            var type = 2;
            var msg = "Are you sure you want to delete?";

            $(document).find('#default_confirm').modal('show');
            $(document).find('#default_confirm').find('.dynamic_message').text(msg);
            var join_selected_values = allVals.join(",");
            $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
            $(document).find('#default_confirm').find('.checkbox_type').val(type);
        });

        $(document).on('click', '.yes_click', function(e) {
            var join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
            var type = $(document).find('#default_confirm').find('.checkbox_type').val();
            var csrf = "{{ csrf_token() }}";
            ajaxUpdateAll(csrf, join_selected_values, type, action);
        });

        function ajaxUpdateAll(csrf, join_selected_values, type) {
            $.ajax({
                url: "{{ route('categoryUpdateAll') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: 'ids=' + join_selected_values + '&status=' + type,
                beforeSend: function() {
                    // $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(data) {

                    if (data.success == true) {
                        $('.loader').css("visibility", "hidden");
                        $('#success_file_popup').append(messages('alert-success', data.msg));
                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);


                        $(document).find('#default_confirm').modal('hide');
                        var tabe = $('#label').DataTable();
                        $(document).find('#action').prop('selectedIndex', 0);
                        tabe.ajax.reload(null, false);
                        $("#checkAll").prop('checked', false);

                    } else {
                        $('.loader').css("visibility", "hidden");
                        $('#success_file_popup').append(messages('alert-danger', data.error));

                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);
                    }
                },
                error: function(data) {
                    alert(data.responseText);
                }
            });
        }
        $("#action").change(function() {
            if (this.value == "delete") {
                /*var numberOfChecked = $('input:checkbox:checked').length;
                if(numberOfChecked == ''){
                    alert("Please select row.");
                    return false;
                } */
                $("#submit_all").css("display", "none");
                $("#submit_show_msg").css("display", "inline-block");
            } else {
                $("#submit_all").css("display", "inline-block");
                $("#submit_show_msg").css("display", "none");
            }
        });

        function messages(classname, msg) {
            return '<div class="alert ' + classname +
                ' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' +
                msg + '</div>';
        }
        setTimeout(function() {
            $('#topmsgall').hide();
        }, 5000);
    </script>
@endpush
