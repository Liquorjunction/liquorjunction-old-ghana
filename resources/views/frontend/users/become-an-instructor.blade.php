@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>Become an instructor</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <!--Page Title-->

            <!--Breadcrumb-->
                <div class="breadcrumb_cover internal_page_breadcrumb">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Become an instructor</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <!--Breadcrumb-->

            <!--Become an instructor Page-->
                <section class="become_an_instructor">
                    @include('sweetalert::alert')
                    <div class="container">
                        <div class="instructor_form">
                            <form action="{{route('edit_become_an_instructor')}}" class="row" enctype="multipart/form-data" method="POST">
                                @csrf
                                @if(session()->has('success'))
                                    <div class="alert alert-success" id="successMessage">
                                        {{ session()->get('success') }}
                                    </div>
                                @endif                                
                                <div class="col-lg-12 form-group">
                                    <h2>Profile Information</h2>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="name"  placeholder="Name of instructor" value="{{old('name', @$users->name?:'')}}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <div class="upload drop-area">
                                        <div class="upload-info">
                                            <svg t="1581822650945" class="clip" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3250" width="20" height="20">
                                                <path d="M645.51621918 141.21142578c21.36236596 0 41.79528808 4.04901123 61.4025879 12.06298852a159.71594214 159.71594214 0 0 1 54.26367236 35.87255836c15.84503198 16.07739258 27.76959252 34.13726783 35.78356909 54.13513184 7.86071778 19.30572486 11.76635766 39.80291724 11.76635767 61.53607177 0 21.68371583-3.90563989 42.22045875-11.76635767 61.54101586-8.01397729 19.99291992-19.95831275 38.02807617-35.78356909 54.08569313l-301.39672877 302.0839231c-9.21038818 9.22027564-20.15112281 16.48278832-32.74310277 21.77270508-12.29040503 4.81036401-24.54125953 7.19329834-36.82177783 7.19329834-12.29040503 0-24.56103516-2.38293433-36.85638427-7.19329834-12.63647461-5.28991675-23.53271461-12.55737281-32.7381587-21.77270508-9.55151367-9.58117675-16.69042992-20.44775367-21.50573731-32.57995583-4.7856443-11.61804223-7.15869117-23.91339135-7.15869188-36.9255979 0-13.14074708 2.37304688-25.55474854 7.16363524-37.19256639 4.81036401-11.94927954 11.94927954-22.78619408 21.50079395-32.55029274l278.11614966-278.46221923c6.45172119-6.51104737 14.22344971-9.75421118 23.27563501-9.75421119 8.8692627 0 16.54705787 3.24316383 23.03338622 9.75421119 6.47644019 6.49127173 9.73937964 14.18389916 9.73937964 23.08282495 0 9.0521853-3.26293945 16.81896972-9.73937964 23.32012891L366.97489888 629.73773218c-6.32812477 6.2935183-9.48724342 14.08007836-9.48724415 23.30529736 0 9.06701684 3.15417457 16.75964356 9.48724414 23.08776904 6.80273414 6.50610328 14.55963111 9.75915528 23.26574683 9.75915527 8.67150855 0 16.43334961-3.253052 23.27563501-9.76409935l301.37695313-302.04931665c18.93988037-18.96459937 28.40734887-42.04742432 28.40734814-69.25836158 0-27.16149926-9.4674685-50.26409912-28.40734815-69.22869849-19.44415283-19.13269043-42.55664086-28.72375464-69.31274438-28.72375536-26.97363258 0-49.99218727 9.59106422-69.1001587 28.72375536L274.3370815 536.89227319a159.99774146 159.99774146 0 0 0-35.80828883 54.33288526c-8.0337522 19.65179443-12.04321289 40.2824707-12.04321289 61.79809618 0 21.20910645 4.00451661 41.81011963 12.04321289 61.79809547 8.17218018 20.34393287 20.10168481 38.36920166 35.80828883 54.08569312 16.225708 16.06256104 34.30535888 28.13049292 54.23400854 36.15930176 19.91381813 8.0337522 40.47033667 12.06793189 61.64978002 12.0679326 21.13989281 0 41.70135474-4.03417969 61.63000513-12.0679326 19.91876221-8.02386474 38.01818872-20.09674073 54.2241211-36.15435768l300.86773656-301.53515601c6.47644019-6.50115991 14.23828125-9.76904273 23.28057912-9.76904344 8.88903833 0 16.56188941 3.26293945 23.04821776 9.76904344 6.48632836 6.48632836 9.7245481 14.17895508 9.7245481 23.06799269 0 9.09667992-3.23822046 16.8535769-9.7245481 23.37451172L552.40379244 815.35449242c-22.00012231 22.01989722-47.32745362 38.88336158-75.986938 50.49151564C449.10209565 877.14270043 420.37834101 882.78857422 390.21592671 882.78857422c-30.01904297 0-58.74279761-5.64587378-86.20587183-16.94256616-28.6842041-11.60815406-54.00659203-28.47161842-76.00671362-50.49151564a226.19586182 226.19586182 0 0 1-50.13061524-75.90289354A226.86328125 226.86328125 0 0 1 160.9697104 653.04797364c0-30.08331323 5.62115479-58.88122559 16.90795899-86.38385035 11.40545654-28.37768578 28.11566138-53.75939917 50.13061523-76.15997313h0.24719287L530.14164643 189.20135474c15.69177247-15.731323 33.68737817-27.70037818 53.98681641-35.89727735C604.09666377 145.26043701 624.55430562 141.23120141 645.51127583 141.23120141V141.21142578z" p-id="3251">
                                                </path>
                                            </svg>
                                            <span class="upload-filename inactive drop-text" id="upload_filename">Upload profile picture</span>
                                        </div>
                                        <div class="upload-button">
                                                <input type="file" id="image_file" name="profile" accept="image/*" />
                                                @if ($errors->has('profile'))
                                                <span class="help-block">
                                                    <span  style="color: red;" class='validate'>{{ $errors->first('profile') }}</span>
                                                </span>
                                                @endif
                                                <label for="image_file" class="upload-button-text">Choose file</label>
                                            </div>
                                            <div class="upload-hint">Uploading...</div>
                                            <div class="upload-progress"></div>                                       
                                    </div>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <textarea rows="3" name="about_me" placeholder="About me (Bio)">{{old('about_me', @$users->about_me?:'')}}</textarea>
                                    @if ($errors->has('about_me'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('about_me') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <textarea rows="3" name="instructor_location" placeholder="Location/Address">{{old('instructor_location', @$users->instructor_location?:'')}}</textarea>
                                    @if ($errors->has('instructor_location'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_location') }}</span>
                                        </span>
                                        @endif
                                </div>    
                                <div class="col-lg-6 form-group">
                                        <select class="form-select" aria-label="category_selection" name="category_dance_instructor">
                                            <option selected="" value="0">Category of dance</option>
                                            <option value="1" {{ ( $users->category_dance_instructor == 1) ? 'selected' : '' }}>Male</option>
                                            <option value="2" {{ ( $users->category_dance_instructor == 2) ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @if ($errors->has('category_dance_instructor'))
                                            <span class="help-block">
                                                <span  style="color: red;" class='validate'>{{ $errors->first('category_dance_instructor') }}</span>
                                            </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="instructor_since" placeholder="Instructor since"  value="{{old('instructor_since', @$users->instructor_since?:'')}}">
                                    @if ($errors->has('instructor_since'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_since') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="dance_group_name" placeholder="Affiliated Groups"  value="{{old('dance_group_name', @$users->dance_group_name?:'')}}">
                                     @if ($errors->has('dance_group_name'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('dance_group_name') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="instructor_facebook_link" placeholder="Facebook Profile Link"  value="{{old('instructor_facebook_link', @$users->instructor_facebook_link?:'')}}">
                                     @if ($errors->has('instructor_facebook_link'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_facebook_link') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="instructor_web_link" placeholder="Youtube Profile Link"  value="{{old('instructor_web_link', @$users->instructor_web_link?:'')}}">
                                     @if ($errors->has('instructor_web_link'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_web_link') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="instructor_instagram_link" placeholder="Instagram Profile Link"  value="{{old('instructor_instagram_link', @$users->instructor_instagram_link?:'')}}">
                                     @if ($errors->has('instructor_instagram_link'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('instructor_instagram_link') }}</span>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-lg-12 form-group">
                                    <h2>Portfolio Image</h2>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <!-- <form class="upload-prescription-form" method="post" enctype="multipart/form-data"> -->
                                        <div class="field" align="left">
                                            <div class="uplo-pres-img-box">
                                                <label class="other-upload-prescription" for="files"><span class="edit-profile-add">+</span></label>
                                                <input type="file" id="files" multiple name="instructor_portfolio_image[]" accept="image/*" />
                                                 @if ($errors->has('instructor_portfolio_image'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('instructor_portfolio_image') }}</span>
                                                    </span>
                                                 @endif
                                            </div>
                                        </div>
                                    <!-- </form> -->
                                </div>
                                
                                <div class="col-lg-12 form-group">
                                    <h2>Portfolio Video</h2>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <!-- <form class="upload-video-form" method="post" enctype="multipart/form-data">
                                        <div class="upload-video position-relative">
                                            <input type="file" class="d-none" accept="audio/*|video/*|video/x-m4v|video/webm|video/x-ms-wmv|video/x-msvideo|video/3gpp|video/flv|video/x-flv|video/mp4|video/quicktime|video/mpeg|video/ogv|.ts|.mkv|image/*|image/heic|image/heif" onchange="previewFiles()" id="inputUp" multiple>
                                            <a class="mediaUp mr-4"><i class="material-icons mr-2"  data-tippy="add (Video, Audio, Photo)" onclick="trgger('inputUp')">perm_media</i></a>
                                        </div>
                                        <div class="Imgpreview">
                                            <i class="material-icons remove">close</i>
                                            <video data-tippy-content="SampleVideo_1280x720_1mb.mp4 / file size 1.0 Mo" onclick='this.classList.toggle("Imgpreview-zoom");' autoplay="" preload="auto" src="" tabindex="0" width="100%"></video>
                                        </div>

                                    </form> -->
                                    <!-- <form class="upload-prescription-form" method="post" enctype="multipart/form-data"> -->
                                        <div class="field" align="left">
                                            <div class="uplo-pres-img-box">
                                                <label class="other-upload-prescription" for="videofiles"><span class="edit-profile-add">+</span></label>
                                                <input type="file" id="videofiles" name="instructor_portfolio_video" accept="video/mp4"  />
                                                 @if ($errors->has('instructor_portfolio_video'))
                                                    <span class="help-block">
                                                        <span  style="color: red;" class='validate'>{{ $errors->first('instructor_portfolio_video') }}</span>
                                                    </span>
                                                 @endif
                                            </div>
                                        </div>
                                    <!-- </form> -->
                                </div>
                                <div class="col-lg-12 form-group">
                                    <div class="btn_holder">
                                        <input type="submit" value="submit">
                                        <!-- <button type="submit">Submit</button> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                <!--Become an instructor Page-->
        </div>
        <script type="text/javascript">
                var input = document.getElementById('image_file');
                var infoArea = document.getElementById('upload_filename');

                input.addEventListener('change', showFileName);

                function showFileName(event) {
                    var input = event.srcElement;
                    var fileName = input.files[0].name;
                    infoArea.textContent = '' + fileName;
                }
        </script>
        <script type="text/javascript">setTimeout(function() {
          $('#successMessage').fadeOut('fast');
        }, 3000); // <-- time in milliseconds</script>
        
@endsection
