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
                                    <input id="phone_number_input" min="0" required type="number" class="form-control" name="phone_number" placeholder="05xxxxxxxxx">
                                    <label for="phone_number">@lang('messages.phone_number') (05xxxxxxxxx)</label>
                                    <span  style="font-weight: bold;font-size: 12px;color: darkorange;" class="validation-message" id="phone-validation"></span>
                                </div>
                                <br/>
                                <div id="email_personnal_block"  class="form-floating d-none">
                                    <input id="email_personnal"  type="email" class="form-control" name="email_employee">
                                    <label for="personnal_email">@lang('messages.personnal_email')</label>
                                    <span style="font-weight: bold;font-size: 12px;color: red;" id="email_verification_message" class="verification-message"></span>
                                    <span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer"></span>
                                </div>
                                <br/>
{{--                                <div  id="block_send_code" class="form-floating d-none">--}}
{{--                                    <input required type="text" class="form-control" name="verification_code_number" placeholder="Subject">--}}
{{--                                    <label for="verification_code_number">@lang('messages.Send_Code')</label>--}}
{{--                                    <span style="font-weight: bold;font-size: 12px;color: red;" class="verification_code_number" id="verification_code_number"></span>--}}
{{--                                </div>--}}
                                <br/>
                                <div class="row g-2">
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <button class="btn btn-primary w-100 h-100" type="submit">@lang('messages.Submit_user')</button>
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
    <div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true">
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

    <div class="modal" id="EmailModal" tabindex="-1" role="dialog" aria-labelledby="EmailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EmailModalLabel">@lang("messages.email_employee")</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input type="email" required class="form-control" id="email_employee_verified" placeholder="Enter Email">
                        </div>
                        <div>
                            <label><span style="font-size: 12px; color: red; font-weight: bold;" id="countdown_timer_check_email"></span></label>

                            <button id="send_btn_email_check" type="button" style="float: right" class="btn btn-warning btn-sm" onclick="sendEmailBtnCheck()">@lang("messages.otp_btn")</button>
                        </div>
                        <div class="form-group">
                            <input type="text" required class="form-control" id="otp_email" placeholder="Enter OTP">
                        </div>
                        <br/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="checkEmail()">@lang("messages.Submit_user")</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#email_personnal').keyup(function(event) {
                const input = event.target;
                const emailRegex = /^[a-zA-Z0-9._%+-]+@(ajmi\.com|alajmicompany\.com)$/;

                if (emailRegex.test(input.value)) {
                    $('#email_verification_message').text("@lang('messages.personnal_validate')");
                } else {
                    $('#email_verification_message').text('');
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


                                    $('#otpModal').modal('show');
                                    // $('#email_personnal_block').removeClass('d-none').addClass('d-block');
                                    // $('#block_phone_number').removeClass('d-none').addClass('d-block');
                                    // $('#block_send_code').removeClass('d-none').addClass('d-block');
                                    // $('#btn_send_code').removeClass('d-none').addClass('d-block');
                                }
                                if (response.verified === 0) {
                                    $('#out_verifed_iqama').text(response.results).css('color', 'red');
                                    $('#block_phone_number').removeClass('d-block').addClass('d-none');
                                    $('#block_send_code').removeClass('d-block').addClass('d-none');
                                    $('#btn_send_code').removeClass('d-block').addClass('d-none');
                                    $('#email_personnal_block').removeClass('d-block').addClass('d-none');
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
                alert("success verified phone number follow enter data again")
                $('#email_personnal_block').removeClass('d-none').addClass('d-block');
                $('#block_phone_number').removeClass('d-none').addClass('d-block');
                $('#phone_number_input').val(phoneNumber_after_verified);
                $('#otpModal').modal('hide');
                $('#EmailModal').modal('show');


            }else{
                alert("failed verified phone number")
            }
        }
        async function sendOtpBtnCheck() {
            var phoneNumber = document.getElementById("phoneNumber").value;
            phoneNumber_after_verified = phoneNumber
            const employeeNumber = $('input[name="emp_number"]').val();
            $('#send_btn_otp_check').hide();

            const response = await $.ajax({
                url: "{{ route('send_otp_for_check_before') }}",
                method: 'POST',
                data: {
                    phone_number: phoneNumber,
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
                    if (countdownTime < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown_timer_check').text("@lang('messages.Countdown finished')");
                        $('#send_btn_otp_check').show();
                    }
                }
            }
        }
        async function sendEmailBtnCheck() {
            var email_employee = document.getElementById("email_employee_verified").value;
            email_employee_after_verified = email_employee
            const employeeNumber = $('input[name="emp_number"]').val();
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

                function updateCountdown() {
                    var minutes = Math.floor(countdownTime / 60);
                    var seconds = countdownTime % 60;
                    $('#countdown_timer_check_email').text(minutes + " minutes " + seconds + " seconds");
                    countdownTime--;
                    if (countdownTime < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown_timer_check_email').text("@lang('messages.Countdown finished')");
                        $('#send_btn_email_check').show();
                    }
                }
            }


        }

        async function checkEmail(){
            const employeeNumber = $('input[name="emp_number"]').val();
            var otp = document.getElementById("otp_email").value;

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
                $('#email_personnal_block').removeClass('d-none').addClass('d-block');
                $('#block_phone_number').removeClass('d-none').addClass('d-block');
                $('#email_personnal').val(email_employee_after_verified);
                $('#otpModal').modal('hide');
                $('#EmailModal').modal('hide');
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
