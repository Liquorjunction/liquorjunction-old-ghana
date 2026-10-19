<!DOCTYPE html>
<html>

<head>
  @include('dashboard.layouts.head')
</head>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
    const orderBaseUrl = "{{ url('admin/adminorder') }}"; // Blade evaluates this once
    const orderAcceptBaseUrl = "{{ url('admin/adminorder') }}"; // Blade evaluates this once
    document.addEventListener("DOMContentLoaded", function() {
        const audio = document.getElementById("orderSound");
        const enableBtn = document.getElementById("enableSoundBtn");
        const toastContainer = document.getElementById("toastContainer");

        let soundEnabled = localStorage.getItem("soundEnabled") === "true";

        // 🔊 Try autoplay once
        if (!soundEnabled) {
            audio.play().then(() => {
                console.log("✅ Autoplay worked");
                audio.pause();
                audio.currentTime = 0;
                soundEnabled = true;
                localStorage.setItem("soundEnabled", "true");
            }).catch(() => {
                console.log("🔇 Autoplay blocked, showing Enable Sound button");
                enableBtn.style.display = "inline-block";
            });
        }

        enableBtn.addEventListener("click", () => {
                console.log("✅ Sound unlocked");
                audio.pause();
                audio.currentTime = 0;
                enableBtn.style.display = "none";
                soundEnabled = true;
                localStorage.setItem("soundEnabled", "true");
            });

        // 📡 Pusher
        var pusher = new Pusher('ee6976119bdc3161ea8d', {
            cluster: 'mt1'
        });
        var channel = pusher.subscribe('univercity');

        channel.bind('notice', function(event) {
            let data = event.data || {};
            console.log('🎉 Event received:', data);

            // Create new toast
            const toast = document.createElement("div");
            toast.style.cssText = `
            background:#222; color:#fff; padding:12px; border-radius:8px;
            width:300px; box-shadow:0 2px 8px rgba(0,0,0,0.3);
            animation: fadeIn 0.3s ease-in-out;
        `;
            toast.innerHTML = `
            <div><b>🔔 New Order Received!</b></div>
            <div style="margin-top:6px; font-size:14px; color:#ddd;">
                <strong>User:</strong> ${data.user_name ?? 'N/A'} <br>
                <strong>Order ID:</strong> ${data.order_number ?? 'N/A'} <br>
                <strong>Order Type:</strong> ${data.order_type ?? 'N/A'} <br>
                <strong>Total:</strong> GH₵${data.total_amount ?? 0}
            </div>
            <div style="margin-top:10px; display:flex; justify-content:space-between;">
                <button class="acceptBtn">✅ Accept</button>
                <a href="${orderBaseUrl}/${data.id}/show" class="closeBtn" 
                  style="background:#555; color:white; border:none; 
                          padding:6px 12px; border-radius:5px; cursor:pointer; text-decoration:none; display:inline-block;">
                    ✅ Open
                </a>
            </div>
        `;

            toastContainer.prepend(toast); // New toast at top

            // Buttons inside this toast
            //const stopBtn = toast.querySelector(".stopBtn");
            const closeBtn = toast.querySelector(".closeBtn");

            const acceptBtn = toast.querySelector(".acceptBtn");
            acceptBtn.addEventListener("click", () => {
                updateOrderStatus(
                    data.id, // make sure you send order_id in your Pusher payload
                    2, // example: status 2 = Confirmed
                    '',
                    '',
                    '',
                    ''
                );
                audio.pause();
                toast.remove();
            });

            closeBtn.addEventListener("click", () => {
                audio.pause();
                toast.remove();
            });

            // Play sound
            if (soundEnabled) {
                audio.pause();
                audio.currentTime = 0;
                audio.play().catch(() => {
                    console.log("⚠️ Sound blocked");
                });
            }
        });
    });

    function updateOrderStatus(order_id, order_status, note, mobile_number, phone_code, user_id) {
        let customer_mobile_number = "+" + phone_code + mobile_number;

        if (order_id && order_status) {
            $.ajax({
                url: "{{ route('adminorder.updateOrderStatus') }}",
                type: 'POST',
                data: {
                    order_id: order_id,
                    order_status: order_status,
                    note: note
                },
                success: function(result) {
                    $('#updateOrderStatus').modal('hide');
                    $('#order_type_status').trigger('change');

                    if (result.success == true) {
                        $('#success_file_popup').append(messages('alert-success', result.msg));
                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);

                        if ([2, 3, 5, 6].includes(parseInt(order_status))) {
                            let message = '';
                            if (order_status == 2) message = 'Order is Confirmed';
                            else if (order_status == 5) message = 'Order is Ready to Pick Up';
                            else if (order_status == 6) message = 'Order is Out Of Delivery';
                            else message = 'Order is Delivered';

                            $.ajax({
                                url: "{{ route('send-sms') }}",
                                type: 'POST',
                                data: {
                                    mobile_number: mobile_number,
                                    message: message
                                }
                            });
                        }
                    } else {
                        $('#success_file_popup').append(messages('alert-danger', result.msg));
                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);
                    }
                }
            });
        } else {
            $("#updateOrderStatus").modal('hide');
            alert('Something went wrong!!!');
            location.reload();
        }
    }
</script>

<body>
    <audio id="orderSound" src="/sounds/buzzer.mp3" preload="auto"></audio>
    <div id="toastContainer"
        style="position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px;"></div>

    <div class="app" id="app">
        @include('dashboard.layouts.menu')

        <div id="content" class="app-content box-shadow-z0" role="main">
            @include('dashboard.layouts.header')
            @include('dashboard.layouts.footer')
            <div ui-view class="app-body" id="view">
                <div class="success_message" style="margin-bottom: 10px;"></div>
                <div id="success_file_popup" style="margin-bottom: 10px;"></div>
                <!-- Enable sound button -->
                <button id="enableSoundBtn"
                    style="display:none; background:#28a745; color:white; border:none;
                  padding:6px 12px; border-radius:5px; cursor:pointer; margin:10px;">
                    🔊 Enable Sound
                </button>
                {{-- @include('dashboard.layouts.errors') --}}
                @if (Session::has('doneMessage'))
                    <div class="padding p-b-0" id="topmsgall">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-success m-b-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    {{ Session::get('doneMessage') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (Session::has('errorMessage'))
                    <div class="padding p-b-0" id="topmsgall">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-danger m-b-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    {{ Session::get('errorMessage') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>

        @include('dashboard.layouts.settings')
    </div>
    @include('dashboard.layouts.foot')
    <div class="modal fade" id="alert_confirm" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                </div>
                <div class="modal-body">
                    <p class="alert_dynamic_message">
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="default_confirm" tabindex="-1" data-backdrop="static"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                </div>
                <div class="modal-body p-lg">
                    <p class="dynamic_message">
                        Are you sure ?
                    </p>
                    <input type="hidden" name="checkbox_data" class="checkbox_data">
                    <input type="hidden" name="checkbox_type" class="checkbox_type">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger yes_click">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancel_confirm" tabindex="-1" data-backdrop="static"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                </div>
                <div class="modal-body p-lg">
                    <p class="dynamic_message">
                        Are you sure ?
                    </p>
                    <input type="hidden" name="checkbox_data" class="checkbox_data">
                    <input type="hidden" name="checkbox_type" class="checkbox_type">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger yes_cancel">Yes</button>
                </div>
            </div>
        </div>
    </div>

</body>
<script type="text/javascript">
  const public_folder_path = "";
  const public_lang = "";
  const first_day_of_week = "Monday";
   function checkChange() {
         var totalCheckbox = document.querySelectorAll('input[name="ids[]"]').length;
         var totalChecked = document.querySelectorAll('input[name="ids[]"]:checked').length;
        if (totalCheckbox == totalChecked) {
           $('#checkAll').not(this).prop('checked', true);
        } else {
          $('#checkAll').not(this).prop('checked', false);
        }
}
</script>
</html>