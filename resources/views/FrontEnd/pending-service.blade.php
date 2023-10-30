

<div class="container">
    <br/>
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
        <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Services') </h6>
    </div>
    <br>
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s"></div>
    <br>
    <div class="row">
        @php
            $baseUrl = \URL::to('pending-employee') . '?';
                 $urlParams = [

    'requested_notification' => is_null($requested_notification) ? null : json_encode($requested_notification),
    'toggle_unauthorized_annual' => $toggle_unauthorized_annual,
    'special_type_user_default' => $special_type_user_default,
    'last_requested_to_play_notify' =>is_null($last_requested_to_play_notify) ? null : json_encode($last_requested_to_play_notify),
    'getAllPendingDifferent' => is_null($getAllPendingDifferent) ? null : json_encode($getAllPendingDifferent),
    'employee' => is_null($employee) ? null : json_encode($employee),
    'all_services' => is_null($all_services) ? null : json_encode($all_services),
    'absence_requests' => is_null($absence_requests) ? null : json_encode($absence_requests),
    'user_type' => $user_type,
    'specail_services' => is_null($specail_services) ? null : json_encode($specail_services),
];


                  foreach ($urlParams as $key => $value) {
                      if ($value !== null) {
                          $baseUrl .= "$key=$value&";
                      }
                  }

        @endphp
        @foreach($getAllPendingDifferent as $service)
            <div  class="col-lg-3 col-md-6 wow fadeInUp service-card @if($service['condition']=="disabled")   @endif" data-wow-delay="0.1s">
                <div class="team-item bg-light">
                    <div class="overflow-hidden">

                        <a
                            @if($service['id']=='serv_p_1')
                                href="{{$baseUrl}}status_request_pending=absence"
                           @elseif($service['id']=='serv_p_2')
                                href="{{$baseUrl}}status_request_pending=eos"
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



