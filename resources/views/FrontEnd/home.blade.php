<!DOCTYPE html>
<html lang="en">

@include('includes._header')

<body>


@php
 $Pend_approved_pending_req= \App\Enums\AppKeysProps::Pend_approved_pending_req()->value;
 $adminManger= \App\Enums\AppKeysProps::AdminManger()->value;
 $topManger= \App\Enums\AppKeysProps::TopManger()->value;
@endphp

    <audio id="notificationSound" src="http://127.0.0.1:8000/notification.mp3"></audio>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->
    @include('includes._navbar',['requested_notification' => $requested_notification])


        <div class="container"  style="    margin-top: 26px;">
            <div class="row">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="mb-5" style="margin-top: 9px">@lang('messages.Absence_Management') </h1>
                </div>
                <div class="col-1">

                </div>
                <div class="col-12" style="margin-top: -49px;">
                    <div style="width: 200px ;position: absolute;
    top: 463px;">

                    </div>
                    <div style=" overflow: auto;">
                        <table id="absense_table" class="table table-striped bg-dark" style="width:100%; color: white">
                            <thead style="background: #1dafbd">
                            <tr>
                                <th>@lang('messages.SerialNumber')</th>
                                @if(session()->get('user_type') != "employee")
                                    <th>@lang('messages.EmpNumber')</th>
                                    <th>@lang('messages.EmpName')</th>
                                    @if(session()->get('user_type') == "top_mng" or session()->get('user_type') == "admin_manger")
                                        <th>@lang('messages.EmpDeptNo')</th>
                                        <th>@lang('messages.EmpDeptName')</th>
                                        <th>@lang('messages.MngrName')</th>
                                    @endif

                                @endif
                                <th>@lang('messages.AbsenceType')</th>
                                <th>@lang('messages.CreationDate')</th>
                                <th>@lang('messages.ApprovalStatus')</th>
                                <th>Details</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($absence_requests as $absence)

                                @if($user_type == $adminManger or $user_type == $topManger )
                                    @if(str_contains($absence->approval_status,'Rejected'))
                                    @else
                                        @if(session()->get("user_type") == "employee" and $absence->approval_status == $Pend_approved_pending_req)
                                            <tr>
                                                <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                                @if(session()->get('user_type') != "employee")
                                                    <td  style="color: white; font-size: 15px;">{{$absence->empno}}</td>
                                                    <td style="color: white; font-size: 15px;">{{$absence->emp_name}}</td>

                                                    @if(session()->get('user_type') == "top_mng" or session()->get('user_type') == "admin_manger")
                                                        <td style="color: white; font-size: 15px;">{{$absence->cost_center_no}}</td>
                                                        <td style="color: white; font-size: 15px;">{{$absence->cost_center_name}}</td>
                                                        <td style="color: white; font-size: 15px;">{{$absence->mgr_name}}</td>
                                                    @endif


                                                @endif
                                                <td style="color: white; font-size: 15px;">{{$absence->absence_type}}</td>
                                                <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($absence->creation_date)->format('Y-m-d')}}</td>
                                                <td style="color: white; font-size: 15px;">@if(str_contains($absence->approval_status,"Rejected")) Rejected @else {{$absence->approval_status}} @endif</td>
                                                <td>
                                                    <a href="{{route("get-details",['transaction_id'=>$absence->transaction_id])}}">
                                                        <button type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
                                                    </a>
                                                    @if(str_contains($absence->approval_status,"Approved"))
                                                    @elseif(str_contains($absence->approval_status,"Rejected"))
                                                    @else
                                                        <a href="{{route("delete-service",['transaction_id'=>$absence->transaction_id])}}">
                                                            <button style="color: red" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                        </a>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endif
                                        @if($absence->approval_status != $Pend_approved_pending_req)
                                            <tr>
                                                <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                                @if(session()->get('user_type') != "employee")
                                                    <td style="color: white; font-size: 15px;">{{$absence->empno}}</td>
                                                    <td style="color: white; font-size: 15px;">{{$absence->emp_name}}</td>

                                                    @if(session()->get('user_type') == "top_mng" or session()->get('user_type') == "admin_manger")
                                                        <td style="color: white; font-size: 15px;">{{$absence->cost_center_no}}</td>
                                                        <td style="color: white; font-size: 15px;">{{$absence->cost_center_name}}</td>
                                                        <td style="color: white; font-size: 15px;">{{$absence->mgr_name}}</td>
                                                    @endif


                                                @endif
                                                <td style="color: white; font-size: 15px;">{{$absence->absence_type}}</td>
                                                <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($absence->creation_date)->format('Y-m-d')}}</td>
                                                <td style="color: white; font-size: 15px;">@if(str_contains($absence->approval_status,"Rejected")) Rejected @else {{$absence->approval_status}} @endif</td>
                                                <td>
                                                    <a href="{{route("get-details",['transaction_id'=>$absence->transaction_id])}}">
                                                        <button type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
                                                    </a>
                                                    @if(str_contains($absence->approval_status,"Approved"))
                                                    @elseif(str_contains($absence->approval_status,"Rejected"))
                                                    @else
                                                        <a href="{{route("delete-service",['transaction_id'=>$absence->transaction_id])}}">
                                                            <button style="color: red" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @else
                                    @if(session()->get("user_type") == "employee" and session()->get("employee")->employee_number == $absence->empno and $absence->approval_status == $Pend_approved_pending_req)
                                        <tr>
                                            <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                            @if(session()->get('user_type') != "employee")
                                                <td style="color: white; font-size: 15px;">{{$absence->empno}}</td>
                                                <td style="color: white; font-size: 15px;">{{$absence->emp_name}}</td>

                                                @if(session()->get('user_type') == "top_mng" or session()->get('user_type') == "admin_manger")
                                                    <td style="color: white; font-size: 15px;">{{$absence->cost_center_no}}</td>
                                                    <td style="color: white; font-size: 15px;">{{$absence->cost_center_name}}</td>
                                                    <td style="color: white; font-size: 15px;">{{$absence->mgr_name}}</td>
                                                @endif


                                            @endif
                                            <td style="color: white; font-size: 15px;">{{$absence->absence_type}}</td>
                                            <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($absence->creation_date)->format('Y-m-d')}}</td>
                                            <td style="color: white; font-size: 15px;">@if(str_contains($absence->approval_status,"Rejected")) Rejected @else {{$absence->approval_status}} @endif</td>
                                            <td>
                                                <a href="{{route("get-details",['transaction_id'=>$absence->transaction_id])}}">
                                                    <button type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
                                                </a>
                                                @if(str_contains($absence->approval_status,"Approved"))
                                                @elseif(str_contains($absence->approval_status,"Rejected"))
                                                @else
                                                    <a href="{{route("delete-service",['transaction_id'=>$absence->transaction_id])}}">
                                                        <button style="color: red" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if($absence->approval_status != $Pend_approved_pending_req)
                                        <tr>
                                            <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                            @if(session()->get('user_type') != "employee")
                                                <td style="color: white; font-size: 15px;">{{$absence->empno}}</td>
                                                <td style="color: white; font-size: 15px;">{{$absence->emp_name}}</td>

                                                @if(session()->get('user_type') == "top_mng" or session()->get('user_type') == "admin_manger")
                                                    <td style="color: white; font-size: 15px;">{{$absence->cost_center_no}}</td>
                                                    <td style="color: white; font-size: 15px;">{{$absence->cost_center_name}}</td>
                                                    <td style="color: white; font-size: 15px;">{{$absence->mgr_name}}</td>
                                                @endif


                                            @endif
                                            <td style="color: white; font-size: 15px;">{{$absence->absence_type}}</td>
                                            <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($absence->creation_date)->format('Y-m-d')}}</td>
                                            <td style="color: white; font-size: 15px;">@if(str_contains($absence->approval_status,"Rejected")) Rejected @else {{$absence->approval_status}} @endif</td>
                                            <td>
                                                <a href="{{route("get-details",['transaction_id'=>$absence->transaction_id])}}">
                                                    <button type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
                                                </a>
                                                @if(str_contains($absence->approval_status,"Approved"))
                                                @elseif(str_contains($absence->approval_status,"Rejected"))
                                                @else
                                                    <a href="{{route("delete-service",['transaction_id'=>$absence->transaction_id])}}">
                                                        <button style="color: red" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>


@include('includes._footer')



@if(isset($_GET['isFirstLogin']) && ($_GET['isFirstLogin'] == 1))
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('spinner').style.display = 'none'; // Hide the spinner
            window.location.href = "/public/index.php/profile-employee";
        }, 500); // 500 milliseconds = 0.5 seconds
    </script>
@endif


</body>


</html>
