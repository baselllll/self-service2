<!DOCTYPE html>
<html lang="en">

@include('includes._header')

<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>



    @include('includes._navbar')

    <div class="container">
        <br>
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.login')</h6>
        </div>
        <br>
        <div class="container" id="login_container">
            <div class="row">
                <div class="col-md-6">
                    <div class="signin-image text-center" style="margin-top: 52px;">
                        <img style="max-width: 250px;" src="{{asset("img/ajmi.png")}}" alt="sing up image">
                    </div>
                </div>
                <div class="col-md-6">
                    <br>
                    <div class="wow fadeInUp" data-wow-delay="0.1s">
                        <form action="{{route('auth-login')}}" method="post">
                            @csrf
                            <div class="row g-4" style="margin-top: 36px;">
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-8">
                                        <div class="form-floating">
                                            <input type="number" min="0"  required  class="form-control" style="width: 100%;" name="employee_number" id="employee_number" placeholder="@lang('messages.employee_number')">
                                            <label for="employee_number">@lang('messages.employee_number')</label>
                                            <button class="btn btn-primary btn-sm" style="float: right;    background: orange;
    color: white;" id="verification_success_btn_click">@lang('messages.otp_btn')</button>
                                            <span style="font-size: 12px; color:red; font-weight: bold;" id="verification_success_message"></span>
                                        </div>

                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-8" style="margin-top:10px">
                                        <div class="form-floating">
                                            <input required type="number" min="0" class="form-control" style="width: 100%;" name="verification_code" id="verification_code" placeholder="@lang('messages.Send_Code')">
                                            <label for="Verification_Code">@lang('messages.Send_Code')</label>
                                            <br/>
                                            <span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer"></span>
                                        </div>
                                    </div>
                                    <div class="col-2"></div>

                                </div>

                                <div class="row">
                                    <div class="col-4"></div>
                                    <div class="col-6" style="margin-left: 44px;">
                                        <button style="width: 98px;height: 34px;font-size: 19px;" class="btn btn-primary btn-sm" type="submit">@lang('messages.login_click')</button>
                                        <br/>
                                        <br/>
                                    </div>
                                    <div class="col-2"></div>
                                </div>

                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-8 mb-3" style="margin-top: -24px;text-decoration: underline;font-size: 14px;font-weight: bold;">
                                        <a href="{{route("update-user-view")}}" style="    color: black;" target="_blank">@lang("messages.register_data")</a>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('includes._footer')

</body>

<script>
    $(document).ready(function() {
        $('#verification_success_btn_click').click(async function() {
           $("#verification_success_btn_click").hide();
            var emp_number = $('#employee_number').val();

            try {
                var response = await $.ajax({
                    url: "{{ route('send-otp') }}",
                    method: 'POST',
                    data: {
                        emp_number: emp_number,
                        _token: "{{ csrf_token() }}"
                    }
                });

                if (response.results) {
                    $('#verification_success_message').html(response.results);
                    if (response.results == "Please Check Mail" || response.results == "Please Check Phone" || response.results == "Please Check Phone and Mail" || response.results =="يرجى التحقق من الجوال والايميل" || response.results == "Please Check Mail Only") {
                        if (emp_number) {
                            // Start the countdown
                            var countdownTime = 120; // 2 minutes in seconds
                            var countdownInterval = setInterval(updateCountdown, 1000);

                            function updateCountdown() {
                                var minutes = Math.floor(countdownTime / 60);
                                var seconds = countdownTime % 60;
                                $('#countdown_timer').text(minutes + " minutes " + seconds + " seconds");

                                countdownTime--;

                                if (countdownTime < 0) {
                                    clearInterval(countdownInterval);
                                    $('#countdown_timer').text("@lang('messages.Countdown finished')");
                                }
                            }
                            $('#verification_success_btn_click').hide()

                        }
                    }else {
                        console.log(response.hide_button)
                        $('#verification_success_btn_click').hide()
                    }
                } else {
                    $('#verification_success_message').html("@lang('messages.alert_check_employee')");
                }
                $('#verification_success_btn_click').hide()
            } catch (error) {
                // Handle error here
                console.error(error);
            }
        });

    });
</script>



</html>
