<!DOCTYPE html>
<html lang="en">

@include('includes._header')

<body>
<!-- Spinner Start -->
<div id="spinner"
     class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
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
<!-- Contact Start -->
<div class="container-xxl py-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Employee') </h6>
                <h1 class="mb-5">@lang('messages.Employee Details')</h1>
            </div>
            <div class="testimonial-item text-center">
                <img class="border rounded-circle p-2 mx-auto mb-3" src="{{asset("img/emp.png")}}"
                     style="width: 100px; height: 100px;">
                <h5 class="mb-0">
                    {{logicTranslate($employee->employee_name)}}
                </h5>
                <p>@lang('messages.Employer')</p>
                <div class="testimonial-text bg-light text-center p-4">
                        <p> @lang('messages.Employee Number') : <span>{{$employee->employee_number}}</span></p>
                        <p>@lang('messages.Department') : <span>{{$employee->department}}</span></p>
                        <p>@lang('messages.Job') : <span>{{$employee->job}}</span></p>
                        <p>@lang('messages.Position') : <span>{{$employee->position}}</span></p>
                        <p>@lang('messages.Location') : <span>{{$employee->location}}</span></p>
                    </div>
                </div>
            </div>

        </div>


    </div>


@include('includes._footer')
</body>

</html>
