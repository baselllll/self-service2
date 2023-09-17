<!DOCTYPE html>
<html lang="en">

@include('includes._header')
<style>
    span {
        font-weight: bold;
        text-decoration: underline;
    }
</style>

<body>

<!-- Spinner Start -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex apgn-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!-- Spinner End -->


@include('includes._navbar',['requested_notification' => $requested_notification])

<div class="container-xxl py-5">
    <div class="container" style="margin-top: -22px;">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-right text-primary px-3">@lang('messages.help_message') </h6>
            <br/>
        </div>
        <br>
        <div class="container">
            <!-- Welcome Modal -->
            <div class="modal fade landscape-modal" id="welcomeModal">
                <!-- Modal content here (same as your provided code) -->
            </div>

            <!-- Contact Section -->
            <div class="container-xxl py-5" style="margin-top: -64px;">
                <div class="container" >
                    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    </div>
                    <br>
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                            <h6>@lang('messages.services')</h6>
                            <ul>
                                <li>@lang('messages.home')</li>
                                <li>@lang('messages.delegated_requests')</li>
                                <li>@lang('messages.request_new_service')</li>
                                <li>@lang('messages.pending_requests')</li>
                                <li>@lang('messages.logout_help')</li>
                                <li>@lang('messages.delegation')</li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <h6>@lang('messages.rules')</h6>
                            <ul>
                                <li>@lang('messages.rules_first_three_logins')</li>
                                <li>@lang('messages.rules_update_email_phone')</li>
                                <li>@lang('messages.rules_approval_annual_leave')</li>
                                <li>@lang('messages.rules_approval_service_requests')</li>
                                <li>@lang('messages.rules_request_annual_leave')</li>
                                <li>@lang('messages.rules_hajj_leave')</li>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
                            <h6>@lang('messages.languages')</h6>
                            <ul>
                                <li>@lang('messages.arabic')</li>
                                <li>@lang('messages.english')</li>
                            </ul>

                            <h6>@lang('messages.notifications')</h6>
                            <ul>
                                <li>@lang('messages.stay_updated')</li>
                            </ul>

                            <h6>@lang('messages.support')</h6>
                            <ul>
                                <li>@lang('messages.encounter_issues')</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@include('includes._footer')

</body>

</html>
