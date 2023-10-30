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
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
        <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Services') </h6>
    </div>
    <br>
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s"></div>
    <br>
    <div class="row">
        @foreach($all_services_category as $service)
            <div  class="col-lg-3 col-md-6 wow fadeInUp service-card @if($service['condition']=="disabled") disabled-card  @endif" data-wow-delay="0.1s">
                <div class="team-item bg-light">
                    <div class="overflow-hidden">

                        <a
                           @if($service['name']==\App\Enums\AppKeysProps::Absence_Services()->value)
                            href="{{route('profile-employee',['status_request'=>'request_service'])}}"
                            @elseif($service['name']==\App\Enums\AppKeysProps::Certificate_Services()->value)
                                href="{{route('certificate-service',['status_request'=>'request_service'])}}"
                            @elseif($service['name']==\App\Enums\AppKeysProps::Letter_Services()->value)
                                href="{{route('letter-service',['status_request'=>'request_service'])}}"
                            @elseif($service['name']==\App\Enums\AppKeysProps::Loan_Services()->value)
                                href="{{route('loan-service',['status_request'=>'request_service'])}}"
                            @elseif($service['name']==\App\Enums\AppKeysProps::Insurance()->value)
                                href="{{route('insurance-service',['status_request'=>'request_service'])}}"
                            @elseif($service['name']==\App\Enums\AppKeysProps::Other()->value)
                                href="{{route('other-service',['status_request'=>'request_service'])}}"
                            @elseif($service['name']==\App\Enums\AppKeysProps::EOS()->value)
                                href="{{route('end-service',['status_request'=>'request_service'])}}"
                            @endif
                        >
                            <img class="img-fluid" src="{{ asset("img/" . $service['image']) }}" alt="">
                        </a>
                    </div>

                    <div class="text-center p-4">
                        <h5 class="mb-0"> @lang('messages.' .$service['name'])</h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


@include('includes._footer')

</body>
<script>
    var today = new Date();
    document.getElementById('out-date').innerText  = today;
</script>
</html>
