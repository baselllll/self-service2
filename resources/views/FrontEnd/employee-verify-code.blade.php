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


    @include('includes._navbar',['requested_notification' => $requested_notification])


    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown"></h1>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Contact Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.otp') </h6>
            </div>
            <br>
            <div class="row g-4">

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">

                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <form action="{{route('auth-login')}}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input required type="text" class="form-control" name="employee_number" id="employee_number" placeholder="Subject">
                                    <label for="employee_number">@lang('messages.OTP')</label>
                                </div>
                            </div>
                           <div class="row g-2">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <button class="btn btn-primary w-100 py-3" type="submit">@lang('messages.verify')</button>
                            </div>
                            <div class="col-4"> </div>
                           </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-12 wow fadeInUp" data-wow-delay="0.5s"></div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
   @include('includes._footer')

</body>

</html>
