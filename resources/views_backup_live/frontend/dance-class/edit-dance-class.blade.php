@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<style type="text/css">
    .center {
        text-align: center;
        margin-top: 200px;
    }

    .no_content {
        font-weight: bold;
        font-size: xx-large;
    }

    form .error {
        color: red;
    }
</style>
<div class="site_content_cover">
    <!--Page Title-->
    <div class="page_title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Edit Class</h1>
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
                    <li class="breadcrumb-item active" aria-current="page">Edit Class</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--Breadcrumb-->

    <!--Add Class Page-->
    <section class="add_class">
        <div class="container">
            <div class="row add_class_row">
                <div class="col-lg-3">
                    <h2>Class Information</h2>
                </div>
                <div class="col-lg-9">
                    <!-- <ul id="progressbar">
                                    <li class="active" id="basic">
                                        <div class="progress_icons">1</div>
                                        <span>Basic</span>
                                    </li>
                                    <li id="options">
                                        <div class="progress_icons">2</div>
                                        <span>Add Lesson</span>
                                    </li>
                                </ul> -->

                    <ul id="progressbar">
                        <li class="active" id="basic">
                            <div class="progress_icons">1</div>
                            <span>Basic</span>
                        </li>
                        <li id="lessons">
                            <div class="progress_icons">2</div>
                            <span>Add Lesson</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="add_class_form">
                <form action="{{route('edit-class-update', ['id' => $dance_class->class_id]) }}" class="msform" id="add-class-form" method="POST" enctype="multipart/form-data">
                    <fieldset id="form_1">
                        <div class="form-card">
                            <div class="row">
                                <div class="col-md-3 upload_img_col form-group">
                                    <div class="upload__box ">
                                        <div class="upload__btn-box">
                                            <div class="upload__img-wrap">
                                                <div class="upload__img-box">
                                                    <!-- <div style="background-image:url('uploads/dance_class/images/<?php echo $dance_class->class_thumbnail_image; ?>')" data-number="0" data-file="menu-iteam1.png" class="img-bg"> -->
                                                         <?php $url = asset('uploads/dance_class/images/'. $dance_class->class_thumbnail_image); ?>
                                                         <div style="background-image:url(<?php echo $url; ?>)" data-number="0" data-file="menu-iteam1.png" class="img-bg">

                                                        <div class="upload__img-close"></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <label class="upload__btn">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.75 10.75H13.25V3.25C13.25 2.91848 13.1183 2.60054 12.8839 2.36612C12.6495 2.1317 12.3315 2 12 2C11.6685 2 11.3505 2.1317 11.1161 2.36612C10.8817 2.60054 10.75 2.91848 10.75 3.25V10.75H3.25C2.91848 10.75 2.60054 10.8817 2.36612 11.1161C2.1317 11.3505 2 11.6685 2 12C2 12.3315 2.1317 12.6495 2.36612 12.8839C2.60054 13.1183 2.91848 13.25 3.25 13.25H10.75V20.75C10.75 21.0815 10.8817 21.3995 11.1161 21.6339C11.3505 21.8683 11.6685 22 12 22C12.3315 22 12.6495 21.8683 12.8839 21.6339C13.1183 21.3995 13.25 21.0815 13.25 20.75V13.25H20.75C21.0815 13.25 21.3995 13.1183 21.6339 12.8839C21.8683 12.6495 22 12.3315 22 12C22 11.6685 21.8683 11.3505 21.6339 11.1161C21.3995 10.8817 21.0815 10.75 20.75 10.75Z" fill="#ff8200" />
                                                </svg>

                                                <input type="file" data-max_length="1" class="upload__inputfile" name="class_thumbnail_image" accept="image/png, image/jpg, image/jpeg" id="class_image" value="{{isset($dance_class->class_thumbnail_image) ? $dance_class->class_thumbnail_image : ''}}">
                                            </label>
                                            <!-- <label id="class_thumbnail_image-error" for="class_thumbnail_image" style="display: none; color: red;">Please select thumbnail image</label> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="text" placeholder="Class Title" name="class_name" value="{{isset($dance_class->class_name) ? $dance_class->class_name : ''}}">

                                    </div>
                                    <div class="form-group">
                                        <select class="form-select" aria-label="category_selection" name="category_name">
                                            <option selected="" value="null" disabled hidden>Category</option>
                                            @foreach ($dance_category as $key => $dc)
                                            <option value="{{ $dc->id }}" {{ ( $dc->id == $dance_class->dance_category_id) ? 'selected' : '' }}>{{ $dc->category_name }}</option>
                                            @endforeach
                                            <!--  <option value="1">Breakin</option>
                                                        <option value="2">Choreography</option>
                                                        <option value="3">Dancehall</option>
                                                        <option value="4">Krump</option>
                                                        <option value="5">Locking</option> -->
                                            
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <!-- <input type="text" placeholder="Dance Level" name="dance_level"> -->
                                        <select class="form-select" aria-label="level_selection" name="dance_level">
                                            <option selected="" value="null" disabled hidden>Dance Level</option>
                                            @foreach ($dance_level as $key => $dl)
                                            <option value="{{ $dl->id }}" {{ ( $dl->id == $dance_class->dance_level) ? 'selected' : '' }}>{{ $dl->title }}</option>
                                            @endforeach
                                            <!--  <option value="1">Open Level</option>
                                                        <option value="2">Beginner</option>
                                                        <option value="3">Intermediate</option>
                                                        <option value="4">Advance</option> -->
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <input type="text" placeholder="Duration (Min)" name="duration" onkeypress="return isNumber(event)" value="{{isset($dance_class->duration) ? $dance_class->duration : ''}}">

                                    </div>
                                    <div class="form-group">
                                        <input type="text" placeholder="Price" name="price" onkeypress="return isNumber(event)" value="{{isset($dance_class->price) ? $dance_class->price : ''}}">

                                    </div>
                                    <div class="form-group">
                                        <input type="text" placeholder="Discount (%)" name="discount_price" onkeypress="return isNumber(event)" value="{{isset($dance_class->discount) ? $dance_class->discount : ''}}">

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea rows="3" placeholder="Description" name="class_description">{{isset($dance_class->class_description) ? $dance_class->class_description : ''}}</textarea>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- <div class="col-12 intro_video">
                                    <h2>Add Introduction video </h2>
                                    <div class="upload__box ">
                                        <div class="upload__btn-box">
                                            <div class="upload__img-wrap"></div>
                                            <label class="upload__btn">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.75 10.75H13.25V3.25C13.25 2.91848 13.1183 2.60054 12.8839 2.36612C12.6495 2.1317 12.3315 2 12 2C11.6685 2 11.3505 2.1317 11.1161 2.36612C10.8817 2.60054 10.75 2.91848 10.75 3.25V10.75H3.25C2.91848 10.75 2.60054 10.8817 2.36612 11.1161C2.1317 11.3505 2 11.6685 2 12C2 12.3315 2.1317 12.6495 2.36612 12.8839C2.60054 13.1183 2.91848 13.25 3.25 13.25H10.75V20.75C10.75 21.0815 10.8817 21.3995 11.1161 21.6339C11.3505 21.8683 11.6685 22 12 22C12.3315 22 12.6495 21.8683 12.8839 21.6339C13.1183 21.3995 13.25 21.0815 13.25 20.75V13.25H20.75C21.0815 13.25 21.3995 13.1183 21.6339 12.8839C21.8683 12.6495 22 12.3315 22 12C22 11.6685 21.8683 11.3505 21.6339 11.1161C21.3995 10.8817 21.0815 10.75 20.75 10.75Z" fill="#ff8200" />
                                                </svg>

                                                <input type="file" data-max_length="1" class="upload__inputfile" name="instruction_video" accept="video/mp4" id="class_video" value="{{isset($dance_class->instruction_video) ? $dance_class->instruction_video : ''}}">
                                            </label>
                                        </div>
                                        <label id="instruction_video-error" for="instruction_video" style="display: none; color: red;">Please select introduction video</label> 
                                    </div>
                                </div> -->

                                <div class="col-lg-6 form-group">
                                    <div class="upload drop-area">
                                        <div class="upload-info">
                                            <svg t="1581822650945" class="clip" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3250" width="20" height="20">
                                                <path d="M645.51621918 141.21142578c21.36236596 0 41.79528808 4.04901123 61.4025879 12.06298852a159.71594214 159.71594214 0 0 1 54.26367236 35.87255836c15.84503198 16.07739258 27.76959252 34.13726783 35.78356909 54.13513184 7.86071778 19.30572486 11.76635766 39.80291724 11.76635767 61.53607177 0 21.68371583-3.90563989 42.22045875-11.76635767 61.54101586-8.01397729 19.99291992-19.95831275 38.02807617-35.78356909 54.08569313l-301.39672877 302.0839231c-9.21038818 9.22027564-20.15112281 16.48278832-32.74310277 21.77270508-12.29040503 4.81036401-24.54125953 7.19329834-36.82177783 7.19329834-12.29040503 0-24.56103516-2.38293433-36.85638427-7.19329834-12.63647461-5.28991675-23.53271461-12.55737281-32.7381587-21.77270508-9.55151367-9.58117675-16.69042992-20.44775367-21.50573731-32.57995583-4.7856443-11.61804223-7.15869117-23.91339135-7.15869188-36.9255979 0-13.14074708 2.37304688-25.55474854 7.16363524-37.19256639 4.81036401-11.94927954 11.94927954-22.78619408 21.50079395-32.55029274l278.11614966-278.46221923c6.45172119-6.51104737 14.22344971-9.75421118 23.27563501-9.75421119 8.8692627 0 16.54705787 3.24316383 23.03338622 9.75421119 6.47644019 6.49127173 9.73937964 14.18389916 9.73937964 23.08282495 0 9.0521853-3.26293945 16.81896972-9.73937964 23.32012891L366.97489888 629.73773218c-6.32812477 6.2935183-9.48724342 14.08007836-9.48724415 23.30529736 0 9.06701684 3.15417457 16.75964356 9.48724414 23.08776904 6.80273414 6.50610328 14.55963111 9.75915528 23.26574683 9.75915527 8.67150855 0 16.43334961-3.253052 23.27563501-9.76409935l301.37695313-302.04931665c18.93988037-18.96459937 28.40734887-42.04742432 28.40734814-69.25836158 0-27.16149926-9.4674685-50.26409912-28.40734815-69.22869849-19.44415283-19.13269043-42.55664086-28.72375464-69.31274438-28.72375536-26.97363258 0-49.99218727 9.59106422-69.1001587 28.72375536L274.3370815 536.89227319a159.99774146 159.99774146 0 0 0-35.80828883 54.33288526c-8.0337522 19.65179443-12.04321289 40.2824707-12.04321289 61.79809618 0 21.20910645 4.00451661 41.81011963 12.04321289 61.79809547 8.17218018 20.34393287 20.10168481 38.36920166 35.80828883 54.08569312 16.225708 16.06256104 34.30535888 28.13049292 54.23400854 36.15930176 19.91381813 8.0337522 40.47033667 12.06793189 61.64978002 12.0679326 21.13989281 0 41.70135474-4.03417969 61.63000513-12.0679326 19.91876221-8.02386474 38.01818872-20.09674073 54.2241211-36.15435768l300.86773656-301.53515601c6.47644019-6.50115991 14.23828125-9.76904273 23.28057912-9.76904344 8.88903833 0 16.56188941 3.26293945 23.04821776 9.76904344 6.48632836 6.48632836 9.7245481 14.17895508 9.7245481 23.06799269 0 9.09667992-3.23822046 16.8535769-9.7245481 23.37451172L552.40379244 815.35449242c-22.00012231 22.01989722-47.32745362 38.88336158-75.986938 50.49151564C449.10209565 877.14270043 420.37834101 882.78857422 390.21592671 882.78857422c-30.01904297 0-58.74279761-5.64587378-86.20587183-16.94256616-28.6842041-11.60815406-54.00659203-28.47161842-76.00671362-50.49151564a226.19586182 226.19586182 0 0 1-50.13061524-75.90289354A226.86328125 226.86328125 0 0 1 160.9697104 653.04797364c0-30.08331323 5.62115479-58.88122559 16.90795899-86.38385035 11.40545654-28.37768578 28.11566138-53.75939917 50.13061523-76.15997313h0.24719287L530.14164643 189.20135474c15.69177247-15.731323 33.68737817-27.70037818 53.98681641-35.89727735C604.09666377 145.26043701 624.55430562 141.23120141 645.51127583 141.23120141V141.21142578z" p-id="3251">
                                                </path>
                                            </svg>
                                            <span class="upload-filename inactive drop-text" id="upload_filename">Upload introduction video</span>
                                        </div>
                                        <div class="upload-button">
                                                <input type="file" id="image_file" name="instruction_video" accept="video/mp4" />
                                                <label for="image_file" class="upload-button-text">Choose file</label>
                                            </div>
                                            <div class="upload-hint">Uploading...</div>
                                            <div class="upload-progress"></div>                                       
                                    </div>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <div class="upload_video_label">
                                        <span class="upload_video_label"></span>
                                        <ul class="edit_delete">
                                            <input type="hidden" name="cl_id" value="" id="cl_id">
                                            <li><a href="javascript:void(0)" class="delete_btn" id="delete-class"></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="next action-button" id="next_btn">Next</button>
                    </fieldset>

                    <fieldset id="form_2">
                        <div class="row">
                            <div class="col-12">
                                <div class="lessions_cover">
                                    <h3>Add Section</h3>
                                    <a href="javascript:void(0)" onclick="addSection()" class="add_lession">Add New</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center" id="loader" style="display:none;     margin-left: 560px; margin-top: -100px;">
                                <img class="logo-img" alt="" src="{{ asset('assets/dashboard/images/loading.gif')}}">
                        </div>
                        <?php $key = 1; $j = 0;?>
                        @foreach($class_lesson as $c => $cl)
                        <?php $j = $c; ?>
                        <input type="hidden" name="lesson_id[]" value="{{$cl->id}}">
                        <div class="add-section-new">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Name of lesson" name="lessons[{{$key}}][lesson_name]" value="{{isset($cl->title) ? $cl->title : ''}}">
                                    </div>
                                </div>
                                <div class="{{$j == 0 ? 'col-md-6' : 'col-md-5'}}">
                                    <div class="form-group lession_desri">
                                        <textarea rows="3" placeholder="Description of lesson" name="lessons[{{$key}}][lesson_description]">{{isset($cl->description) ? $cl->description : ''}}</textarea>
                                    </div>
                                </div>
                                @if($j != 0)
                                <div class="col-md-1">
                                    <div class="form-group mb-0 mb-md-auto">
                                        <button type="button" class="m-0 action-button-previous video-del" onclick="deleteSection(this)">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif

                            </div>
                            <?php $class_lesson_video = App\Models\ClassLessionVideo::where('class_lession_id', $cl->id)->get();
                                $i = 0;
                            ?>
                            @foreach($class_lesson_video as $k => $clv)
                            <?php $i = $k; ?>
                            <input type="hidden" name="video_id[]" value="{{$clv->id}}">
                            <div class="video_container">
                                <div class="row">
                                    <div class="col-md-6 add-videos-title">
                                        <div class="form-group">
                                            <input type="text" placeholder="Video title" name="lessons[{{$key}}][videos][video_title][]" value="{{isset($clv->video_name) ? $clv->video_name : ''}}">
                                        </div>
                                    </div>

                                    <div class="col-md-5 add-videos-class">
                                        <div class="form-group">
                                            <input type="file" name="lessons[{{$key}}][videos][video_file][]" accept="video/mp4" value="{{isset($clv->video_file) ? $clv->video_file : ''}}">
                                        </div>
                                    </div>
                                    @if($i == 0)
                                    <div class="col-md-1">
                                        <div class="form-group mb-0 mb-md-auto">
                                            <button type="button" class="action-button m-0 video-add" onclick="addVideo()">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-1">
                                        <input type="hidden" name="remove_video_id[]" value="{{$clv->id}}">
                                        <div class="form-group mb-0 mb-md-auto">
                                            <button type="button" class="m-0 action-button-previous video-del" onclick="deleteVideo(this)">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <?php $key++;?>
                        @endforeach
                        <button type="submit" class="action-button" id="submit_btn">Submit</button>
                        <button type="button" class="previous action-button-previous" id="prev_btn">Previous</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </section>
    <!--Add Class Page-->
</div>
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">

    function deleteSection(thisitem)
    {
        $(thisitem).closest('.add-section-new').remove();
    }

    function deleteVideo(thisitem)
    {
        $(thisitem).closest('.add-videos-title').remove();
        $(thisitem).closest('.add-videos-class').remove();
    }

    function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

    var count = 1;
    var count1 = 1;
    function addSection() {
        count++;
        count1++;
        var html = '';
        html += '<div class="add-section-new rounded">';
        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += '<div class="form-group">';
        html += '<input type="text" placeholder="Name of lesson" name="lessons['+count+'][lesson_name]">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-6">';
        html += '<div class="form-group lession_desri">';
        html += '<textarea rows="3" placeholder="Description of lesson" name="lessons['+count+'][lesson_description]"></textarea>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '<div class="video_container">';
        html += '<div class="row">';
        html += '<div class="col-md-6 add-videos-title">';
        html += '<div class="form-group">';
        html += '<input type="text" placeholder="Video title" name="lessons['+count+'][videos][video_title][]">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-5 add-videos-class">';
        html += '<div class="form-group">';
        html += '<input type="file" name="lessons['+count+'][videos][video_file][]" accept="video/mp4">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-1">';
        html += '<div class="form-group mb-0 mb-md-auto">';
        html += '<button type="button" class="action-button m-0 video-add">';
        html += '<i class="bi bi-plus"></i>';
        html += '</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $(html).insertAfter('.add-section-new:last');
    }

    function addVideo(thisitem) {
        var html = '';
        // html += '<div class="add-videos-class">';
        html += '<div class="col-md-6 add-videos-title">';
        html += '<div class="form-group">';
        html += '<input type="text" placeholder="Video title" name="lessons['+count+'][videos][video_title][]">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-5 add-videos-class">';
        html += '<div class="form-group">';
        html += '<input type="file" name="lessons['+count+'][videos][video_file][]" accept="video/mp4">';
        html += '</div>';
        html += '</div>';
        // html += '</div>';
        // $(html).insertAfter(( $(".add-section-new").closest('.add-videos-class:last')));
        // $('.add-section-new:last').find('.add-videos-class:last').after(html);
        $(thisitem).parents('.add-section-new').find('.add-videos-class:last').after(html);
    }

    $(document).ready(function() {
        $(document).ready(function() {
            if (window.File && window.FileList && window.FileReader) {
                $("#files").on("change", function(e) {
                    var files = e.target.files,
                        filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[i]
                        var fileReader = new FileReader();
                        fileReader.onload = (function(e) {
                            var file = e.target;
                            $("<span class=\"pip\">" + "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" + "<br/><span class=\"remove\">Remove image</span>" + "</span>").insertAfter("#files");
                            $(".remove").click(function() {
                                $(this).parent(".pip").remove();
                            });
                        });
                        fileReader.readAsDataURL(f);
                    }
                });
            } else {
                alert("Your browser doesn't support to File API")
            }
        });

        $("#videofiles").on('change', function(event) {
            console.log('HElloo');
            var files = event.target.files; //FileList object
            console.log('Files : ' + files);

            for (var i = 0; i < files.length; i++) {
                console.log('Imaeg : ' + $(this)[i]);

                var file = files[i];

                // var imageLInk = 'https://vrinsoft.in/design-test/salamtak/images/arabic%201.svg';
                var imageLInk = './images/video_thumbnail.svg';
                if (typeof(FileReader) != "undefined") {

                    if (file.type.match('image')) {

                        //loop for each file selected for uploaded.
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $("<span class=\"pip\">" +
                                "<img id=\"imageReplace\" class=\"imageThumb\" src=\"" + e.target.result + "\">" +
                                "<br/><span class=\"remove\">Remove image</span>" +
                                "</span>").insertAfter("#files");
                            $(".remove").click(function() {

                                $(this).parent(".pip").remove();
                            });
                        }

                        // image_holder.show();
                        // reader.readAsDataURL($(this)[0].files[i]);
                        reader.readAsDataURL(file);
                        //}

                    } else {
                        // var reader = new FileReader();
                        // reader.onload = function (e) {
                        $("<span class=\"pip\">" +
                            "<img id=\"imageReplace\" class=\"imageThumb\" src=\"" + imageLInk + "\">" +
                            "<br/><span class=\"remove\">Remove File</span>" +
                            "</span>").insertAfter("#videofiles");
                        $(".remove").click(function() {
                            $(this).parent(".pip").remove();
                        });
                        // }
                    }
                } else {
                    alert("This browser does not support FileReader.");
                }
            }

        });

        // Progressbar 
        $(document).ready(function() {

            var current_fs, next_fs, previous_fs; //fieldsets
            var opacity;
            var current = 1;
            var steps = $("fieldset").length;

            setProgressBar(current);

            // $(".next").click(function(){

            // current_fs = $(this).parent();
            // next_fs = $(this).parent().next();

            // //Add Class Active
            // $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

            // //show the next fieldset
            // next_fs.show();
            // //hide the current fieldset with style
            // current_fs.animate({opacity: 0}, {
            // step: function(now) {
            // // for making fielset appear animation
            // opacity = 1 - now;

            // current_fs.css({
            // 'display': 'none',
            // 'position': 'relative'
            // });
            // next_fs.css({'opacity': opacity});
            // },
            // duration: 500
            // });
            // setProgressBar(++current);
            // });

            $(".previous").click(function() {

                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();

                //Remove class active
                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

                //show the previous fieldset
                previous_fs.show();

                //hide the current fieldset with style
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now) {
                        // for making fielset appear animation
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        previous_fs.css({
                            'opacity': opacity
                        });
                    },
                    duration: 500
                });
                setProgressBar(--current);
            });

            function setProgressBar(curStep) {
                var percent = parseFloat(100 / steps) * curStep;
                percent = percent.toFixed();
                $(".progress-bar")
                    .css("width", percent + "%")
            }

            $(".submit").click(function() {
                return false;
            })

        });

        // $('#next_btn').click(function(e){

        //     e.preventDefault();

        //     var email = $("input[name=email]").val();
        //     var url = '{{ route('subscribeemail') }}';

        //     $.ajax({
        //        url:url,
        //        method:'POST',
        //        dataType: 'json',
        //        data:{
        //               email:email
        //             },
        //        success:function(response){
        //           if(response.success){
        //                   $("span#successMessage").css("display", "block");
        //                   $("span#successMsg").css("display", "block");
        //                   $("span#successMsg").html("Your email is subscribed..!");
        //                   setTimeout(function() {
        //                       $('#successMessage').fadeOut('fast');
        //                     }, 5000);
        //                   $('#subscribe_email').val('');
        //           }else{
        //                   $("span#successMessage").css("display", "block");
        //                   $("span#successMsg").css("display", "block");
        //                   $("span#successMsg").html("Your email is subscribed..!");
        //                   setTimeout(function() {
        //                       $('#successMessage').fadeOut('fast');
        //                     }, 5000);
        //                   $('#subscribe_email').val('');
        //           }
        //        },
        //        error:function(error){

        //            var erroJson = JSON.parse(error.responseText);
        //               for (var err in erroJson) {
        //                 for (var errstr of erroJson[err])
        //                   $("span#errorMessage").css("display", "block");
        //                   $("span#errorMsg").css("display", "block");
        //                   $("span#errorMsg").html(errstr);
        //                   setTimeout(function() {
        //                       $('#errorMessage').fadeOut('fast');
        //                     }, 5000);
        //                   $('#subscribe_email').val('');
        //               }
        //        }
        //     });
        // });

        $("#add-class-form").validate({
            rules: {
                'class_name': {
                    required: true,
                    maxlength: 50,
                },
                'category_name': {
                    required: true,
                },
                'dance_level': {
                    required: true,
                },
                'duration': {
                    required: true,
                },
                'price': {
                    required: true,
                },
                // 'discount_price': {
                //     required: true,
                // },
                'class_description': {
                    required: true,
                },
                // 'lesson_name': {
                //     required: true,
                // },
                // 'lesson_description': {
                //     required: true,
                // },
                // 'video_title': {
                //     required: true,
                // },
                // 'video_file': {
                //     required: true,
                //     accept: "video/mp4",
                // },
            },
            messages: {
                class_name: {
                    required: "Please enter class name",
                    maxlength: "Class name should not be more than 50 characters",
                },
                category_name: {
                    required: "Please select category name",
                },
                dance_level: {
                    required: "Please select dance level",
                },
                duration: {
                    required: "Please enter duration",
                },
                price: {
                    required: "Please enter class price",
                },
                // discount_price: {
                //     required: "Please enter class discount price",
                // },
                class_description: {
                    required: "Please enter class description",
                },
                
                // lesson_name: {
                //     required: "Please enter lesson name",
                // },
                // lesson_description: {
                //     required: "Please enter lesson description",
                // },
                // video_title: {
                //     required: "Please enter video title",
                // },
                // video_file: {
                //     required: "Please select video file",
                //     accept: "Video should be mp4 extension type",
                // },
            },
            highlight: function(element) {
                $(element).parent().addClass('error')
            },
            unhighlight: function(element) {
                $(element).parent().removeClass('error')
            }
        });

        $('#add-class-form').on("submit",function(e) {
                e.preventDefault();
               
                var frm = $('#add-class-form');
                    var formData = new FormData(frm[0]);
                    var id = "{{$dance_class->class_id}}"
                    loadershow();
                    //var newUrl = "{{route('my-class')}}";
                    $.ajax({
                        url: "{{ url('edit-class/update') }}" + '/' + id,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) 
                        {
                            if(response.success == true)
                            {
                                loaderhide();
                                var newUrl = response.route;
                                window.location.href = newUrl;

                            }
                            else
                            {
                                console.log(response);

                            }
                        },
                        error:function(err) {
                            if (err.status == 422)
                            {
                              
                                loaderhide();
                                console.log(err);
                            }
                        }
                   });

            });


        $('#next_btn').click(function(e) {
            if ($("#add-class-form").valid()) {

                    current_fs = $(this).parent();
                    next_fs = $(this).parent().next();

                    //Add Class Active
                    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                    //show the next fieldset
                    next_fs.show();
                    //hide the current fieldset with style
                    current_fs.animate({
                        opacity: 0
                    }, {
                        step: function(now) {
                            // for making fielset appear animation
                            opacity = 1 - now;

                            current_fs.css({
                                'display': 'none',
                                'position': 'relative'
                            });
                            next_fs.css({
                                'opacity': opacity
                            });
                        },
                        duration: 500
                    });
                    setProgressBar(++current);
            }
        });
    });

        function loadershow(){
            $("#center-block").attr('disabled','disabled');
            $('#loader').show();                               
          } 

          function loaderhide(){
             
             $('#loader').hide();                               
           }
</script>
@endsection