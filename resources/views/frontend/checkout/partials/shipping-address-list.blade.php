@foreach($UserAddressData as $key=>$data)
@php
    $isSelected = isset($selectedId) && !empty($selectedId) 
        ? ($data->id == $selectedId)
        : ($data->is_selected_address_id == 1 || ($loop->first && !$UserAddressData->contains('is_selected_address_id', 1)));
    $fullAddress = Helper::formatUserAddress($data, false);
    $phoneNumber = '(+' . $data->phonecode . ') ' . $data->phone;
    $hasMapLocation = (!empty($data->latitude) && !empty($data->longitude));
@endphp
<div class="address-group radio-group {{ $key >= 3 ? 'extra-shipping-address d-none' : '' }}" id="addressGroupItem_{{$data->id}}">
    <div class="radio-btn w-100" style="min-width: 0;">
        <input id="radioAddress{{$data->id}}" {{ $isSelected ? 'checked' : "" }} type="radio" value="{{$data->id}}" name="address_radio" class="addressRadioCurrent" data-has-location="{{ $hasMapLocation ? '1' : '0' }}" />
        <label onclick="checkDeliveryAddressByArea('{{$data->id}}')" for="radioAddress{{$data->id}}"></label>
        <div class="detail w-100" style="min-width: 0;">
            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                <span class="fw-bold text-dark text-truncate" style="max-width: 240px; font-weight: 700;" title="{{ $data->name }}">{{ $data->name }}</span>
                <span class="fw-bold text-dark text-nowrap" style="font-weight: 700;"><i class="fa fa-phone small me-1 text-muted" style="font-size: 11px;"></i>{{ $phoneNumber }}</span>
                @if($data->is_selected_address_id == 1)
                    <span class="badge bg-warning text-dark py-1 px-2" style="font-size: 10px; font-weight: 600;">Default</span>
                @endif
            </div>

            <div class="text-secondary text-truncate small mb-1" style="max-width: 500px;" title="{{ $fullAddress }}">
                <i class="fa fa-map-marker-alt text-warning me-1"></i>{{ \Illuminate\Support\Str::limit($fullAddress, 70, '...') }}
            </div>

            @if(!$hasMapLocation)
                <div class="alert alert-warning py-1 px-2 my-1 d-inline-flex align-items-center gap-2" style="font-size: 11px; border-radius: 4px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                    <span>
                        <i class="fa fa-exclamation-triangle text-danger me-1"></i>
                        <strong>Location not set on map!</strong>
                    </span>
                    <a href="javascript:void(0)" class="btn btn-xs py-0 px-2 fw-bold text-dark open-address-modal" data-type="shipping" data-action="edit" data-id="{{@$data->id}}" data-lat="{{@$data->latitude}}" data-lng="{{@$data->longitude}}" data-is-checkout="1" style="background: #fbb516; font-size: 10.5px; border-radius: 4px; white-space: nowrap;">
                        <i class="fa fa-map-marker-alt me-1"></i>Set on Map
                    </a>
                </div>
            @endif

            <div class="mt-1">
                <input type="hidden" name="checkout_page" id="checkout_page" value="1">
                <input type="hidden" name="checkout_address" id="checkout_address" value="1">
                <input type="hidden" name="address_id" id="address_id" value="{{@$data->id}}">
                <input type="hidden" class="user_address_id" name="user_address_id" value="{{@$data->id}}">
            </div>
        </div>
    </div>
    
    <a href="javascript:void(0)" class="btn-card-action btn-card-edit open-address-modal EditAddress" data-type="shipping" data-action="edit" data-id="{{@$data->id}}" data-lat="{{@$data->latitude}}" data-lng="{{@$data->longitude}}" data-is-checkout="1" title="Edit Address">
        <i class="fa fa-pen" style="font-size: 11px;"></i>
    </a>
</div>
@endforeach
