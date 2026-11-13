        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.EditTimeFrame') }}</h4>
        </div>
        <form class="cmxform" id="timeframeEditForm" method="post" action="" autocomplete="off">
        <div class="modal-body">
            
           <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!!  __('backend.name') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name"  value="{{@$timeframeData->name}}">
                            <span class="help-block" id="errorMessage" style="display:none">
                                <span  style="color: red;display: none;" id="errorMsg" class='validate'></span>
                            </span>
                        </div>
                    </div> 
        </div>
        <div class="modal-footer">
            <input type="hidden" name="time_frame_id" id="time_frame_id" value="{{@$timeframeData->id}}">
          <button type="submit" class="btn btn-default btn btn-primary">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {{Form::close()}}
      <!-- </div> -->

<script type="text/javascript">
   
    $(document).ready(function () {
 
            $("#timeframeEditForm").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    name: "required",
                    
                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    name: " Name field is required",
                   
                },
                submitHandler: function(){
                    var form_data = new FormData($('#timeframeEditForm')[0]);
                    action_url = "{{ route('time_frame.store') }}";
                    var csrf = "{{ csrf_token() }}";
                    $.ajax({
                            url: action_url,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            processData: false,
                            contentType: false,
                            type: "POST",
                            dataType: 'json',
                             beforeSend: function(){
                                    // $(".loader").fadeIn();
                                    // $('.loader').css("visibility", "visible");
                                },
                            success: function(data){
                                // $(el).parents('.cart-product-box-content').find('b[name=price]').text(fix_price*text);
                               // return false;
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('time_frame')}}";
                                }
                            },
                            error: function (errors) {
                                   // alert(errors);
                                    $('.loader').css("visibility", "hidden");
                                 var erroJson = JSON.parse(errors.responseText);
                                 console.log(erroJson.title);
                                   for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                                  $("span#errorMessage").css("display", "block");
                              $("span#errorMsg").css("display", "block");

                              $("span#errorMsg").html(errstr);
                              }
                               }
                            
                        });
                }
            });
        });
</script>

