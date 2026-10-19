<div class="tab-pane {{  ( Session::get('active_tab') == 'webInfo') ? 'active' : '' }}" id="tab-8">
    <div class="p-a-sm">
        <h5>Website Information</h5>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">
            {{--<div class="col-sm-4">
                <label>{{ __('backend.dateFormat') }} </label>
                <select name="date_format" class="form-control c-select">
                    <option value="Y-m-d" {{ env('DATE_FORMAT', 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>Y-m-d</option>
                    <option value="d-m-Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'd-m-Y' ? 'selected' : '' }}>d-m-Y</option>
                    <option value="m-d-Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'm-d-Y' ? 'selected' : '' }}>m-d-Y</option>
                    <option value="d/m/Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>d/m/Y</option>
                    <option value="m/d/Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>m/d/Y</option>
                </select>
            </div>--}}
            <div class="col-sm-6" style="margin-bottom: 2px;">
            <label>Language: </label>
            <select name="language_id" class="form-control c-select">
                <option {{ (isset($WebmasterSetting->language_id) && $WebmasterSetting->language_id	 == 1) || old('language_id') == 1  ? 'selected' : '' }} value="1">EN</option>
                <option {{ (isset($WebmasterSetting->language_id	) && $WebmasterSetting->language_id	 == 2) || old('language_id') == 2  ? 'selected' : '' }} value="2">FR</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label for="phone">Phone Number:</label> {!! Form::text('phone', $WebmasterSetting->phone, [ 'placeholder' => '', 'class' => 'form-control', 'id' => 'phone', ]) !!}
        </div>
        </div>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">
            <div class="col-sm-6" id="">
                <label>Email:</label> {!! Form::text('email', $WebmasterSetting->email, [ 'id' => 'email', 'class' => 'form-control']) !!} @if ($errors->has('email')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                </span> @endif
            </div>
            <div class="col-sm-6" id="">
                <label>Fax :</label> {!! Form::text('fax', $WebmasterSetting->fax, ['id' => 'fax', 'class' => 'form-control']) !!} @if ($errors->has('fax')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('fax') }}</span>
                </span> @endif
            </div>
        </div>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">            
            <div class="col-sm-6" id="">
                    <label>Address [EN]:</label>
                    <textarea rows="3" name="address" class="form-control">{{$WebmasterSetting->address}}</textarea>
            </div>         
            <div class="col-sm-6" id="">
                    <label>Address [FR]:</label>
                    <textarea rows="3" name="address_fr" class="form-control">{{$WebmasterSetting->address_fr}}</textarea>
            </div>
           
        </div>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">            
            <div class="col-sm-6" id="">
                    <label>Map URL:</label>
                    <input type="text" name="map_url" class= "form-control" value="{{$WebmasterSetting->map_url}}">
            </div>         
           
        </div>
    </div>
   
    </div>
    <script type="text/javascript">
        $('#map_distance').on("input", function() {
            this.value = this.value.replace(/[^0-9\.]/g, '');
            $(this).val($(this).val().replace(/^\s+/g, ''));
        });
        function isNumberBlock(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            // alert($(this).val())
            // evt.which.val().length
            if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                // alert()
                return false;
            return true;
        }
    </script>
{{-- <div class="p-a-md col-md-12">
						<div class="row">
							<div class="col-sm-4" id="instagram_link">
								<label>Youtube Link</label>
            {!! Form::text('youtube_link',env("YOUTUBE_LINK"), array('id' => 'youtube_link','class' => 'form-control', 'dir'=>'ltr')) !!}
        
							</div>
						</div>
					</div> --}}
</div>