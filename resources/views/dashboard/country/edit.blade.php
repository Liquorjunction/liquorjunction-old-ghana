        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ __('backend.EditSuffix') }}</h4>
      </div>
      <form class="cmxform" id="suffixEditForm" method="post" action="" autocomplete="off">
        <div class="modal-body">
         <div class="form-group row">
            <label class="col-sm-2 form-control-label">{!!  __('backend.title') !!} <span class="valid_field">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="suffix_title" id="edit_suffix_title" class="form-control" onkeypress="return isNumberKey(event)" placeholder="Enter suffix title"  value="{{@$suffixData->suffix_title}}">
                <span class="help-block" id="errorMessage" style="display:none">
                    <span  style="color: red;display: none;" id="errorMsg" class='validate'></span>
                </span>
            </div>
        </div> 
    </div>
    <div class="modal-footer">
        <input type="hidden" name="suffix_id" id="suffix_id" value="{{@$suffixData->id}}">
        <button type="submit" class="btn btn-default btn btn-primary">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
        {{Form::close()}}
<script type="text/javascript">
    document.getElementById('edit_suffix_title').addEventListener('keydown', function (event) {
    if (event.keyCode == 8) {
        $("span#errorMsg").css("display", "none");
    }    
});

    function isNumberKey(evt){ 
    //var e = evt || window.event;
    var keyCode = (evt.which) ? evt.which : evt.keyCode;
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
         
        return false;
            return true;
    
    }
    
    $(document).ready(function () {
            $("#suffixEditForm").validate({
                 rules: {
                    suffix_title: {
                        required : true,
                        maxlength : 15,
                    },
                   
                },
                messages: {
                    suffix_title: {
                        required : " Title field is required.",
                        maxlength : "Title should not be more than 15 characters."
                    },
                },
                submitHandler: function(){
                    var form_data = new FormData($('#suffixEditForm')[0]);
                    action_url = "{{ route('suffix.store') }}";
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
                                    $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function(data){
                                if (data.success) {
                                    $('.loader').css("visibility", "visible");
                                    window.location.href = "{{ route('suffix')}}";
                                }
                            },
                            error: function (errors) {
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

