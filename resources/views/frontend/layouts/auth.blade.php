@include('frontend.layouts.head')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/auth.css') }}?v={{ filemtime(public_path('assets/frontend/css/auth.css')) }}">

<script>
    document.getElementById('appBody')?.classList.add('lj-auth-body');
</script>

<div class="loader" id="loader"></div>

<div class="lj-auth-shell">
    <!-- Left Side: Desktop Theme Showcase (Hidden on Mobile) -->
    <aside class="lj-auth-visual" aria-label="Liquor Junction Showcase">
        <div class="lj-auth-visual__top">
            <a href="{{ route('frontend.home') }}" class="lj-auth-visual__logo">
                <img src="{{ asset('assets/dashboard/images/liquor-logo.svg') }}"
                    alt="Think Celebration Think Liquor Junction">
            </a>
        </div>

        <div class="lj-auth-visual__showcase">
            <img src="{{ asset('assets/frontend/images/Our-Range.jpg') }}" alt="Liquor Junction Store Range">
            <div class="lj-auth-visual__caption">
                <span class="lj-auth-visual__accent-bar"></span>
                <h2>@yield('auth_visual_title', 'Think Celebration, Think Liquor Junction')</h2>
                <p>@yield('auth_visual_subtitle', 'Explore authentic whiskies, fine wines, craft beers & spirits delivered to your door.')</p>
            </div>
        </div>

        <div class="lj-auth-visual__footer">
            &copy; {{ date('Y') }} liquorjunction. All rights reserved.
        </div>
    </aside>

    <!-- Right Side (Full Width on Mobile): Auth Form View -->
    <main class="lj-auth-main">
        <div class="lj-auth-topbar">
            <a href="{{ route('frontend.home') }}" class="lj-auth-mobile-logo">
                <img src="{{ asset('assets/dashboard/images/liquor-logo.svg') }}" alt="Liquor Junction">
            </a>
            <a href="{{ route('frontend.home') }}" class="lj-auth-back-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <div class="lj-auth-form-center">
            <div class="lj-auth-form-wrap @yield('auth_wrap_class')">
                @yield('content')
            </div>
        </div>
    </main>
</div>

@include('frontend.layouts.foot')
<script>
    if (window.jQuery) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }
</script>
@include('frontend.partials.recaptcha')
@stack('after-scripts')
</body>
</html>
