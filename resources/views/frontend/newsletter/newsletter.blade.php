<style >
label.error{
    margin-top : 0px !important;
}
</style>
<!-- Newsletter -->
<section class="newsletter">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="newsletter-main">
                    <div class="newsletter-wrapper">
                        <h3>{{ \Helper::language('subscribe_our_newsletter');}}</h3>
                        <form action="" class="mb-0"id ="subscribe_form" >
                            <!-- <div class="subscription-form">
                                <div class="row">
                                    <div class="col-md-9 col-12">
                                        <input type="email" autocomplete="off" name="email" placeholder="{{ \Helper::language('enter_your_email_address_place');}}">
                                        <span style="color:red;" id="newslatter-error"></span>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <input type="submit" name="submit" value="{{ \Helper::language('subscribe');}}" class="solid-button">
                                    </div>
                                </div>
                            </div> -->
                            {{-- <div class="subscription-form">
                                <input type="email" autocomplete="off" name="email" placeholder="{{ \Helper::language('enter_your_email_address_place');}}">
                                <span style="color:red;" id="newslatter-error"></span>
                                <input type="submit" name="submit" value="{{ \Helper::language('subscribe');}}" class="solid-button">
                            </div>
                        </div> --> --}}
                        <div class="subscription-form">
                            <input type="email" autocomplete="off" id="sub_email" name="email" placeholder="{{ \Helper::language('enter_your_email_address_place');}}">
                            <span style="color:red;" id="newslatter-error"></span>
                            <input type="submit" name="submit" value="{{ \Helper::language('subscribe');}}" class="solid-button">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Newsletter -->
@push('after-scripts')
<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
<script>
     var validation_email_required="{{ \Helper::language('email_field_required'); }}";
     var validation_email="{{ \Helper::language('enter_valid_email_validation'); }}";

     $(document).ready(function() {
        // Regular expression for email validation
        var semailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        // Function to validate email
        function isValidEmail(semail) {
            return semailPattern.test(semail);        }
        // Form submission handler
        $('#subscribe_form').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting
            // Get the entered email address
            var semail = $("#sub_email").val();
            if(semail==''){
                $("#newslatter-error").text(validation_email_required);
                return false;
            }
            // Check if the email is valid
            if (isValidEmail(semail)) {
                $("#newslatter-error").text("");
                //alert('Email is valid!'); // You can replace this with your desired action
            } else {
                $("#newslatter-error").text(validation_email);
                return false;
            }

            var form_data = new FormData($('#subscribe_form')[0]);
            action_url = "{{route('subscribeemail')}}";
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
                beforeSend: function() {
                    $(".loader").fadeIn();
                    $('.loader').css("visibility", "visible");
                },
                success: function(response) {
                    $('.loader').css("visibility", "hidden");
                    var url = "{{route('frontend.home')}}";
                    window.location.href = url;
                },             
                error: function(errors) {
                    $('.loader').css("visibility", "hidden");
                    var errors = errors.responseJSON;
                    if (errors.email) { 
                        $("span#newslatter-error").text(errors.email[0]);
                    }
                }
            });
        });
    });
</script>
@endpush