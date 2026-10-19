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
                        <div class="col-lg-4 col-sm-6 address-column">
                            <a href="{{ route('add-address') }}" class="common-card add-address">
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
                        <div class="col-lg-4 col-sm-6 address-column">
                            <div class="common-card account-address">
                                <h4 class="mb-3">{{ @Helper::language('default_billing_address') }} </h4>
                                {{-- <p class="title-two">{{@$data->name}}</p> --}}
                                <address class="title-two">
                                    @php
                                        (isset($data->name))? $address = $data->name : '';
                                        (isset($data->address))? $address .= ', <br>'.$data->address: '';
                                        (isset($data->area->title))? $address .= ', <br>'.$data->area->title : '';
                                        (isset($data->region->title))? $address .= ', <br>'.$data->region->title : '';
                                        (isset($data->country->name))? $address .= ',<br> '.$data->country->name : '';
                                        echo $address;  
                                        $phone_number = '('.$data->phonecode.') '.$data->phone;
                                    @endphp
                                </address>
                                <a href="tel:+{{ @$phone_number }}" class="body-large"> + {{ @$phone_number }}</a>
                                <div class="account-address-links flex-fill">
                                    <a href="{{ route('edit-address', $data->id) }}"
                                        class="red-text-link body-normal text-uppercase">{{ @Helper::language('edit_label') }}</a>
                                    <a href="javascript:void(0)"
                                        class="red-text-link body-normal text-uppercase data_id_address"
                                        data-bs-toggle="modal" data-bs-target="#removeItemModal"
                                        data-id={{ @$data->id }}>{{ @Helper::language('remove_btn') }}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Billing Address --}}
            <div class="row">
                <div class="col-lg-3 col-md-4">
                        {{--  --}}
                </div>
                <div class="col-lg-9 col-md-8">
                    <h2>Billing Address</h2>
                    <div class="row address-row">
                        @if($UserBillAddressData->isEmpty())  
                        <div class="col-lg-4 col-sm-6 address-column">
                            <a href="{{ route('add-bill-address') }}" class="common-card add-address">
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
                        <div class="col-lg-4 col-sm-6 address-column">
                            <div class="common-card account-address">
                                <h4 class="mb-3">{{ @Helper::language('default_billing_address') }} </h4>
                                {{-- <p class="title-two">{{@$data->name}}</p> --}}
                                <address class="title-two">
                                    @php
                                        (isset($data->name))? $address = $data->name : '';
                                        (isset($data->address))? $address .= ', <br>'.$data->address: '';
                                        (isset($data->area->title))? $address .= ', <br>'.$data->area->title : '';
                                        (isset($data->region->title))? $address .= ', <br>'.$data->region->title : '';
                                        (isset($data->country->name))? $address .= ',<br> '.$data->country->name : '';
                                        echo $address;  
                                        $phone_number = '('.$data->phonecode.') '.$data->phone;
                                    @endphp
                                </address>
                                <a href="tel:+{{ @$phone_number }}" class="body-large"> + {{ @$phone_number }}</a>
                                <div class="account-address-links flex-fill">
                                    <a href="{{ route('edit-bill-address', $data->id) }}"
                                        class="red-text-link body-normal text-uppercase">{{ @Helper::language('edit_label') }}</a>
                                    <a href="javascript:void(0)"
                                        class="red-text-link body-normal text-uppercase data_billId_address"
                                        data-bs-toggle="modal" data-bs-target="#removeBillItemModal"
                                        data-id={{ @$data->id }}>{{ @Helper::language('remove_btn') }}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Ship address Modal --}}
    <div class="modal fade remove-item-modal p-0 show" id="removeItemModal" tabindex="-1" aria-modal="true">
        <div class="modal-dialog">
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
    <div class="modal fade remove-item-modal p-0 show" id="removeBillItemModal" tabindex="-1" aria-modal="true">
        <div class="modal-dialog">
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
        });

        function removeAddress() {
            var address_id = $('#address_id').val();
            // alert(product_id);
            action_url = "{{ route('addressremove') }}";
            var csrf = "{{ csrf_token() }}";

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
                    // return false;
                    var url = "{{ route('my-address') }}";
                    window.location.href = url;
                    // location.reload();
                },
            });
        }

        function removeBillAddress() {
            var address_id = $('#bill_address_id').val();
            action_url = "{{ route('billaddressremove') }}";
            var csrf = "{{ csrf_token() }}";

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
            });
        }
    </script>
@endsection
