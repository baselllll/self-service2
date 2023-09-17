<!-- Navbar Start -->

<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <div class="d-flex justify-content-between">
        <a href="{{ route('setLocale', ['locale' => 'ar']) }}"><span class="fi fi-sa"></span></a> &nbsp &nbsp
        <a href="{{ route('setLocale', ['locale' => 'en']) }}"><span class="fi fi-us"></span></a>
    </div>
    <button type="button" class="navbar-toggler order-1 ms-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div style="margin-left: 64px;">
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            @if(session()->has('employee') and session()->get('user_type')!=="top_mng")
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" style="position: relative;">
                    <span class="badge badge-warning navbar-badge badge-above">
                        @if(isset($requested_notification) and count($requested_notification) > 0)
                            @if( $requested_notification[0]->empno==session()->get('employee')->employee_number)
                                {{count($requested_notification)}}
                            @else
                                0
                            @endif
                        @else
                            0
                        @endif
                    </span>
                        <i class="far fa-bell fa-lg"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <!-- Your dropdown menu content here -->
                        @if(isset($requested_notification))
                            @foreach($requested_notification as $item)
                                @if($item->empno==session()->get('employee')->employee_number)
                                    <a href="#" class="dropdown-item">
                                        <i class="fas fa-comment-dots"></i>
                                        @if($item->approval_status=="Manager Approved")
                                            <span style="font-weight: bold; text-decoration: underline" >Manager Approved</span>
                                        @elseif($item->approval_status=="Admin Mgr Approved")
                                            <span style="font-weight: bold; text-decoration: underline" >Admin Manager Approved</span>
                                        @elseif($item->approval_status=="Approved")
                                        @elseif($item->approval_status=="Delegated Assistant Approved")
                                            <span style="font-weight: bold; text-decoration: underline" > 	Delegated Assistant Approved </span>
                                        @elseif($item->approval_status=="Rejected")
                                            <span style="font-weight: bold; text-decoration: underline" > Rejected </span>
                                        @else
                                            <span style="font-weight: bold; text-decoration: underline" > Pending Approval </span>
                                        @endif
                                        &nbsp {{$item->absence_type}} ({{$item->transaction_id}}) Request
                                    </a>
                                    <div class="dropdown-divider">{{$item->transaction_id}}</div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </li>
            @endif
        </ul>
    </div>

    @if(session()->has('employee'))
        <a style="font-weight: bold;color: red;font-size: 15px;text-decoration: underline;" href="{{route('get-employee-information')}}" class="nav-item nav-link">{{session()->get('employee_data')->employee_number}}</a>
        <a style="font-weight: bold;color: red; font-size: 15px;text-decoration: underline;"  href="{{route('get-employee-information')}}" class="nav-item nav-link ">{{logicTranslate(session()->get('employee_data')->employee_name)}}</a>
    @endif

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0" >
            @if(session()->has('employee'))
                @if( session()->get("taswaya_emp")==true)
                    <a style="font-weight: bold;text-decoration: underline"  href="{{route('taswaya')}}" class="nav-item nav-link">@lang('messages.taswaya')</a>
                @endif
                <a style="font-weight: bold;text-decoration: underline"  href="{{route('profile-employee')}}" class="nav-item nav-link">@lang('messages.EmpRequests')</a>
                <a style="font-weight: bold;text-decoration: underline" href="{{route('home')}}" class="nav-item nav-link">@lang('messages.HOME')</a>
                @if(session()->get('user_type')!=='top_mng')

                    <a style="font-weight: bold;text-decoration: underline" href="{{route('services-category')}}" class="nav-item nav-link" > <span >@lang('messages.NewRequest')</span></a>


                @endif


                @if(session()->get('user_type')=='top_mng' or session()->get('user_type')=='admin_manger' or session()->get('user_type')=='manger' or session()->get('delegated_type')=='delegated')
                    <a style="font-weight: bold;text-decoration: underline" href="{{route('delegation-view')}}" class="nav-item nav-link">@lang('messages.Delegation')</a>
                @else
                    <a style="font-weight: bold;text-decoration: underline" href="{{route('delegation-history-employee')}}" class="nav-item nav-link">@lang('messages.Delegation')</a>
                @endif

                <a style="font-weight: bold;text-decoration: underline" href="{{route('help')}}" class="nav-item nav-link">@lang('messages.help')</a>
                <a style="font-weight: bold;" href="{{route('logout')}}" class="btn btn-primary ">@lang('messages.logout')<i class="fa fa-arrow-right ms-3"></i></a>


            @else
                {{--                <a href="{{route('login-page')}}" class="btn btn-primary ">@lang('messages.login_click')<i class="fa fa-arrow-right ms-3"></i></a>--}}
            @endif
        </div>
    </div>
    @include('sweetalert::alert')
    {{--    <script>--}}
    {{--        @if(isset($last_requested_to_play_notify) and $last_requested_to_play_notify->empno==session()->get('employee')->employee_number)--}}
    {{--            console.log("{{$last_requested_to_play_notify->approval_status}}")--}}
    {{--            @php--}}
    {{--                $currentDate = \Carbon\Carbon::now();// Current timestamp--}}
    {{--            @endphp--}}
    {{--            console.log("{{$currentDate}}")--}}
    {{--            @if(str_contains($last_requested_to_play_notify->approval_status, "Approved")|| $last_requested_to_play_notify->approval_status=="Admin Mgr Approved" || str_contains($last_requested_to_play_notify->approval_status, "Rejected"))--}}
    {{--                console.log("{{$last_requested_to_play_notify->approval_status}}");--}}
    {{--                console.log("{{$last_requested_to_play_notify->update_date}}");--}}
    {{--                @if($last_requested_to_play_notify->creation_date <= $last_requested_to_play_notify->update_date)--}}
    {{--                   var notificationSound = document.getElementById("notificationSound");--}}
    {{--                   console.log(notificationSound)--}}
    {{--                   notificationSound.play();--}}
    {{--                @endif--}}
    {{--        @endif--}}
    {{--    @endif--}}
    {{--    </script>--}}
</nav>

<!-- Navbar End -->

