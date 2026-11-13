@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>Edit profile</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <!--Page Title-->
            <!--Breadcrumb-->
                <div class="breadcrumb_cover">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <!--Breadcrumb-->

            <!--Build the profile Page-->
                <section class="become_an_instructor">
                    @include('sweetalert::alert')
                    <div class="container">
                        <div class="instructor_form">
                            <form action="{{ route('websiteuserprofileedit', ['id' => $id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <h2>Profile Information</h2>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <input type="text" name="name" value="{{ isset($users->name)?$users->name:old('name') }}" placeholder="Name">
                                        @if ($errors->has('name'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <textarea rows="3" value="" name="about_me" placeholder="About Me">{{ isset($users->about_me)?$users->about_me:old('about_me') }}</textarea>
                                        @if ($errors->has('about_me'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('about_me') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <!-- <div class="col-lg-6 form-group">
                                        <input type="text" value="Krump" required="">
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="https://www.facebook.com/admin" required="">
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="https://www.youtube.com/watch?v=GAm7Mp7QlR4" required="">
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="https://www.instagram.com/admin/" required="">
                                    </div> -->
                                    <div class="col-lg-6 form-group">
                                        <input type="email" value="{{ isset($users->email)?$users->email:old('email') }}" name="email" tabindex="1" placeholder="Email">
                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="{{ isset($users->phone)?$users->phone:old('phone') }}" name="phone" tabindex="1" onkeypress="return isNumber(event)" maxlength="15" placeholder="Mobile No.">
                                        @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('phone') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <div class="btn_holder">
                                            <input type="submit" value="Save profile">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                <!--Build the profile Page-->
        </div>
        <script type="text/javascript">
            function isNumber(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                return true;
            }
        </script>    
@endsection