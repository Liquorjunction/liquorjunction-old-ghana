

    
    <div class="tab-pane {{ Session::get('active_tab') === 'maintenance' ? 'active' : '' }}" id="tab-12">
        <div class="p-a-sm">
            <h5>Website Maintenance</h5>
        </div>
        <div class="p-a-sm col-md-12">
            <div class="row">
                <div class="col-sm-6 mb-2">
                    <label>Put site under maintenance?</label><br>
                    <label>
                        <input type="radio" name="maintenance" value="yes"
                            {{ isset($WebmasterSetting->site_maintenance) && $WebmasterSetting->site_maintenance == 1 ? 'checked' : '' }}>
                        Yes
                    </label>
                    <label class="ms-3" style="margin-left: 15px;">
                        <input type="radio" name="maintenance" value="no"
                            {{ isset($WebmasterSetting->site_maintenance) && $WebmasterSetting->site_maintenance == 0 ? 'checked' : '' }}>
                        No
                    </label>
                </div>
            </div>
        </div>
    </div>
    