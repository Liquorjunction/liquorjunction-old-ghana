<div class="tab-pane {{ Session::get('active_tab') == 'languageSettingsTab' || Session::get('active_tab') == '' ? 'active' : '' }}" id="tab-2">
    <div class="p-a-sm">
        <h5>{!! __('backend.languageSettings') !!}</h5>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">
            {{-- 
                <div class="col-sm-6">
				<div class="form-group">
					<label>{{ __('backend.defaultLanguage') }} : </label>
                    <div>
                        <select name="languages_by_default" class="form-control c-select"> @foreach (Helper::languagesList() as $ActiveLanguage) @if ($ActiveLanguage->box_status) <option value="{{ $ActiveLanguage->code }}" {{ ($WebmasterSetting->languages_by_default==$ActiveLanguage->code)?"selected='selected'":"" }}>{{ $ActiveLanguage->title }}</option> @endif @endforeach </select>
                    </div>
                </div>
                </div> 
            --}} 
            {{-- 
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Gap of Package In Days </label>
                        {!! Form::number('gap_of_package',isset($WebmasterSetting->gap_of_package) ? $WebmasterSetting->gap_of_package : '', array('placeholder' => 'Gap of Package In Days','class' => 'form-control','id'=>'Gap of Package In Days')) !!}
                    
                    
                    </div>
                </div> 
            --}}
            <div class="col-sm-4">
                <label>{{ __('backend.dateFormat') }} </label>
                <select name="date_format" class="form-control c-select">
                    <option value="Y-m-d" {{ env('DATE_FORMAT', 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>Y-m-d</option>
                    <option value="d-m-Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'd-m-Y' ? 'selected' : '' }}>d-m-Y</option>
                    <option value="m-d-Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'm-d-Y' ? 'selected' : '' }}>m-d-Y</option>
                    <option value="d/m/Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>d/m/Y</option>
                    <option value="m/d/Y" {{ env('DATE_FORMAT', 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>m/d/Y</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="email">Currency Symbol </label> {!! Form::text('currency_symbol', $WebmasterSetting->currency_symbol, [ 'placeholder' => '', 'class' => 'form-control', 'id' => 'currency_sybmol', ]) !!}
            </div>
            <div class="col-sm-4" id="">
                <label>Tax (%) :</label> {!! Form::text('tax', $WebmasterSetting->tax, ['id' => 'tax', 'class' => 'form-control','maxlength'=>'2', 'dir' => 'ltr']) !!} @if ($errors->has('tax')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('tax') }}</span>
                </span> @endif
            </div>            
            {{-- <div class="col-sm-4">
                <label for="email">Commission (%) :
                </label>
                {!! Form::text('commission_in_per',$WebmasterSetting->commission_in_per, array('placeholder' => '','class' => 'form-control','id'=>'commission_in_per', )) !!}
            
            </div> --}}
        </div>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">
             <div class="col-sm-4" id="">
                <label>Purchase Amount :</label> {!! Form::text('delivery_amount', $WebmasterSetting->delivery_amount, [ 'id' => 'delivery_amount', 'class' => 'form-control', 'dir' => 'ltr','maxlength'=>'4', 'onkeypress'=>"return isNumberBlock(event)"]) !!} @if ($errors->has('delivery_amount')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('delivery_amount') }}</span>
                </span> @endif
            </div>
            <div class="col-sm-4" id="">
                <label>Gift Amount</label> {!! Form::text('delivery_fee', $WebmasterSetting->delivery_fee, [ 'id' => 'delivery_fee', 'class' => 'form-control', 'dir' => 'ltr','maxlength'=>'4', 'onkeypress'=>"return isNumberBlock(event)"]) !!} @if ($errors->has('delivery_fee')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('delivery_fee') }}</span>
                </span> @endif
            </div>
            <div class="col-sm-4" id="facebook_link">
                <label>Facebook Link :</label> {!! Form::text('facebook_link', $WebmasterSetting->facebook_link, [ 'id' => 'facebook_link', 'class' => 'form-control', 'dir' => 'ltr', ]) !!} @if ($errors->has('facebook_link')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('facebook_link') }}</span>
                </span> @endif
            </div>
            <div class="col-sm-4" id="instagram_link">
                <label>Instagram Link :</label> {!! Form::text('instagram_link', $WebmasterSetting->instagram_link, [ 'id' => 'instagram_link', 'class' => 'form-control', 'dir' => 'ltr', ]) !!} @if ($errors->has('instagram_link')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('instagram_link') }}</span>
                </span> @endif
            </div>
            
        </div>
    </div>
    <div class="p-a-sm col-md-12">
        <div class="row">            
            <div class="col-sm-4" id="twitter_link">
                <label>Linkedin Link :</label> {!! Form::text('twitter_link', $WebmasterSetting->twitter_link, [ 'id' => 'twitter_link', 'class' => 'form-control', 'dir' => 'ltr', ]) !!} @if ($errors->has('twitter_link')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('twitter_link') }}</span>
                </span> @endif
            </div>
            <div class="col-sm-4" id="youtube_link">
                <label>Youtube Link :</label> {!! Form::text('youtube_link', $WebmasterSetting->youtube_link, [ 'id' => 'youtube_link', 'class' => 'form-control', 'dir' => 'ltr', ]) !!} @if ($errors->has('youtube_link')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('youtube_link') }}</span>
                </span> @endif
            </div>
            <div class="col-sm-4" id="youtube_link">
                <label>Map Distance :</label> {!! Form::text('map_distance', $WebmasterSetting->map_distance, [ 'id' => 'map_distance', 'class' => 'form-control', 'dir' => 'ltr', ]) !!} @if ($errors->has('map_distance')) <span class="help-block">
                    <span style="color: red;" class='validate'>{{ $errors->first('map_distance') }}</span>
                </span> @endif
            </div>
        </div>
    </div>
    <div class="p-a-sm col-md-12 row">
        <div class="col-sm-4" id="site_title_ar">
            <div class="form-group">
                <label>Android Version</label> {!! Form::text( 'android_version', old('android_version', @$WebmasterSetting->android_version ? $WebmasterSetting->android_version : ''), ['id' => 'android_version', 'class' => 'form-control', 'dir' => 'ltr'], ) !!}
            </div>
        </div>
        <div class="col-sm-4" id="site_title_ar">
            <div class="form-group">
                <label>IOS Version</label> {!! Form::text( 'ios_version', old('ios_version', @$WebmasterSetting->ios_version ? $WebmasterSetting->ios_version : ''), ['id' => 'ios_version', 'class' => 'form-control', 'dir' => 'ltr'], ) !!}
            </div>
        </div>
        <div class="col-sm-4" id="site_title_ar">
            <div class="form-group">
                <label>Delivery Days</label> {!! Form::text( 'delivery_days', old('delivery_days', @$WebmasterSetting->delivery_days ? $WebmasterSetting->delivery_days : ''), ['id' => 'delivery_days', 'class' => 'form-control', 'dir' => 'ltr'], ) !!}
            </div>
        </div>
    </div>
    <div class="p-a-sm col-md-12 row ">
        <div class="col-sm-6" id="site_title_ar">
            <div class="form-group">
                <label>Is Android Version Update On:</label>
                <input type="radio" name="android_version_update" id="yes" value="1" class="from-control-label" style="margin-top:15px;" {{ $WebmasterSetting->android_version_update == '1' ? 'checked' : '' }}>
                <label>Yes</label>
                <input type="radio" name="android_version_update" id="no" value="2" class="from-control-label" style="margin-top:15px;" {{ $WebmasterSetting->android_version_update == '2' ? 'checked' : '' }}>
                <label>No</label>
            </div>
        </div>
        <div class="col-sm-6" id="site_title_ar">
            <div class="form-group">
                <label>Is IOS Version Update On:</label>
                <input type="radio" name="ios_version_update" id="yes" value="1" class="from-control-label" style="margin-top:15px;" {{ $WebmasterSetting->ios_version_update == '1' ? 'checked' : '' }}>
                <label>Yes</label>
                <input type="radio" name="ios_version_update" id="no" value="2" class="from-control-label" style="margin-top:15px;" {{ $WebmasterSetting->ios_version_update == '2' ? 'checked' : '' }}>
                <label>No</label>
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