@extends('frontend.layouts.app')
@section('title', Helper::language('my_address_label'))
@section('content')
    @include('sweetalert::alert')
    <div class="bread-crumb-block">
        <div class="container">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{ @Helper::language('home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"> {{ @Helper::language('my_address_label') }}</li>
            </ul>
        </div>
    </div>

    <style>
        .account-address address {
            max-width: 100% !important;
        }
        .btn-card-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-card-edit {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        .btn-card-edit:hover {
            background: #fbb516;
            border-color: #fbb516;
            color: #ffffff;
        }
        .btn-card-remove {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #dc3545;
        }
        .btn-card-remove:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: #ffffff;
        }
        .mark-default-wrap label:hover {
            color: #fbb516 !important;
        }
    </style>

    <section class="address-listing pt-20 pb-60">
        <div class="container">
            {{-- Shipping Address --}}
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    @include('frontend.layouts.account-sidebar')
                </div>
                <div class="col-lg-9 col-md-8">
                    <h2>Shipping Address</h2>
                    <div class="row address-row">
                        <div class="col-lg-4 col-sm-6 address-column mb-4">
                            <a href="javascript:void(0)" class="common-card add-address open-address-modal" data-type="shipping" data-action="add" data-is-checkout="0" title="Add New Shipping Address">
                                <svg width="60" height="60" viewBox="0 0 60 60" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g id="icon_pluz">
                                        <path id="Vector"
                                            d="M26.75 26.25V26.75H26.25H13.125C11.3269 26.75 9.875 28.2019 9.875 30C9.875 31.7981 11.3269 33.25 13.125 33.25H26.25H26.75V33.75V46.875C26.75 48.6731 28.2019 50.125 30 50.125C31.7981 50.125 33.25 48.6731 33.25 46.875V33.75V33.25H33.75H46.875C48.6731 33.25 50.125 31.7981 50.125 30C50.125 28.2019 48.6731 26.75 46.875 26.75H33.75H33.25V26.25V13.125C33.25 11.3269 31.7981 9.875 30 9.875C28.2019 9.875 26.75 11.3269 26.75 13.125V26.25Z"
                                            fill="#858584" stroke="#858584" />
                                    </g>
                                </svg>
                            </a>
                        </div>
                        
                        @foreach ($UserAddressData as $data)
                        <div class="col-lg-4 col-sm-6 address-column mb-4">
                            <div class="common-card account-address">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="radio-group mb-0 mark-default-wrap {{ ($data->is_selected_address_id == 1) ? 'd-none' : '' }}" id="mark_wrap_{{ $data->id }}">
                                            <input type="radio" 
                                                   id="default_shipping_{{ $data->id }}" 
                                                   name="default_shipping_address" 
                                                   value="{{ $data->id }}" 
                                                   class="myaddress-default-radio">
                                            <label for="default_shipping_{{ $data->id }}" class="text-muted small fw-semibold mb-0" style="font-size: 12.5px; cursor: pointer;">
                                                Mark Default
                                            </label>
                                        </div>
                                        <span class="badge bg-warning text-dark py-1 px-2 fw-bold active-default-badge {{ ($data->is_selected_address_id == 1) ? '' : 'd-none' }}" id="badge_default_{{ $data->id }}" style="font-size: 11px; border-radius: 6px;">
                                            <i class="fa fa-check-circle me-1"></i>Active Default
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="javascript:void(0)" 
                                           class="open-address-modal btn-card-action btn-card-edit" 
                                           data-type="shipping" 
                                           data-action="edit" 
                                           data-id="{{ $data->id }}" 
                                           data-lat="{{ $data->latitude ?? '' }}" 
                                           data-lng="{{ $data->longitude ?? '' }}" 
                                           data-is-checkout="0" 
                                           title="Edit Address">
                                            <i class="fa fa-pen" style="font-size: 11px;"></i>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           class="data_id_address btn-card-action btn-card-remove" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#removeItemModal" 
                                           data-id="{{ @$data->id }}" 
                                           title="Remove Address">
                                            <i class="fa fa-trash-alt" style="font-size: 11px;"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2 my-2">
                                    <span class="fw-bold text-dark text-truncate" style="max-width: 190px; font-size: 15.5px; font-weight: 700;" title="{{ @$data->name }}">{{ @$data->name }}</span>
                                    <span class="fw-bold text-dark text-nowrap" style="font-size: 13.5px; font-weight: 700;"><i class="fa fa-phone small me-1 text-muted" style="font-size: 11px;"></i>(+{{ $data->phonecode }}) {{ $data->phone }}</span>
                                </div>
                                <address class="title-two mb-2" style="font-weight: 400; font-size: 13.5px; line-height: 1.5; color: #555; max-width: 100%;">
                                    {!! Helper::formatUserAddress($data, true) !!}
                                </address>

                                @php
                                    $hasMapLocation = (!empty($data->latitude) && !empty($data->longitude));
                                @endphp

                                @if(!$hasMapLocation)
                                    <div class="alert alert-warning py-1 px-2 mt-auto mb-0 d-flex align-items-center justify-content-between" style="font-size: 11px; border-radius: 6px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                                        <span>
                                            <i class="fa fa-exclamation-triangle text-danger me-1"></i> Location not set
                                        </span>
                                        <a href="javascript:void(0)" class="btn btn-xs py-0 px-2 fw-bold text-dark open-address-modal" data-type="shipping" data-action="edit" data-id="{{ $data->id }}" data-lat="{{ $data->latitude ?? '' }}" data-lng="{{ $data->longitude ?? '' }}" data-is-checkout="0" style="background: #fbb516; font-size: 10.5px; border-radius: 4px; white-space: nowrap;">
                                            <i class="fa fa-map-marker-alt me-1"></i>Set on Map
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Billing Address --}}
            <div class="row mt-4">
                <div class="col-lg-3 col-md-4">
                        {{--  --}}
                </div>
                <div class="col-lg-9 col-md-8">
                    <h2>Billing Address</h2>
                    <div class="row address-row">
                        @if($UserBillAddressData->isEmpty())  
                        <div class="col-lg-4 col-sm-6 address-column mb-4">
                            <a href="javascript:void(0)" class="common-card add-address open-address-modal" data-type="billing" data-action="add" data-is-checkout="0" title="Add Billing Address">
                                <svg width="60" height="60" viewBox="0 0 60 60" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g id="icon_pluz">
                                        <path id="Vector"
                                            d="M26.75 26.25V26.75H26.25H13.125C11.3269 26.75 9.875 28.2019 9.875 30C9.875 31.7981 11.3269 33.25 13.125 33.25H26.25H26.75V33.75V46.875C26.75 48.6731 28.2019 50.125 30 50.125C31.7981 50.125 33.25 48.6731 33.25 46.875V33.75V33.25H33.75H46.875C48.6731 33.25 50.125 31.7981 50.125 30C50.125 28.2019 48.6731 26.75 46.875 26.75H33.75H33.25V26.25V13.125C33.25 11.3269 31.7981 9.875 30 9.875C28.2019 9.875 26.75 11.3269 26.75 13.125V26.25Z"
                                            fill="#858584" stroke="#858584" />
                                    </g>
                                </svg>
                            </a>
                        </div>
                        @endif
                        
                        @foreach ($UserBillAddressData as $data)
                        <div class="col-lg-4 col-sm-6 address-column mb-4">
                            <div class="common-card account-address">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-warning text-dark py-1 px-2 fw-bold" style="font-size: 11px; border-radius: 6px;">
                                        <i class="fa fa-shield-alt me-1"></i>Default Billing
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="javascript:void(0)" 
                                           class="open-address-modal btn-card-action btn-card-edit" 
                                           data-type="billing" 
                                           data-action="edit" 
                                           data-id="{{ $data->id }}" 
                                           data-lat="{{ $data->latitude ?? '' }}" 
                                           data-lng="{{ $data->longitude ?? '' }}" 
                                           data-is-checkout="0" 
                                           title="Edit Address">
                                            <i class="fa fa-pen" style="font-size: 11px;"></i>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           class="data_billId_address btn-card-action btn-card-remove" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#removeBillItemModal" 
                                           data-id="{{ @$data->id }}" 
                                           title="Remove Address">
                                            <i class="fa fa-trash-alt" style="font-size: 11px;"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2 my-2">
                                    <span class="fw-bold text-dark text-truncate" style="max-width: 190px; font-size: 15.5px; font-weight: 700;" title="{{ @$data->name }}">{{ @$data->name }}</span>
                                    <span class="fw-bold text-dark text-nowrap" style="font-size: 13.5px; font-weight: 700;"><i class="fa fa-phone small me-1 text-muted" style="font-size: 11px;"></i>(+{{ $data->phonecode }}) {{ $data->phone }}</span>
                                </div>
                                <address class="title-two mb-2" style="font-weight: 400; font-size: 13.5px; line-height: 1.5; color: #555; max-width: 100%;">
                                    {!! Helper::formatUserAddress($data, true) !!}
                                </address>

                                @php
                                    $hasBillMapLocation = (!empty($data->latitude) && !empty($data->longitude));
                                @endphp

                                @if(!$hasBillMapLocation)
                                    <div class="alert alert-warning py-1 px-2 mt-auto mb-0 d-flex align-items-center justify-content-between" style="font-size: 11px; border-radius: 6px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                                        <span>
                                            <i class="fa fa-exclamation-triangle text-danger me-1"></i> Location not set
                                        </span>
                                        <a href="javascript:void(0)" class="btn btn-xs py-0 px-2 fw-bold text-dark open-address-modal" data-type="billing" data-action="edit" data-id="{{ $data->id }}" data-lat="{{ $data->latitude ?? '' }}" data-lng="{{ $data->longitude ?? '' }}" data-is-checkout="0" style="background: #fbb516; font-size: 10.5px; border-radius: 4px; white-space: nowrap;">
                                            <i class="fa fa-map-marker-alt me-1"></i>Set on Map
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Ship address Modal --}}
    <div class="modal fade remove-item-modal p-0" id="removeItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid fa-xmark"></i></button>
                    <input type="hidden" name="address_id" id="address_id" value="">
                    <div class="modal-body">
                        <h5 class="mb-3">{{ @Helper::language('remove_address') }}</h5>
                        <p class="body-large mb-4">{{ @Helper::language('are_you_sure_you_want_to_remove_this_address') }}
                        </p>
                        <div class="d-flex justify-content-between gap-2">
                            <button type="submit" class="solid-button w-100" data-bs-dismiss="modal"
                                aria-label="Close">{{ @Helper::language('cancel_btn') }}</button>
                            <button type="submit" class="solid-button w-100"
                                onclick="return removeAddress()">{{ @Helper::language('remove_btn') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Bill Address Modal --}}
    <div class="modal fade remove-item-modal p-0" id="removeBillItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid fa-xmark"></i></button>
                    <input type="hidden" name="bill_address_id" id="bill_address_id" value="">
                    <div class="modal-body">
                        <h5 class="mb-3">{{ @Helper::language('remove_address') }}</h5>
                        <p class="body-large mb-4">{{ @Helper::language('are_you_sure_you_want_to_remove_this_address') }}
                        </p>
                        <div class="d-flex justify-content-between gap-2">
                            <button type="submit" class="solid-button w-100" data-bs-dismiss="modal"
                                aria-label="Close">{{ @Helper::language('cancel_btn') }}</button>
                            <button type="submit" class="solid-button w-100"
                                onclick="return removeBillAddress()">{{ @Helper::language('remove_btn') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".data_id_address").on("click", function() {
                var id = $(this).data("id");
                $("#address_id").val(id);
            });

            $(".data_billId_address").on("click", function() {
                var id = $(this).data("id");
                $("#bill_address_id").val(id);
            });

            $(document).on('change', 'input[name="default_shipping_address"]', function() {
                var addressId = $(this).val();
                var csrf = "{{ csrf_token() }}";
                
                $.ajax({
                    url: "{{ route('selected-address') }}",
                    type: 'POST',
                    data: {
                        address_id: addressId
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Reset all addresses to show "Mark Default" and hide "Active Default"
                        $('.active-default-badge').addClass('d-none');
                        $('.mark-default-wrap').removeClass('d-none');
                        $('input[name="default_shipping_address"]').prop('checked', false);

                        // Set clicked address to show "Active Default" badge and hide "Mark Default"
                        $('#mark_wrap_' + addressId).addClass('d-none');
                        $('#badge_default_' + addressId).removeClass('d-none');
                        $('#default_shipping_' + addressId).prop('checked', true);
                    },
                    error: function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Unable to update default address. Please try again.'
                            });
                        }
                    }
                });
            });
        });

        function removeAddress() {
            var address_id = $('#address_id').val();
            action_url = "{{ route('addressremove') }}";
            var csrf = "{{ csrf_token() }}";

            $('#removeItemModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');

            $.ajax({
                url: action_url,
                data: {
                    'address_id': address_id
                },
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                type: "POST",
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(response) {
                    var url = "{{ route('my-address') }}";
                    window.location.href = url;
                },
                error: function() {
                    $(".loader").fadeOut();
                }
            });
            return false;
        }

        function removeBillAddress() {
            var address_id = $('#bill_address_id').val();
            action_url = "{{ route('billaddressremove') }}";
            var csrf = "{{ csrf_token() }}";

            $('#removeBillItemModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');

            $.ajax({
                url: action_url,
                data: {
                    'address_id': address_id
                },
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                type: "POST",
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(response) {
                    var url = "{{ route('my-address') }}";
                    window.location.href = url;
                },
                error: function() {
                    $(".loader").fadeOut();
                }
            });
            return false;
        }
    </script>
@include('frontend.components.address-modal')
@endsection
