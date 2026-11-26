@extends('frontend.layouts.app')
@section('title', 'Products | Liquor Junction')

@section('content')
<script>
    // Critical: Define currency_type before any layout scripts run to prevent ReferenceError
    var currency_type = "{{ Helper::Settings('currency_symbol') ?: 'GH₵' }}";
</script>

@include('sweetalert::alert')

<style>
    /* =========================================================
       NATIVE THEME COMPLIANCE & PREMIUM FILTER SIDEBAR UI
       ========================================================= */
    :root {
        --lj-gold: #fbb516;
        --lj-black: #242424;
        --lj-muted: #858584;
        --lj-light-bg: #f8f9fa;
        --lj-border: #e9ecef;
    }

    /* Keep native promo badges identical to theme */
    .bogo {
        top: 5px;
        right: 14px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding: 4px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
    }

    .offer {
        top: 5px;
        right: 14px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding: 8px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
    }

    /* Sticky Filter Sidebar Container */
    .faceted-sidebar-card {
        background: #ffffff;
        border: 1px solid var(--lj-border);
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        position: sticky;
        top: 90px;
        max-height: calc(100vh - 110px);
        overflow-y: auto;
    }
    .faceted-sidebar-card::-webkit-scrollbar {
        width: 4px;
    }
    .faceted-sidebar-card::-webkit-scrollbar-thumb {
        background: #dcdcdc;
        border-radius: 4px;
    }

    /* Filter Header */
    .widget-head {
        padding: 16px 20px;
        background: #fbfbfb;
        border-bottom: 1px solid var(--lj-border);
    }
    .widget-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--lj-black);
        letter-spacing: 0.3px;
    }
    .reset-link {
        font-size: 12px;
        font-weight: 600;
        color: #d32f2f;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .reset-link:hover {
        color: #b71c1c;
        text-decoration: underline;
    }

    /* Facet Accordion Groups */
    .facet-group {
        border-bottom: 1px solid var(--lj-border);
    }
    .facet-header {
        padding: 14px 20px;
        cursor: pointer;
        user-select: none;
        background: #ffffff;
        transition: background 0.2s ease;
    }
    .facet-header:hover {
        background: #fafafa;
    }
    .facet-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--lj-black);
    }
    .facet-arrow {
        font-size: 12px;
        color: var(--lj-muted);
        transition: transform 0.25s ease;
    }
    .facet-arrow.rotated {
        transform: rotate(180deg);
    }

    .facet-body {
        padding: 8px 20px 16px;
    }

    /* Facet List & Checkboxes */
    .facet-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .see-more-wrap {
        padding-top: 5px;
        padding-bottom: 2px;
    }
    .btn-see-more {
        background: transparent;
        border: none;
        padding: 4px 0;
        font-size: 12.5px;
        font-weight: 600;
        color: #b38600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s ease;
        text-decoration: none;
        line-height: 1.2;
    }
    .btn-see-more:hover {
        color: var(--lj-black);
    }
    .btn-see-more i {
        font-size: 11px;
    }
    .sub-see-more-wrap {
        padding-left: 24px;
        margin-top: 2px;
    }
    .sub-see-more-btn {
        font-size: 11.5px;
        color: #777777;
    }
    .sub-see-more-btn:hover {
        color: var(--lj-black);
    }

    .facet-item {
        padding: 6px 0;
        transition: opacity 0.2s;
    }
    .facet-row-inner {
        padding: 3px 0;
    }
    .facet-item.zero-count {
        display: none !important;
    }

    /* Custom Checkbox UI */
    .custom-check-wrap {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        position: relative;
        user-select: none;
        margin-bottom: 0;
        flex: 1;
    }
    .custom-check-wrap input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .check-box-ui {
        width: 17px;
        height: 17px;
        background-color: #ffffff;
        border: 1.5px solid #c9c9c9;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .custom-check-wrap input:checked ~ .check-box-ui {
        background-color: var(--lj-gold);
        border-color: var(--lj-gold);
    }
    .custom-check-wrap input:checked ~ .check-box-ui::after {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        font-size: 10px;
        color: var(--lj-black);
    }

    .radio-box-ui {
        width: 17px;
        height: 17px;
        border-radius: 50%;
        border: 1.5px solid #c9c9c9;
        display: inline-block;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .custom-check-wrap input:checked ~ .radio-box-ui {
        border-color: var(--lj-gold);
        background-color: var(--lj-gold);
        box-shadow: inset 0 0 0 3px #fff;
    }

    .check-label {
        font-size: 13.5px;
        color: var(--lj-black);
        font-weight: 400;
    }
    .stars-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        line-height: 1;
    }
    .stars-group {
        display: inline-flex;
        align-items: center;
        gap: 2.5px;
        line-height: 1;
        width: 90px;
        flex-shrink: 0;
    }
    .stars-group i,
    .stars-label i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        font-size: 13px;
        line-height: 1;
        text-align: center;
        flex-shrink: 0;
        vertical-align: middle;
        box-sizing: border-box;
    }
    .stars-group .star-filled,
    .stars-gold i {
        color: #f59e0b;
    }
    .stars-group .star-empty,
    .stars-label i.fa-regular {
        color: #d1d5db;
    }
    .stars-text {
        font-size: 12.5px;
        color: #4b5563;
        font-weight: 500;
        white-space: nowrap;
    }

    .count-pill {
        font-size: 11px;
        font-weight: 500;
        color: #757575;
        background: #f1f1f1;
        padding: 2px 7px;
        border-radius: 10px;
        flex-shrink: 0;
    }

    /* Subcategories */
    .facet-sub-wrap {
        display: none;
    }
    .facet-sub-wrap.show {
        display: block;
    }
    .facet-sub-list {
        list-style: none;
        padding-left: 24px;
        margin: 4px 0 0;
        display: block;
    }
    .facet-sub-item {
        padding: 4px 0;
    }
    .sub-label {
        font-size: 12.5px;
        color: #555555;
    }
    .sub-pill {
        font-size: 10px;
        padding: 1px 5px;
    }
    .sub-toggle-btn {
        cursor: pointer;
        color: #858584;
        font-size: 11px;
        padding: 2px 4px;
        width: 20px;
        text-align: center;
        display: inline-block;
    }
    .sub-toggle-btn:hover {
        color: var(--lj-black);
    }
    .sub-toggle-btn-placeholder {
        width: 20px;
        height: 14px;
        display: inline-block;
        flex-shrink: 0;
    }

    /* Chip Pills Grid for Brand & Bottle Size (Compact multi-line tag cloud) */
    .facet-chips-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        list-style: none;
        padding: 4px 0 2px;
        margin: 0;
    }
    .facet-chip-item {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .facet-chip-item.zero-count {
        display: none !important;
    }
    .chip-filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 9px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 16px;
        font-size: 11.5px;
        font-weight: 500;
        color: #242424;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
        position: relative;
        margin: 0;
        line-height: 1.2;
        max-width: 100%;
    }
    .chip-filter-btn input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }
    .chip-filter-btn:hover {
        background: #fffdf5;
        border-color: var(--lj-gold);
        box-shadow: 0 1px 3px rgba(251, 181, 22, 0.15);
    }
    .chip-filter-btn .chip-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 130px;
    }
    .chip-filter-btn .chip-count {
        font-size: 10px;
        font-weight: 600;
        color: #6c757d;
        background: #e9ecef;
        padding: 1px 5px;
        border-radius: 10px;
        transition: all 0.15s ease;
    }
    /* Active / Checked State */
    .chip-filter-btn.active,
    .chip-filter-btn:has(input:checked) {
        background: var(--lj-gold);
        border-color: var(--lj-gold);
        color: #242424;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(251, 181, 22, 0.25);
    }
    .chip-filter-btn.active .chip-count,
    .chip-filter-btn:has(input:checked) .chip-count {
        background: rgba(0, 0, 0, 0.14);
        color: #242424;
    }
    .chip-filter-btn:has(input:disabled) {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Price Slider Styling */
    .price-display-tag {
        background: #fbfbfb;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ebebeb;
        margin-bottom: 12px;
    }
    .price-val {
        font-size: 14px;
        font-weight: 700;
        color: var(--lj-black);
    }
    .price-sep {
        font-size: 12px;
        color: var(--lj-muted);
        text-transform: uppercase;
    }

    .range-slider-box {
        position: relative;
        height: 24px;
        margin: 10px 0 16px;
    }
    .range-track-bg {
        position: absolute;
        top: 9px;
        width: 100%;
        height: 6px;
        background: #e4e2e2;
        border-radius: 4px;
    }
    .range-track-active {
        position: absolute;
        top: 9px;
        height: 6px;
        background: var(--lj-gold);
        border-radius: 4px;
    }
    .range-slider-box input[type="range"] {
        position: absolute;
        top: 0;
        width: 100%;
        height: 24px;
        background: none;
        pointer-events: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }
    .range-slider-box input[type="range"]::-webkit-slider-thumb {
        height: 18px;
        width: 18px;
        border-radius: 50%;
        background: var(--lj-gold);
        border: 2px solid var(--lj-black);
        cursor: pointer;
        pointer-events: auto;
        -webkit-appearance: none;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }
    .range-slider-box input[type="range"]::-moz-range-thumb {
        height: 18px;
        width: 18px;
        border-radius: 50%;
        background: var(--lj-gold);
        border: 2px solid var(--lj-black);
        cursor: pointer;
        pointer-events: auto;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }

    .price-input-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--lj-muted);
        margin-bottom: 3px;
        display: block;
    }
    .input-with-sym {
        display: flex;
        align-items: center;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        padding: 2px 6px;
        background: #ffffff;
        height: 32px;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-with-sym:focus-within {
        border-color: var(--lj-gold);
        box-shadow: 0 0 0 2px rgba(251, 181, 22, 0.2);
    }
    .input-with-sym span {
        font-size: 11px;
        color: var(--lj-muted);
        margin-right: 3px;
        white-space: nowrap;
        flex-shrink: 0;
        user-select: none;
    }
    .input-with-sym input {
        width: 100%;
        min-width: 0;
        border: none;
        outline: none;
        font-size: 12px;
        font-weight: 600;
        color: var(--lj-black);
        padding: 0;
        background: transparent;
        -moz-appearance: textfield;
    }
    .input-with-sym input::-webkit-outer-spin-button,
    .input-with-sym input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .quick-chips-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }
    .quick-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 9px;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .quick-chip:hover {
        background: var(--lj-gold);
        border-color: var(--lj-gold);
        color: var(--lj-black);
    }

    /* Brand Search Box */
    .brand-search-box {
        position: relative;
        margin-top: 6px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .brand-search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 13px;
        color: #94a3b8;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        width: 14px;
        height: 14px;
        z-index: 2;
    }
    .brand-search-box input,
    .brand-search-box .brand-search-input {
        width: 100%;
        height: 36px;
        padding: 6px 14px 6px 38px !important;
        border: 1px solid #dcdcdc;
        border-radius: 20px !important;
        font-size: 13px;
        line-height: normal;
        background: #ffffff;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .brand-search-box input:focus,
    .brand-search-box .brand-search-input:focus {
        border-color: var(--lj-gold);
        box-shadow: 0 0 0 2px rgba(251, 181, 22, 0.2);
    }
    .brand-search-box input::placeholder,
    .brand-search-box .brand-search-input::placeholder {
        color: #94a3b8;
        font-size: 12.5px;
    }

    /* Active Filter Pills Bar (Chips) - Swiper Slider Integration */
    .active-filter-pills-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 12px;
        margin-top: -2px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        min-height: 38px;
        position: relative;
    }
    .active-pills-label-wrap {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
        font-size: 11.5px;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding-right: 8px;
        border-right: 1px solid #e9ecef;
    }
    .active-pills-slider.swiper {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        padding: 2px 0;
        margin: 0 4px;
    }
    .active-pills-slider .swiper-wrapper {
        display: flex;
        align-items: center;
    }
    .filter-chip-slide.swiper-slide {
        width: auto !important;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
    }
    .pills-nav-btn {
        width: 22px;
        height: 22px;
        min-width: 22px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9.5px;
        cursor: pointer;
        padding: 0;
        transition: all 0.2s ease;
        flex-shrink: 0;
        user-select: none;
    }
    .pills-nav-btn:hover:not(.swiper-button-disabled):not(.swiper-button-lock) {
        background: var(--lj-gold, #fbb516);
        border-color: var(--lj-gold, #fbb516);
        color: #242424;
    }
    .pills-nav-btn.swiper-button-disabled,
    .pills-nav-btn.swiper-button-lock {
        opacity: 0.2;
        cursor: default;
        pointer-events: none;
    }
    .active-pills-track {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #242424;
        font-size: 11.5px;
        font-weight: 500;
        padding: 3px 8px 3px 10px;
        border-radius: 14px;
        flex-shrink: 0;
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .filter-chip:hover {
        background: #fffdf5;
        border-color: var(--lj-gold);
        box-shadow: 0 1px 3px rgba(251, 181, 22, 0.15);
    }
    .filter-chip .chip-text {
        white-space: nowrap;
        line-height: 1.2;
    }
    .chip-remove {
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        margin-left: 2px;
        transition: all 0.15s ease;
    }
    .chip-remove:hover {
        background: #dc3545;
        color: #ffffff;
    }
    .btn-clear-all {
        flex-shrink: 0;
        background: transparent;
        border: none;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 700;
        color: #dc3545;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        cursor: pointer;
        border-radius: 4px;
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .btn-clear-all:hover {
        background: #fff5f5;
        color: #bd2130;
        text-decoration: underline;
    }

    /* Mobile Drawer Styling & Close Button Fix */
    .filteroffcanvas {
        max-width: 320px;
        width: 85vw;
        z-index: 1055;
    }
    .filteroffcanvas .offcanvas-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 12px 18px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #e9ecef !important;
        position: relative !important;
        min-height: 52px;
    }
    .filteroffcanvas .offcanvas-header .offcanvas-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: var(--lj-black) !important;
        margin: 0 !important;
        line-height: 1 !important;
    }
    .filteroffcanvas .offcanvas-header .btn-close {
        position: static !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        margin: 0 !important;
        padding: 6px !important;
        font-size: 12px !important;
        opacity: 0.7 !important;
        box-shadow: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }
    .filteroffcanvas .offcanvas-header .btn-close:hover {
        opacity: 1 !important;
        transform: scale(1.1);
    }
    .filteroffcanvas .offcanvas-body {
        padding: 0 !important;
        overflow-y: auto !important;
    }
    .filteroffcanvas .faceted-filter-widget > .widget-head {
        display: none !important;
    }

    /* Wishlist / Favorite Heart - Prevent page jump & align checkbox */
    .bs-box .bs-content .favs-button {
        cursor: pointer;
        z-index: 5;
    }
    .bs-box .bs-content input[type="checkbox"].favs-icon {
        position: absolute !important;
        top: -348px !important;
        right: -2px !important;
        left: auto !important;
        width: 24px !important;
        height: 23px !important;
        opacity: 0 !important;
        pointer-events: none !important;
        z-index: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    @media (min-width: 991px) and (max-width: 1199px) {
        .bs-box .bs-content input[type="checkbox"].favs-icon { top: -310px !important; }
    }
    @media (min-width: 768px) and (max-width: 991px) {
        .bs-box .bs-content input[type="checkbox"].favs-icon { top: -240px !important; }
    }
    @media (min-width: 581px) and (max-width: 767px) {
        .bs-box .bs-content input[type="checkbox"].favs-icon { top: -245px !important; }
    }
    @media (min-width: 477px) and (max-width: 580px) {
        .bs-box .bs-content input[type="checkbox"].favs-icon { top: -235px !important; }
    }
    @media (max-width: 476px) {
        .bs-box .bs-content input[type="checkbox"].favs-icon { top: -266px !important; }
    }

    /* Product Title & Rating Alignment: Prevent long product titles from overlapping top-right rating */
    .bs-box .bs-content {
        position: relative;
    }
    .bs-box .bs-content:has(.product-rating) h6,
    .bs-content:has(.product-rating) h6,
    .offers-content:has(.product-rating) h6 {
        padding-right: 48px !important;
        max-width: 100% !important;
        word-break: break-word;
    }
    @supports not (selector(:has(*))) {
        .bs-box .bs-content h6,
        .bs-content h6,
        .offers-content h6 {
            padding-right: 48px !important;
            max-width: 100% !important;
        }
    }
    .bs-box .bs-content .product-rating,
    .bs-content .product-rating,
    .offers-content .product-rating {
        position: absolute !important;
        top: 2px !important;
        right: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 3px !important;
        white-space: nowrap !important;
        border: 0 !important;
        padding: 0 !important;
        z-index: 2 !important;
        line-height: 1 !important;
    }
    .bs-content .product-rating span,
    .offers-content .product-rating span {
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #242424 !important;
        line-height: 1 !important;
    }
    .bs-content .product-rating i,
    .offers-content .product-rating i {
        display: inline-flex !important;
        align-items: center !important;
        line-height: 1 !important;
    }

    /* Product Image Zoom: Keep zoomed image strictly within image container */
    .bs-image,
    .offers-image {
        position: relative !important;
        overflow: hidden !important;
        -webkit-mask-image: -webkit-radial-gradient(white, black);
        mask-image: radial-gradient(white, black);
        transform: translateZ(0);
        border-radius: 14px;
    }

    .bs-image > a,
    .offers-image > a {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        overflow: hidden !important;
        position: relative !important;
        border-radius: inherit;
    }

    .bs-image img,
    .offers-image img {
        max-width: 100% !important;
        max-height: 100% !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        display: block !important;
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        transform: scale(1);
        transform-origin: center center;
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    .bs-box:hover .bs-image img,
    .offers-box:hover .offers-image img,
    .bs-image:hover img,
    .offers-image:hover img {
        transform: scale(1.08) !important;
    }

    /* =========================================================
       CUSTOM SORT DROPDOWN UI (THEME & FILTER MATCHING)
       ========================================================= */
    .native-sort-select,
    #sort-by.native-sort-select,
    .short-by-block form select.native-sort-select {
        display: none !important;
    }

    .custom-sort-dropdown {
        position: relative;
        display: inline-block;
        user-select: none;
    }

    /* Trigger Button matching top-bar */
    .sort-dropdown-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: none;
        padding: 0;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        color: var(--lj-black, #242424);
        cursor: pointer;
        outline: none;
        transition: color 0.2s ease;
    }
    .sort-dropdown-toggle:hover {
        color: #000000;
    }
    .sort-dropdown-toggle:focus-visible {
        outline: none;
    }
    .sort-current-label {
        white-space: nowrap;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        color: var(--lj-black, #242424);
    }
    .sort-chevron-icon {
        font-size: 12px;
        color: var(--lj-black, #242424);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-sort-dropdown.open .sort-chevron-icon {
        transform: rotate(180deg);
        color: var(--lj-black, #242424);
    }

    /* Options Dropdown Menu Card */
    .sort-dropdown-menu {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        min-width: 195px;
        background: #ffffff;
        border: 1px solid var(--lj-border, #e9ecef);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
        padding: 6px;
        margin: 0;
        list-style: none;
        z-index: 1050;
        display: none;
        opacity: 0;
        transform: translateY(-6px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .custom-sort-dropdown.open .sort-dropdown-menu {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    /* Individual Option Item */
    .sort-dropdown-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 12px;
        margin: 2px 0;
        border-radius: 6px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--lj-black, #242424);
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.18s ease;
        line-height: 1.3;
    }

    /* Hover effect: according to the side filter (.chip-filter-btn:hover / quick chips) */
    .sort-dropdown-option:hover:not(.is-selected) {
        background: #fffdf5;
        border-color: var(--lj-gold, #fbb516);
        color: var(--lj-black, #242424);
        box-shadow: 0 1px 4px rgba(251, 181, 22, 0.18);
    }

    /* Selected effect: according to the side filter (.chip-filter-btn.active & checkbox:checked) */
    .sort-dropdown-option.is-selected {
        background: var(--lj-gold, #fbb516);
        border-color: var(--lj-gold, #fbb516);
        color: #242424;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(251, 181, 22, 0.28);
    }
    .sort-dropdown-option.is-selected:hover {
        background: #f5aa0b;
        border-color: #f5aa0b;
        color: #242424;
    }

    /* Checkmark Icon inside Selected Option (matching side filter checked mark) */
    .option-check-icon {
        font-size: 11px;
        font-weight: 900;
        color: #242424;
        opacity: 0;
        visibility: hidden;
        transform: scale(0.6);
        transition: all 0.18s ease;
        flex-shrink: 0;
    }
    .sort-dropdown-option.is-selected .option-check-icon {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }

    @media (max-width: 576px) {
        .sort-dropdown-menu {
            left: 0;
            right: auto;
        }
    }
</style>

<div class="loader" id="loader"></div>

{{-- Breadcrumb --}}
<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="{{ route('frontend.home') }}">{{ Helper::language('home') ?: 'Home' }}</a></li>
            <li class="active">Products</li>
        </ul>
    </div>
</div>

{{-- Main Product Listing Section --}}
<section class="product-listing mt-4 mb-5">
    <div class="container">
        <div class="row">
            {{-- DESKTOP LEFT FILTER SIDEBAR (Width >= 992px) --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="faceted-sidebar-card">
                    @include('frontend.products.components.sidebar')
                </div>
            </div>

            {{-- RIGHT PRODUCTS CATALOG AREA --}}
            <div class="col-lg-9 col-12">
                {{-- Top Sort Bar --}}
                <div class="short-by mb-3">
                    <input type="hidden" name="keyword" id="keyword" value="{{ $filters['keyword'] ?? '' }}">
                    <p class="mb-0">
                        {{ Helper::language('all_products') ?: 'All Products' }} 
                        ( <span id="total_page_count">{{ $total_product_count ?: '0' }}</span> Items )
                    </p>
                    <div class="short-by-block">
                        <form action="javascript:void(0);" class="m-0 p-0">
                            {{-- Native select kept hidden for backward compatibility & form serialization --}}
                            <select name="sort_by" id="sort-by" class="native-sort-select" style="display: none !important;" aria-hidden="true" tabindex="-1">
                                <option value="newest" {{ ($filters['sort_by'] ?? '') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="high_to_low" {{ ($filters['sort_by'] ?? '') == 'high_to_low' ? 'selected' : '' }}>{{ Helper::language('high_to_low') ?: 'Price: High to Low' }}</option>
                                <option value="low_to_high" {{ ($filters['sort_by'] ?? '') == 'low_to_high' ? 'selected' : '' }}>{{ Helper::language('low_to_high') ?: 'Price: Low to High' }}</option>
                                <option value="popular" {{ ($filters['sort_by'] ?? '') == 'popular' ? 'selected' : '' }}>Popular</option>
                                <option value="rating" {{ ($filters['sort_by'] ?? '') == 'rating' ? 'selected' : '' }}>Highest Rating</option>
                            </select>

                            @php
                                $selectedSort = $filters['sort_by'] ?? 'newest';
                                $sortMap = [
                                    'newest' => 'Newest',
                                    'high_to_low' => Helper::language('high_to_low') ?: 'Price: High to Low',
                                    'low_to_high' => Helper::language('low_to_high') ?: 'Price: Low to High',
                                    'popular' => 'Popular',
                                    'rating' => 'Highest Rating',
                                ];
                                $selectedSortLabel = $sortMap[$selectedSort] ?? 'Newest';
                            @endphp

                            {{-- Custom Theme-Compliant Sort Dropdown (Options styled matching side filter) --}}
                            <div class="custom-sort-dropdown" id="custom-sort-dropdown">
                                <button type="button" class="sort-dropdown-toggle" id="sortDropdownToggle" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="sort-current-label" id="sortCurrentLabel">{{ $selectedSortLabel }}</span>
                                    <i class="fa-solid fa-chevron-down sort-chevron-icon"></i>
                                </button>

                                <ul class="sort-dropdown-menu" id="sortDropdownMenu" role="listbox" aria-labelledby="sortDropdownToggle">
                                    @foreach($sortMap as $val => $label)
                                        @php $isSelected = ($selectedSort == $val); @endphp
                                        <li class="sort-dropdown-option {{ $isSelected ? 'is-selected' : '' }}"
                                            data-value="{{ $val }}"
                                            role="option"
                                            aria-selected="{{ $isSelected ? 'true' : 'false' }}">
                                            <span class="option-label">{{ $label }}</span>
                                            <i class="fa-solid fa-check option-check-icon"></i>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </form>

                        {{-- Mobile Filter Drawer Toggle Button --}}
                        <button type="button" class="solid-button backdrop d-lg-none" data-bs-toggle="offcanvas"
                                data-bs-target="#filteroffcanvas" aria-controls="filteroffcanvas">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-1" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.5 8.61971H12.641C12.8656 9.54023 13.6863 10.2248 14.6621 10.2248C15.6378 10.2248 16.4585 9.54023 16.6831 8.61971H19.5C19.7761 8.61971 20 8.39257 20 8.11243C20 7.83229 19.7761 7.60515 19.5 7.60515H16.6831C16.4585 6.68463 15.6378 6 14.662 6C13.6863 6 12.8655 6.68463 12.641 7.60515H4.5C4.22387 7.60515 4 7.83229 4 8.11243C4 8.39257 4.22387 8.61971 4.5 8.61971Z" fill="#242424" />
                            </svg>
                            {{ Helper::language('filter') ?: 'Filters' }}
                        </button>
                    </div>
                </div>

                {{-- Active Filter Pills (Chips Bar - Swiper Slider) --}}
                <div class="active-filter-pills-bar" id="active-pills-container" style="{{ empty($active_pills) ? 'display: none;' : '' }}">
                    <div class="active-pills-label-wrap">
                        <i class="fa-solid fa-filter text-warning" style="font-size: 11px;"></i>
                        <span class="active-pills-label">Active:</span>
                    </div>

                    <button type="button" class="pills-nav-btn pills-swiper-prev" aria-label="Previous">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="swiper active-pills-slider" id="pills-slider">
                        <div id="pills-list" class="swiper-wrapper active-pills-track">
                            @foreach ($active_pills as $pill)
                                <div class="swiper-slide filter-chip-slide">
                                    <span class="filter-chip" data-type="{{ $pill['type'] }}" data-id="{{ $pill['id'] }}">
                                        <span class="chip-text">{{ $pill['label'] }}</span>
                                        <span class="chip-remove" onclick="removeSinglePill('{{ $pill['type'] }}', '{{ $pill['id'] }}');" title="Remove filter">&times;</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="pills-nav-btn pills-swiper-next" aria-label="Next">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <button type="button" onclick="resetAllFacetedFilters();" class="btn-clear-all" title="Clear all active filters">
                        Clear All
                    </button>
                </div>

                {{-- Products Grid Container --}}
                <div class="row product-listing-row" id="table_data">
                    @include('frontend.products.partials.product_grid')
                </div>

                {{-- Show More (Pagination) Area --}}
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <div class="show-more">
                            <button type="button" 
                                    class="solid-button show-more load-more-data mx-auto"
                                    id="load-more-btn"
                                    onclick="loadMoreProducts();"
                                    style="{{ $total_product_count > 16 && $showing_product_count < $total_product_count ? 'display: inline-block;' : 'display: none;' }}">
                                {{ Helper::language('show_more') ?: 'Show More' }}
                            </button>
                            <p id="showing" class="mt-2 text-muted" style="font-size: 14px;">
                                {{ Helper::language('showing') ?: 'Showing' }}
                                <span id="p-current-count">{{ $showing_product_count ?: 0 }}</span> of 
                                <span id="p-total-count">{{ $total_product_count ?: 0 }}</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- MOBILE OFFCANVAS FILTER DRAWER --}}
<div class="offcanvas offcanvas-end filteroffcanvas d-lg-none" tabindex="-1" id="filteroffcanvas" aria-labelledby="offcanvasFilterLabel">
    <div class="offcanvas-header d-flex justify-content-between align-items-center">
        <h5 class="offcanvas-title fw-bold" id="offcanvasFilterLabel">{{ Helper::language('filter') ?: 'Filters' }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('frontend.products.components.sidebar')
    </div>
    <div class="offcanvas-footer p-3 border-top bg-white">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-dark w-50 py-2 fw-semibold" onclick="resetAllFacetedFilters();">
                Reset
            </button>
            <button type="button" class="solid-button w-50 py-2" data-bs-dismiss="offcanvas">
                View Results
            </button>
        </div>
    </div>
</div>

<!-- Service -->
@include('frontend.service.service')
<!-- End Service -->

<!-- Newsletter -->
@include('frontend.newsletter.newsletter')
<!-- End Newsletter -->

@endsection

@push('after-scripts')
<script type="text/javascript">
(function() {
    window.globalCatalogMin = {{ $catalogMinPrice }};
    window.globalCatalogMax = {{ $catalogMaxPrice }};
    window.currentMinPrice = {{ $filters['min_price'] ?? $catalogMinPrice }};
    window.currentMaxPrice = {{ $filters['max_price'] ?? $catalogMaxPrice }};
    window.currencySymbol = "{{ Helper::Settings('currency_symbol') ?: 'GH₵' }}";
    window.filterTimer = null;
    window.isAjaxRunning = false;
    window.currentOffset = 0;

    // Standard Site Loader Integration
    window.showSiteLoader = function() {
        if (window.jQuery) {
            window.jQuery('.loader').fadeIn(100).css('visibility', 'visible');
        }
    };
    window.hideSiteLoader = function() {
        if (window.jQuery) {
            window.jQuery('.loader').fadeOut(100).css('visibility', 'hidden');
        }
    };

    // Toggle Facet Accordion Section with smooth slide
    window.toggleFacetSection = function(header) {
        if (window.jQuery) {
            var $header = jQuery(header);
            $header.next('.facet-body').stop(true, true).slideToggle(200);
            $header.find('.facet-arrow').toggleClass('rotated');
        } else {
            var body = header.nextElementSibling;
            if (body) {
                body.style.display = (body.style.display === 'none') ? 'block' : 'none';
                var arrow = header.querySelector('.facet-arrow');
                if (arrow) arrow.classList.toggle('rotated');
            }
        }
    };

    // Toggle Subcategories under Category
    window.toggleSubcatList = function(e, btn) {
        if (e) e.stopPropagation();
        if (window.jQuery) {
            var $btn = jQuery(btn);
            var $item = $btn.closest('.facet-cat-item');
            var $wrap = $item.find('.facet-sub-wrap');
            var willOpen = !$wrap.is(':visible');
            $wrap.stop(true, true).slideToggle(180, function() {
                var isOpen = jQuery(this).is(':visible');
                $btn.find('i').removeClass('fa-angle-up fa-angle-down').addClass(isOpen ? 'fa-angle-up' : 'fa-angle-down');
            });
            $btn.find('i').removeClass('fa-angle-up fa-angle-down').addClass(willOpen ? 'fa-angle-up' : 'fa-angle-down');
        }
    };

    // Dual Slider Fill Track Color
    window.fillSliderTrack = function() {
        if (window.jQuery) {
            var $ = window.jQuery;
            $('.range-slider-box').each(function() {
                var $box = $(this);
                var $sMin = $box.find('.slider-min-input, #slider-min');
                var $sMax = $box.find('.slider-max-input, #slider-max');
                var $track = $box.find('.range-highlight-track, #range-highlight-track');
                if (!$sMin.length || !$sMax.length || !$track.length) return;

                var minVal = parseFloat($sMin.val());
                var maxVal = parseFloat($sMax.val());
                var minLimit = parseFloat($sMin.attr('min'));
                var maxLimit = parseFloat($sMin.attr('max'));
                if (maxLimit <= minLimit) maxLimit = minLimit + 100;

                var p1 = Math.max(0, Math.min(100, ((minVal - minLimit) / (maxLimit - minLimit)) * 100));
                var p2 = Math.max(0, Math.min(100, ((maxVal - minLimit) / (maxLimit - minLimit)) * 100));

                $track.css({
                    left: p1 + '%',
                    width: (p2 - p1) + '%'
                });
            });
        }
    };

    // Min Slider Move
    window.onMinSliderMove = function(el) {
        var $ = window.jQuery;
        var val = parseFloat(el.value);
        var maxVal = window.currentMaxPrice;

        if ($) {
            var $box = $(el).closest('.range-slider-box');
            var sMax = $box.find('.slider-max-input, #slider-max')[0];
            if (sMax) maxVal = parseFloat(sMax.value);
        }

        if (val >= maxVal - 1) {
            val = maxVal - 1;
            el.value = val;
        }

        window.currentMinPrice = val;
        if ($) {
            $('.slider-min-input, #slider-min').val(val);
            $('.num-input-min, #num-input-min').val(val);
            $('.price-display-min, #price-display-min').text(window.currencySymbol + val.toFixed(2));
        }

        window.fillSliderTrack();
        window.triggerDebouncedFilter();
    };

    // Max Slider Move
    window.onMaxSliderMove = function(el) {
        var $ = window.jQuery;
        var val = parseFloat(el.value);
        var minVal = window.currentMinPrice;

        if ($) {
            var $box = $(el).closest('.range-slider-box');
            var sMin = $box.find('.slider-min-input, #slider-min')[0];
            if (sMin) minVal = parseFloat(sMin.value);
        }

        if (val <= minVal + 1) {
            val = minVal + 1;
            el.value = val;
        }

        window.currentMaxPrice = val;
        if ($) {
            $('.slider-max-input, #slider-max').val(val);
            $('.num-input-max, #num-input-max').val(val);
            $('.price-display-max, #price-display-max').text(window.currencySymbol + val.toFixed(2));
        }

        window.fillSliderTrack();
        window.triggerDebouncedFilter();
    };

    // Debounced filter trigger for slider dragging
    window.triggerDebouncedFilter = function() {
        if (window.filterTimer) clearTimeout(window.filterTimer);
        window.filterTimer = setTimeout(function() {
            window.fetchFacetedProducts(false);
        }, 350);
    };

    // Price Direct Numeric Input Change
    window.onPriceInputChange = function(sourceEl) {
        var $ = window.jQuery;
        var minVal = window.globalCatalogMin;
        var maxVal = window.globalCatalogMax;

        if ($) {
            var $minInputs = $('.num-input-min, #num-input-min');
            var $maxInputs = $('.num-input-max, #num-input-max');

            var isMinSource = sourceEl && ($(sourceEl).hasClass('num-input-min') || sourceEl.id === 'num-input-min');
            var isMaxSource = sourceEl && ($(sourceEl).hasClass('num-input-max') || sourceEl.id === 'num-input-max');

            var rawMin = isMinSource ? $(sourceEl).val() : $minInputs.first().val();
            var rawMax = isMaxSource ? $(sourceEl).val() : $maxInputs.first().val();

            minVal = parseFloat(rawMin);
            maxVal = parseFloat(rawMax);

            if (isNaN(minVal) || minVal < 0) minVal = 0;
            if (isNaN(maxVal) || maxVal < 0) maxVal = 0;

            // Clamping rules:
            // "input me min max max me min se kam value nahi dal sakta or min me max se jyada value nahi dal sakta or inme mainus(-) me value nahi ja sakti"
            if (isMinSource) {
                if (minVal > maxVal) {
                    minVal = maxVal;
                }
            } else if (isMaxSource) {
                if (maxVal < minVal) {
                    maxVal = minVal;
                }
            } else {
                if (minVal > maxVal) {
                    minVal = maxVal;
                }
            }

            // Sync all numeric inputs
            $minInputs.val(minVal);
            $maxInputs.val(maxVal);

            // Sync sliders
            $('.slider-min-input, #slider-min').val(minVal);
            $('.slider-max-input, #slider-max').val(maxVal);

            // Sync display text
            $('.price-display-min, #price-display-min').text(window.currencySymbol + minVal.toFixed(2));
            $('.price-display-max, #price-display-max').text(window.currencySymbol + maxVal.toFixed(2));
        }

        window.currentMinPrice = minVal;
        window.currentMaxPrice = maxVal;

        window.fillSliderTrack();
        window.fetchFacetedProducts(false);
    };

    // Set Quick Price Pills
    window.setQuickPrice = function(min, max) {
        var $ = window.jQuery;
        min = parseFloat(min);
        max = parseFloat(max);
        if (isNaN(min) || min < 0) min = 0;
        if (isNaN(max) || max < 0) max = 0;

        if ($) {
            $('.slider-min-input, #slider-min').val(min);
            $('.slider-max-input, #slider-max').val(max);
            $('.num-input-min, #num-input-min').val(min);
            $('.num-input-max, #num-input-max').val(max);
            $('.price-display-min, #price-display-min').text(window.currencySymbol + min.toFixed(2));
            $('.price-display-max, #price-display-max').text(window.currencySymbol + max.toFixed(2));
        }

        window.currentMinPrice = min;
        window.currentMaxPrice = max;

        window.fillSliderTrack();
        window.fetchFacetedProducts(false);
    };

    // Instant Brand Search Filter
    window.liveBrandFilter = function(term) {
        if (!window.jQuery) return;
        var kw = String(term || '').toLowerCase().trim();
        var visibleCount = 0;
        var $brandBtns = jQuery('.brand-see-more-btn');

        // Sync to other brand search input (e.g. mobile vs desktop) without stealing focus
        jQuery('.brand-search-input, #brand-search-field').not(':focus').val(term);

        if (kw !== '') {
            jQuery('.brand-scroll-list .facet-brand-item').each(function() {
                var brandName = String(jQuery(this).attr('data-name') || jQuery(this).find('.brand-title-text').text() || '').toLowerCase().trim();
                var isZeroCount = jQuery(this).hasClass('zero-count');
                if (!isZeroCount && brandName.indexOf(kw) > -1) {
                    jQuery(this).show();
                    visibleCount++;
                } else {
                    jQuery(this).hide();
                }
            });
            $brandBtns.hide();
        } else {
            if (window.facetLimitManager) {
                window.facetLimitManager.syncAll();
            }
            visibleCount = jQuery('.brand-scroll-list .facet-brand-item').not('.zero-count').length;
        }

        if (visibleCount === 0) {
            jQuery('#no-brands-msg').show();
        } else {
            jQuery('#no-brands-msg').hide();
        }
    };

    // Move All Checked Filter Items To Top (Category, Subcategory, Brand, Bottle Size)
    window.moveCheckedFiltersToTop = function() {
        if (!window.jQuery) return;
        var $ = window.jQuery;

        $('.faceted-filter-widget').each(function() {
            var $widget = $(this);

            // 1. Categories: Move categories with checked category checkbox OR checked subcategory to top
            var $catList = $widget.find('.facet-cat-list');
            var $checkedCats = $catList.children('li.facet-cat-item').filter(function() {
                return $(this).find('.filter-cat-cb:checked').length > 0 || $(this).find('.filter-subcat-cb:checked').length > 0;
            });
            if ($checkedCats.length > 0) {
                $catList.prepend($checkedCats);
            }

            // 2. Subcategories: Move checked subcategories to top inside each category
            $widget.find('.facet-sub-list').each(function() {
                var $subList = $(this);
                var $checkedSubs = $subList.children('li.facet-sub-item').filter(function() {
                    return $(this).find('.filter-subcat-cb:checked').length > 0;
                });
                if ($checkedSubs.length > 0) {
                    $subList.prepend($checkedSubs);
                }
            });

            // 3. Brands: Move checked brands to top
            var $brandList = $widget.find('.brand-scroll-list');
            var $checkedBrands = $brandList.children('li.facet-brand-item').filter(function() {
                return $(this).find('.filter-brand-cb:checked').length > 0;
            });
            if ($checkedBrands.length > 0) {
                $brandList.prepend($checkedBrands);
            }

            // 4. Bottle Sizes: Move checked sizes to top
            var $sizeList = $widget.find('.facet-size-list');
            var $checkedSizes = $sizeList.children('li.facet-size-item').filter(function() {
                return $(this).find('.filter-size-cb:checked').length > 0;
            });
            if ($checkedSizes.length > 0) {
                $sizeList.prepend($checkedSizes);
            }
        });
    };

    // Facet Limit Manager: Shows first 4 items by default, provides "+ See X more" toggle, keeps checked items at top
    window.facetLimitManager = {
        LIMIT: 4,

        getLimit: function($list) {
            if ($list && ($list.hasClass('brand-scroll-list') || $list.hasClass('facet-size-list') || $list.hasClass('facet-chips-wrap'))) {
                return 8; // 8 compact chips for brands/sizes (~2-3 rows)
            }
            return 4; // 4 rows for categories
        },

        applyLimit: function($list, $btn, forcedState) {
            if (!$list || $list.length === 0) return;

            var self = this;
            var limit = self.getLimit($list);
            var $items = $list.children('li').not('#no-brands-msg, #no-sizes-msg');

            // Move checked items to top within $list
            var $checked = $items.filter(function() {
                return $(this).find('input[type="checkbox"]:checked').length > 0;
            });
            if ($checked.length > 0) {
                $list.prepend($checked);
            }

            // Re-fetch sorted children after moving to top
            $items = $list.children('li').not('#no-brands-msg, #no-sizes-msg');

            var $availableItems = $items.filter(function() {
                var $li = $(this);
                var isChecked = $li.find('input[type="checkbox"]:checked').length > 0;
                return isChecked || !$li.hasClass('zero-count');
            });

            var total = $availableItems.length;

            // If total available items is within limit, show all available and hide button
            if (total <= limit) {
                $availableItems.show();
                $items.filter('.zero-count').not(':has(:checked)').hide();
                if ($btn && $btn.length) {
                    $btn.attr('data-state', 'collapsed');
                    $btn.hide();
                }
                return;
            }

            var checkedCount = $checked.length;
            var potentialHidden = Math.max(0, total - Math.max(limit, checkedCount));

            // If all available items are already shown (e.g. all selected), no items remain hidden -> hide button
            if (potentialHidden === 0) {
                $availableItems.show();
                $items.filter('.zero-count').not(':has(:checked)').hide();
                if ($btn && $btn.length) {
                    $btn.attr('data-state', 'collapsed');
                    $btn.hide();
                }
                return;
            }

            var state = forcedState || ($btn ? $btn.attr('data-state') : 'collapsed') || 'collapsed';
            var unselectedSlots = Math.max(0, limit - checkedCount);
            var shownUnselected = 0;
            var actualHidden = 0;

            $items.each(function() {
                var $li = $(this);
                var isChecked = $li.find('input[type="checkbox"]:checked').length > 0;
                var isZero = $li.hasClass('zero-count') && !isChecked;

                if (isZero) {
                    $li.hide();
                    return;
                }

                if (isChecked) {
                    $li.show();
                } else if (state === 'expanded') {
                    $li.show();
                } else if (shownUnselected < unselectedSlots) {
                    $li.show();
                    shownUnselected++;
                } else {
                    $li.hide();
                    actualHidden++;
                }
            });

            if ($btn && $btn.length) {
                if (state === 'expanded') {
                    $btn.attr('data-state', 'expanded');
                    $btn.find('.see-more-text').text('See less');
                    $btn.find('i').removeClass('fa-angle-down fa-chevron-down').addClass('fa-angle-up');
                    $btn.show();
                } else {
                    $btn.attr('data-state', 'collapsed');
                    $btn.find('.see-more-text').text('+ See ' + actualHidden + ' more');
                    $btn.find('i').removeClass('fa-angle-up fa-chevron-up').addClass('fa-angle-down');
                    if (actualHidden > 0) {
                        $btn.show();
                    } else {
                        $btn.hide();
                    }
                }
            }
        },

        syncAll: function() {
            if (!window.jQuery) return;
            var $ = window.jQuery;
            var self = this;

            // Make sure checked items are always moved to the top before applying limits
            if (typeof window.moveCheckedFiltersToTop === 'function') {
                window.moveCheckedFiltersToTop();
            }

            $('.faceted-filter-widget').each(function() {
                var $widget = $(this);

                // 1. Categories
                var $catList = $widget.find('.facet-cat-list');
                var $catBtn = $widget.find('.cat-see-more-btn');
                self.applyLimit($catList, $catBtn);

                // 2. Subcategories in each category
                $widget.find('.facet-sub-wrap').each(function() {
                    var $subWrap = $(this);
                    var $subList = $subWrap.find('.facet-sub-list');
                    var $subBtn = $subWrap.find('.sub-see-more-btn');
                    self.applyLimit($subList, $subBtn);
                });

                // 3. Brands (if brand search is blank)
                var $brandInput = $widget.find('#brand-search-field, .brand-search-input');
                if (!$brandInput.val() || $brandInput.val().trim() === '') {
                    var $brandList = $widget.find('.brand-scroll-list');
                    var $brandBtn = $widget.find('.brand-see-more-btn');
                    self.applyLimit($brandList, $brandBtn);
                }

                // 4. Bottle Sizes
                var $sizeList = $widget.find('.facet-size-list');
                var $sizeBtn = $widget.find('.size-see-more-btn');
                self.applyLimit($sizeList, $sizeBtn);
            });
        }
    };

    // Collect Filter State
    window.collectFilterState = function() {
        if (!window.jQuery) return {};
        var $ = window.jQuery;

        var categories = [];
        $('.filter-cat-cb:checked').each(function() {
            var val = $(this).val();
            if (val && categories.indexOf(val) === -1) {
                categories.push(val);
            }
        });

        var subcategories = [];
        $('.filter-subcat-cb:checked').each(function() {
            var val = $(this).val();
            if (val && subcategories.indexOf(val) === -1) {
                subcategories.push(val);
            }
        });

        var brands = [];
        $('.filter-brand-cb:checked').each(function() {
            var val = $(this).val();
            if (val && brands.indexOf(val) === -1) {
                brands.push(val);
            }
        });

        var sizes = [];
        $('.filter-size-cb:checked').each(function() {
            var val = $(this).val();
            if (val && sizes.indexOf(val) === -1) {
                sizes.push(val);
            }
        });

        var ratings = [];
        $('.filter-rating-cb:checked').each(function() {
            var val = $(this).val();
            if (val && ratings.indexOf(val) === -1) {
                ratings.push(val);
            }
        });

        var bestseller = $('.filter-bestseller-cb:checked').length > 0 ? 1 : 0;
        var offer = $('.filter-offer-cb:checked').length > 0 ? 1 : 0;
        var in_stock = $('.filter-instock-cb:checked').length > 0 ? 1 : 0;
        var sortBy = $('#sort-by').val() || 'newest';
        var keyword = $('#keyword').val() || '';

        var minPrice = (window.currentMinPrice !== null && window.currentMinPrice > window.globalCatalogMin) ? window.currentMinPrice : null;
        var maxPrice = (window.currentMaxPrice !== null && window.currentMaxPrice < window.globalCatalogMax) ? window.currentMaxPrice : null;

        return {
            categories: categories,
            subcategories: subcategories,
            brands: brands,
            sizes: sizes,
            min_price: minPrice,
            max_price: maxPrice,
            ratings: ratings,
            rating: ratings.length ? ratings[0] : null,
            bestseller: bestseller,
            offer: offer,
            in_stock: in_stock,
            sort_by: sortBy,
            keyword: keyword,
            _token: "{{ csrf_token() }}"
        };
    };

    // Smooth scroll to top of product listing section when applying a filter
    window.scrollToProductTop = function() {
        if (!window.jQuery) return;
        var $ = window.jQuery;

        var $target = $('.short-by:visible');
        if (!$target.length) {
            $target = $('#table_data');
        }
        if (!$target.length) {
            $target = $('.product-listing');
        }

        if ($target.length) {
            var headerHeight = 0;
            var $header = $('header.site-header, #masthead, .site-header');
            if ($header.length) {
                var pos = $header.css('position');
                if (pos === 'fixed' || pos === 'sticky') {
                    headerHeight = $header.outerHeight() || 0;
                }
            }
            if (!headerHeight) {
                $('header, .top-header, .middle-header, nav').each(function() {
                    var p = $(this).css('position');
                    if (p === 'fixed' || p === 'sticky') {
                        headerHeight = Math.max(headerHeight, $(this).outerHeight() || 0);
                    }
                });
            }

            var targetTop = $target.offset().top - headerHeight - 15;
            if (targetTop < 0) targetTop = 0;

            if ($(window).scrollTop() > targetTop + 25) {
                $('html, body').stop().animate({
                    scrollTop: targetTop
                }, 350);
            }
        }
    };

    // Central AJAX Request
    window.fetchFacetedProducts = function(isAppend) {
        if (!window.jQuery) return;
        var $ = window.jQuery;

        if (window.isAjaxRunning) return;
        window.isAjaxRunning = true;

        if (!isAppend) {
            window.currentOffset = 0;
            window.scrollToProductTop();
        }

        var state = window.collectFilterState();
        state.offset = window.currentOffset;
        state.append = isAppend ? 1 : 0;

        window.showSiteLoader();
        $('#table_data').css('opacity', '0.4');

        $.ajax({
            url: "{{ route('products') }}",
            type: "GET",
            data: state,
            dataType: "json",
            success: function(response) {
                window.hideSiteLoader();
                $('#table_data').css('opacity', '1');
                window.isAjaxRunning = false;

                if (response.success) {
                    if (isAppend) {
                        $('#table_data').append(response.html);
                    } else {
                        $('#table_data').html(response.html);
                        window.scrollToProductTop();
                    }

                    $('#total_page_count').text(response.total_product_count);
                    $('#p-total-count').text(response.total_product_count);
                    $('#p-current-count').text(response.showing_product_count);

                    if (response.has_more) {
                        $('#load-more-btn').show();
                    } else {
                        $('#load-more-btn').hide();
                    }

                    if (!isAppend && response.facets) {
                        window.syncDynamicFacets(response.facets, response.price_bounds);
                    }

                    if (!isAppend) {
                        window.renderActivePills(response.active_pills);
                    }

                    if (!isAppend) {
                        window.syncBrowserUrl(state);
                    }
                }
            },
            error: function() {
                window.hideSiteLoader();
                $('#table_data').css('opacity', '1');
                window.isAjaxRunning = false;
            }
        });
    };

    // Load More (Show More)
    window.loadMoreProducts = function() {
        window.currentOffset += 16;
        window.fetchFacetedProducts(true);
    };

    // Dynamic Facets DOM Synchronization ("Filter bhi Filter ho" - Dynamic Pruning & Group Auto-Hide)
    window.syncDynamicFacets = function(facets, priceBounds) {
        if (!window.jQuery || !facets) return;
        var $ = window.jQuery;

        // 1. Categories
        if (facets.categories) {
            $('.faceted-filter-widget').each(function() {
                var $widget = $(this);
                var visibleCats = 0;
                $widget.find('.facet-cat-item').each(function() {
                    var cid = $(this).data('id');
                    var count = facets.categories[cid] !== undefined ? parseInt(facets.categories[cid], 10) : 0;
                    var cb = $(this).find('> .facet-row-inner .filter-cat-cb');

                    $(this).find('> .facet-row-inner .count-pill').text(count);
                    if (count === 0 && !cb.is(':checked')) {
                        $(this).addClass('zero-count').hide();
                        cb.prop('disabled', true);
                    } else {
                        $(this).removeClass('zero-count').show();
                        cb.prop('disabled', false);
                        visibleCats++;
                    }
                });

                // Auto-hide entire category filter section if no category has items or is checked
                var $catGroup = $widget.find('.facet-group-categories');
                if (visibleCats === 0) {
                    $catGroup.removeClass('d-none').hide();
                } else {
                    $catGroup.removeClass('d-none').show();
                }
            });
        }

        // 2. Subcategories (Always displayed under parent category, auto-hidden if 0 available)
        if (facets.subcategories) {
            $('.faceted-filter-widget').each(function() {
                var $widget = $(this);
                $widget.find('.facet-cat-item').each(function() {
                    var $catItem = $(this);
                    var $subWrap = $catItem.find('.facet-sub-wrap');
                    var visibleSubs = 0;

                    $catItem.find('.facet-sub-item').each(function() {
                        var sid = $(this).data('id');
                        var count = facets.subcategories[sid] !== undefined ? parseInt(facets.subcategories[sid], 10) : 0;
                        var cb = $(this).find('.filter-subcat-cb');

                        $(this).find('.sub-pill').text(count);
                        if (count === 0 && !cb.is(':checked')) {
                            $(this).addClass('zero-count').hide();
                            cb.prop('disabled', true);
                        } else {
                            $(this).removeClass('zero-count').show();
                            cb.prop('disabled', false);
                            visibleSubs++;
                        }
                    });

                    var isCatChecked = $catItem.find('> .facet-row-inner .filter-cat-cb').is(':checked');
                    var $toggleBtn = $catItem.find('.sub-toggle-btn');
                    if (visibleSubs === 0) {
                        $subWrap.removeClass('d-none').hide();
                        $toggleBtn.hide();
                    } else {
                        $toggleBtn.show();
                        if (isCatChecked) {
                            $subWrap.removeClass('d-none').show();
                            $toggleBtn.find('i').removeClass('fa-angle-down').addClass('fa-angle-up');
                        }
                    }
                });
            });
        }

        // 3. Brands (Dynamic Pruning & Section Auto-Hide)
        if (facets.brands) {
            $('.faceted-filter-widget').each(function() {
                var $widget = $(this);
                var visibleBrands = 0;
                $widget.find('.facet-brand-item').each(function() {
                    var bid = $(this).data('id');
                    var count = facets.brands[bid] !== undefined ? parseInt(facets.brands[bid], 10) : 0;
                    var cb = $(this).find('.filter-brand-cb');

                    $(this).find('.chip-count, .count-pill').text(count);
                    $(this).find('.chip-filter-btn').toggleClass('active', cb.is(':checked'));
                    if (count === 0 && !cb.is(':checked')) {
                        $(this).addClass('zero-count').hide();
                        cb.prop('disabled', true);
                    } else {
                        $(this).removeClass('zero-count').show();
                        cb.prop('disabled', false);
                        visibleBrands++;
                    }
                });

                var $brandGroup = $widget.find('.facet-group-brands');
                if (visibleBrands === 0) {
                    $brandGroup.removeClass('d-none').hide();
                    $widget.find('#no-brands-msg').show();
                } else {
                    $brandGroup.removeClass('d-none').show();
                    $widget.find('#no-brands-msg').hide();
                }
            });
        }

        // 4. Bottle Sizes (Dynamic Pruning & Section Auto-Hide)
        if (facets.sizes) {
            $('.faceted-filter-widget').each(function() {
                var $widget = $(this);
                var visibleSizes = 0;
                $widget.find('.facet-size-item').each(function() {
                    var sz = $(this).data('size');
                    var count = facets.sizes[sz] !== undefined ? parseInt(facets.sizes[sz], 10) : 0;
                    var cb = $(this).find('.filter-size-cb');

                    $(this).find('.chip-count, .count-pill, #size-count-' + sz).text(count);
                    $(this).find('.chip-filter-btn').toggleClass('active', cb.is(':checked'));
                    if (count === 0 && !cb.is(':checked')) {
                        $(this).addClass('zero-count').hide();
                        cb.prop('disabled', true);
                    } else {
                        $(this).removeClass('zero-count').show();
                        cb.prop('disabled', false);
                        visibleSizes++;
                    }
                });

                var $sizeGroup = $widget.find('.facet-group-sizes');
                if (visibleSizes === 0) {
                    $sizeGroup.removeClass('d-none').hide();
                    $widget.find('#no-sizes-msg').show();
                } else {
                    $sizeGroup.removeClass('d-none').show();
                    $widget.find('#no-sizes-msg').hide();
                }
            });
        }

        // 5. Ratings Dynamic Update
        if (facets.ratings) {
            [5, 4, 3, 2, 1].forEach(function(r) {
                var cnt = facets.ratings[r] !== undefined ? parseInt(facets.ratings[r], 10) : 0;
                $('.rating-count-pill[data-rating="' + r + '"], [id="rating-count-' + r + '"]').text(cnt);
            });

            var isAnyRatingChecked = $('.filter-rating-cb:checked').length > 0;
            var hasRatings = isAnyRatingChecked || [5, 4, 3, 2, 1].some(function(r) {
                return (facets.ratings[r] || 0) > 0;
            });
            $('.facet-group-ratings').removeClass('d-none').toggle(hasRatings);
        }

        // 6. Promotions & Deals
        if (facets.promotions) {
            var bs = facets.promotions['bestsellers'] || 0;
            var off = facets.promotions['offers'] || 0;
            var stk = facets.promotions['in_stock'] || 0;

            $('[id="promo-count-bestseller"]').text(bs);
            $('[id="promo-count-offer"]').text(off);
            $('[id="promo-count-instock"]').text(stk);

            var bsChecked = $('.filter-bestseller-cb').is(':checked');
            var offChecked = $('.filter-offer-cb').is(':checked');
            var stkChecked = $('.filter-instock-cb').is(':checked');

            $('[id="promo-row-bestseller"]').toggle(bs > 0 || bsChecked);
            $('[id="promo-row-offer"]').toggle(off > 0 || offChecked);
            $('[id="promo-row-instock"]').toggle(stk > 0 || stkChecked);

            var hasPromos = (bs > 0 || bsChecked) || (off > 0 || offChecked) || (stk > 0 || stkChecked);
            $('.facet-group-promos').removeClass('d-none').toggle(hasPromos);
        }

        if (window.facetLimitManager) {
            window.facetLimitManager.syncAll();
        }
    };

    window.pillsSwiperInstance = null;

    window.initOrUpdatePillsSlider = function() {
        if (typeof Swiper === 'undefined') return;

        var sliderEl = document.querySelector('.active-pills-slider');
        if (!sliderEl) return;

        if (window.pillsSwiperInstance) {
            window.pillsSwiperInstance.update();
            return;
        }

        window.pillsSwiperInstance = new Swiper('.active-pills-slider', {
            slidesPerView: 'auto',
            spaceBetween: 8,
            freeMode: {
                enabled: true,
                sticky: false,
                momentumBounce: true,
            },
            grabCursor: true,
            mousewheel: {
                forceToAxis: true,
            },
            navigation: {
                nextEl: '.pills-swiper-next',
                prevEl: '.pills-swiper-prev',
            },
            watchOverflow: true,
            observer: true,
            observeParents: true,
        });
    };

    // Active Pills Render
    window.renderActivePills = function(pills) {
        if (!window.jQuery) return;
        var $ = window.jQuery;
        var container = $('#active-pills-container');
        var list = $('#pills-list');

        if (!pills || pills.length === 0) {
            container.hide();
            list.html('');
            if (window.pillsSwiperInstance) {
                window.pillsSwiperInstance.update();
            }
            return;
        }

        var html = '';
        pills.forEach(function(p) {
            html += '<div class="swiper-slide filter-chip-slide">';
            html += '<span class="filter-chip" data-type="' + p.type + '" data-id="' + p.id + '">';
            html += '<span class="chip-text">' + p.label + '</span>';
            html += '<span class="chip-remove" onclick="removeSinglePill(\'' + p.type + '\', \'' + p.id + '\');" title="Remove filter">&times;</span>';
            html += '</span>';
            html += '</div>';
        });

        list.html(html);
        container.css('display', 'flex').show();

        setTimeout(function() {
            if (window.pillsSwiperInstance) {
                window.pillsSwiperInstance.update();
                window.pillsSwiperInstance.slideTo(0, 150);
            } else {
                window.initOrUpdatePillsSlider();
            }
        }, 30);
    };

    // Remove Single Pill
    window.removeSinglePill = function(type, id) {
        if (!window.jQuery) return;
        var $ = window.jQuery;

        if (type === 'keyword') {
            $('#keyword').val('');
        } else if (type === 'category') {
            $('.filter-cat-cb[value="' + id + '"]').prop('checked', false);
            $('.filter-subcat-cb[data-parent="' + id + '"]').prop('checked', false);
            var $catItems = $('.filter-cat-cb[value="' + id + '"]').closest('.facet-cat-item');
            $catItems.find('.facet-sub-wrap').slideUp(180);
            $catItems.find('.sub-toggle-btn i').removeClass('fa-angle-up').addClass('fa-angle-down');
        } else if (type === 'subcategory') {
            var $sub = $('.filter-subcat-cb[value="' + id + '"]');
            var parentId = $sub.attr('data-parent');
            $sub.prop('checked', false);
            if (parentId) {
                $('.filter-cat-cb[value="' + parentId + '"]').prop('checked', false);
            }
        } else if (type === 'brand') {
            $('.filter-brand-cb[value="' + id + '"]').prop('checked', false);
            $('.filter-brand-cb[value="' + id + '"]').closest('.chip-filter-btn').removeClass('active');
        } else if (type === 'size') {
            $('.filter-size-cb[value="' + id + '"]').prop('checked', false);
            $('.filter-size-cb[value="' + id + '"]').closest('.chip-filter-btn').removeClass('active');
        } else if (type === 'price') {
            window.setQuickPrice(window.globalCatalogMin, window.globalCatalogMax);
            return;
        } else if (type === 'rating') {
            if (id) {
                $('.filter-rating-cb[value="' + id + '"]').prop('checked', false);
            } else {
                $('.filter-rating-cb').prop('checked', false);
            }
        } else if (type === 'bestseller') {
            $('.filter-bestseller-cb').prop('checked', false);
        } else if (type === 'offer') {
            $('.filter-offer-cb').prop('checked', false);
        } else if (type === 'in_stock') {
            $('.filter-instock-cb').prop('checked', false);
        }

        if (window.facetLimitManager) {
            window.facetLimitManager.syncAll();
        }

        window.fetchFacetedProducts(false);
    };

    // Reset All Filters
    window.resetAllFacetedFilters = function() {
        if (!window.jQuery) return;
        var $ = window.jQuery;

        $('.filter-cat-cb, .filter-subcat-cb, .filter-brand-cb, .filter-size-cb, .filter-bestseller-cb, .filter-offer-cb, .filter-instock-cb').prop('checked', false).prop('disabled', false);
        $('.chip-filter-btn').removeClass('active');
        $('.filter-rating-cb').prop('checked', false).prop('disabled', false);
        $('#keyword').val('');
        $('#brand-search-field').val('');
        $('.facet-group-categories, .facet-group-brands, .facet-group-sizes, .facet-group-ratings, .facet-group-promos').removeClass('d-none').show();
        $('.sub-toggle-btn').show();
        $('.facet-item, .facet-sub-item, .facet-rating-item').removeClass('zero-count').show();
        $('#no-brands-msg, #no-sizes-msg').hide();

        // 1. Collapse all opened subcategory accordion lists and reset icons to angle-down
        $('.facet-sub-wrap').slideUp(180).removeClass('show');
        $('.sub-toggle-btn i').removeClass('fa-angle-up').addClass('fa-angle-down');

        // 2. Reset all "See More" buttons back to collapsed state
        $('.btn-see-more').attr('data-state', 'collapsed');

        // 3. Ensure all main facet sections are open and their arrows are pointing down (non-rotated)
        $('.facet-body').show();
        $('.facet-arrow').removeClass('rotated');

        if (window.facetLimitManager) {
            window.facetLimitManager.syncAll();
        }

        window.setQuickPrice(window.globalCatalogMin, window.globalCatalogMax);
    };

    // URL Sync
    window.syncBrowserUrl = function(state) {
        var params = new URLSearchParams();

        if (state.keyword) params.set('search', state.keyword);
        if (state.categories && state.categories.length) params.set('categories', state.categories.join(','));
        if (state.subcategories && state.subcategories.length) params.set('subcategories', state.subcategories.join(','));
        if (state.brands && state.brands.length) params.set('brands', state.brands.join(','));
        if (state.sizes && state.sizes.length) params.set('sizes', state.sizes.join(','));
        if (state.min_price && state.min_price > window.globalCatalogMin) params.set('min_price', state.min_price);
        if (state.max_price && state.max_price < window.globalCatalogMax) params.set('max_price', state.max_price);
        if (state.ratings && state.ratings.length) {
            params.set('ratings', state.ratings.join(','));
        } else if (state.rating) {
            params.set('rating', state.rating);
        }
        if (state.bestseller) params.set('bestseller', '1');
        if (state.offer) params.set('offer', '1');
        if (state.in_stock) params.set('in_stock', '1');
        if (state.sort_by && state.sort_by !== 'newest') params.set('sort_by', state.sort_by);

        var qs = params.toString();
        var newUrl = "{{ route('products') }}" + (qs ? '?' + qs : '');
        window.history.pushState({ path: newUrl }, '', newUrl);
    };

    // Wishlist Toggle
    window.productFav = function(productId, status, targetEl) {
        if (typeof toggleProductFav === 'function') {
            return toggleProductFav(productId, status, targetEl);
        }
    };

    // Setup jQuery Event Handlers as soon as jQuery is ready
    function initJQueryHandlers() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initJQueryHandlers, 40);
            return;
        }

        jQuery(function($) {
            window.fillSliderTrack();

            // Initial check to ensure empty facet groups are hidden
            $('.faceted-filter-widget').each(function() {
                var $widget = $(this);
                var catCount = $widget.find('.facet-cat-item:not(.zero-count), .filter-cat-cb:checked').length;
                if (catCount === 0) $widget.find('.facet-group-categories').hide();

                var brandCount = $widget.find('.facet-brand-item:not(.zero-count), .filter-brand-cb:checked').length;
                if (brandCount === 0) $widget.find('.facet-group-brands').hide();

                var sizeCount = $widget.find('.facet-size-item:not(.zero-count), .filter-size-cb:checked').length;
                if (sizeCount === 0) $widget.find('.facet-group-sizes').hide();
            });

            if (window.facetLimitManager) {
                window.facetLimitManager.syncAll();
            }

            // Initial sync of category sub-toggle icons based on visibility
            $('.facet-cat-item').each(function() {
                var $item = $(this);
                var $subWrap = $item.find('.facet-sub-wrap');
                var isVisible = $subWrap.hasClass('show') || $subWrap.is(':visible');
                var $icon = $item.find('.sub-toggle-btn i');
                if (isVisible) {
                    $icon.removeClass('fa-angle-down').addClass('fa-angle-up');
                } else {
                    $icon.removeClass('fa-angle-up').addClass('fa-angle-down');
                }
            });

            // Initial sync of facet section accordion arrows
            $('.facet-group').each(function() {
                var $group = $(this);
                var $body = $group.find('.facet-body');
                var $arrow = $group.find('.facet-arrow');
                if ($body.length && $arrow.length) {
                    if ($body.is(':visible')) {
                        $arrow.removeClass('rotated');
                    } else {
                        $arrow.addClass('rotated');
                    }
                }
            });

            // Horizontal mouse wheel scrolling for Active Filters scroller
            var scroller = document.getElementById('pills-scroller');
            if (scroller) {
                scroller.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault();
                        scroller.scrollLeft += e.deltaY;
                    }
                }, { passive: false });
            }

            // Category Checkbox Change
            $(document).on('change', '.filter-cat-cb', function() {
                var parentId = $(this).val();
                var isChecked = $(this).is(':checked');
                // Synchronize all instances (desktop & mobile drawer)
                $('.filter-cat-cb[value="' + parentId + '"]').prop('checked', isChecked);

                var $catItems = $('.filter-cat-cb[value="' + parentId + '"]').closest('.facet-cat-item');

                if (isChecked) {
                    // Move selected category to the top of the list in both sidebars
                    $('.filter-cat-cb[value="' + parentId + '"]').each(function() {
                        var $li = $(this).closest('.facet-cat-item');
                        $li.parent().prepend($li);
                    });
                    $catItems.find('.facet-sub-wrap').slideDown(180);
                    $catItems.find('.sub-toggle-btn i').removeClass('fa-angle-down').addClass('fa-angle-up');

                    // Requirement: Auto-select all subcategories under this category
                    $('.filter-subcat-cb[data-parent="' + parentId + '"]').prop('checked', true);
                } else {
                    $catItems.find('.facet-sub-wrap').slideUp(180);
                    $catItems.find('.sub-toggle-btn i').removeClass('fa-angle-up').addClass('fa-angle-down');
                    // When category is unchecked, also uncheck all its child subcategories
                    $('.filter-subcat-cb[data-parent="' + parentId + '"]').prop('checked', false);
                }

                if (window.facetLimitManager) {
                    window.facetLimitManager.syncAll();
                }
                window.fetchFacetedProducts(false);
            });

            // Subcategory Checkbox Change
            $(document).on('change', '.filter-subcat-cb', function() {
                var val = $(this).val();
                var parentId = $(this).attr('data-parent');
                var isChecked = $(this).is(':checked');
                // Synchronize all instances (desktop & mobile drawer)
                $('.filter-subcat-cb[value="' + val + '"]').prop('checked', isChecked);

                if (isChecked) {
                    // Move selected subcategory to top within its category sub-list in both sidebars
                    $('.filter-subcat-cb[value="' + val + '"]').each(function() {
                        var $subLi = $(this).closest('.facet-sub-item');
                        $subLi.parent().prepend($subLi);

                        // Also move its parent category to the top of categories list
                        var $catLi = $subLi.closest('.facet-cat-item');
                        $catLi.parent().prepend($catLi);
                        $catLi.find('.facet-sub-wrap').slideDown(180);
                        $catLi.find('.sub-toggle-btn i').removeClass('fa-angle-down').addClass('fa-angle-up');
                    });

                    // Check if all available subcategories under this parent category are checked
                    if (parentId) {
                        var $allSubs = $('.facet-sub-list[data-parent="' + parentId + '"]:first .filter-subcat-cb:not(:disabled)');
                        if ($allSubs.length > 0 && $allSubs.filter(':checked').length === $allSubs.length) {
                            $('.filter-cat-cb[value="' + parentId + '"]').prop('checked', true);
                        }
                    }
                } else {
                    // If any subcategory is unchecked, parent category checkbox is unchecked
                    if (parentId) {
                        $('.filter-cat-cb[value="' + parentId + '"]').prop('checked', false);
                    }
                }

                if (window.facetLimitManager) {
                    window.facetLimitManager.syncAll();
                }
                window.fetchFacetedProducts(false);
            });

            // Brand Checkbox Change
            $(document).on('change', '.filter-brand-cb', function() {
                var val = $(this).val();
                var isChecked = $(this).is(':checked');
                $('.filter-brand-cb[value="' + val + '"]').prop('checked', isChecked);
                $('.filter-brand-cb[value="' + val + '"]').closest('.chip-filter-btn').toggleClass('active', isChecked);

                if (isChecked) {
                    // Move selected brand to top of list in both sidebars
                    $('.filter-brand-cb[value="' + val + '"]').each(function() {
                        var $li = $(this).closest('.facet-brand-item');
                        $li.parent().prepend($li);
                    });
                }

                // If user was searching for a brand, clear the search text so it doesn't linger
                var $brandSearch = $('#brand-search-field, .brand-search-input');
                if ($brandSearch.val() && $brandSearch.val().trim() !== '') {
                    $brandSearch.val('');
                    $('#no-brands-msg').hide();
                }

                if (window.facetLimitManager) {
                    window.facetLimitManager.syncAll();
                }
                window.fetchFacetedProducts(false);
            });

            // Size Checkbox Change
            $(document).on('change', '.filter-size-cb', function() {
                var val = $(this).val();
                var isChecked = $(this).is(':checked');
                $('.filter-size-cb[value="' + val + '"]').prop('checked', isChecked);
                $('.filter-size-cb[value="' + val + '"]').closest('.chip-filter-btn').toggleClass('active', isChecked);

                if (isChecked) {
                    // Move selected size to top of list in both sidebars
                    $('.filter-size-cb[value="' + val + '"]').each(function() {
                        var $li = $(this).closest('.facet-size-item');
                        $li.parent().prepend($li);
                    });
                }

                if (window.facetLimitManager) {
                    window.facetLimitManager.syncAll();
                }
                window.fetchFacetedProducts(false);
            });

            // See More / See Less Toggle Click Handler
            $(document).on('click', '.btn-see-more', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var currentState = $btn.attr('data-state') || 'collapsed';
                var nextState = currentState === 'collapsed' ? 'expanded' : 'collapsed';
                $btn.attr('data-state', nextState);

                var $list;
                if ($btn.hasClass('cat-see-more-btn')) {
                    $list = $btn.closest('.facet-body').find('.facet-cat-list');
                } else if ($btn.hasClass('brand-see-more-btn')) {
                    $list = $btn.closest('.facet-body').find('.brand-scroll-list');
                } else if ($btn.hasClass('size-see-more-btn')) {
                    $list = $btn.closest('.facet-body').find('.facet-size-list');
                } else if ($btn.hasClass('sub-see-more-btn')) {
                    $list = $btn.closest('.facet-sub-wrap').find('.facet-sub-list');
                }

                if ($list && $list.length > 0 && window.facetLimitManager) {
                    window.facetLimitManager.applyLimit($list, $btn, nextState);
                }
            });

            // Promos Change
            $(document).on('change', '.filter-bestseller-cb, .filter-offer-cb, .filter-instock-cb', function() {
                var name = $(this).attr('name');
                var isChecked = $(this).is(':checked');
                $('input[name="' + name + '"]').prop('checked', isChecked);
                window.fetchFacetedProducts(false);
            });

            // Customer Rating Checkbox Click / Change Handler (Multi-select)
            $(document).on('change', '.filter-rating-cb', function() {
                var $this = $(this);
                var val = $this.val();
                var isChecked = $this.is(':checked');

                // Synchronize desktop and mobile sidebars for this rating value
                $('.filter-rating-cb[value="' + val + '"]').prop('checked', isChecked);

                window.fetchFacetedProducts(false);
            });

            // Add To Cart
            $(document).on('click', '.add-bucket', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.hide();

                var productId = $this.data('product-id');
                var variantId = $this.data('variant-id');
                var bogoStatus = $this.data('bogo_status');
                var offerStatus = $this.data('offer_status');

                $.ajax({
                    url: "{{ route('productcartadd') }}",
                    type: "POST",
                    data: {
                        product_id: productId,
                        variantId: variantId,
                        quantity: 1,
                        bogo_status: bogoStatus,
                        offer_status: offerStatus,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        window.showSiteLoader();
                    },
                    success: function(response) {
                        window.hideSiteLoader();
                        if (response.cart_count) {
                            $(".cart-item-total-count").html(response.cart_count);
                        }
                        if (typeof updateCartUI === 'function') updateCartUI();
                        if (typeof shakeFloatingCart === 'function') shakeFloatingCart();
                        $this.addClass('shake');
                        setTimeout(function() { $this.removeClass('shake'); }, 500);
                    },
                    error: function() {
                        window.hideSiteLoader();
                        $this.show();
                    }
                });
            });

            // Mobile Drawer close scroll to top
            $(document).on('hidden.bs.offcanvas', '#filteroffcanvas', function () {
                window.scrollToProductTop();
            });

            // Initialize Swiper on active pills slider if pills are present on initial page load
            if ($('#active-pills-container').is(':visible') && $('#pills-list .filter-chip-slide').length > 0) {
                setTimeout(function() {
                    window.initOrUpdatePillsSlider();
                }, 100);
            }

            // Custom Sort Dropdown Handler
            $(document).on('click', '#sortDropdownToggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $dropdown = $('#custom-sort-dropdown');
                var isOpen = $dropdown.hasClass('open');
                if (isOpen) {
                    $dropdown.removeClass('open');
                    $(this).attr('aria-expanded', 'false');
                } else {
                    $dropdown.addClass('open');
                    $(this).attr('aria-expanded', 'true');
                }
            });

            $(document).on('click', '.sort-dropdown-option', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $this = $(this);
                var val = $this.data('value');
                var label = $this.find('.option-label').text();

                // Update UI state
                $('.sort-dropdown-option').removeClass('is-selected').attr('aria-selected', 'false');
                $this.addClass('is-selected').attr('aria-selected', 'true');
                $('#sortCurrentLabel').text(label);

                // Close dropdown
                $('#custom-sort-dropdown').removeClass('open');
                $('#sortDropdownToggle').attr('aria-expanded', 'false');

                // Update native select and trigger product fetch
                var $select = $('#sort-by');
                var oldVal = $select.val();
                $select.val(val);

                if (oldVal !== val) {
                    if (typeof window.fetchFacetedProducts === 'function') {
                        window.fetchFacetedProducts(false);
                    }
                }
            });

            // Close custom sort dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#custom-sort-dropdown').length) {
                    $('#custom-sort-dropdown').removeClass('open');
                    $('#sortDropdownToggle').attr('aria-expanded', 'false');
                }
            });

            // Keyboard Accessibility for Sort Dropdown
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    $('#custom-sort-dropdown').removeClass('open');
                    $('#sortDropdownToggle').attr('aria-expanded', 'false');
                }
            });

            // Synchronize custom sort dropdown if #sort-by is changed programmatically
            window.syncSortDropdownUI = function() {
                var val = $('#sort-by').val() || 'newest';
                var $opt = $('.sort-dropdown-option[data-value="' + val + '"]');
                if ($opt.length) {
                    $('.sort-dropdown-option').removeClass('is-selected').attr('aria-selected', 'false');
                    $opt.addClass('is-selected').attr('aria-selected', 'true');
                    $('#sortCurrentLabel').text($opt.find('.option-label').text());
                }
            };
            $('#sort-by').on('change', window.syncSortDropdownUI);
        });
    }

    initJQueryHandlers();
})();
</script>
@endpush
