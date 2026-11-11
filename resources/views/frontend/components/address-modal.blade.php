@php



    $modalCountryData = $countryData ?? \DB::table('countries')->where('status', 1)->orderby('name', 'ASC')->get();



@endphp



<!-- Leaflet CSS & JS (Local project assets + CDN fallback) -->



<link rel="stylesheet" href="{{ asset('dist/assets/plugins/custom/leaflet/leaflet.bundle.css') }}" />



<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />



<script src="{{ asset('dist/assets/plugins/custom/leaflet/leaflet.bundle.js') }}"></script>



<script>



    if (typeof L === 'undefined') {



        var lScript = document.createElement('script');



        lScript.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';



        document.head.appendChild(lScript);



    }



</script>



<style>



/* Point 6: Lock background body scroll cleanly when modal is open without collapsing document height */

body.modal-open {
    overflow: hidden !important;
}



/* Point 3: Hide floating cart pulse button when modal is open */





/* =========================================================================
   Standardize Address Modal Form Inputs & Dropdowns (Uniform Height & Style)
   ========================================================================= */
#unified_address_form .form-control,
#unified_address_form .form-select {
    height: 38px !important;
    min-height: 38px !important;
    max-height: 38px !important;
    font-size: 13px !important;
    line-height: 1.5 !important;
    padding: 6px 12px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
    box-sizing: border-box !important;
    box-shadow: none !important;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
}

#unified_address_form .form-control::placeholder {
    color: #94a3b8 !important;
    font-size: 13px !important;
    opacity: 1 !important;
}

/* Dropdown specific styling: Clean modern SVG chevron, proper alignment */
#unified_address_form .form-select {
    padding-right: 32px !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    background-size: 12px 10px !important;
    cursor: pointer !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
}

/* Phone input group: seamless joining and identical height */
#unified_address_form .input-group {
    display: flex !important;
    align-items: stretch !important;
    width: 100% !important;
}

#unified_address_form .input-group > #modal_phonecode {
    flex: 0 0 72px !important;
    width: 72px !important;
    max-width: 72px !important;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    border-right: 1px solid #cbd5e1 !important;
    padding-left: 6px !important;
    padding-right: 18px !important;
    background-position: right 4px center !important;
    font-size: 12px !important;
    cursor: pointer !important;
}

#unified_address_form .input-group > #modal_phone {
    flex: 1 1 auto !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

#modal_zip_code {
    text-transform: uppercase !important;
}

/* Focus States */
#unified_address_form .form-control:focus,
#unified_address_form .form-select:focus {
    border-color: #fbb516 !important;
    box-shadow: 0 0 0 0.2rem rgba(251, 181, 22, 0.25) !important;
    outline: none !important;
    z-index: 3 !important;
}

/* Validation Invalid States */
#unified_address_form .form-control.is-invalid, 
#unified_address_form .form-select.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
    z-index: 2 !important;
}

#addressModal label.error-text {
    font-size: 11.5px !important;
    color: #dc3545 !important;
    font-weight: 500 !important;
    margin-top: 3px !important;
    margin-bottom: 0 !important;
    display: none;
}

#unified_address_form .form-label {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    margin-bottom: 4px !important;
    line-height: 1.3 !important;
    display: inline-block !important;
}

/* Proper Centered Popup Modal Styling & Responsiveness */



#addressModal.modal {

    padding: 0 !important;

}



@media (min-width: 768px) {

    #addressModal.modal {

        overflow-y: hidden !important;

    }

}



/* Clean backdrop for address modal only without breaking other modals */

#addressModal ~ .modal-backdrop,

.modal-backdrop.show {

    opacity: 0.6 !important;

}



#addressModal .modal-dialog {

    max-width: 1040px;

    width: 95%;

    margin: 1rem auto;

}



#addressModal .modal-content {

    border-radius: 12px;

    overflow: hidden;

    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);

    border: 1px solid rgba(255, 255, 255, 0.15);

    max-height: calc(100vh - 2rem);

    display: flex;

    flex-direction: column;

    background: #ffffff;

}



#addressModal .modal-header {

    padding: 7px 14px !important;

    border-top-left-radius: 12px;

    border-top-right-radius: 12px;

    flex-shrink: 0;

}



/* Point 2: Scrollbar elimination on standard screens */

#addressModal,
#addressModal .modal-dialog,
#addressModal .modal-content,
#addressModal .modal-body,
#addressModal .modal-form-col,
#unified_address_form {
    scroll-behavior: auto !important;
    overscroll-behavior: contain !important;
}

#addressModal .modal-body {

    flex: 1 1 auto;

    overflow-y: auto;

    overflow-x: hidden;

    background: #f8fafc;

    padding: 6px 12px !important;

    scrollbar-width: thin;

}



#addressModal .modal-body::-webkit-scrollbar {

    width: 5px;

}

#addressModal .modal-body::-webkit-scrollbar-track {

    background: transparent;

}

#addressModal .modal-body::-webkit-scrollbar-thumb {

    background: #cbd5e1;

    border-radius: 4px;

}



@media (max-width: 991.98px) {
    #addressModal .modal-dialog {
        max-width: 95%;
        margin: 0.5rem auto;
    }
    #addressModal .modal-content {
        max-height: calc(100vh - 1rem);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
    }
    #addressModal .modal-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        flex-shrink: 0;
    }
    #addressModal .modal-body {
        padding: 6px 10px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
    }
}

@media (max-width: 575.98px) {
    #addressModal .modal-dialog {
        width: calc(100% - 12px);
        margin: 6px auto;
        max-width: 100%;
    }
    #addressModal .modal-content {
        max-height: calc(100vh - 12px);
        border-radius: 12px;
    }
    #addressModal .modal-header {
        padding: 7px 10px !important;
    }
    #addressModal .modal-body {
        padding: 4px 8px !important;
    }
}



/* Rapido / Uber Style Fixed Center Pin Marker & Compact Map Container */

.rapido-map-container {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    background: #e5e7eb;
}

/* Fixed Map Column & Scrollable Form Column on Desktop */
@media (min-width: 992px) {
    .modal-map-col {
        height: 100% !important;
        min-height: 480px !important;
        max-height: 520px !important;
        position: sticky !important;
        top: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-start !important;
    }
    .modal-map-col .rapido-map-container {
        flex: 1 1 auto !important;
        height: 420px !important;
        min-height: 400px !important;
        max-height: 440px !important;
        width: 100% !important;
    }
    .modal-map-status-bar {
        flex: 0 0 auto !important;
        min-height: 38px !important;
        margin-top: 8px !important;
        width: 100% !important;
    }
    .modal-form-col {
        max-height: 520px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        scrollbar-width: thin;
    }
    .modal-form-col::-webkit-scrollbar {
        width: 5px;
    }
    .modal-form-col::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
}

@media (max-width: 991.98px) {
    #addressModal .modal-content-row {
        display: block !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .modal-map-col {
        position: sticky !important;
        top: 0 !important;
        z-index: 100 !important;
        background: #f8fafc !important;
        width: 100% !important;
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
        padding: 0 0 6px 0 !important;
        margin-bottom: 8px !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }
    .modal-map-col .rapido-map-container {
        min-height: 210px !important;
        height: 210px !important;
        max-height: 210px !important;
    }
    .modal-map-status-bar {
        margin-top: 8px !important;
        width: 100% !important;
    }
    .modal-form-col {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        padding: 0 0 16px 0 !important;
    }

    /* Map Controls on Mobile */
    #modal_address_map .leaflet-bottom.leaflet-right {
        bottom: 10px !important;
        right: 10px !important;
    }
    #modal_address_map .leaflet-control-zoom a {
        width: 28px !important;
        height: 28px !important;
        line-height: 28px !important;
        font-size: 14px !important;
    }
    #btn_modal_locate_me {
        bottom: 74px !important;
        right: 10px !important;
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
    }
    #btn_modal_locate_me svg {
        width: 18px !important;
        height: 18px !important;
    }
}

@media (max-width: 575.98px) {
    .modal-map-col {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
    }
    .modal-map-col .rapido-map-container {
        min-height: 180px !important;
        height: 180px !important;
        max-height: 180px !important;
    }
}

@media (max-width: 991.98px) and (max-height: 500px) {
    .modal-map-col {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
    }
    .modal-map-col .rapido-map-container {
        min-height: 140px !important;
        height: 140px !important;
        max-height: 140px !important;
    }
    #btn_modal_locate_me {
        bottom: 52px !important;
    }
}

/* Map Controls Separation: Zoom on Bottom-Right, Locate Me above Zoom */
#modal_address_map .leaflet-bottom.leaflet-right {
    bottom: 12px !important;
    right: 12px !important;
    z-index: 450 !important;
}
#modal_address_map .leaflet-control-zoom {
    margin: 0 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18) !important;
    border: 1px solid #cbd5e1 !important;
}
#modal_address_map .leaflet-control-zoom a {
    width: 30px !important;
    height: 30px !important;
    line-height: 30px !important;
    font-size: 15px !important;
    color: #1e293b !important;
    background: #ffffff !important;
}
#btn_modal_locate_me {
    position: absolute !important;
    bottom: 80px !important;
    right: 12px !important;
    left: auto !important;
    top: auto !important;
    z-index: 500 !important;
    width: 36px !important;
    height: 36px !important;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 50% !important;
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    color: #2563eb !important;
    transition: all 0.2s ease-in-out !important;
    outline: none !important;
}

/* Location Status Bar Below Map */
.modal-map-status-bar {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    line-height: 1.4;
    box-sizing: border-box;
    width: 100%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

#btn_modal_locate_me:hover {
    background-color: #eff6ff !important;
    border-color: #3b82f6 !important;
    color: #1d4ed8 !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28) !important;
    transform: translateY(-1px);
}

#btn_modal_locate_me:active {
    transform: scale(0.95);
    background-color: #dbeafe !important;
}

#btn_modal_locate_me svg {
    width: 20px !important;
    height: 20px !important;
    display: block !important;
    pointer-events: none !important;
    flex-shrink: 0 !important;
}

#btn_modal_locate_me.locating svg {
    animation: locatePulse 1s infinite alternate;
}

@keyframes locatePulse {
    0% { transform: scale(0.9); opacity: 0.6; }
    100% { transform: scale(1.15); opacity: 1; }
}



/* Point 3: 100% Full Width Floating location search dropdown on map */

.map-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    background: #ffffff;
    border-radius: 0 0 8px 8px;
    max-height: 220px;
    overflow-y: auto;
    overflow-x: hidden !important;
    z-index: 1060;
    margin-top: 2px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.map-search-dropdown::-webkit-scrollbar {
    width: 5px;
}
.map-search-dropdown::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.map-search-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.map-search-dropdown .suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    font-size: 12px;
    line-height: 1.35;
    transition: background 0.15s ease;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow: hidden;
}

.map-search-dropdown .suggestion-item:last-child {
    border-bottom: none;
}

.map-search-dropdown .suggestion-item:hover {
    background: #f8fafc;
}

.map-search-dropdown .suggestion-icon {
    flex-shrink: 0;
    width: 16px;
    margin-top: 2px;
}

.map-search-dropdown .suggestion-text-wrap {
    flex: 1 1 0%;
    min-width: 0;
    max-width: 100%;
    overflow: hidden;
}

.map-search-dropdown .suggestion-main-text {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.map-search-dropdown .suggestion-sub-text {
    display: block;
    font-size: 11px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    margin-top: 1px;
}

.rapido-center-marker {



    position: absolute;



    top: 50%;



    left: 50%;



    transform: translate(-50%, -100%);



    pointer-events: none;



    z-index: 450;



    display: flex;



    flex-direction: column;



    align-items: center;



    transition: transform 0.16s cubic-bezier(0.175, 0.885, 0.32, 1.275);



}



/* While map is dragging/panning: pin lifts up smoothly */



.rapido-center-marker.is-moving {



    transform: translate(-50%, -125%) scale(1.08);



}



.rapido-pin-icon {



    line-height: 1;



}



.rapido-pin-shadow {



    width: 14px;



    height: 6px;



    background: rgba(0, 0, 0, 0.35);



    border-radius: 50%;



    margin-top: -3px;



    filter: blur(1.5px);



    transition: transform 0.16s ease, opacity 0.16s ease;



}



.rapido-center-marker.is-moving .rapido-pin-shadow {



    transform: scale(0.5);



    opacity: 0.25;



}



/* Point 7: Full width street address suggestions dropdown */



#address_suggestions_dropdown {



    position: absolute;



    top: 100%;



    left: 0;



    right: 0;



    width: 100%;



    background: #ffffff;



    border: 1px solid #cbd5e1;



    border-top: none;



    border-radius: 0 0 10px 10px;



    max-height: 260px;



    overflow-y: auto;



    z-index: 1080;



    box-shadow: 0 12px 28px rgba(0,0,0,0.2);



    margin-top: 2px;



}



#address_suggestions_dropdown .suggestion-item {



    padding: 11px 15px;



    cursor: pointer;



    border-bottom: 1px solid #f1f5f9;



    font-size: 13px;



    line-height: 1.4;



    transition: background 0.15s ease;



    display: flex;



    align-items: flex-start;



    gap: 10px;



}



#address_suggestions_dropdown .suggestion-item:last-child {



    border-bottom: none;



}



#address_suggestions_dropdown .suggestion-item:hover,



#address_suggestions_dropdown .suggestion-item.active {



    background: #f8fafc;



}



#address_suggestions_dropdown .suggestion-icon {



    flex-shrink: 0;



    margin-top: 2px;



}



#address_suggestions_dropdown .suggestion-title {



    font-weight: 600;



    color: #0f172a;



    font-size: 13px;



    display: block;



}



#address_suggestions_dropdown .suggestion-sub {



    font-size: 11px;



    color: #64748b;



    display: block;



    margin-top: 2px;



}



/* Toggle pills */



.address-type-pill {



    color: #475569;



    background: transparent;



    border: none;



    font-weight: 600;



}



.address-type-pill.active {



    background: #1e293b !important;



    color: #ffffff !important;



    box-shadow: 0 2px 6px rgba(0,0,0,0.2);



}



.text-red-point {



    color: #dc2626;



}




/* Delivery options radio group & container matching checkout page design */
#addressModal .delivery-instruction-box {
    border: 1px solid #DDDDDD;
    margin-bottom: 12px;
    padding: 14px 18px 10px 18px;
    background: #ffffff;
    border-radius: 4px;
}
#addressModal .delivery-instruction-box .radio-group {
    display: inline-flex;
    align-items: center;
}
#addressModal .delivery-instruction-box .radio-group input[type="radio"] {
    opacity: 0;
    display: none;
}
#addressModal .delivery-instruction-box .radio-group input[type="radio"] + label {
    cursor: pointer;
    position: relative;
    padding-left: 28px;
    margin: 0;
    font-size: 14px;
    line-height: 22px;
    font-weight: 500;
    color: #242424;
}
#addressModal .delivery-instruction-box .radio-group input[type="radio"] + label::before {
    border: 2px solid #96999c;
    border-radius: 50%;
    content: "";
    height: 18px;
    width: 18px;
    left: 0;
    position: absolute;
    top: 2px;
    box-sizing: border-box;
}
#addressModal .delivery-instruction-box .radio-group input[type="radio"] + label::after {
    border-radius: 50%;
    content: "";
    height: 10px;
    width: 10px;
    left: 4px;
    position: absolute;
    top: 6px;
    box-sizing: border-box;
}
#addressModal .delivery-instruction-box .radio-group input[type="radio"]:checked + label::before {
    border: 2px solid #fbb516;
}
#addressModal .delivery-instruction-box .radio-group input[type="radio"]:checked + label::after {
    background: #fbb516;
}
#addressModal .delivery-instruction-box textarea {
    width: 100%;
    border: 1px solid #DDDDDD;
    border-radius: 4px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: #242424;
    background: #fff;
    outline: none;
    transition: border-color 0.3s;
    font-family: inherit;
}
#addressModal .delivery-instruction-box textarea:focus {
    border-color: #fbb516 !important;
    outline: none !important;
    box-shadow: none !important;
}
</style>



<!-- Unified Centered Address Popup Modal -->



<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true" data-bs-backdrop="static">



    <div class="modal-dialog modal-xl modal-dialog-centered">



        <div class="modal-content border-0">



            <!-- Modal Header -->



            <div class="modal-header py-2 px-3 px-md-4 d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #fff; border-bottom: 2px solid #fbb516;">



                <div class="d-flex align-items-center gap-2">



                    <div style="width: 34px; height: 34px; background: rgba(251, 181, 22, 0.18); border-radius: 50%; display: flex; align-items: center; justify-content: center;">



                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#fbb516">



                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>



                        </svg>



                    </div>



                    <div>



                        <h5 class="modal-title mb-0 text-white fw-bold" id="addressModalLabel" style="font-size: 17px;">



                            {{ @Helper::language('add_address') }}



                        </h5>



                    </div>



                </div>



                <!-- Point 1: 100% Reliable Inline SVG Cross (X) Close Icon -->



                <button type="button" class="btn p-0 border-0 modal-close-btn" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" 



                        style="background: rgba(255,255,255,0.18); border-radius: 50%; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;"



                        onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">



                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;">



                        <line x1="18" y1="6" x2="6" y2="18"></line>



                        <line x1="6" y1="6" x2="18" y2="18"></line>



                    </svg>



                </button>



            </div>



            <div class="modal-body p-2 p-md-3">



                <div class="container-fluid p-0">



                    <!-- Address Type Switcher -->



                    <div class="row mb-1 modal-type-pills-row">



                        <div class="col-12 col-md-6 col-lg-4 mx-auto">



                            <div class="address-type-toggle-wrapper d-flex justify-content-center p-1" style="background: #e2e8f0; border-radius: 30px;">



                                <button type="button" class="btn address-type-pill active flex-fill py-1" id="pill_shipping" onclick="switchAddressModalType('shipping')" style="border-radius: 25px; font-size: 13px;">



                                    Shipping Address



                                </button>



                                <button type="button" class="btn address-type-pill flex-fill py-1" id="pill_billing" onclick="switchAddressModalType('billing')" style="border-radius: 25px; font-size: 13px;">



                                    Billing Address



                                </button>



                            </div>



                        </div>



                    </div>



                    <!-- 2 Columns Layout on Desktop / Stacked on Mobile (Point 2: Compact g-2 gutter) -->



                    <div class="row g-2 align-items-start modal-content-row" style="margin-top: 0;">

                        <!-- Left Side: Rapido / Uber Style Map (Fixed height, sticky) -->

                        <div class="col-12 col-lg-6 d-flex flex-column modal-map-col">



                            <div class="rapido-map-container flex-fill">



                                <!-- The Map container -->



                                <div id="modal_address_map" style="width: 100%; height: 100%;"></div>



                                <!-- Point 3: Full Width Floating Location Search Bar over Map (Spanning 100% full width of map) -->

                                <div class="map-floating-search-wrapper" style="position: absolute; top: 10px; left: 10px; right: 10px; z-index: 1050;">

                                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #ffffff; width: 100%;">

                                        <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted" style="border-color: #cbd5e1;">

                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">

                                                <circle cx="11" cy="11" r="8"></circle>

                                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>

                                            </svg>

                                        </span>

                                        <input type="text" id="map_location_search_input" class="form-control border-start-0 py-1 px-2" 

                                               placeholder="Search location, street, or landmark on map..." 

                                               autocomplete="off" 

                                               style="font-size: 12px; border-color: #cbd5e1; box-shadow: none;">

                                        <span class="input-group-text bg-white border-start-0 py-1 px-2 d-none" id="map_search_spinner" style="border-color: #cbd5e1;">

                                            <span class="spinner-border spinner-border-sm text-warning" style="width: 13px; height: 13px;"></span>

                                        </span>

                                    </div>

                                    <!-- Search Suggestions Dropdown for Map (100% full width of search bar and map) -->

                                    <div id="map_search_suggestions" class="map-search-dropdown d-none shadow-lg"></div>

                                </div>



                                <!-- Fixed Center Pin Marker (Clean SVG Pin, no confusing text bubble) -->



                                <div class="rapido-center-marker" id="rapido_pin_container">



                                    <div class="rapido-pin-icon" id="rapido_pin_icon">



                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="#dc2626" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.45));">



                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>



                                        </svg>



                                    </div>



                                    <div class="rapido-pin-shadow" id="rapido_pin_shadow"></div>



                                </div>



                                <!-- Floating Locate Me Button (Clean GPS Target placed bottom-right above zoom control) -->
                                <button type="button" class="btn p-0" id="btn_modal_locate_me" 
                                        onclick="modalLocateCurrentPosition()" title="Locate Me" aria-label="Locate Me">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <circle cx="12" cy="12" r="2.5" fill="currentColor"></circle>
                                        <line x1="12" y1="1.5" x2="12" y2="4.5"></line>
                                        <line x1="12" y1="19.5" x2="12" y2="22.5"></line>
                                        <line x1="1.5" y1="12" x2="4.5" y2="12"></line>
                                        <line x1="19.5" y1="12" x2="22.5" y2="12"></line>
                                    </svg>
                                </button>

                            </div>

                            <!-- Bottom Status Bar Below Map (Map visible cleanly without overlay) -->
                            <div class="modal-map-status-bar" id="modal_map_status_bar">
                                <span id="modal_map_status"></span>
                            </div>

                        </div>



                        <!-- Right Side: Address Form Fields (Scrollable container) -->

                        <div class="col-12 col-lg-6 d-flex flex-column modal-form-col">



                            <div class="card border shadow-none flex-fill" style="border-radius: 10px; background: #ffffff; border-color: #e2e8f0 !important;">



                                <div class="card-body p-2 d-flex flex-column" style="padding: 6px 10px !important;">



                                    <div id="modal_alert_box"></div>



                                    <form id="unified_address_form" autocomplete="off" novalidate class="flex-fill d-flex flex-column">



                                        <input type="hidden" name="address_id" id="modal_address_id" value="">



                                        <input type="hidden" name="address_type" id="modal_address_type" value="shipping">



                                        <input type="hidden" name="action_type" id="modal_action_type" value="add">



                                        <input type="hidden" name="checkout_page" id="modal_checkout_page" value="0">



                                        <input type="hidden" name="latitude" id="modal_latitude" value="">



                                        <input type="hidden" name="longitude" id="modal_longitude" value="">



                                        <div class="row g-2 flex-fill">



                                            <!-- Full Name (Original: name_label_web) -->



                                            <div class="col-12 col-md-6">



                                                <div class="form-group mb-1">



                                                    <label for="modal_name" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('name_label_web') }} <span class="text-red-point">*</span>



                                                    </label>



                                                    <input type="text" name="name" id="modal_name" class="form-control form-control-sm" placeholder="{{ @Helper::language('enter_name_web') }}" maxlength="40">
                                                    <label id="modal_name_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- Phone (Original: phone_number) -->



                                            <div class="col-12 col-md-6">



                                                <div class="form-group mb-1">



                                                    <label for="modal_phone" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('phone_number') }} <span class="text-red-point">*</span>



                                                    </label>



                                                    <div class="input-group input-group-sm">



                                                        <select class="form-select form-select-sm" name="phonecode" id="modal_phonecode">



                                                            @foreach($modalCountryData as $c)



                                                                <option value="{{ $c->phonecode }}" {{ $loop->first ? "selected" : "" }}>



                                                                    +{{ $c->phonecode }}



                                                                </option>



                                                            @endforeach



                                                        </select>



                                                        <input type="tel" name="phone" id="modal_phone" class="form-control form-control-sm" placeholder="{{ @Helper::language('enter_phone_number_place') }}" minlength="8" maxlength="15">



                                                    </div>
                                                    <label id="modal_phone_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- Country (Original: country_label_web) -->



                                            <div class="col-12 col-md-6">



                                                <div class="form-group mb-1">



                                                    <label for="modal_country_id" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('country_label_web') }} <span class="text-red-point">*</span>



                                                    </label>



                                                    <select name="country_id" id="modal_country_id" class="form-select form-select-sm" onchange="modalGetSubCatList(this.value)" required>



                                                        <option value="">{{ @Helper::language('choose_country_web') }}</option>



                                                        @foreach($modalCountryData as $c)



                                                            <option value="{{ $c->id }}" data-phonecode="{{ $c->phonecode }}" {{ $loop->first ? "selected" : "" }}>



                                                                {{ $c->name }}



                                                            </option>



                                                        @endforeach



                                                    </select>
                                                    <label id="modal_country_id_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- Region (Original: region_label_web) -->



                                            <div class="col-12 col-md-6">



                                                <div class="form-group mb-1">



                                                    <label for="modal_region_id" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('region_label_web') }} <span class="text-red-point">*</span>



                                                        <span id="modal_region_loader" class="spinner-border spinner-border-sm text-secondary d-none ms-1" style="width: 12px; height: 12px;"></span>



                                                    </label>



                                                    <select name="region_id" id="modal_region_id" class="form-select form-select-sm" onchange="modalGetAreaList(this.value)" required>



                                                        <option value="">{{ @Helper::language('choose_region_web') }}</option>



                                                    </select>
                                                    <label id="modal_region_id_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- City (Original: city_label) -->



                                            <div class="col-12 col-md-6">



                                                <div class="form-group mb-1">



                                                    <label for="modal_city" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('city_label') }} <span class="text-red-point">*</span>



                                                    </label>



                                                    <input type="text" name="city" id="modal_city" class="form-control form-control-sm" placeholder="{{ @Helper::language('enter_city_label') }}">
                                                    <label id="modal_city_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- Area (Original: area_label_web) -->



                                            <div class="col-12 col-md-6">



                                                <div class="form-group mb-1">



                                                    <label for="modal_area_id" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('area_label_web') }} <span class="text-red-point">*</span>



                                                        <span id="modal_area_loader" class="spinner-border spinner-border-sm text-secondary d-none ms-1" style="width: 12px; height: 12px;"></span>



                                                    </label>



                                                    <select name="area_id" id="modal_area_id" class="form-select form-select-sm" required>



                                                        <option value="">{{ @Helper::language('choose_area_web') }}</option>



                                                    </select>
                                                    <label id="modal_area_id_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- Street Address (Pure User Input, untouched by map movement) -->



                                            <div class="col-12">



                                                <div class="form-group mb-1">



                                                    <label for="modal_address" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('street_address') }} <span class="text-red-point">*</span>



                                                    </label>



                                                    <input type="text" name="address" id="modal_address" class="form-control form-control-sm" 



                                                           placeholder="{{ @Helper::language('enter_street_address') }}" autocomplete="off">
                                                    <label id="modal_address_error" class="error text-danger error-text"></label>



                                                    <div id="modal_address_location_alert" class="d-none mt-1 text-danger fw-semibold" style="font-size: 11px;">

                                                        <i class="fa fa-exclamation-triangle me-1"></i> Please set your pickup/delivery location on the map.

                                                    </div>



                                                </div>



                                            </div>



                                            <!-- Zip Code (Original: zip_code_label) -->



                                            <div class="col-12">



                                                <div class="form-group mb-1">



                                                    <label for="modal_zip_code" class="form-label mb-1" style="font-size: 12px; font-weight: 600;">



                                                        {{ @Helper::language('zip_code_label') }} <span class="text-red-point">*</span>



                                                    </label>



                                                    <input type="text" name="zip_code" id="modal_zip_code" class="form-control form-control-sm" placeholder="{{ @Helper::language('enter_zip_code') }}" style="text-transform: uppercase;" autocomplete="off">
                                                    <label id="modal_zip_code_error" class="error text-danger error-text"></label>



                                                </div>



                                            </div>



                                            <!-- Checkout-specific Shipping Options (Drop-off Options & Delivery Instructions) -->



                                            <div id="modal_checkout_shipping_options" class="col-12 d-none">
                                                <div class="form-group delivery-instruction-box">
                                                    <label for="modal_instruction" style="font-size: 14px; font-weight: 600; color: #242424; margin-bottom: 8px; display: block;">Delivery Instructions (Optional)</label>
                                                    
                                                    <div class="mt-2 mb-2">
                                                        <label style="font-size: 13.5px; font-weight: 500; color: #333333; margin-bottom: 6px; display: block;">Drop off Options&nbsp;&nbsp;</label>
                                                        <div class="d-flex align-items-center flex-wrap" style="gap: 24px;">
                                                            <div class="radio-group">
                                                                <input id="modal_delivery_opt_1" class="radioPurchase" type="radio" name="delivery_options" value="1" checked/>
                                                                <label for="modal_delivery_opt_1">Hand over to Me</label>
                                                            </div>
                                                            <div class="radio-group">
                                                                <input id="modal_delivery_opt_2" class="radioPurchase" type="radio" name="delivery_options" value="2"/>
                                                                <label for="modal_delivery_opt_2">Leave it at my Door</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <p class="sidebar-item" style="font-size: 12.5px; color: #858584; margin-top: 10px; margin-bottom: 6px;">Please advise us of any special requirements,if applicable</p>
                                                    <div class="form-group mb-0">
                                                        <textarea name="instruction" id="modal_instruction" col="2" rows="1" placeholder="E.g. Leave at front door"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Shipping Specific Option (Default Delivery Address Checkbox) -->
                                            <div id="modal_shipping_default_fields" class="col-12">
                                                <div class="my-2 p-2 border rounded-3 bg-light d-flex align-items-center gap-2" style="cursor: pointer;" onclick="if(event.target.tagName !== 'INPUT'){ $('#modal_is_default_shipping').click(); }">
                                                    <input class="form-check-input m-0" type="checkbox" name="isDefaultShipping" id="modal_is_default_shipping" value="1" checked style="cursor: pointer; flex-shrink: 0; width: 17px; height: 17px;">
                                                    <label class="form-check-label m-0 text-dark" for="modal_is_default_shipping" style="font-size: 12.5px; font-weight: 600; cursor: pointer; line-height: 1.2;">
                                                        Make this my default delivery address
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Billing Specific Option -->
                                            <div id="modal_billing_fields" class="col-12 d-none">
                                                <input type="hidden" name="isDefault" id="modal_is_default" value="1">
                                            </div>



                                        </div>



                                        <!-- Modal Actions Footer -->



                                        <div class="d-flex justify-content-end gap-2 pt-2 mt-auto border-top">



                                            <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal" data-dismiss="modal">



                                                Cancel



                                            </button>



                                            <button type="submit" class="btn btn-sm px-4 fw-bold text-dark" id="btn_save_unified_address" style="background: #fbb516;">



                                                <span class="spinner-border spinner-border-sm me-1 d-none" id="btn_save_spinner" role="status"></span>



                                                <span id="btn_save_text">{{ @Helper::language('save_details_btn') }}</span>



                                            </button>



                                        </div>



                                    </form>



                                </div>



                            </div>



                        </div>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>



@php

    $allowedLocationsTree = \App\Models\Country::where('status', 1)->get()->map(function($c) {

        $regions = \App\Models\Region::where('country_id', $c->id)->where('status', 1)->get()->map(function($r) {

            $areas = \App\Models\Area::where('region_id', $r->id)->where('status', 1)->get()->map(function($a) {

                return [

                    'id' => $a->id,

                    'title' => $a->title

                ];

            });

            return [

                'id' => $r->id,

                'title' => $r->title,

                'areas' => $areas->values()->toArray()

            ];

        });

        return [

            'id' => $c->id,

            'name' => $c->name,

            'code' => $c->shortname,

            'phonecode' => $c->phonecode,

            'regions' => $regions->values()->toArray()

        ];

    });

@endphp

<script>

    // System Serviceable Locations Pool (Countries, Regions, Areas configured in Admin / Database)

    window.allowedServiceLocations = {!! json_encode($allowedLocationsTree) !!};



    var DEFAULT_MAP_CENTER = { lat: 26.4691, lng: 74.6390, zoom: 13 }; // Ajmer Hub (Default Serviceable Center)



    function getActiveCountryCenter() {

        return DEFAULT_MAP_CENTER;

    }





    // =========================================================================

    // SERVICEABLE LOCATIONS ENGINE (Allowed Countries, Regions, Areas from DB)

    // =========================================================================

    var isLocationServiceable = false;



    function getAllowedCountryCodes() {

        if (!window.allowedServiceLocations || !window.allowedServiceLocations.length) return ['gh', 'in'];

        return window.allowedServiceLocations.map(function(c) {

            return (c.code || '').toLowerCase();

        }).filter(Boolean);

    }



    function getServiceableSummaryText() {

        if (!window.allowedServiceLocations || !window.allowedServiceLocations.length) {

            return 'Configured delivery locations';

        }

        return window.allowedServiceLocations.map(function(c) {

            var regStr = (c.regions || []).map(function(r) {

                var areaStr = (r.areas || []).map(function(a) { return a.title; }).join(', ');

                return r.title + (areaStr ? ' (' + areaStr + ')' : '');

            }).join('; ');

            return c.name + (regStr ? ' [' + regStr + ']' : '');

        }).join(' | ');

    }



    function checkLocationServiceability(a, lat, lng) {

        if (!window.allowedServiceLocations || !window.allowedServiceLocations.length) {

            return { serviceable: true };

        }

        if (!a) {

            return { serviceable: false, message: 'Could not resolve location. Please select on map.' };

        }



        var countryCode = (a.country_code || '').toLowerCase().trim();

        var countryName = (a.country || '').toLowerCase().trim();



        // 1. Country Check

        var matchedCountry = null;

        for (var i = 0; i < window.allowedServiceLocations.length; i++) {

            var c = window.allowedServiceLocations[i];

            var cCode = (c.code || '').toLowerCase().trim();

            var cName = (c.name || '').toLowerCase().trim();

            if (countryCode && cCode && countryCode === cCode) {

                matchedCountry = c;

                break;

            }

            if (countryName && cName && (countryName.indexOf(cName) !== -1 || cName.indexOf(countryName) !== -1)) {

                matchedCountry = c;

                break;

            }

        }



        var isBill = ($('#modal_address_type').val() === 'billing');

        if (!matchedCountry) {

            return {

                serviceable: false,

                message: (isBill ? 'Billing not available in ' : 'Delivery not available in ') + (a.country || 'this country') + '. Only Available in: ' + window.allowedServiceLocations.map(function(x){return x.name;}).join(', ')

            };

        }



        // 2. Region Check

        var stateName = (a.state || a.region || a.province || '').toLowerCase().trim();

        var matchedRegion = null;

        if (matchedCountry.regions && matchedCountry.regions.length > 0) {

            for (var j = 0; j < matchedCountry.regions.length; j++) {

                var r = matchedCountry.regions[j];

                var rTitle = r.title.toLowerCase().trim();

                if (stateName && (stateName.indexOf(rTitle) !== -1 || rTitle.indexOf(stateName) !== -1)) {

                    matchedRegion = r;

                    break;

                }

            }

        }



        if (!matchedRegion) {

            var regList = matchedCountry.regions.map(function(x){return x.title;}).join(', ');

            return {

                serviceable: false,

                country: matchedCountry,

                message: (isBill ? 'Billing not available in ' : 'Delivery not available in ') + (a.state || 'this region') + '. Only Available in: ' + regList

            };

        }



        // 3. Area Check (Strict administrative entity matching - NO fake coordinate boxes!)

        var matchedArea = null;

        var searchFields = [

            a.city || '',

            a.town || '',

            a.village || '',

            a.suburb || '',

            a.neighbourhood || '',

            a.municipality || '',

            a.district || '',

            a.state_district || '',

            a.county || ''

        ];



        if (matchedRegion.areas && matchedRegion.areas.length > 0) {

            for (var k = 0; k < matchedRegion.areas.length; k++) {

                var ar = matchedRegion.areas[k];

                var aTitle = ar.title.toLowerCase().trim();

                var safeRegex = new RegExp('\\b' + aTitle.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + '\\b', 'i');



                for (var f = 0; f < searchFields.length; f++) {

                    var fieldVal = searchFields[f].trim();

                    if (!fieldVal) continue;

                    // Overarching administrative divisions (e.g. "Ajmer Division" covers Nagaur/Bhilwara) are NOT delivery areas

                    if (/\bdivision\b/i.test(fieldVal)) continue;

                    // Road / Highway names (e.g. "Ajmer Road", "Jaipur Expressway") are NOT delivery areas

                    if (/\b(road|highway|expressway|bypass|marg|path)\b/i.test(fieldVal)) continue;



                    if (safeRegex.test(fieldVal)) {

                        matchedArea = ar;

                        break;

                    }

                }

                if (matchedArea) break;

            }

        }



        if (!matchedArea) {

            var detectedLoc = a.village || a.suburb || a.town || a.city || a.county || a.state_district || '';

            if (!detectedLoc) detectedLoc = 'this location';



            var areaList = matchedRegion.areas.map(function(x){return x.title;}).join(', ');

            return {

                serviceable: false,

                country: matchedCountry,

                region: matchedRegion,

                message: (isBill ? 'Billing not available in ' : 'Delivery not available in ') + detectedLoc + '. Only Available in: ' + areaList

            };

        }



        return {

            serviceable: true,

            country: matchedCountry,

            region: matchedRegion,

            area: matchedArea

        };

    }



    function syncDropdownsWithServiceableLocation(match) {

        if (!match || !match.country || !match.region || !match.area) return;



        var cId = match.country.id;

        var rId = match.region.id;

        var aId = match.area.id;



        // 1. Country

        if ($('#modal_country_id').val() != cId) {

            $('#modal_country_id').val(cId);

            if (match.country.phonecode) {

                $('#modal_phonecode').val(match.country.phonecode);

            }

        }



        // 2. Region

        var curRegId = $('#modal_region_id').val();

        if (curRegId != rId) {

            var regHtml = '<option value="">{{ @Helper::language("choose_region_web") }}</option>';

            match.country.regions.forEach(function(r) {

                var sel = (r.id == rId) ? 'selected' : '';

                regHtml += '<option value="' + r.id + '" ' + sel + '>' + r.title + '</option>';

            });

            $('#modal_region_id').html(regHtml);

        }



        // 3. Area

        var curAreaId = $('#modal_area_id').val();

        if (curAreaId != aId) {

            var areaHtml = '<option value="">{{ @Helper::language("choose_area_web") }}</option>';

            match.region.areas.forEach(function(a) {

                var sel = (a.id == aId) ? 'selected' : '';

                areaHtml += '<option value="' + a.id + '" ' + sel + '>' + a.title + '</option>';

            });

            $('#modal_area_id').html(areaHtml);

        }

    }

    // State Variables



    var addressMap = null;



    var leafletMap = null;



    var usingLeaflet = false;



    var currentMarkerLat = null;



    var currentMarkerLng = null;



    var isSyncingFromMap = false;

    var hasLocationBeenSet = false;



    var lockAddressInput = false;



    var userChosenAddress = null;



    var geocoder = null;



    var reverseGeocodeDebounceTimer = null;



    var streetSearchDebounceTimer = null;



    // Routes passed from Blade



    var urlGetSubcatlist = "{{ route('getsubcatlist') }}";



    var urlGetArealist = "{{ route('getarealist') }}";



    var urlStoreAddress = "{{ route('store-address') }}";



    var urlUpdateAddress = "{{ route('upadte-address') }}";



    var urlStoreBillAddress = "{{ route('store-bill-address') }}";



    var urlUpdateBillAddress = "{{ route('update-bill-address') }}";



    var urlGetAddressDetailsBase = "{{ url('/get-address-details') }}";



    var csrfToken = "{{ csrf_token() }}";



    var modalAddressCache = { shipping: null, billing: null };

    var modalActiveIds = { shipping: '', billing: '' };

    window.modalAddressCache = modalAddressCache;

    window.modalActiveIds = modalActiveIds;



    // Global listener for Google Maps auth failure



    window.googleMapsAuthFailed = window.googleMapsAuthFailed || false;



    window.gm_authFailure = function() {



        console.warn("Google Maps Auth Failure detected. Switching to OpenStreetMap...");



        window.googleMapsAuthFailed = true;



        switchToLeafletMap();



    };



    window.switchToLeafletMap = function() {



        switchToLeafletMap();



    };



    // Switch between Shipping and Billing address types (Point 1: Full Tab & Map Sync)

    function applyAddressDataToFormAndMap(type, d) {

        if (!d) return;

        $('#modal_alert_box').empty();

        $('#modal_address_location_alert').addClass('d-none').empty();

        $('#unified_address_form').removeClass('was-validated');

        $('#addressModal label.error-text').hide().text('');

        $('#addressModal .form-control, #addressModal .form-select').removeClass('is-invalid');

        $('#modal_address_id').val(d.id || '');

        $('#modal_action_type').val('edit');

        $('#addressModalLabel').text(type === 'shipping' ? 'Edit Shipping Address' : 'Edit Billing Address');



        $('#modal_name').val(d.name || d.bill_name || '');

        $('#modal_phone').val(d.phone || d.bill_phone || '');

        if (d.phonecode || d.bill_phonecode) {

            $('#modal_phonecode').val(d.phonecode || d.bill_phonecode);

        }

        $('#modal_city').val(d.city || d.bill_city || '');

        $('#modal_address').val(d.address || d.bill_address || '');

        $('#modal_zip_code').val(d.zip_code || d.bill_zip_code || '');



        if (type === 'shipping') {

            if (d.delivery_options == 2) {

                $('#modal_delivery_opt_2').prop('checked', true);

            } else {

                $('#modal_delivery_opt_1').prop('checked', true);

            }

            $('#modal_instruction').val(d.delivery_instructions || '');
            $('#modal_is_default_shipping').prop('checked', (d.is_selected_address_id == 1));

        } else {

            $('#modal_is_default').prop('checked', (d.default == 1));

        }



        var countryVal = d.country_id || d.bill_country_id;

        if (countryVal) {

            $('#modal_country_id').val(countryVal);

        }



        var regId = d.region_id || d.bill_region_id;

        var areaId = d.area_id || d.bill_area_id;



        if (d._regions && d._regions.length > 0) {

            var regionHtml = '<option value="">{{ @Helper::language("choose_region_web") }}</option>';

            $.each(d._regions, function(k, v) {

                var sel = (v.id == regId) ? 'selected' : '';

                regionHtml += '<option value="' + v.id + '" ' + sel + '>' + v.title + '</option>';

            });

            $('#modal_region_id').html(regionHtml);

        }



        if (d._areas && d._areas.length > 0) {

            var areaHtml = '<option value="">{{ @Helper::language("choose_area_web") }}</option>';

            $.each(d._areas, function(k, v) {

                var sel = (v.id == areaId) ? 'selected' : '';

                areaHtml += '<option value="' + v.id + '" ' + sel + '>' + v.title + '</option>';

            });

            $('#modal_area_id').html(areaHtml);

        }



        // MAP LOCATION SYNCHRONIZATION:

        var lat = d.latitude || d.bill_latitude;

        var lng = d.longitude || d.bill_longitude;

        var parsedLat = lat ? parseFloat(lat) : null;

        var parsedLng = lng ? parseFloat(lng) : null;



        var loadedAreaTitle = '';

        if (d._areas && areaId) {

            var matchA = d._areas.find(function(x) { return x.id == areaId; });

            if (matchA) loadedAreaTitle = matchA.title;

        }

        var loadedRegTitle = '';

        if (d._regions && regId) {

            var matchR = d._regions.find(function(x) { return x.id == regId; });

            if (matchR) loadedRegTitle = matchR.title;

        }



        if (loadedAreaTitle) {

            $('#modal_map_boundary_badge').html('<i class="fa fa-map-marker-alt text-success me-1"></i> ' + loadedAreaTitle);

        } else if (loadedRegTitle) {

            $('#modal_map_boundary_badge').html('<i class="fa fa-map-marker-alt text-success me-1"></i> ' + loadedRegTitle);

        }



        if (parsedLat && parsedLng && !isNaN(parsedLat) && !isNaN(parsedLng)) {

            window.savedAddressLat = parsedLat;
            window.savedAddressLng = parsedLng;

            hasLocationBeenSet = true;

            isLocationServiceable = true;

            isProgrammaticMove = true;

            $('#modal_address_location_alert').addClass('d-none');

            $('#modal_latitude').val(parsedLat.toFixed(6));

            $('#modal_longitude').val(parsedLng.toFixed(6));

            currentMarkerLat = parsedLat;

            currentMarkerLng = parsedLng;

            initAddressMap(parsedLat, parsedLng);

            panMapToLocation(parsedLat, parsedLng);

            updateCoordsBadge(parsedLat, parsedLng);

            // Pre-fill map search input with selected saved address and reverse geocode pin location
            var initialSavedAddr = [
                d.address || d.bill_address,
                loadedAreaTitle,
                loadedRegTitle,
                (d.country && d.country.name) || ''
            ].filter(Boolean).join(', ');
            if (initialSavedAddr) {
                updateMapSearchInputAddress(initialSavedAddr);
            }
            reverseGeocodePosition(parsedLat, parsedLng, true);


        } else {

            window.savedAddressLat = null;
            window.savedAddressLng = null;

            // Fallback for address without saved coordinates:
            // Orient directly to Area/Region/Country in ONE smooth render without intermediate Ajmer jump
            hasLocationBeenSet = false;
            $('#modal_latitude').val('');
            $('#modal_longitude').val('');

            var areaTitle = (d.area && d.area.title) ? d.area.title : '';
            if (!areaTitle && d._areas && areaId) {
                var matchA = d._areas.find(function(x) { return x.id == areaId; });
                if (matchA) areaTitle = matchA.title;
            }

            var regTitle = (d.region && d.region.title) ? d.region.title : '';
            if (!regTitle && d._regions && regId) {
                var matchR = d._regions.find(function(x) { return x.id == regId; });
                if (matchR) regTitle = matchR.title;
            }

            var countryText = (d.country && d.country.name) ? d.country.name : ($('#modal_country_id').find('option:selected').text().trim() || '');

            var primaryQuery = [areaTitle, regTitle, countryText].filter(Boolean).join(', ');
            var fallbackQuery = [regTitle, countryText].filter(Boolean).join(', ');

            $('#modal_address_location_alert').removeClass('d-none').html(
                '<i class="fa fa-info-circle me-1"></i> Centering location on <strong>' + 
                (areaTitle || regTitle || 'Area') + '</strong>...'
            );
            $('#modal_map_status').html('<span class="spinner-border spinner-border-sm text-warning"></span> Locating on map...');

            if (primaryQuery || fallbackQuery) {
                geocodeAddressToMap(primaryQuery || fallbackQuery, fallbackQuery, function(resolvedLat, resolvedLng, placeData) {
                    initAddressMap(resolvedLat, resolvedLng);
                    panMapToLocation(resolvedLat, resolvedLng);
                    if (placeData) {
                        processReverseGeocodeResult(placeData, resolvedLat, resolvedLng, true);
                    }
                    $('#modal_address_location_alert').removeClass('d-none').html(
                        '<i class="fa fa-info-circle me-1"></i> Location centered on <strong>' + 
                        (areaTitle || regTitle || 'Area') + '</strong>. Move map or search to confirm exact location.'
                    );
                });
            } else {
                var defCenter = getActiveCountryCenter();
                initAddressMap(defCenter.lat, defCenter.lng);
                panMapToLocation(defCenter.lat, defCenter.lng);
            }
        }

    }



    function loadAndDisplayAddress(type, id) {

        window.modalActiveIds = window.modalActiveIds || { shipping: '', billing: '' };

        window.modalAddressCache = window.modalAddressCache || { shipping: null, billing: null };

        var modalActiveIds = window.modalActiveIds;

        var modalAddressCache = window.modalAddressCache;

        if (modalAddressCache[type]) {

            applyAddressDataToFormAndMap(type, modalAddressCache[type]);

            return;

        }



        if (!id) {

            if (type === 'shipping') {

                id = modalActiveIds.shipping || $('input[name="address_radio"]:checked').val() || $('.open-address-modal[data-type="shipping"][data-action="edit"]').first().data('id') || '';

            } else {

                id = modalActiveIds.billing || $('#user_bill_address_id').val() || $('.open-address-modal[data-type="billing"][data-action="edit"]').first().data('id') || '';

            }

        }



        if (!id) {

            // No existing address found for this type -> clean add state for this tab

            $('#modal_action_type').val('add');

            $('#modal_address_id').val('');

            $('#addressModalLabel').text(type === 'shipping' ? '{{ @Helper::language("add_address") }}' : 'Add Billing Address');

            $('#modal_name').val('');

            $('#modal_phone').val('');

            $('#modal_city').val('');

            $('#modal_address').val('');

            $('#modal_zip_code').val('');

            $('#modal_instruction').val('');

            $('#modal_latitude').val('');

            $('#modal_longitude').val('');

            $('#modal_delivery_opt_1').prop('checked', true);

            $('#modal_is_default').prop('checked', true);

            $('#modal_alert_box').empty();

            $('#modal_map_boundary_badge').html(
                '<i class="fa fa-map-marker-alt me-1"></i> ' + (type === 'shipping' ? 'Delivery Zone' : 'Billing Zone')
            );

            selectDefaultCountry();

            if (hasLocationBeenSet && currentMarkerLat && currentMarkerLng) {
                $('#modal_latitude').val(currentMarkerLat.toFixed(6));
                $('#modal_longitude').val(currentMarkerLng.toFixed(6));
                panMapToLocation(currentMarkerLat, currentMarkerLng);
                updateCoordsBadge(currentMarkerLat, currentMarkerLng);
            } else {
                hasLocationBeenSet = false;
                isLocationServiceable = false;
                $('#modal_latitude').val('');
                $('#modal_longitude').val('');
                $('#modal_address_location_alert').removeClass('d-none').html(
                    '<i class="fa fa-exclamation-triangle me-1"></i> Please set your ' + (type === 'shipping' ? 'pickup/delivery' : 'billing') + ' location on the map.'
                );
                autoDetectCurrentLocationOrFallback();
            }

            return;

        }



        modalActiveIds[type] = id;

        $('#btn_save_text').text('Loading...');

        $('#btn_save_spinner').removeClass('d-none');



        $.ajax({

            url: urlGetAddressDetailsBase + '/' + type + '/' + id,

            type: 'GET',

            dataType: 'json',

            success: function(res) {

                $('#btn_save_text').text('{{ @Helper::language("save_details_btn") }}');

                $('#btn_save_spinner').addClass('d-none');

                if (res && res.success && res.data) {

                    var d = res.data;

                    d._regions = res.regions;

                    d._areas = res.areas;

                    modalAddressCache[type] = d;

                    applyAddressDataToFormAndMap(type, d);

                }

            },

            error: function() {

                $('#btn_save_text').text('{{ @Helper::language("save_details_btn") }}');

                $('#btn_save_spinner').addClass('d-none');

            }

        });

    }



    function switchAddressModalType(type, isUserTabClick) {

        if (typeof isUserTabClick === 'undefined') {
            isUserTabClick = true;
        }

        var prevType = $('#modal_address_type').val() || 'shipping';

        var action = $('#modal_action_type').val() || 'add';

        var isCheckout = parseInt($('#modal_checkout_page').val()) || 0;



        // In Add mode: save draft of previous tab if user typed something

        window.modalAddDrafts = window.modalAddDrafts || { shipping: null, billing: null };

        if (action === 'add' && prevType !== type) {

            window.modalAddDrafts[prevType] = {

                name: $('#modal_name').val(),

                phone: $('#modal_phone').val(),

                phonecode: $('#modal_phonecode').val(),

                city: $('#modal_city').val(),

                address: $('#modal_address').val(),

                zip_code: $('#modal_zip_code').val(),

                country_id: $('#modal_country_id').val(),

                region_id: $('#modal_region_id').val(),

                area_id: $('#modal_area_id').val(),

                instruction: $('#modal_instruction').val(),

                delivery_opt: $('input[name="delivery_options"]:checked').val(),

                latitude: $('#modal_latitude').val(),

                longitude: $('#modal_longitude').val(),

                hasLocationBeenSet: hasLocationBeenSet,

                currentMarkerLat: currentMarkerLat,

                currentMarkerLng: currentMarkerLng,

                map_search_input: $('#map_location_search_input').val()

            };

        }



        $('#modal_address_type').val(type);



        // Clean ALL previous alerts, errors, and validation states immediately on tab switch

        $('#modal_alert_box').empty();

        $('#modal_address_location_alert').addClass('d-none').empty();

        $('#unified_address_form').removeClass('was-validated');

        $('#btn_save_text').text('{{ @Helper::language("save_details_btn") }}');

        $('#btn_save_spinner').addClass('d-none');

        $('#btn_save_unified_address').prop('disabled', false);



        if (type === 'shipping') {

            $('#pill_shipping').addClass('active');

            $('#pill_billing').removeClass('active');

            $('#modal_shipping_default_fields').removeClass('d-none');
            $('#modal_billing_fields').addClass('d-none');

            if (isCheckout === 1) {

                $('#modal_checkout_shipping_options').removeClass('d-none');

            } else {

                $('#modal_checkout_shipping_options').addClass('d-none');

            }

            $('#addressModalLabel').text(action === 'edit' ? 'Edit Shipping Address' : '{{ @Helper::language("add_address") }}');

        } else {

            $('#pill_billing').addClass('active');

            $('#pill_shipping').removeClass('active');

            $('#modal_shipping_default_fields').addClass('d-none');
            $('#modal_billing_fields').removeClass('d-none');

            $('#modal_checkout_shipping_options').addClass('d-none');

            $('#addressModalLabel').text(action === 'edit' ? 'Edit Billing Address' : 'Add Billing Address');

        }



        // Restore or clean form inputs

        if (action === 'edit') {

            if (isUserTabClick) {
                loadAndDisplayAddress(type);
            }

        } else {

            // Add mode: restore draft if exists, else reset to clean empty state

            if (window.modalAddDrafts && window.modalAddDrafts[type]) {

                var d = window.modalAddDrafts[type];

                $('#modal_name').val(d.name || '');

                $('#modal_phone').val(d.phone || '');

                if (d.phonecode) $('#modal_phonecode').val(d.phonecode);

                $('#modal_city').val(d.city || '');

                $('#modal_address').val(d.address || '');

                $('#modal_zip_code').val(d.zip_code || '');

                if (d.country_id) $('#modal_country_id').val(d.country_id);

                if (d.region_id) $('#modal_region_id').val(d.region_id);

                if (d.area_id) $('#modal_area_id').val(d.area_id);

                if (d.instruction) $('#modal_instruction').val(d.instruction);

                if (d.delivery_opt == 2) {

                    $('#modal_delivery_opt_2').prop('checked', true);

                } else {

                    $('#modal_delivery_opt_1').prop('checked', true);

                }

                if (d.latitude && d.longitude) {
                    $('#modal_latitude').val(d.latitude);
                    $('#modal_longitude').val(d.longitude);
                    currentMarkerLat = parseFloat(d.latitude);
                    currentMarkerLng = parseFloat(d.longitude);
                    hasLocationBeenSet = true;
                    panMapToLocation(currentMarkerLat, currentMarkerLng);
                    updateCoordsBadge(currentMarkerLat, currentMarkerLng);
                }

                if (d.map_search_input) {
                    $('#map_location_search_input').val(d.map_search_input);
                }

            } else {

                // Fresh clean state for this tab

                $('#modal_name').val('');

                $('#modal_phone').val('');

                $('#modal_city').val('');

                $('#modal_address').val('');

                $('#modal_zip_code').val('');

                $('#modal_instruction').val('');

                $('#modal_delivery_opt_1').prop('checked', true);

                $('#modal_is_default').prop('checked', true);

                selectDefaultCountry();

                // If location was already set from other tab or GPS in this session, keep it!
                if (hasLocationBeenSet && currentMarkerLat && currentMarkerLng) {
                    $('#modal_latitude').val(currentMarkerLat.toFixed(6));
                    $('#modal_longitude').val(currentMarkerLng.toFixed(6));
                    panMapToLocation(currentMarkerLat, currentMarkerLng);
                    updateCoordsBadge(currentMarkerLat, currentMarkerLng);
                } else {
                    autoDetectCurrentLocationOrFallback();
                }

            }

            if (hasLocationBeenSet && currentMarkerLat && currentMarkerLng) {

                panMapToLocation(currentMarkerLat, currentMarkerLng);
                $('#modal_latitude').val(currentMarkerLat.toFixed(6));
                $('#modal_longitude').val(currentMarkerLng.toFixed(6));
                updateCoordsBadge(currentMarkerLat, currentMarkerLng);

            }

        }



        // Re-evaluate map badge & location notices specifically for the new tab type

        if (window.lastGeocodedAddressData && window.lastGeocodedLat && window.lastGeocodedLng) {

            processReverseGeocodeResult(

                { address: window.lastGeocodedAddressData, display_name: window.lastGeocodedAddressData.display_name },

                window.lastGeocodedLat,

                window.lastGeocodedLng,

                true

            );

        } else if (!hasLocationBeenSet) {

            $('#modal_map_boundary_badge').html(

                '<i class="fa fa-map-marker-alt me-1"></i> ' + (type === 'shipping' ? 'Delivery Zone' : 'Billing Zone')

            );

            $('#modal_address_location_alert').removeClass('d-none').html(

                '<i class="fa fa-exclamation-triangle me-1"></i> Please set your ' + (type === 'shipping' ? 'pickup/delivery' : 'billing') + ' location on the map.'

            );

        }

        if (typeof resetAddressModalScroll === 'function') {
            resetAddressModalScroll();
        }

    }



    function onMapMovementStart(isUserGesture) {



        if (isUserGesture) {



            // User manually dragged map -> unlock address input so reverse geocode can update it



            lockAddressInput = false;



            userChosenAddress = null;



        }



        $('#rapido_pin_container').addClass('is-moving');



        $('#modal_map_status').html('<span class="spinner-border spinner-border-sm text-warning"></span> Moving map...');



    }



    var isProgrammaticMove = false;

    function onMapMovementEnd(lat, lng) {

        $('#rapido_pin_container').removeClass('is-moving');

        currentMarkerLat = lat;

        currentMarkerLng = lng;

        updateCoordsBadge(lat, lng);

        if (isProgrammaticMove) {

            isProgrammaticMove = false;

            return;

        }

        clearTimeout(reverseGeocodeDebounceTimer);

        reverseGeocodeDebounceTimer = setTimeout(function() {

            reverseGeocodePosition(lat, lng);

        }, 300);

    }



    // Switch to OpenStreetMap (Leaflet)



    function switchToLeafletMap() {



        usingLeaflet = true;



        window.googleMapsAuthFailed = true;



        var mapDiv = document.getElementById('modal_address_map');



        if (!mapDiv) return;



        var defCenter = getActiveCountryCenter();

        var lat = currentMarkerLat || defCenter.lat;



        var lng = currentMarkerLng || defCenter.lng;



        if (typeof L === 'undefined') {



            setTimeout(switchToLeafletMap, 150);



            return;



        }



        if (leafletMap) {



            try {



                leafletMap.invalidateSize();



                leafletMap.setView([lat, lng], leafletMap.getZoom() || 15);



                updateCoordsBadge(lat, lng);



            } catch (e) {



                console.warn("Leaflet refresh error:", e);



            }



            return;



        }



        // Clean out Google Maps error overlay or previous DOM



        mapDiv.innerHTML = '';



        if (mapDiv._leaflet_id) {



            delete mapDiv._leaflet_id;



        }



        try {



            // minZoom 6.0 allows comfortable viewing of map view without tiny thumbnail lock



            leafletMap = L.map(mapDiv, {



                center: [lat, lng],



                zoom: 14,



                minZoom: 2.5,



                maxZoom: 19,



                zoomControl: false

            });
            L.control.zoom({ position: 'bottomright' }).addTo(leafletMap);



            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {



                maxZoom: 19,



                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'



            }).addTo(leafletMap);



            // Free movement map - boundaries validated on pin drop



            // User Drag Gestures vs Programmatic moves



            leafletMap.on('dragstart', function() {



                onMapMovementStart(true);



            });



            leafletMap.on('movestart', function() {



                onMapMovementStart(false);



            });



            // Tap/Click on Map: directly pans to clicked map location and zooms in



            leafletMap.on('click', function(e) {



                var cLat = e.latlng.lat;



                var cLng = e.latlng.lng;



                onMapMovementStart(true);

                var targetZoom = Math.max(leafletMap.getZoom() || 15, 14);

                leafletMap.flyTo([cLat, cLng], targetZoom, { duration: 0.4 });



            });



            leafletMap.on('moveend', function() {

                var center = leafletMap.getCenter();

                onMapMovementEnd(center.lat, center.lng);

            });



            updateCoordsBadge(lat, lng);



            setTimeout(function() {



                if (leafletMap) leafletMap.invalidateSize();



            }, 300);



        } catch (err) {



            console.error("Leaflet init error:", err);



        }



    }



    // Initialize or refresh map



    function initAddressMap(targetLat, targetLng) {



        var defCenter = getActiveCountryCenter();

        var lat = parseFloat(targetLat) || defCenter.lat;



        var lng = parseFloat(targetLng) || defCenter.lng;



        currentMarkerLat = lat;



        currentMarkerLng = lng;



        // If Google Maps previously failed or Leaflet is active, use Leaflet immediately



        if (window.googleMapsAuthFailed || usingLeaflet) {



            switchToLeafletMap();



            panMapToLocation(lat, lng);

                hasLocationBeenSet = true;

                $('#modal_address_location_alert').addClass('d-none');



            updateCoordsBadge(lat, lng);



            return;



        }



        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {



            switchToLeafletMap();



            panMapToLocation(lat, lng);



            updateCoordsBadge(lat, lng);



            return;



        }



        var latLng = { lat: lat, lng: lng };



        var mapDiv = document.getElementById('modal_address_map');



        if (!mapDiv) return;



        if (!geocoder) {



            geocoder = new google.maps.Geocoder();



        }



        if (!addressMap) {



            try {



                // Attach observer to catch Google Maps auth error overlay instantly



                if (window.MutationObserver) {



                    var errObserver = new MutationObserver(function() {



                        if (mapDiv.querySelector('.gm-err-container') || 



                            mapDiv.innerHTML.indexOf('gm-err') !== -1 || 



                            mapDiv.innerText.indexOf('Oops! Something went wrong') !== -1) {



                            console.warn("Google Maps error overlay detected. Switching instantly to OpenStreetMap...");



                            errObserver.disconnect();



                            switchToLeafletMap();



                        }



                    });



                    errObserver.observe(mapDiv, { childList: true, subtree: true });



                }



                addressMap = new google.maps.Map(mapDiv, {



                    center: latLng,



                    zoom: 15,



                    minZoom: 6.0,



                    maxZoom: 19,



                    mapTypeControl: false,



                    streetViewControl: false,



                    fullscreenControl: false,

                    zoomControl: true,

                    zoomControlOptions: {
                        position: (typeof google !== 'undefined' && google.maps && google.maps.ControlPosition) 
                            ? google.maps.ControlPosition.LEFT_BOTTOM 
                            : undefined
                    }

                });



                // Free movement map - boundaries validated on pin drop



                // Tap/Click on Map: directly pans to clicked map location and zooms in



                google.maps.event.addListener(addressMap, 'click', function(e) {



                    if (!e.latLng) return;



                    var cLat = e.latLng.lat();



                    var cLng = e.latLng.lng();



                    onMapMovementStart(true);

                    addressMap.panTo({ lat: cLat, lng: cLng });



                    if (addressMap.getZoom() < 14) {



                        addressMap.setZoom(14);



                    }



                });



                // Rapido center pin movement listeners on Google Maps



                google.maps.event.addListener(addressMap, 'dragstart', function() {



                    onMapMovementStart(true);



                });



                google.maps.event.addListener(addressMap, 'idle', function() {



                    if (!addressMap) return;



                    var center = addressMap.getCenter();



                    if (!center) return;



                    var cLat = center.lat();



                    var cLng = center.lng();



                    onMapMovementEnd(cLat, cLng);



                });



                // Timeout fallback check



                setTimeout(function() {



                    if (mapDiv && (mapDiv.querySelector('.gm-err-container') || 



                                   mapDiv.innerHTML.indexOf('gm-err') !== -1 || 



                                   mapDiv.innerText.indexOf('Oops! Something went wrong') !== -1)) {



                        console.warn("Google Maps error detected via timeout. Switching to OpenStreetMap...");



                        switchToLeafletMap();



                    }



                }, 250);



            } catch (err) {



                console.error("Google Maps initialization failed:", err);



                switchToLeafletMap();



            }



        } else {



            panMapToLocation(lat, lng);



        }



        updateCoordsBadge(lat, lng);



    }



    // Pan active map to specified lat, lng



    function panMapToLocation(lat, lng) {

        currentMarkerLat = lat;

        currentMarkerLng = lng;

        if (usingLeaflet && leafletMap) {

            leafletMap.setView([lat, lng], leafletMap.getZoom() || 15, { animate: false });

        } else if (addressMap) {

            addressMap.setCenter({ lat: lat, lng: lng });

        }

        updateCoordsBadge(lat, lng);

    }



    function updateCoordsBadge(lat, lng) {



        $('#modal_latitude').val(lat.toFixed(6));



        $('#modal_longitude').val(lng.toFixed(6));



        $('#modal_map_coords').text(lat.toFixed(4) + ', ' + lng.toFixed(4));



    }



    // Reverse geocode lat, lng to update fields (Point 5: Works for all serviceable locations)



    function reverseGeocodePosition(lat, lng, keepAddressIntact) {
        updateCoordsBadge(lat, lng);
        $('#modal_map_status').html('<span class="spinner-border spinner-border-sm text-warning"></span> Locating address...');
        $('#map_search_spinner').removeClass('d-none');

        var hasGoogleGeocoder = (typeof google !== 'undefined' && google.maps && google.maps.Geocoder && !window.googleMapsAuthFailed);
        if (hasGoogleGeocoder) {
            if (!geocoder) {
                geocoder = new google.maps.Geocoder();
            }
            geocoder.geocode({ 
                location: { lat: lat, lng: lng }
            }, function(results, status) {
                if (status === 'OK' && results && results.length > 0) {
                    var bestResult = results[0];
                    for (var i = 0; i < results.length; i++) {
                        var resItem = results[i];
                        var isPlusCodeOnly = (resItem.types && resItem.types.indexOf('plus_code') !== -1);
                        var startsWithPlus = /^[A-Z0-9]{2,8}\+[A-Z0-9]{2,4}/i.test(resItem.formatted_address || '');
                        if (!isPlusCodeOnly && !startsWithPlus) {
                            bestResult = resItem;
                            break;
                        }
                    }
                    processGoogleGeocodeResult(bestResult, lat, lng, keepAddressIntact);
                } else {
                    callNominatimReverse(lat, lng, keepAddressIntact);
                }
            });
        } else {
            callNominatimReverse(lat, lng, keepAddressIntact);
        }
    }

    function callNominatimReverse(lat, lng, keepAddressIntact) {
        $.ajax({
            url: 'https://nominatim.openstreetmap.org/reverse',
            type: 'GET',
            data: {
                format: 'json',
                lat: lat,
                lon: lng,
                zoom: 18,
                addressdetails: 1
            },
            dataType: 'json',
            timeout: 7000,
            success: function(data) {
                processReverseGeocodeResult(data, lat, lng, keepAddressIntact);
            },
            error: function() {
                $('#map_search_spinner').addClass('d-none');
                $('#modal_map_status').html('<span class="text-muted"><i class="fa fa-map-marker text-danger"></i> Pin placed (' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')</span>');
            }
        });
    }

    function removePlusCode(text) {
        if (!text || typeof text !== 'string') return '';
        // Strip Plus Codes (Open Location Codes) e.g. "FJ7J+MQQ, " or "8Q8G+4X, " or "7JVW52GR+2V, "
        return text.replace(/^[A-Z0-9]{2,8}\+[A-Z0-9]{2,4}(?:,\s*|\s+)/i, '')
                   .replace(/\b[A-Z0-9]{2,8}\+[A-Z0-9]{2,4}\b,?\s*/gi, '')
                   .replace(/^,\s*|,\s*$/g, '')
                   .trim();
    }

    function formatUserFriendlyAddress(rawText) {
        if (!rawText || typeof rawText !== 'string') return '';
        var noPlusCode = removePlusCode(rawText);
        var clean = noPlusCode.replace(/[\r\n\t]+/g, ' ').replace(/\s{2,}/g, ' ').trim();
        var parts = clean.split(',').map(function(p) { return p.trim(); }).filter(Boolean);
        var seen = {};
        var uniqueParts = [];
        for (var i = 0; i < parts.length; i++) {
            var lower = parts[i].toLowerCase();
            if (!seen[lower]) {
                seen[lower] = true;
                uniqueParts.push(parts[i]);
            }
        }
        return uniqueParts.join(', ');
    }

    function updateMapSearchInputAddress(addressText) {
        if (!addressText) return;
        // If user already explicitly picked an address from search suggestions and has not moved the map, retain it!
        if (userChosenAddress) {
            $('#map_location_search_input').val(userChosenAddress);
            $('#map_search_spinner').addClass('d-none');
            return;
        }
        var formatted = formatUserFriendlyAddress(addressText);
        if (!formatted) return;
        if (!$('#map_location_search_input').is(':focus')) {
            $('#map_location_search_input').val(formatted);
        }
        $('#map_search_spinner').addClass('d-none');
    }

    function processReverseGeocodeResult(data, lat, lng, keepAddressIntact) {

        if (!data || !data.address) {

            $('#modal_map_status').html('<span class="text-muted"><i class="fa fa-map-marker-alt"></i> Location pinned</span>');

            $('#map_search_spinner').addClass('d-none');

            return;

        }

        var addrData = $.extend({}, data.address, {

            display_name: data.display_name || ''

        });

        window.lastGeocodedAddressData = addrData;

        window.lastGeocodedLat = lat;

        window.lastGeocodedLng = lng;

        // Synchronize map location search bar with reverse-geocoded address of the pin
        var resolvedAddrText = data.display_name;
        if (!resolvedAddrText && data.address) {
            var a = data.address;
            resolvedAddrText = [
                a.road || a.pedestrian || a.suburb || a.neighbourhood || '',
                a.city || a.town || a.village || a.district || '',
                a.state || '',
                a.country || ''
            ].filter(Boolean).join(', ');
        }
        if (resolvedAddrText) {
            updateMapSearchInputAddress(resolvedAddrText);
        }



        var isBill = ($('#modal_address_type').val() === 'billing');

        var check = checkLocationServiceability(addrData, lat, lng);

        if (check.serviceable) {

            isLocationServiceable = true;

            hasLocationBeenSet = true;

            $('#modal_address_location_alert').addClass('d-none').empty();

            $('#modal_alert_box').empty();

            $('#btn_save_unified_address').prop('disabled', false);



            var areaTitle = (check.area && check.area.title) ? check.area.title : '';

            var regTitle = (check.region && check.region.title) ? check.region.title : '';

            var countryTitle = (check.country && check.country.name) ? check.country.name : '';



            $('#modal_map_status').html(

                '<span class="text-success fw-semibold"><i class="fa fa-check-circle"></i> ' + (isBill ? 'Billing Area: ' : 'Serviceable: ') + 

                areaTitle + ', ' + regTitle + ' (' + countryTitle + ')</span>'

            );



            $('#modal_map_boundary_badge').html(

                '<i class="fa fa-check-circle text-success me-1"></i> ' + areaTitle + ', ' + countryTitle

            );



            syncDropdownsWithServiceableLocation(check);

            populateFieldsFromNominatim(data, keepAddressIntact);

        } else {

            isLocationServiceable = false;

            hasLocationBeenSet = false;

            $('#btn_save_unified_address').prop('disabled', true);

            $('#modal_map_status').html(

                '<span class="text-danger fw-semibold"><i class="fa fa-ban"></i> ' + check.message + '</span>'

            );

            $('#modal_map_boundary_badge').html(

                '<i class="fa fa-exclamation-triangle text-danger me-1"></i> ' + (isBill ? 'Outside Billing Area' : 'Outside Delivery Area')

            );

            $('#modal_alert_box').html(

                '<div class="alert alert-warning py-2 small d-flex align-items-center gap-2"><i class="fa fa-exclamation-triangle"></i><span>' + check.message + '</span></div>'

            );

        }

    }



    function processGoogleGeocodeResult(place, lat, lng, keepAddressIntact) {

        if (!place || !place.address_components) {

            $('#modal_map_status').html('<span class="text-muted"><i class="fa fa-map-marker-alt"></i> Location pinned</span>');

            return;

        }



        var addrObj = {

            country: '',

            country_code: '',

            state: '',

            city: '',

            town: '',

            suburb: '',

            display_name: place.formatted_address || ''

        };



        place.address_components.forEach(function(c) {

            var types = c.types;

            var val = c.long_name;

            var isDivision = /\bdivision\b/i.test(val);



            if (types.indexOf('country') !== -1) {

                addrObj.country = val;

                addrObj.country_code = c.short_name;

            } else if (types.indexOf('administrative_area_level_1') !== -1) {

                addrObj.state = val;

            } else if (types.indexOf('locality') !== -1) {

                addrObj.city = val;

            } else if (types.indexOf('administrative_area_level_2') !== -1) {

                if (!isDivision) {

                    addrObj.district = val;

                    addrObj.town = val;

                }

            } else if (types.indexOf('administrative_area_level_3') !== -1) {

                if (!isDivision) {

                    addrObj.county = val;

                }

            } else if (types.indexOf('sublocality') !== -1 || types.indexOf('neighborhood') !== -1) {

                addrObj.suburb = val;

            }

        });



        window.lastGeocodedAddressData = addrObj;

        window.lastGeocodedLat = lat;

        window.lastGeocodedLng = lng;

        // Synchronize map location search bar with reverse-geocoded Google address
        if (place.formatted_address) {
            updateMapSearchInputAddress(place.formatted_address);
        }



        var isBill = ($('#modal_address_type').val() === 'billing');

        var check = checkLocationServiceability(addrObj, lat, lng);

        if (check.serviceable) {

            isLocationServiceable = true;

            hasLocationBeenSet = true;

            $('#modal_address_location_alert').addClass('d-none').empty();

            $('#modal_alert_box').empty();

            $('#btn_save_unified_address').prop('disabled', false);



            var areaTitle = (check.area && check.area.title) ? check.area.title : '';

            var regTitle = (check.region && check.region.title) ? check.region.title : '';

            var countryTitle = (check.country && check.country.name) ? check.country.name : '';



            $('#modal_map_status').html(

                '<span class="text-success fw-semibold"><i class="fa fa-check-circle"></i> ' + (isBill ? 'Billing Area: ' : 'Serviceable: ') + 

                areaTitle + ', ' + regTitle + ' (' + countryTitle + ')</span>'

            );



            $('#modal_map_boundary_badge').html(

                '<i class="fa fa-check-circle text-success me-1"></i> ' + areaTitle + ', ' + countryTitle

            );



            syncDropdownsWithServiceableLocation(check);

            populateFieldsFromPlace(place, keepAddressIntact);

        } else {

            isLocationServiceable = false;

            hasLocationBeenSet = false;

            $('#btn_save_unified_address').prop('disabled', true);

            $('#modal_map_status').html(

                '<span class="text-danger fw-semibold"><i class="fa fa-ban"></i> ' + check.message + '</span>'

            );

            $('#modal_map_boundary_badge').html(

                '<i class="fa fa-exclamation-triangle text-danger me-1"></i> ' + (isBill ? 'Outside Billing Area' : 'Outside Delivery Area')

            );

            $('#modal_alert_box').html(

                '<div class="alert alert-warning py-2 small d-flex align-items-center gap-2"><i class="fa fa-exclamation-triangle"></i><span>' + check.message + '</span></div>'

            );

        }

    }



    function populateFieldsFromNominatim(data, keepAddressIntact) {

        if (!data || !data.address) return;

        var a = data.address;

        var city = a.city || a.town || a.village || a.county || '';

        if (city && !$('#modal_city').val()) {

            $('#modal_city').val(city);

        }

        var postcode = a.postcode || '';

        if (postcode && !$('#modal_zip_code').val()) {

            $('#modal_zip_code').val(postcode);

        }

    }



    // Populate inputs from Google Geocoder Result - Never touches or resets Region/Area dropdowns!

    function populateFieldsFromPlace(place, keepAddressIntact) {

        if (!place || !place.address_components) return;

        var cityName = '';

        var postalCode = '';

        place.address_components.forEach(function(c) {

            var types = c.types;

            if (types.indexOf('locality') !== -1 || types.indexOf('administrative_area_level_2') !== -1) {

                if (!cityName) cityName = c.long_name;

            }

            if (types.indexOf('postal_code') !== -1) {

                postalCode = c.long_name;

            }

        });

        if (cityName && !$('#modal_city').val()) $('#modal_city').val(cityName);

        if (postalCode && !$('#modal_zip_code').val()) $('#modal_zip_code').val(postalCode);

    }



    function selectDefaultCountry(callback) {

        var $firstCountry = $('#modal_country_id option:not([value=""]):first');

        if ($firstCountry.length) {

            $('#modal_country_id').val($firstCountry.val());

            var pcode = $firstCountry.data('phonecode');

            if (pcode) $('#modal_phonecode').val(pcode);

        }

        if (callback) callback();

    }



    function matchRegionByName(name) {



        var lower = name.toLowerCase().replace('region', '').trim();



        $('#modal_region_id option').each(function() {



            var txt = $(this).text().trim().toLowerCase();



            if (txt.indexOf(lower) !== -1 || lower.indexOf(txt) !== -1) {



                $('#modal_region_id').val($(this).val());



                return false;



            }



        });



    }



    function matchAreaByName(name) {



        var lower = name.toLowerCase().trim();



        $('#modal_area_id option').each(function() {



            var txt = $(this).text().trim().toLowerCase();



            if (txt.indexOf(lower) !== -1 || lower.indexOf(txt) !== -1) {



                $('#modal_area_id').val($(this).val());



                return false;



            }



        });



    }



    // ==========================================



    // POINT 7: STREET ADDRESS AUTOCOMPLETE



    // ==========================================





    $(document).ready(function() {



        // Task 2: Map location search bar events (over the map)

        $('#map_location_search_input').on('input', function() {

            userChosenAddress = null;

            var query = $(this).val().trim();

            if (query.length < 3) {

                $('#map_search_suggestions').addClass('d-none').empty();

                $('#map_search_spinner').addClass('d-none');

                return;

            }

            $('#map_search_spinner').removeClass('d-none');

            clearTimeout(streetSearchDebounceTimer);

            streetSearchDebounceTimer = setTimeout(function() {

                searchLocationOnMap(query);

            }, 350);

        });



        // Handle Enter key on map search input

        $('#map_location_search_input').on('keypress', function(e) {

            if (e.which === 13) {

                e.preventDefault();

                var query = $(this).val().trim();

                if (query.length >= 3) {

                    $('#map_search_suggestions').addClass('d-none');

                    geocodeAddressToMap(query);

                }

            }

        });



        // Hide map search suggestions on click outside

        $(document).on('click', function(e) {

            if (!$(e.target).closest('.map-floating-search-wrapper').length) {

                $('#map_search_suggestions').addClass('d-none');

            }

        });



        // Smoothly orient map when Country, Region or Area changes (Manual assistance only - does NOT auto-commit pin)

        $(document).on('change', '#modal_country_id', function() {

            var countryText = $(this).find('option:selected').text().trim();

            var phonecode = $(this).find('option:selected').data('phonecode');

            if (phonecode) {

                $('#modal_phonecode').val(phonecode);

            }

            if (countryText && countryText.toLowerCase().indexOf('choose') === -1) {

                $('#modal_map_status').html('<span class="text-warning"><span class="spinner-border spinner-border-sm"></span> Moving to ' + countryText + '...</span>');

                geocodeAddressToMapOnly(countryText, 6);

            }

        });



        $(document).on('change', '#modal_region_id', function() {

            var regText = $(this).find('option:selected').text().trim();

            var countryText = $('#modal_country_id').find('option:selected').text().trim();

            if (countryText.toLowerCase().indexOf('choose') !== -1) countryText = '';

            if (regText && regText.toLowerCase().indexOf('choose') === -1) {

                var rQuery = regText + (countryText ? (', ' + countryText) : '');

                $('#modal_map_status').html('<span class="text-warning"><span class="spinner-border spinner-border-sm"></span> Moving to ' + regText + '...</span>');

                geocodeAddressToMapOnly(rQuery, 10);

            }

        });



        $(document).on('change', '#modal_area_id', function() {

            var areaText = $(this).find('option:selected').text().trim();

            var regText = $('#modal_region_id').find('option:selected').text().trim();

            var countryText = $('#modal_country_id').find('option:selected').text().trim();

            if (regText.toLowerCase().indexOf('choose') !== -1) regText = '';

            if (countryText.toLowerCase().indexOf('choose') !== -1) countryText = '';

            if (areaText && areaText.toLowerCase().indexOf('choose') === -1) {

                var aQuery = areaText + (regText ? (', ' + regText) : '') + (countryText ? (', ' + countryText) : '');

                $('#modal_map_status').html('<span class="text-warning"><span class="spinner-border spinner-border-sm"></span> Moving to ' + areaText + '...</span>');

                geocodeAddressToMapOnly(aQuery, 14);

            }

        });



        function geocodeAddressToMapOnly(query, targetZoom) {

            $.ajax({

                url: 'https://nominatim.openstreetmap.org/search',

                type: 'GET',

                data: {

                    format: 'json',

                    q: query,

                    limit: 1

                },

                dataType: 'json',

                success: function(res) {

                    if (res && res.length > 0) {

                        var lat = parseFloat(res[0].lat);

                        var lon = parseFloat(res[0].lon);

                        isProgrammaticMove = true;

                        if (usingLeaflet && leafletMap) {

                            leafletMap.setView([lat, lon], targetZoom || 14);

                        } else if (addressMap) {

                            addressMap.setCenter({ lat: lat, lng: lon });

                            addressMap.setZoom(targetZoom || 14);

                        }

                        $('#modal_map_status').html('<span class="text-info"><i class="fa fa-info-circle"></i> View moved to ' + (res[0].name || query) + '. Please pin your exact location on the map.</span>');

                    }

                }

            });

        }



    });



    // Task 2: Search address suggestions for Map (Supports Google Places or Nominatim)



    function searchLocationOnMap(query) {



        if (typeof google !== 'undefined' && google.maps && google.maps.places && google.maps.places.AutocompleteService && !window.googleMapsAuthFailed) {



            try {



                var autoService = new google.maps.places.AutocompleteService();



                var allowedCodes = getAllowedCountryCodes();

                autoService.getPlacePredictions({



                    input: query,



                    componentRestrictions: { country: allowedCodes }



                }, function(predictions, status) {



                    $('#map_search_spinner').addClass('d-none');



                    if (status === google.maps.places.PlacesServiceStatus.OK && predictions && predictions.length > 0) {



                        renderGooglePlacesPredictions(predictions);



                        return;



                    }



                    queryNominatimSearch(query);



                });



                return;



            } catch (e) {



                console.warn("Google Places Autocomplete error:", e);



            }



        }



        queryNominatimSearch(query);



    }



    function renderGooglePlacesPredictions(predictions) {



        var $dropdown = $('#map_search_suggestions');



        $dropdown.empty();



        predictions.forEach(function(p) {



            var fullDesc = p.description;



            var mainText = (p.structured_formatting && p.structured_formatting.main_text) ? p.structured_formatting.main_text : fullDesc;



            var secText = (p.structured_formatting && p.structured_formatting.secondary_text) ? p.structured_formatting.secondary_text : '';



            var $item = $('<div class="suggestion-item"></div>');



            $item.html(
                '<div class="suggestion-icon text-danger mt-1">' +
                    '<svg width="15" height="15" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>' +
                '</div>' +
                '<div class="suggestion-text-wrap">' +
                    '<span class="suggestion-main-text">' + mainText + '</span>' +
                    (secText ? ('<span class="suggestion-sub-text">' + secText + '</span>') : '') +
                '</div>'
            );



            $item.on('click', function() {

                var selectedOptionText = fullDesc || (mainText + (secText ? ', ' + secText : ''));

                userChosenAddress = formatUserFriendlyAddress(selectedOptionText);

                $('#map_location_search_input').val(userChosenAddress);

                hasLocationBeenSet = true;

                $('#modal_address_location_alert').addClass('d-none');

                $('#modal_alert_box').html('');

                $dropdown.addClass('d-none').empty();

                // Geocode and move map only - NEVER touch #modal_address!
                if (geocoder) {

                    var geocodeReq = p.place_id ? { placeId: p.place_id } : { address: fullDesc };

                    geocoder.geocode(geocodeReq, function(res, st) {

                        if (st === 'OK' && res && res[0]) {

                            var loc = res[0].geometry.location;

                            var lat = loc.lat();

                            var lng = loc.lng();

                            panMapToLocation(lat, lng);

                            updateCoordsBadge(lat, lng);

                            processGoogleGeocodeResult(res[0], lat, lng, true);

                            $('#map_location_search_input').val(userChosenAddress);

                        } else {

                            geocodeAddressToMap(fullDesc);

                        }

                    });

                } else {

                    geocodeAddressToMap(fullDesc);

                }

            });



            $dropdown.append($item);



        });



        $dropdown.removeClass('d-none');



    }



    function queryNominatimSearch(query) {

        var allowedCodes = getAllowedCountryCodes();

        $.ajax({

            url: 'https://nominatim.openstreetmap.org/search',

            type: 'GET',

            data: {

                format: 'json',

                q: query,

                countrycodes: allowedCodes.join(','),

                limit: 6,

                addressdetails: 1

            },



            dataType: 'json',



            timeout: 6000,



            success: function(results) {



                $('#map_search_spinner').addClass('d-none');



                var $dropdown = $('#map_search_suggestions');



                $dropdown.empty();



                if (results && results.length > 0) {



                    results.forEach(function(item) {



                        var lat = parseFloat(item.lat);



                        var lon = parseFloat(item.lon);



                        var fullAddr = item.display_name;



                        var parts = fullAddr.split(',');



                        var mainTitle = item.name || parts[0];



                        var subTitle = parts.slice(1, 4).join(',').trim();



                        var $item = $('<div class="suggestion-item"></div>');



                        $item.html(
                            '<div class="suggestion-icon text-danger mt-1">' +
                                '<svg width="15" height="15" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>' +
                            '</div>' +
                            '<div class="suggestion-text-wrap">' +
                                '<span class="suggestion-main-text">' + mainTitle + '</span>' +
                                ((subTitle || fullAddr) ? ('<span class="suggestion-sub-text">' + (subTitle || fullAddr) + '</span>') : '') +
                            '</div>'
                        );



                        $item.on('click', function() {

                            var selectedOptionText = fullAddr || (mainTitle + (subTitle ? ', ' + subTitle : ''));

                            userChosenAddress = formatUserFriendlyAddress(selectedOptionText);

                            $('#map_location_search_input').val(userChosenAddress);

                            hasLocationBeenSet = true;

                            $('#modal_address_location_alert').addClass('d-none');

                            $('#modal_alert_box').html('');

                            $dropdown.addClass('d-none').empty();

                            panMapToLocation(lat, lon);

                            updateCoordsBadge(lat, lon);

                            processReverseGeocodeResult(item, lat, lon, true);

                            $('#map_location_search_input').val(userChosenAddress);

                        });



                        $dropdown.append($item);



                    });



                    $dropdown.removeClass('d-none');



                } else {



                    $dropdown.html('<div class="p-2 text-muted small text-center">No location found for "' + query + '"</div>');



                    $dropdown.removeClass('d-none');



                }



            },



            error: function() {



                $('#map_search_spinner').addClass('d-none');



            }



        });



    }



    // Geocode full address to pan map (checks multi-country serviceable pool without wiping inputs)

    function geocodeAddressToMap(query, fallbackQuery, callback) {

        var countryText = $('#modal_country_id').find('option:selected').text().trim() || '';

        var searchQuery = query;

        if (countryText && countryText.toLowerCase().indexOf('choose') === -1 && query.toLowerCase().indexOf(countryText.toLowerCase()) === -1) {

            searchQuery = query + ', ' + countryText;

        }

        var allowedCodes = getAllowedCountryCodes();

        $.ajax({

            url: 'https://nominatim.openstreetmap.org/search',

            type: 'GET',

            data: {

                format: 'json',

                q: searchQuery,

                countrycodes: allowedCodes.join(','),

                limit: 1,

                addressdetails: 1

            },

            dataType: 'json',

            timeout: 6000,

            success: function(res) {

                if (res && res.length > 0) {

                    var lat = parseFloat(res[0].lat);

                    var lon = parseFloat(res[0].lon);

                    hasLocationBeenSet = true;

                    isProgrammaticMove = true;

                    $('#modal_address_location_alert').addClass('d-none');

                    $('#modal_latitude').val(lat.toFixed(6));

                    $('#modal_longitude').val(lon.toFixed(6));

                    currentMarkerLat = lat;

                    currentMarkerLng = lon;

                    if (callback) {

                        callback(lat, lon, res[0]);

                    } else {

                        panMapToLocation(lat, lon);

                        processReverseGeocodeResult(res[0], lat, lon, true);

                    }

                    updateCoordsBadge(lat, lon);

                    return;

                }

                if (fallbackQuery) {

                    geocodeAddressToMap(fallbackQuery, null, callback);

                } else {

                    $('#modal_map_status').html('<span class="text-danger fw-semibold"><i class="fa fa-ban"></i> Location not found.</span>');

                    if (callback) {

                        var defCenter = getActiveCountryCenter();

                        callback(defCenter.lat, defCenter.lng, null);

                    }

                }

            },

            error: function() {

                if (fallbackQuery) {

                    geocodeAddressToMap(fallbackQuery, null, callback);

                } else if (callback) {

                    var defCenter = getActiveCountryCenter();

                    callback(defCenter.lat, defCenter.lng, null);

                }

            }

        });

    }



    // Locate Me (Detects GPS anywhere and verifies serviceability)



    function modalLocateCurrentPosition() {

        if (!navigator.geolocation) {

            alert("Geolocation is not supported by your browser.");

            return;

        }

        var $btn = $('#btn_modal_locate_me');
        $btn.addClass('locating').prop('disabled', true);
        $('#modal_map_status').html('<span class="spinner-border spinner-border-sm text-primary me-1"></span> Detecting GPS position...');

        navigator.geolocation.getCurrentPosition(function(pos) {

            $btn.removeClass('locating').prop('disabled', false);
            var lat = pos.coords.latitude;

            var lng = pos.coords.longitude;

            hasLocationBeenSet = true;
            isProgrammaticMove = true;
            $('#modal_latitude').val(lat.toFixed(6));
            $('#modal_longitude').val(lng.toFixed(6));
            currentMarkerLat = lat;
            currentMarkerLng = lng;

            panMapToLocation(lat, lng);
            updateCoordsBadge(lat, lng);
            reverseGeocodePosition(lat, lng, false);

        }, function(err) {

            $btn.removeClass('locating').prop('disabled', false);
            $('#modal_map_status').html('<span class="text-muted"><i class="fa fa-map-marker text-danger"></i> Move map to set exact pin</span>');

        }, { timeout: 8000, enableHighAccuracy: true });

    }

    // Auto-detect GPS Current Location for Add Address mode with graceful fallback
    function autoDetectCurrentLocationOrFallback(fallbackQuery) {

        if (!navigator.geolocation) {
            if (fallbackQuery) {
                geocodeAddressToMap(fallbackQuery);
            } else {
                var defCenter = getActiveCountryCenter();
                panMapToLocation(defCenter.lat, defCenter.lng);
                $('#modal_map_status').html('<span class="text-muted"><i class="fa fa-map-marker text-danger"></i> Move map or search location</span>');
                reverseGeocodePosition(defCenter.lat, defCenter.lng, false);
            }
            return;
        }

        $('#modal_map_status').html('<span class="spinner-border spinner-border-sm text-warning"></span> Auto-detecting current location...');

        navigator.geolocation.getCurrentPosition(function(pos) {

            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;

            hasLocationBeenSet = true;
            isProgrammaticMove = true;
            $('#modal_latitude').val(lat.toFixed(6));
            $('#modal_longitude').val(lng.toFixed(6));
            currentMarkerLat = lat;
            currentMarkerLng = lng;

            panMapToLocation(lat, lng);
            updateCoordsBadge(lat, lng);
            reverseGeocodePosition(lat, lng, false);

        }, function(err) {

            console.warn("Auto GPS detection unavailable or denied:", err.message);

            if (fallbackQuery) {
                geocodeAddressToMap(fallbackQuery);
            } else {
                var defCenter = getActiveCountryCenter();
                panMapToLocation(defCenter.lat, defCenter.lng);
                $('#modal_map_status').html('<span class="text-muted"><i class="fa fa-map-marker text-danger"></i> Move map or search location</span>');
                reverseGeocodePosition(defCenter.lat, defCenter.lng, false);
            }

        }, { timeout: 8000, enableHighAccuracy: true, maximumAge: 60000 });

    }



    // Dependent Dropdowns: Country -> Region



    function modalGetSubCatList(countryId, callback) {

        // Always reset both Region and Area dropdowns when Country changes
        $('#modal_region_id').html('<option value="">{{ @Helper::language("choose_region_web") }}</option>');
        $('#modal_area_id').html('<option value="">{{ @Helper::language("choose_area_web") }}</option>');

        if (!countryId) {
            if (callback) callback();
            return;
        }



        $('#modal_region_loader').removeClass('d-none');



        $.ajax({



            url: urlGetSubcatlist,



            type: "POST",



            data: {



                id: countryId,



                _token: csrfToken



            },



            dataType: 'json',



            success: function(res) {



                $('#modal_region_loader').addClass('d-none');



                var html = '<option value="">{{ @Helper::language("choose_region_web") }}</option>';



                if (res && res.sub) {



                    $.each(res.sub, function(key, val) {



                        html += '<option value="' + val.id + '">' + val.title + '</option>';



                    });



                }



                $('#modal_region_id').html(html);



                if (callback) callback();



            },



            error: function() {



                $('#modal_region_loader').addClass('d-none');



                if (callback) callback();



            }



        });



    }



    // Dependent Dropdowns: Region -> Area



    function modalGetAreaList(regionId, callback) {

        // Always reset Area dropdown when Region changes
        $('#modal_area_id').html('<option value="">{{ @Helper::language("choose_area_web") }}</option>');

        if (!regionId) {
            if (callback) callback();
            return;
        }



        $('#modal_area_loader').removeClass('d-none');



        $.ajax({



            url: urlGetArealist,



            type: "POST",



            data: {



                id: regionId,



                _token: csrfToken



            },



            dataType: 'json',



            success: function(res) {



                $('#modal_area_loader').addClass('d-none');



                var html = '<option value="">{{ @Helper::language("choose_area_web") }}</option>';



                if (res && res.sub) {



                    $.each(res.sub, function(key, val) {



                        html += '<option value="' + val.id + '">' + val.title + '</option>';



                    });



                }



                $('#modal_area_id').html(html);



                if (callback) callback();



            },



            error: function() {



                $('#modal_area_loader').addClass('d-none');



                if (callback) callback();



            }



        });



    }



    // Open Unified Address Modal (Point 1: Modularized handler)

    window.resetMapToSavedAddressLocation = function() {
        if (window.savedAddressLat && window.savedAddressLng) {
            isProgrammaticMove = true;
            currentMarkerLat = window.savedAddressLat;
            currentMarkerLng = window.savedAddressLng;
            $('#modal_latitude').val(window.savedAddressLat.toFixed(6));
            $('#modal_longitude').val(window.savedAddressLng.toFixed(6));
            hasLocationBeenSet = true;
            panMapToLocation(window.savedAddressLat, window.savedAddressLng);
            updateCoordsBadge(window.savedAddressLat, window.savedAddressLng);
            $('#modal_address_location_alert').addClass('d-none');
            $('#modal_map_status').html('<span class="text-success"><i class="fa fa-check-circle me-1"></i> Reset to saved location</span>');
            setTimeout(function() { $('#modal_map_status').empty(); }, 2500);
            reverseGeocodePosition(window.savedAddressLat, window.savedAddressLng, true);
        }
    };

    var savedPageScrollY = 0;
    var isAddressModalOpen = false;

    function lockWindowScroll() {
        if (isAddressModalOpen) {
            window.scrollTo(0, savedPageScrollY);
        }
    }

    function resetAddressModalScroll() {
        var elements = document.querySelectorAll('#addressModal, #addressModal .modal-dialog, #addressModal .modal-content, #addressModal .modal-body, #addressModal .modal-form-col, #unified_address_form');
        for (var i = 0; i < elements.length; i++) {
            elements[i].scrollTop = 0;
        }
        $('#addressModal, #addressModal .modal-dialog, #addressModal .modal-content, #addressModal .modal-body, #addressModal .modal-form-col, #unified_address_form').scrollTop(0);
    }

    function openAddressModal(type, action, id, isCheckout, initialLat, initialLng) {

        if (!savedPageScrollY || savedPageScrollY === 0) {
            savedPageScrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        }
        isAddressModalOpen = true;
        resetAddressModalScroll();

        window.savedAddressLat = null;
        window.savedAddressLng = null;
        currentMarkerLat = null;
        currentMarkerLng = null;
        hasLocationBeenSet = false;
        isLocationServiceable = false;
        isProgrammaticMove = true;
        userChosenAddress = null;

        window.modalActiveIds = window.modalActiveIds || { shipping: '', billing: '' };
        window.modalAddressCache = { shipping: null, billing: null };

        var modalActiveIds = window.modalActiveIds;
        var modalAddressCache = window.modalAddressCache;

        type = type || 'shipping';
        action = action || 'add';
        id = id || '';

        // If initial lat/lng was passed directly from button data attributes, apply immediately!
        if (initialLat && initialLng) {
            var pLat = parseFloat(initialLat);
            var pLng = parseFloat(initialLng);
            if (!isNaN(pLat) && !isNaN(pLng) && pLat !== 0 && pLng !== 0) {
                window.savedAddressLat = pLat;
                window.savedAddressLng = pLng;
                currentMarkerLat = pLat;
                currentMarkerLng = pLng;
                hasLocationBeenSet = true;
                $('#modal_latitude').val(pLat.toFixed(6));
                $('#modal_longitude').val(pLng.toFixed(6));
                initAddressMap(pLat, pLng);
                panMapToLocation(pLat, pLng);
                updateCoordsBadge(pLat, pLng);
                $('#modal_address_location_alert').addClass('d-none');
                reverseGeocodePosition(pLat, pLng, true);
            }
        }



        // Auto-detect if on checkout page

        if (typeof isCheckout === 'undefined' || isCheckout === null) {

            isCheckout = ($('#billingAddressContainer').length > 0 || window.location.pathname.indexOf('checkout') !== -1) ? 1 : 0;

        } else {

            isCheckout = parseInt(isCheckout) || 0;

        }



        // Store active IDs

        if (type === 'shipping') {

            modalActiveIds.shipping = id;

            if (!modalActiveIds.billing) {

                modalActiveIds.billing = $('#user_bill_address_id').val() || $('.open-address-modal[data-type="billing"][data-action="edit"]').first().data('id') || '';

            }

        } else {

            modalActiveIds.billing = id;

            if (!modalActiveIds.shipping) {

                modalActiveIds.shipping = $('input[name="address_radio"]:checked').val() || $('.open-address-modal[data-type="shipping"][data-action="edit"]').first().data('id') || '';

            }

        }



        $('#modal_address_id').val(id);

        $('#modal_address_type').val(type);

        $('#modal_action_type').val(action);

        $('#modal_checkout_page').val(isCheckout);

        $('#modal_latitude').val('');

        $('#modal_longitude').val('');

        hasLocationBeenSet = false;

        isLocationServiceable = false;

        window.lastGeocodedAddressData = null;

        window.lastGeocodedLat = null;

        window.lastGeocodedLng = null;

        window.modalAddDrafts = { shipping: null, billing: null };

        if (action === 'add') {

            $('#modal_address_location_alert').removeClass('d-none').html('<i class="fa fa-exclamation-triangle me-1"></i> Please set your ' + (type === 'shipping' ? 'pickup/delivery' : 'billing') + ' location on the map.');

        } else {

            $('#modal_address_location_alert').addClass('d-none').empty();

        }

        $('#modal_map_boundary_badge').html(

            '<i class="fa fa-map-marker-alt me-1"></i> ' + (type === 'shipping' ? 'Delivery Zone' : 'Billing Zone')

        );

        $('#modal_alert_box').empty();

        $('#map_search_suggestions').addClass('d-none').empty();

        $('#map_location_search_input').val('');



        // Reset form
        $('#unified_address_form')[0].reset();
        $('#unified_address_form').removeClass('was-validated');

        window.modalAddressCache = { shipping: null, billing: null };

        // Pre-select default country
        selectDefaultCountry();

        // Switch to appropriate tab without premature loadAndDisplayAddress
        switchAddressModalType(type, false);



        // Reset scroll strictly BEFORE opening modal
        resetAddressModalScroll();

        // Show modal
        $('#addressModal').modal('show');



        if (action === 'edit' && id) {

            loadAndDisplayAddress(type, id);

        } else {

            // New Address -> initialize map centered on active hub/country

            var defaultCountry = $('#modal_country_id').val();

            if (defaultCountry) {

                modalGetSubCatList(defaultCountry);

            }

            var defCenter = getActiveCountryCenter();

            initAddressMap(defCenter.lat, defCenter.lng);

            panMapToLocation(defCenter.lat, defCenter.lng);

            // Automatically detect current GPS location in Add Mode
            autoDetectCurrentLocationOrFallback();

        }

        if (typeof resetAddressModalScroll === 'function') {
            resetAddressModalScroll();
        }

    }



    // Modal Lifecycle Listeners (Point 3: Hide floating cart, Point 6: Lock background scroll)



    $(document).ready(function() {

        $('#addressModal').on('show.bs.modal', function() {

            if (!savedPageScrollY || savedPageScrollY === 0) {
                savedPageScrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
            }

            isAddressModalOpen = true;
            $('body').addClass('modal-open');
            window.addEventListener('scroll', lockWindowScroll, { passive: true });
            resetAddressModalScroll();

            if (savedPageScrollY > 0) {
                window.scrollTo(0, savedPageScrollY);
            }

        });

        $('#addressModal').on('hide.bs.modal', function() {
            resetAddressModalScroll();
        });

        $('#addressModal').on('hidden.bs.modal', function() {

            isAddressModalOpen = false;
            window.removeEventListener('scroll', lockWindowScroll);
            $('body').removeClass('modal-open').css('overflow', '');

            if (savedPageScrollY > 0) {
                window.scrollTo(0, savedPageScrollY);
                savedPageScrollY = 0;
            }

            resetAddressModalScroll();

            // CRITICAL: Clean up moved coordinates when modal closes
            // so next time user clicks button, it won't show old moved position!
            window.modalAddressCache = { shipping: null, billing: null };
            window.savedAddressLat = null;
            window.savedAddressLng = null;
            currentMarkerLat = null;
            currentMarkerLng = null;
            hasLocationBeenSet = false;

        });

        $('#addressModal').on('shown.bs.modal', function() {
            var refreshMap = function() {
                var targetLat = window.savedAddressLat || currentMarkerLat;
                var targetLng = window.savedAddressLng || currentMarkerLng;
                if (usingLeaflet && leafletMap) {
                    leafletMap.invalidateSize({ pan: false });
                    if (targetLat && targetLng) {
                        leafletMap.setView([targetLat, targetLng], leafletMap.getZoom() || 15, { animate: false });
                    }
                } else if (addressMap) {
                    google.maps.event.trigger(addressMap, 'resize');
                    if (targetLat && targetLng) {
                        addressMap.setCenter({ lat: targetLat, lng: targetLng });
                    }
                }
            };
            refreshMap();
            setTimeout(refreshMap, 150);
        });

        // Point 6: Prevent background body from scrolling when mouse wheel / touch occurs inside or over modal
        var addressModalEl = document.getElementById('addressModal');
        if (addressModalEl) {
            addressModalEl.addEventListener('wheel', function(e) {
                var scrollContainer = e.target.closest('.modal-form-col, .modal-body');
                if (!scrollContainer) {
                    e.preventDefault();
                    return false;
                }
                var delta = e.deltaY;
                var atTop = delta < 0 && scrollContainer.scrollTop <= 0;
                var atBottom = delta > 0 && (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight - 1);
                if (atTop || atBottom) {
                    e.preventDefault();
                    return false;
                }
            }, { passive: false });

            var touchStartY = 0;
            addressModalEl.addEventListener('touchstart', function(e) {
                if (e.touches && e.touches.length) {
                    touchStartY = e.touches[0].clientY;
                }
            }, { passive: true });

            addressModalEl.addEventListener('touchmove', function(e) {
                var scrollContainer = e.target.closest('.modal-form-col, .modal-body');
                if (!scrollContainer) {
                    e.preventDefault();
                    return false;
                }
                if (e.touches && e.touches.length) {
                    var currentY = e.touches[0].clientY;
                    var delta = touchStartY - currentY;
                    var atTop = delta < 0 && scrollContainer.scrollTop <= 0;
                    var atBottom = delta > 0 && (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight - 1);
                    if (atTop || atBottom) {
                        e.preventDefault();
                        return false;
                    }
                }
            }, { passive: false });
        }



        // Field Validation function
        function validateAddressModalForm() {
            var isValid = true;
            var firstInvalid = null;

            // Clear previous errors
            $('#addressModal label.error-text').hide().text('');
            $('#addressModal .form-control, #addressModal .form-select').removeClass('is-invalid');
            $('#modal_alert_box').empty();

            // 1. Name
            var name = $.trim($('#modal_name').val());
            if (!name) {
                $('#modal_name').addClass('is-invalid');
                $('#modal_name_error').text("{{ @Helper::language('name_field_is_required') ?: 'Name field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_name');
            } else if (name.length > 40) {
                $('#modal_name').addClass('is-invalid');
                $('#modal_name_error').text("{{ @Helper::language('the_name_may_not_be_greater_than_40_characters') ?: 'The name may not be greater than 40 characters' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_name');
            }

            // 2. Phone
            var phone = $.trim($('#modal_phone').val());
            if (!phone) {
                $('#modal_phone').addClass('is-invalid');
                $('#modal_phone_error').text("{{ @Helper::language('phone_number_field_is_required') ?: 'Phone number field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_phone');
            } else if (phone.length < 8 || phone.length > 15) {
                $('#modal_phone').addClass('is-invalid');
                $('#modal_phone_error').text("{{ @Helper::language('phone_number_min_max') ?: 'Phone number must be between 8 and 15 digits' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_phone');
            }

            // 3. Country
            var country = $('#modal_country_id').val();
            if (!country) {
                $('#modal_country_id').addClass('is-invalid');
                $('#modal_country_id_error').text("{{ @Helper::language('validation_country_required') ?: 'Country field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_country_id');
            }

            // 4. Region
            var region = $('#modal_region_id').val();
            if (!region) {
                $('#modal_region_id').addClass('is-invalid');
                $('#modal_region_id_error').text("{{ @Helper::language('validation_region_required') ?: 'Region field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_region_id');
            }

            // 5. City
            var city = $.trim($('#modal_city').val());
            if (!city) {
                $('#modal_city').addClass('is-invalid');
                $('#modal_city_error').text("{{ @Helper::language('city_field_is_required') ?: 'City field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_city');
            }

            // 6. Area
            var area = $('#modal_area_id').val();
            if (!area) {
                $('#modal_area_id').addClass('is-invalid');
                $('#modal_area_id_error').text("{{ @Helper::language('validation_area_required') ?: 'Area field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_area_id');
            }

            // 7. Street Address
            var address = $.trim($('#modal_address').val());
            if (!address) {
                $('#modal_address').addClass('is-invalid');
                $('#modal_address_error').text("{{ @Helper::language('address_field_is_required') ?: 'Address field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_address');
            }

            // 8. Zip Code
            var zip = $.trim($('#modal_zip_code').val());
            if (!zip) {
                $('#modal_zip_code').addClass('is-invalid');
                $('#modal_zip_code_error').text("{{ @Helper::language('zipcode_field_is_required') ?: 'Zipcode field is required' }}").show();
                isValid = false;
                if (!firstInvalid) firstInvalid = $('#modal_zip_code');
            }

            if (!isValid && firstInvalid) {
                firstInvalid.focus();
            }

            return isValid;
        }

        // Live clear error on input / change
        $('#modal_name, #modal_phone, #modal_country_id, #modal_region_id, #modal_city, #modal_area_id, #modal_address, #modal_zip_code').on('input change', function() {
            $(this).removeClass('is-invalid');
            var fId = $(this).attr('id');
            $('#' + fId + '_error').hide().text('');
        });

        // Strict Input Restrictions:
        // 1. Name: only letters and spaces allowed (no numbers, no special characters)
        $('#modal_name').on('keypress', function(e) {
            if (e.which === 0 || e.which === 8 || e.which === 9 || e.which === 13 || e.which === 27) return true;
            var char = String.fromCharCode(e.which);
            if (!/^[a-zA-Z\s]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        }).on('input paste', function() {
            var $el = $(this);
            setTimeout(function() {
                var val = $el.val();
                var clean = val.replace(/[^a-zA-Z\s]/g, '');
                if (val !== clean) {
                    $el.val(clean);
                }
            }, 0);
        });

        // 2. Mobile Number: only digits (0-9) allowed
        $('#modal_phone').on('keypress', function(e) {
            if (e.which === 0 || e.which === 8 || e.which === 9 || e.which === 13 || e.which === 27) return true;
            var char = String.fromCharCode(e.which);
            if (!/^[0-9]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        }).on('input paste', function() {
            var $el = $(this);
            setTimeout(function() {
                var val = $el.val();
                var clean = val.replace(/[^0-9]/g, '');
                if (val !== clean) {
                    $el.val(clean);
                }
            }, 0);
        });

        // 3. Zip Code: only uppercase characters (A-Z, 0-9) allowed
        $('#modal_zip_code').on('keypress', function(e) {
            if (e.which === 0 || e.which === 8 || e.which === 9 || e.which === 13 || e.which === 27) return true;
            var char = String.fromCharCode(e.which);
            if (!/^[a-zA-Z0-9]$/.test(char)) {
                e.preventDefault();
                return false;
            }
        }).on('input paste', function() {
            var $el = $(this);
            setTimeout(function() {
                var val = $el.val();
                var clean = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (val !== clean) {
                    $el.val(clean);
                }
            }, 0);
        });

        // Form submit handler
        $('#unified_address_form').on('submit', function(e) {
            e.preventDefault();

            // Run field validations first
            if (!validateAddressModalForm()) {
                return false;
            }



            var latVal = $('#modal_latitude').val();

            var lngVal = $('#modal_longitude').val();



            var type = $('#modal_address_type').val();

            var action = $('#modal_action_type').val();

            var addressId = $('#modal_address_id').val();

            var isCheckout = parseInt($('#modal_checkout_page').val()) || 0;



            // Strict Map Location Validation for BOTH Shipping and Billing against serviceable locations pool

            if (!hasLocationBeenSet || !latVal || !lngVal || !isLocationServiceable) {

                var locNotice = (!latVal || !lngVal || !hasLocationBeenSet)

                    ? ((type === 'shipping' ? 'Pickup/delivery' : 'Billing') + ' location is not set on the map. Please pin your exact location on the map before saving.')

                    : (type === 'shipping' 

                        ? 'The pinned location is outside our allowed delivery area. Please pin your location inside our allowed Country, Region, and Area before saving.'

                        : 'The pinned location is outside our allowed billing area. Please pin your location inside our allowed Country, Region, and Area before saving.');



                $('#modal_alert_box').html(

                    '<div class="alert alert-danger py-2 d-flex align-items-center gap-2">' +

                        '<i class="fa fa-exclamation-triangle"></i>' +

                        '<span>' + locNotice + '</span>' +

                    '</div>'

                );

                $('#modal_address_location_alert').removeClass('d-none').html(

                    '<i class="fa fa-exclamation-triangle me-1"></i> ' + locNotice

                );

                $('#modal_address').focus();

                return false;

            }



            var targetUrl = "";



            if (type === 'shipping') {



                targetUrl = (action === 'edit' && addressId) ? urlUpdateAddress : urlStoreAddress;



            } else {



                targetUrl = (action === 'edit' && addressId) ? urlUpdateBillAddress : urlStoreBillAddress;



            }



            // Alag alag functions ke liye unke original field parameters



            var payload = {



                _token: csrfToken



            };



            if (type === 'shipping') {



                // Shipping Address (storeAddress / updateAddress)



                payload.name = $('#modal_name').val();



                payload.phonecode = $('#modal_phonecode').val();



                payload.phone = $('#modal_phone').val();



                payload.country_id = $('#modal_country_id').val();



                payload.region_id = $('#modal_region_id').val();



                payload.area_id = $('#modal_area_id').val();



                payload.city = $('#modal_city').val();



                payload.address = $('#modal_address').val();



                payload.zip_code = $('#modal_zip_code').val();

                payload.latitude = $('#modal_latitude').val();

                payload.longitude = $('#modal_longitude').val();



                payload.checkout_page = isCheckout;
                payload.is_default_shipping = $('#modal_is_default_shipping').is(':checked') ? 1 : 0;
                payload.isDefaultShipping = payload.is_default_shipping;



                if (isCheckout === 1) {



                    payload.delivery_options = $('input[name="delivery_options"]:checked').val() || 1;



                    payload.instruction = $('#modal_instruction').val() || '';



                }



                if (action === 'edit' && addressId) {



                    payload.edit_address_id = addressId;



                }



            } else {



                // Billing Address (storeBillAddress / updateBillAddress)



                payload.bill_name = $('#modal_name').val();



                payload.bill_phonecode = $('#modal_phonecode').val();



                payload.bill_phone = $('#modal_phone').val();



                payload.bill_country_id = $('#modal_country_id').val();



                payload.bill_region_id = $('#modal_region_id').val();



                payload.bill_area_id = $('#modal_area_id').val();



                payload.bill_city = $('#modal_city').val();



                payload.bill_address = $('#modal_address').val();



                payload.bill_zip_code = $('#modal_zip_code').val();

                payload.bill_latitude = $('#modal_latitude').val();

                payload.bill_longitude = $('#modal_longitude').val();

                payload.latitude = $('#modal_latitude').val();

                payload.longitude = $('#modal_longitude').val();



                payload.isDefault = 1;



                if (action === 'edit' && addressId) {



                    payload.edit_bill_address_id = addressId;



                }



            }



            // Loading state



            $('#btn_save_text').text('Saving...');



            $('#btn_save_spinner').removeClass('d-none');



            $('#btn_save_unified_address').prop('disabled', true);



            $('#modal_alert_box').html('');



            $.ajax({



                url: targetUrl,



                type: 'POST',



                data: payload,



                dataType: 'json',



                success: function(res) {



                    $('#btn_save_text').text('{{ @Helper::language("save_details_btn") }}');



                    $('#btn_save_spinner').addClass('d-none');



                    $('#btn_save_unified_address').prop('disabled', false);



                    if (res && (res.success === true || res.success === 'true')) {

                        $('#addressModal').modal('hide');

                        if (isCheckout === 1 && typeof refreshCheckoutAddresses === 'function') {
                            refreshCheckoutAddresses(res.id, type, action);
                        } else {
                            window.location.reload();
                        }

                    } else {



                        var msg = (res && res.message) ? res.message : 'Failed to save address. Please check fields.';



                        $('#modal_alert_box').html('<div class="alert alert-danger py-2">' + msg + '</div>');



                    }



                },



                error: function(xhr) {
                    $('#btn_save_text').text('{{ @Helper::language("save_details_btn") }}');
                    $('#btn_save_spinner').addClass('d-none');
                    $('#btn_save_unified_address').prop('disabled', false);

                    var errMsg = 'An error occurred while saving address.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, msgs) {
                                var cleanKey = key.replace('bill_', '');
                                var $field = $('#modal_' + cleanKey);
                                var $errLabel = $('#modal_' + cleanKey + '_error');
                                var msgText = Array.isArray(msgs) ? msgs[0] : msgs;
                                if ($field.length) {
                                    $field.addClass('is-invalid');
                                }
                                if ($errLabel.length) {
                                    $errLabel.text(msgText).show();
                                }
                            });
                            return;
                        } else if (xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                            var lowerMsg = errMsg.toLowerCase();
                            if (lowerMsg.indexOf('name') !== -1) {
                                $('#modal_name').addClass('is-invalid');
                                $('#modal_name_error').text(errMsg).show();
                                $('#modal_name').focus();
                            } else if (lowerMsg.indexOf('phone') !== -1) {
                                $('#modal_phone').addClass('is-invalid');
                                $('#modal_phone_error').text(errMsg).show();
                                $('#modal_phone').focus();
                            } else if (lowerMsg.indexOf('country') !== -1) {
                                $('#modal_country_id').addClass('is-invalid');
                                $('#modal_country_id_error').text(errMsg).show();
                                $('#modal_country_id').focus();
                            } else if (lowerMsg.indexOf('region') !== -1) {
                                $('#modal_region_id').addClass('is-invalid');
                                $('#modal_region_id_error').text(errMsg).show();
                                $('#modal_region_id').focus();
                            } else if (lowerMsg.indexOf('area') !== -1) {
                                $('#modal_area_id').addClass('is-invalid');
                                $('#modal_area_id_error').text(errMsg).show();
                                $('#modal_area_id').focus();
                            } else if (lowerMsg.indexOf('street') !== -1 || lowerMsg.indexOf('address') !== -1) {
                                $('#modal_address').addClass('is-invalid');
                                $('#modal_address_error').text(errMsg).show();
                                $('#modal_address').focus();
                            } else if (lowerMsg.indexOf('city') !== -1) {
                                $('#modal_city').addClass('is-invalid');
                                $('#modal_city_error').text(errMsg).show();
                                $('#modal_city').focus();
                            } else if (lowerMsg.indexOf('zip') !== -1) {
                                $('#modal_zip_code').addClass('is-invalid');
                                $('#modal_zip_code_error').text(errMsg).show();
                                $('#modal_zip_code').focus();
                            } else {
                                $('#modal_alert_box').html('<div class="alert alert-danger py-2">' + errMsg + '</div>');
                            }
                            return;
                        }
                    }
                    $('#modal_alert_box').html('<div class="alert alert-danger py-2">' + errMsg + '</div>');
                }



            });



        });



        // Delegate open-address-modal clicks anywhere on page



        $(document).on('click', '.open-address-modal', function(e) {

            e.preventDefault();

            savedPageScrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
            resetAddressModalScroll();

            var type = $(this).data('type') || 'shipping';



            var action = $(this).data('action') || 'add';



            var id = $(this).data('id') || '';



            var isCheckout = $(this).data('is-checkout');

            var dataLat = $(this).data('lat') || $(this).attr('data-lat') || '';
            var dataLng = $(this).data('lng') || $(this).attr('data-lng') || '';

            openAddressModal(type, action, id, isCheckout, dataLat, dataLng);



        });



    });



</script>

