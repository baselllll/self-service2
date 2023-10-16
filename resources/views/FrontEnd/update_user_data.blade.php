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
    <!-- Spinner End -->


    @include('includes._navbar')

        <div class="container">
            <br/>
            <br/>
            <br/>
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.UpdateUser') </h6>
            </div>
            <br>
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s"></div>
            <br>

            <div class="row">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s"></div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <form action="{{route("update-user")}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input required type="number" min="0" class="form-control" name="emp_number"  id="emp_number_input" placeholder="Subject">
                                    <label for="emp_number">@lang('messages.employee_number')</label>
                                </div>
                                <br/>

                                <div class="form-floating">
                                    <input min="0" required type="number" class="form-control" name="iqama_number" id="iqama_number_input" placeholder="2xxxxxxxxx">
                                    <label for="iqama_number">@lang('messages.iqama_number')</label>
                                    <span style="font-weight: bold;font-size: 12px;color: darkorange;" class="validation-message" id="iqama-validation"></span>
                                </div>
                                <br/>
                                <span style="font-weight: bold;font-size: 14px;color: red;" class="out_verifed_iqama" id="out_verifed_iqama"></span>

                                <div id="block_phone_number" class="form-floating d-none" >
                                    <div class="d-inline-flex form-floating" style="width: 351px;">
                                        <input id="phone_number_input" style="padding-top: 52px;" min="0" required type="number" class="form-control" name="phone_number" >
                                        <button type="button" style="width: 93px; height: 35px;margin-top: 10px;" class="btn btn-sm btn-primary" id="otp_btn_sms" onclick="sendOtpBtnCheck()">@lang("messages.otp_btn")</button>

                                    </div>

                                    <label for="phone_number">@lang('messages.phone_number') (05xxxxxxxxx)</label>
                                    <span  style="font-weight: bold;font-size: 12px;color: darkorange;" class="validation-message" id="phone-validation"></span>
                                    <div class="form-group">
                                        <span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer_check"></span>
                                        <div class="d-inline-flex " style="width: 428px;">
                                            <input type="text" style="width: 135px; margin-top: 2px" required class="form-control" id="otp" placeholder="SMS OTP">
                                            <button type="button" style="width: 52px;height: 26px;margin-top: 6px;" class="btn btn-sm btn-warning" id="otp_btn_sms_check" onclick="checkOTP()">check</button>
                                        </div>
                                    </div>

                                </div>
                                <br/>
                                <div id="email_personnal_block"  class="form-floating d-none">
                                    <div class="d-inline-flex" style="width: 354px;">
                                        <input  required id="email_personnal"  type="email" class="form-control" style="padding-top: 37px;" name="email_employee">
                                        <button id="send_btn_email_check" type="button" style="width: 93px;height: 35px;margin-top: 10px;" class="btn btn-primary btn-sm" onclick="sendEmailBtnCheck()">@lang("messages.otp_btn")</button>
                                    </div>

                                    <label for="personnal_email">@lang('messages.personnal_email')</label>
                                    <span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer_check_email"></span>
                                    <span style="font-weight: bold;font-size: 12px;color: red;" id="email_verification_message_main" class="verification-message"></span>

                                </div>
                                <div class="d-inline-flex d-none otp_email_fieled" style="    margin-top: 4px;">
                                    <input style="width: 135px;" type="number" min="0" required class="form-control" id="otp_email_value" placeholder="Mail OTP">
                                    <button type="button" style="width: 52px;height: 26px;margin-top: 6px;" class="btn btn-sm btn-warning" id="otp_btn_mail_check" onclick="checkEmail()">check</button>

                                </div>
                                <br/>
                                <br/>
                                <div class="row g-2">
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <button id="submit_btn_main" class="btn btn-primary w-100 h-100" type="submit">@lang('messages.Submit_user')</button>
                                    </div>
                                    <div class="col-4"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div  class="form-floating">
                        <button   id="btn_send_code" class="btn btn-primary btn-sm d-none" style="float: right;background: orange;color: white;margin-top: -150px;margin-bottom: 10px;margin-right: -1px;" id="verification_success_btn_click">@lang('messages.otp_btn')</button>
                    </div>

                </div>
                <div class="col-lg-4 col-md-12 wow fadeInUp" data-wow-delay="0.5s"></div>
            </div>
        </div>




  @include('includes._footer')
    <!-- Modal -->
    <div class="modal fade" id="otpModal888" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel">@lang("messages.checkPhoneNumber")</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label class="bg bg-warning"  for="phone_number">(05xxxxxxxxx)</label>
                            <input type="number" required min="0" value="" maxlength="10" class="form-control" id="phoneNumber" placeholder="@lang("messages.phone_number")">
                            <div>
                                <label><span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer_check"></span></label>
                                <span style="font-weight: bold;font-size: 12px;color: red;" id="phone_verification_message" class="verification-message"></span>

                                <button id="send_btn_otp_check" type="button" style="float: right" class="btn btn-warning btn-sm" onclick="sendOtpBtnCheck()">@lang("messages.otp_btn")</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" required class="form-control" id="otp" placeholder="Enter OTP">
                        </div>
                        <br/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="checkOTP()">@lang("messages.Submit_user")</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="EmailModal999" tabindex="-1" role="dialog" aria-labelledby="EmailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EmailModalLabel">@lang("messages.email_employee")</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input type="email" required class="form-control" id="email_employee_verified" placeholder="Enter Personnel Email">
                        </div>
                        <div>
                            <label><span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer_check_email"></span></label>
                            <span style="font-weight: bold;font-size: 12px;color: red;" id="email_verification_message" class="verification-message"></span>

                            <button id="send_btn_email_check" type="button" style="float: right" class="btn btn-warning btn-sm" onclick="sendEmailBtnCheck()">@lang("messages.otp_btn")</button>
                        </div>
                        <div class="form-group">
                            <input type="text" required class="form-control" id="otp_email" placeholder="Enter OTP">
                        </div>
                        <br/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submit_email_btn" onclick="checkEmail()">@lang("messages.Submit_user")</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#email_employee_verified').keyup(function(event) {
                const input = event.target;
                const emailRegex = /^(?!.*ajmi)[a-zA-Z0-9._%+-]+@(?!ajmi\.com|alajmicompany\.com)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if (!emailRegex.test(input.value)) {
                    $("#send_btn_email_check").hide()
                    $("#submit_email_btn").hide()
                    $('#email_verification_message').text("@lang('messages.personnal_validate')");
                } else {
                    $("#submit_email_btn").show()
                    $("#send_btn_email_check").show()
                    $('#email_verification_message').text('');
                }
            });
            $('#email_personnal').keyup(function(event) {
                const input = event.target;
                const emailRegex = /^(?!.*ajmi)[a-zA-Z0-9._%+-]+@(?!ajmi\.com|alajmicompany\.com)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if (!emailRegex.test(input.value)) {

                    $('#email_verification_message_main').text("@lang('messages.personnal_validate')");
                    $("#send_btn_email_check").hide()
                } else {
                    $("#submit_btn_main").show()
                    $("#send_btn_email_check").show()
                    $('#email_verification_message_main').text('');
                }
            });

        });
    </script>
    <script>

        $(document).ready(function() {
            var iqamaInput = document.querySelector('input[name="iqama_number"]');
            var phoneInput = document.querySelector('input[name="phone_number"]');
            var submitButton = document.querySelector('button[type="submit"]');
            var iqamaValidation = document.getElementById("iqama-validation");
            var phoneValidation = document.getElementById("phone-validation");

            submitButton.addEventListener("click", function(event) {
                if (!validatePhoneNumber() || !validateIqamaNumber()) {
                    event.preventDefault();
                }
            });

            function validateIqamaNumber() {
                const iqamaPattern = /^[0-9]{10}$/;
                const iqamaValue = iqamaInput.value;

                if (!iqamaPattern.test(iqamaValue)) {
                    iqamaValidation.innerHTML = "{{__('messages.iqam_validation_message')}}";
                    return false;
                }

                iqamaValidation.innerHTML = "";
                return true;
            }

            function validatePhoneNumber() {
                const phonePattern = /^05[0-9]{8}$/;
                const phoneValue = phoneInput.value;

                if (!phonePattern.test(phoneValue)) {
                    phoneValidation.innerHTML = "{{__('messages.phone_validation_message')}}";
                    return false;
                }

                phoneValidation.innerHTML = "";
                return true;
            }
        });



    </script>
    <script>
        $(document).ready(function() {

            $('#iqama_number_input').keyup(function() {
                const iqamaInput = $('input[name="iqama_number"]').val();
                const employeeNumber = $('input[name="emp_number"]').val();
                if (iqamaInput.length === 10 && employeeNumber !== null) {
                    $.ajax({
                        url: "{{ route('emp_quama_verified') }}",
                        method: 'POST',
                        data: {
                            iqama_number_input: iqamaInput,
                            emp_number_input: employeeNumber,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {

                            if(response.registered_before == "0"){
                                if (response.verified === 1) {
                                    $('#out_verifed_iqama').text(response.results).css('color', 'green');
                                    $('#block_phone_number').removeClass('d-none').addClass('d-block');
                                    $('#email_personnal_block').removeClass('d-none').addClass('d-block');
                                    $('.otp_email_fieled').removeClass('d-none').addClass('d-block');
                                    $('#emp_number_input').prop('readonly', true);
                                    $('#iqama_number_input').prop('readonly', true);

                                }
                                if (response.verified === 0) {
                                    $('#out_verifed_iqama').text(response.results).css('color', 'red');
                                }
                            }
                            else if(response.registered_before == "1"){
                                alert("{{__('messages.register_before')}}");
                                window.location.href = "/public/index.php/login";
                            }
                        }
                    });
                }else if (iqamaInput.length > 10 && employeeNumber !== null) {
                    var iqamaValidation = document.getElementById("iqama-validation");
                    iqamaValidation.innerHTML = "{{__('messages.iqam_validation_message')}}";
                    $('#out_verifed_iqama').text("{{__('messages.out_not_verifed_iqama')}}").css('color', 'red');
                    $('#block_phone_number').removeClass('d-block').addClass('d-none');
                    $('#block_send_code').removeClass('d-block').addClass('d-none');
                    $('#btn_send_code').removeClass('d-block').addClass('d-none');
                    $('#email_personnal_block').removeClass('d-block').addClass('d-none');
                }
            });
            $('#btn_send_code').click(async function() {
                $('#btn_send_code').removeClass('d-block').addClass('d-none');
                const phone_number = $('input[name="phone_number"]').val();
                const employee_number = $('input[name="emp_number"]').val();
                const personal_email = $('input[name="email_employee"]').val();
                if (phone_number !== "") {
                    try {
                        const response = await $.ajax({
                            url: "{{ route('send_otp_for_register') }}",
                            method: 'POST',
                            data: {
                                phone_number: phone_number,
                                email: personal_email,
                                employee_number: employee_number,
                                _token: "{{ csrf_token() }}"
                            }
                        });

                        if (response.verified === 1) {
                            var countdownTime = 120; // 4 minutes in seconds
                            var countdownInterval = setInterval(updateCountdown, 1000);

                            function updateCountdown() {
                                var minutes = Math.floor(countdownTime / 60);
                                var seconds = countdownTime % 60;
                                $('#countdown_timer').text(minutes + " minutes " + seconds + " seconds");

                                countdownTime--;
                                $('#btn_send_code').removeClass('d-block').addClass('d-none');
                                if (countdownTime < 0) {
                                    clearInterval(countdownInterval);
                                    $('#countdown_timer').text("@lang('messages.Countdown finished')");
                                    $('#btn_send_code').removeClass('d-none').addClass('d-block');
                                }
                            }
                        } else {
                            $('#countdown_timer').text("@lang('messages.otp_already_sent')");
                            $('#btn_send_code').removeClass('d-block').addClass('d-none');
                        }
                    } catch (error) {
                        // Handle error here
                        console.error(error);
                    }
                }
            });

            $('#phone_number_input').keyup(function() {
                const phone_number_input = $('input[name="phone_number"]').val();
                const phone_validation = $('#phone-validation');

                if (phone_number_input.length === 10) {
                    phone_validation.html(""); // Clear the validation message
                } else if (phone_number_input.length > 10) {
                    phone_validation.html("{{__('messages.phone_validation_message')}}");
                }
            });


        });

    </script>
    <script>
        var phoneNumber_after_verified = document.getElementById("phoneNumber").value;
        var email_employee_after_verified = document.getElementById("email_employee_verified").value;
        async function checkOTP() {
            var otp = document.getElementById("otp").value;
            if(otp) {
                    const employeeNumber = $('input[name="emp_number"]').val();
                    var otp = document.getElementById("otp").value;
                    const response = await $.ajax({
                        url: "{{ route('send_otp_for_check_otp') }}",
                        method: 'POST',
                        data: {
                            otp: otp,
                            employee_number: employeeNumber,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                    if(response.verified==1){
                        $('#email_personnal_block').removeClass('d-none').addClass('d-block');
                        $('#otp').hide();
                        $("#otp_btn_sms_check").hide();
                        $("#countdown_timer_check").hide();
                        alert("success verified phone number")
                    }else{
                        alert("failed verified phone number")
                    }
                }
        }
        async function sendOtpBtnCheck() {
            var phoneNumber_input_main = document.getElementById("phone_number_input").value;
            $('#send_btn_otp_check').hide();
            if(phoneNumber_input_main.length == 10){
                phoneNumber_after_verified = phoneNumber_input_main
                const employeeNumber = $('input[name="emp_number"]').val();


                const response = await $.ajax({
                    url: "{{ route('send_otp_for_check_before') }}",
                    method: 'POST',
                    data: {
                        phone_number: phoneNumber_input_main,
                        employee_number: employeeNumber,
                        _token: "{{ csrf_token() }}"
                    }
                });
                if (response.sended == 1) {
                    var countdownTime = 120; // 4 minutes in seconds
                    var countdownInterval = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        var minutes = Math.floor(countdownTime / 60);
                        var seconds = countdownTime % 60;
                        $('#countdown_timer_check').text(minutes + " minutes " + seconds + " seconds");
                        countdownTime--;
                        $('#otp_btn_sms').hide();
                        $('#phone_number_input').prop('readonly', true);


                        if (countdownTime < 0) {
                            clearInterval(countdownInterval);
                            $('#countdown_timer_check').text("@lang('messages.Countdown finished')");
                            $('#otp_btn_sms').show();
                            $('#phone_number_input').prop('readonly', false);
                        }
                    }
                }
            }else{
                console.log(phoneNumber_input_main.length);
                $("#phone_number_input").focus();
            }

        }
        async function sendEmailBtnCheck() {
            var email_employee = document.getElementById("email_personnal").value;
            email_employee_after_verified = email_employee
            var employeeNumber = $('input[name="emp_number"]').val();
            $('#send_btn_email_check').hide();

            const response = await $.ajax({
                url: "{{ route('send_email_for_check_before') }}",
                method: 'POST',
                data: {
                    email_employee: email_employee,
                    employee_number: employeeNumber,
                    _token: "{{ csrf_token() }}"
                }
            });
            if (response.sended == 1) {
                var countdownTime = 120; // 4 minutes in seconds
                var countdownInterval = setInterval(updateCountdown, 1000);
                $('#otp_email').focus();
                $('#otp_email').css('border-color', '#ff9900');

                function updateCountdown() {
                    var minutes = Math.floor(countdownTime / 60);
                    var seconds = countdownTime % 60;
                    $('#countdown_timer_check_email').text(minutes + " minutes " + seconds + " seconds");
                    countdownTime--;
                    if (countdownTime < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown_timer_check_email').text("@lang('messages.Countdown finished')");
                        $('#send_btn_email_check').show();
                        $('#email_personnal').prop('readonly', false);
                    }
                }
            }


        }

        async function checkEmail(){
            const employeeNumber = $('input[name="emp_number"]').val();
            var otp = document.getElementById("otp_email_value").value;

            const response = await $.ajax({
                url: "{{ route('send_otp_email_for_check_otp') }}",
                method: 'POST',
                data: {
                    otp: otp,
                    employee_number: employeeNumber,
                    _token: "{{ csrf_token() }}"
                }
            });
            console.log(response)
            if(response.verified==1){
                console.log(email_employee_after_verified)
                alert("success verified Email follow enter data again")
                $('#countdown_timer_check_email').hide();
                $('#otp_email_value').hide();
                $('#otp_btn_mail_check').hide();
                $('#email_personnal').prop('readonly', true);
            }else{
                alert("failed verified Email")
            }
        }
    </script>

</body>

<script>
    var today = new Date();
    document.getElementById('out-date').innerText  = today;
</script>
</html>
