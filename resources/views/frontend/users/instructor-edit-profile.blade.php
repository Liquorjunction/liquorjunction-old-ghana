@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>Build the profile</h1>
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
                                <li class="breadcrumb-item active" aria-current="page">Build the profile</li>
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
                            <form action="{{ route('websiteinstructorprofileedit', ['id' => $id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <h2>Profile Information</h2>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <textarea rows="3" value="" name="about_me" tabindex="1" placeholder="About me (Bio)">{{ isset($users->about_me)?$users->about_me:old('about_me') }}</textarea>
                                        @if ($errors->has('about_me'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('about_me') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="{{ isset($users->category_name)?$users->category_name:old('category_name') }}" name="category_name" tabindex="1" placeholder="Category Name">
                                        @if ($errors->has('category_name'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('category_name') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="{{ isset($users->instructor_facebook_link)?$users->instructor_facebook_link:old('instructor_facebook_link') }}" name="instructor_facebook_link" tabindex="1" placeholder="Instructor Facebook Link">
                                        @if ($errors->has('instructor_facebook_link'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_facebook_link') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="{{ isset($users->instructor_instagram_link)?$users->instructor_instagram_link:old('instructor_instagram_link') }}" name="instructor_instagram_link" tabindex="1" placeholder="Instructor Instagram Link">
                                        @if ($errors->has('instructor_instagram_link'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_instagram_link') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" value="{{ isset($users->instructor_web_link)?$users->instructor_web_link:old('instructor_web_link') }}" name="instructor_web_link" tabindex="1" placeholder="Instructor Web Link">
                                        @if ($errors->has('instructor_web_link'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_web_link') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <h2>Add Photo</h2>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <!-- <div class="build_prefill_data">
                                            <div class="field">
                                                <div class="uplo-pres-img-box">
                                                    <label class="other-upload-prescription" for="files"><span class="edit-profile-add">+</span></label>
                                                    <input type="file" id="files" name="files[]" />
                                                </div>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/listing_popular_img3.jpg" class="imageThumb" alt="listing_popular_img">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/listing_popular_img3.jpg" class="imageThumb" alt="listing_popular_img">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/listing_popular_img3.jpg" class="imageThumb" alt="listing_popular_img">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/listing_popular_img3.jpg" class="imageThumb" alt="listing_popular_img">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                        </div>  -->
                                        
                                        <div class="upload__box build_prefill_data">
                                            <div class="upload__btn-box">
                                                <div class="upload__img-wrap">
                                                    @if(isset($users->instructor_portfolio_image) && $users->instructor_portfolio_image != "")
                                                    <?php $image = $users->instructor_portfolio_image;
                                                            $images = explode(',', $image );
                                                        ?>
                                                    @foreach($images as $im)
                                                    <div class="upload__img-box">
                                                        <!-- asset('uploads/dance_class/images/').'/'.$dc->class_thumbnail_image -->
                                                        
                                                        <div style="background-image:url('uploads/website_users/portfolio_image/<?php echo $im; ?>')" data-number="0" data-file="menu-iteam1.png" class="img-bg">

                                                            <div class="upload__img-close"></div>

                                                        </div>
                                                        
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                    <!-- <div class="upload__img-box">
                                                        <div style="background-image:url(images/listing_popular_img3.jpg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    <div class="upload__img-box">
                                                        <div style="background-image:url(images/listing_popular_img3.jpg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    <div class="upload__img-box">
                                                        <div style="background-image:url(images/listing_popular_img3.jpg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <label class="upload__btn">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M20.75 10.75H13.25V3.25C13.25 2.91848 13.1183 2.60054 12.8839 2.36612C12.6495 2.1317 12.3315 2 12 2C11.6685 2 11.3505 2.1317 11.1161 2.36612C10.8817 2.60054 10.75 2.91848 10.75 3.25V10.75H3.25C2.91848 10.75 2.60054 10.8817 2.36612 11.1161C2.1317 11.3505 2 11.6685 2 12C2 12.3315 2.1317 12.6495 2.36612 12.8839C2.60054 13.1183 2.91848 13.25 3.25 13.25H10.75V20.75C10.75 21.0815 10.8817 21.3995 11.1161 21.6339C11.3505 21.8683 11.6685 22 12 22C12.3315 22 12.6495 21.8683 12.8839 21.6339C13.1183 21.3995 13.25 21.0815 13.25 20.75V13.25H20.75C21.0815 13.25 21.3995 13.1183 21.6339 12.8839C21.8683 12.6495 22 12.3315 22 12C22 11.6685 21.8683 11.3505 21.6339 11.1161C21.3995 10.8817 21.0815 10.75 20.75 10.75Z" fill="#ff8200"/>
                                                    </svg>
                                                    
                                                    <input type="file" multiple data-max_length="20" class="upload__inputfile" name="instructor_portfolio_image[]" accept="image/*">
                                                </label>
                                                @if ($errors->has('instructor_portfolio_image'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('instructor_portfolio_image') }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12 form-group">
                                        <h2>Add Video</h2>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <!-- <div class="build_prefill_data">
                                            <div class="field">
                                                <div class="uplo-pres-img-box">
                                                    <label class="other-upload-prescription" for="files"><span class="edit-profile-add">+</span></label>
                                                    <input type="file" id="files" name="files[]" />
                                                </div>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/video_thumbnail.svg" class="imageThumb" alt="introduction_video">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/video_thumbnail.svg" class="imageThumb" alt="introduction_video">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/video_thumbnail.svg" class="imageThumb" alt="introduction_video">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/video_thumbnail.svg" class="imageThumb" alt="introduction_video">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                        </div>    -->

                                        <div class="upload__box build_prefill_data">
                                            <div class="upload__btn-box">
                                                <div class="upload__img-wrap">
                                                    @if(isset($users->instructor_portfolio_video) && $users->instructor_portfolio_video != "")
                                                    <div class="upload__img-box">
                                                       <!--  <div style="background-image:url('uploads/website_users/portfolio_video/<?php echo $users->instructor_portfolio_video; ?>')" data-number="0" data-file="menu-iteam1.png" class="img-bg"> -->
                                                        <div style="background-image:url('assets/frontend/images/video_thumbnail.svg')" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                   <!--  <div class="upload__img-box">
                                                        <div style="background-image:url(images/video_thumbnail.svg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    <div class="upload__img-box">
                                                        <div style="background-image:url(images/video_thumbnail.svg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    <div class="upload__img-box">
                                                        <div style="background-image:url(images/video_thumbnail.svg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <label class="upload__btn">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M20.75 10.75H13.25V3.25C13.25 2.91848 13.1183 2.60054 12.8839 2.36612C12.6495 2.1317 12.3315 2 12 2C11.6685 2 11.3505 2.1317 11.1161 2.36612C10.8817 2.60054 10.75 2.91848 10.75 3.25V10.75H3.25C2.91848 10.75 2.60054 10.8817 2.36612 11.1161C2.1317 11.3505 2 11.6685 2 12C2 12.3315 2.1317 12.6495 2.36612 12.8839C2.60054 13.1183 2.91848 13.25 3.25 13.25H10.75V20.75C10.75 21.0815 10.8817 21.3995 11.1161 21.6339C11.3505 21.8683 11.6685 22 12 22C12.3315 22 12.6495 21.8683 12.8839 21.6339C13.1183 21.3995 13.25 21.0815 13.25 20.75V13.25H20.75C21.0815 13.25 21.3995 13.1183 21.6339 12.8839C21.8683 12.6495 22 12.3315 22 12C22 11.6685 21.8683 11.3505 21.6339 11.1161C21.3995 10.8817 21.0815 10.75 20.75 10.75Z" fill="#ff8200"/>
                                                    </svg>
                                                    
                                                    <input type="file" data-max_length="20" class="upload__inputfile" name="instructor_portfolio_video" accept="video/mp4">
                                                </label>
                                                @if ($errors->has('instructor_portfolio_video'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('instructor_portfolio_video') }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <h2>Add Introduction Video</h2>
                                    </div>
                                    <div class="col-lg-12 form-group build_prefill_data">
                                        <!-- <div class="build_prefill_data">
                                            <div class="field">
                                                <div class="uplo-pres-img-box">
                                                    <label class="other-upload-prescription" for="files"><span class="edit-profile-add">+</span></label>
                                                    <input type="file" id="files" name="files[]" />
                                                </div>
                                            </div>
                                            <div class="my_upload">
                                                <span class="pip">
                                                    <img src="images/video_thumbnail.svg" class="imageThumb" alt="introduction_video">
                                                    <span class="remove">Remove image</span>
                                                </span>
                                            </div>
                                        </div>    -->

                                        <div class="upload__box build_prefill_data">
                                            <div class="upload__btn-box">
                                                <div class="upload__img-wrap">
                                                     @if(isset($users->introduction_video) && $users->introduction_video != "")
                                                    <div class="upload__img-box">
                                                        <!-- video_thumbnail.svg 
                                                            background-image:url(asset('assets/frontend/images/video_thumbnail.svg)"-->
                                                        <!-- <div style="background-image:url('uploads/website_users/introduction_video/<?php echo $users->introduction_video; ?>')" data-number="0" data-file="menu-iteam1.png" class="img-bg"> -->
                                                        <div style="background-image:url('assets/frontend/images/video_thumbnail.svg')" data-number="0" data-file="menu-iteam1.png" class="img-bg">    
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <!-- <div class="upload__img-box">
                                                        <div style="background-image:url(images/video_thumbnail.svg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    <div class="upload__img-box">
                                                        <div style="background-image:url(images/video_thumbnail.svg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div>
                                                    <div class="upload__img-box">
                                                        <div style="background-image:url(images/video_thumbnail.svg)" data-number="0" data-file="menu-iteam1.png" class="img-bg">
                                                            <div class="upload__img-close"></div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <label class="upload__btn">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M20.75 10.75H13.25V3.25C13.25 2.91848 13.1183 2.60054 12.8839 2.36612C12.6495 2.1317 12.3315 2 12 2C11.6685 2 11.3505 2.1317 11.1161 2.36612C10.8817 2.60054 10.75 2.91848 10.75 3.25V10.75H3.25C2.91848 10.75 2.60054 10.8817 2.36612 11.1161C2.1317 11.3505 2 11.6685 2 12C2 12.3315 2.1317 12.6495 2.36612 12.8839C2.60054 13.1183 2.91848 13.25 3.25 13.25H10.75V20.75C10.75 21.0815 10.8817 21.3995 11.1161 21.6339C11.3505 21.8683 11.6685 22 12 22C12.3315 22 12.6495 21.8683 12.8839 21.6339C13.1183 21.3995 13.25 21.0815 13.25 20.75V13.25H20.75C21.0815 13.25 21.3995 13.1183 21.6339 12.8839C21.8683 12.6495 22 12.3315 22 12C22 11.6685 21.8683 11.3505 21.6339 11.1161C21.3995 10.8817 21.0815 10.75 20.75 10.75Z" fill="#ff8200"/>
                                                    </svg>
                                                    
                                                    <input type="file" data-max_length="20" class="upload__inputfile" name="introduction_video" accept="video/mp4">
                                                </label>
                                                @if ($errors->has('introduction_video'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('introduction_video') }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <h2>Add bank account</h2>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" placeholder="Bank Account Name" name="bank_account_name" value="{{ isset($users->bank_account_name)?$users->bank_account_name:old('bank_account_name') }}" tabindex="1">
                                         @if ($errors->has('bank_account_name'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('bank_account_name') }}</span>
                                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" placeholder="Bank Account Number" name="bank_account_number" value="{{ isset($users->bank_account_number)?$users->bank_account_number:old('bank_account_number') }}" tabindex="1">
                                         @if ($errors->has('bank_account_number'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('bank_account_number') }}</span>
                                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" placeholder="Bank Name" name="bank_name" value="{{ isset($users->bank_name)?$users->bank_name:old('bank_name') }}" tabindex="1"> 
                                        @if ($errors->has('bank_name'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('bank_name') }}</span>
                                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <input type="text" placeholder="G-cash Number" name="gcash_number" value="{{ isset($users->gcash_number)?$users->gcash_number:old('gcash_number') }}" tabindex="1">
                                        @if ($errors->has('gcash_number'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('gcash_number') }}</span>
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
        <script>
            $(document).ready(function(){if(window.File&&window.FileList&&window.FileReader){$("#files").on("change",function(e){var files=e.target.files,filesLength=files.length;for(var i=0;i<filesLength;i++){var f=files[i]
            var fileReader=new FileReader();fileReader.onload=(function(e){var file=e.target;$("<span class=\"pip\">"+"<img class=\"imageThumb\" src=\""+e.target.result+"\" title=\""+file.name+"\"/>"+"<br/><span class=\"remove\">Remove image</span>"+"</span>").insertAfter("#files");$(".remove").click(function(){$(this).parent(".pip").remove();});});fileReader.readAsDataURL(f);}});}else{alert("Your browser doesn't support to File API")}});

            $("#videofiles").on('change', function (event) {
                console.log('HElloo');
                var files = event.target.files; //FileList object
                console.log('Files : ' + files);
                
                for (var i = 0; i < files.length; i++) {
                    console.log('Imaeg : ' + $(this)[i]);
                
                    var file = files[i];
                
                    // var imageLInk = 'https://vrinsoft.in/design-test/salamtak/images/arabic%201.svg';
                    var imageLInk = './images/video_thumbnail.svg';
                    if (typeof (FileReader) != "undefined") {
                        
                        if(file.type.match('image')){

                            //loop for each file selected for uploaded.
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    $("<span class=\"pip\">" +
                                        "<img id=\"imageReplace\" class=\"imageThumb\" src=\"" + e.target.result + "\">" +
                                        "<br/><span class=\"remove\">Remove image</span>" +
                                        "</span>").insertAfter("#files");
                                        $(".remove").click(function(){
                                        $(this).parent(".pip").remove();
                                    });
                                }
                                // image_holder.show();
                                // reader.readAsDataURL($(this)[0].files[i]);
                                reader.readAsDataURL(file);
                            //}
                        }
                        else {
                            // var reader = new FileReader();
                            // reader.onload = function (e) {
                                $("<span class=\"pip\">" +
                                    "<img id=\"imageReplace\" class=\"imageThumb\" src=\"" + imageLInk + "\">" +
                                    "<br/><span class=\"remove\">Remove File</span>" +
                                    "</span>").insertAfter("#videofiles");
                                    $(".remove").click(function(){
                                    $(this).parent(".pip").remove();
                                });
                            // }
                        }
                    } else {
                        alert("This browser does not support FileReader.");
                    }
                }

            });
        </script> 
@endsection