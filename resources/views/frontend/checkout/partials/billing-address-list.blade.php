@if (!$user || $user->is_guest_user != 1)
    @if($UserBillAddressData->isEmpty())
        <div class="py-3 px-3 text-center border rounded bg-light mb-3" style="border-radius: 8px;">
            <p class="text-muted small mb-2"><i class="fa fa-info-circle text-warning me-1"></i> No separate billing address saved yet.</p>
        </div>
    @else
        @foreach($UserBillAddressData as $key=>$data)
        @php
            $fullBillAddress = Helper::formatUserAddress($data, false);
            $phoneBillNumber = '(+' . $data->phonecode . ') ' . $data->phone;
            $hasBillMapLocation = (!empty($data->latitude) && !empty($data->longitude));
        @endphp
        <form class="checkout-form">
            <div class="address-group" id="billAddressGroupItem_{{$data->id}}">
                <div class="detail w-100" style="min-width: 0;">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <span class="fw-bold text-dark text-truncate" style="max-width: 240px; font-weight: 700;" title="{{ $data->name }}">{{ $data->name }}</span>
                        <span class="fw-bold text-dark text-nowrap" style="font-weight: 700;"><i class="fa fa-phone small me-1 text-muted" style="font-size: 11px;"></i>{{ $phoneBillNumber }}</span>
                        <span class="badge bg-warning text-dark py-1 px-2" style="font-size: 10px; font-weight: 600;">Default Billing</span>
                    </div>

                    <div class="text-secondary text-truncate small mb-1" style="max-width: 500px;" title="{{ $fullBillAddress }}">
                        <i class="fa fa-map-marker-alt text-warning me-1"></i>{{ \Illuminate\Support\Str::limit($fullBillAddress, 70, '...') }}
                    </div>

                    @if(!$hasBillMapLocation)
                        <div class="alert alert-warning py-1 px-2 my-1 d-inline-flex align-items-center gap-2" style="font-size: 11px; border-radius: 4px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                            <span>
                                <i class="fa fa-exclamation-triangle text-danger me-1"></i>
                                <strong>Billing location not set on map!</strong>
                            </span>
                            <a href="javascript:void(0)" class="btn btn-xs py-0 px-2 fw-bold text-dark open-address-modal" data-type="billing" data-action="edit" data-id="{{@$data->id}}" data-lat="{{@$data->latitude}}" data-lng="{{@$data->longitude}}" data-is-checkout="1" style="background: #fbb516; font-size: 10.5px; border-radius: 4px; white-space: nowrap;">
                                <i class="fa fa-map-marker-alt me-1"></i>Set on Map
                            </a>
                        </div>
                    @endif
                </div>
        
                <input type="hidden" name="bill_address_id" id="bill_address_id" value="{{@$data->id}}">
                <input type="hidden" class="user_bill_address_id" name="user_bill_address_id" id="user_bill_address_id" value="{{@$data->id}}" data-has-location="{{ $hasBillMapLocation ? '1' : '0' }}"> 
                <a href="javascript:void(0)" class="btn-card-action btn-card-edit open-address-modal EditBillAddress" data-type="billing" data-action="edit" data-id="{{@$data->id}}" data-lat="{{@$data->latitude}}" data-lng="{{@$data->longitude}}" data-is-checkout="1" title="Edit Address">
                    <i class="fa fa-pen" style="font-size: 11px;"></i>
                </a>
            </div>
        </form>
        @endforeach
    @endif
@endif
