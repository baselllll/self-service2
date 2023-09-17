<!DOCTYPE html>
<html lang="en">

@include('includes._header')
<body>
<audio id="notificationSound" src="http://127.0.0.1:8000/mobo.mp3"></audio>
<!-- Spinner Start -->
<div id="spinner"
     class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!-- Spinner End -->
@php
    $pending_status = \App\Enums\AppKeysProps::PendingStatus()->value;
 $manger_user_type = \App\Enums\AppKeysProps::Manger()->value;
 $admin_user_type = \App\Enums\AppKeysProps::AdminManger()->value;
 $top_user_type = \App\Enums\AppKeysProps::TopManger()->value;
 $admin_manger_approved= \App\Enums\AppKeysProps::AdminMgrApproved()->value;
 $Pend_approved_pending_req= \App\Enums\AppKeysProps::Pend_approved_pending_req()->value;
 $Mgr_approved_pending_req= \App\Enums\AppKeysProps::Mgr_approved_pending_req()->value;
 $accrued_balance= new \App\Http\Repository\MainOracleQueryRepo();
 @endphp
@include('includes._navbar',['requested_notification' => $requested_notification])

{{--@php--}}
{{--    $user_type = session()->get('user_type');--}}
{{--@endphp--}}


<div class="container-xxl py-5">

    <div class="row">
        @if($status_request=="request_service")
            <div class="col-12">
                <div class="row">
                    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                        <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Services') </h6>
                        <h1 class="mb-5">@lang('messages.Absence Services')</h1>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-2">
                            <div class="input-group mb-3">
                                <input type="text" id="service-filter" class="form-control" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                @foreach($all_services as $service)
                        <div class="col-lg-3 col-md-6 wow fadeInUp service-card" data-wow-delay="0.1s">
                            <div class="team-item bg-light">
                                <div class="overflow-hidden">
                                    <a href="{{route("service-details",['service_type'=>'Absence','absence_attendance_type_id'=>$service->absence_attendance_type_id,'name'=>$service->name])}}">
                                        <img class="img-fluid" src="{{asset("img/$service->image")}}" alt=""></a>
                                </div>

                                <div class="text-center p-4">
                                    <h5 class="mb-0"> @lang('messages.' . $service->name)</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Notify') </h6>
                    <h1 class="mb-5">@lang('messages.leaveNotify') </h1>
                </div>
                <div class="row">

                    <div class="col-12">
                        <table id="tablerow_structure" class="table table-striped bg-dark" style="width:100%">
                            <thead style="background:#1dafbd;color: white">
                            <tr>
                                <th></th>
                                <th>@lang('messages.SerialNumber')</th>
                                <th scope="col">@lang('messages.mr_ServiceName') </th>
                                <th scope="col">@lang('messages.mr_EmployeeNumber') </th>
                                <th scope="col">@lang('messages.mr_EmployeeName') </th>
                                <th scope="col">@lang('messages.mr_StartDate') </th>
                                <th scope="col">@lang('messages.mr_EndDate') </th>
                                <th scope="col">@lang('messages.mr_Status') </th>

                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody style="color: black">
                            @foreach($requested_notification as $item)

                                @php
                                    $conditions =   ($item->delegate_to_emp==session()->get('employee')->employee_number  and \Carbon\Carbon::now() >= $item->delegate_from_date and \Carbon\Carbon::now() <= $item->delegate_to_date);
                                @endphp
                                <tr style="background-color: #181d38">
                                    @if($item->no_of_approvals==3)
                                         @if((($item->approval_status==$Pend_approved_pending_req or $item->approval_status==str_contains($item->approval_status,'Delegated')) and $user_type==$manger_user_type) or ($item->mgr_person_id == $item->admin_mgr_person_id and  $special_type_user_default==$manger_user_type) or $conditions)
                                           <tr data-approval_status="{{$item->approval_status}}" data-absence_type="{{$item->absence_type}}" class="toggleable-row" @if(isset($toggle_unauthorized_annual) and $toggle_unauthorized_annual==1 and $item->absence_type=="Authorized Unpaid Leave")
                                                   style="display: none"
                                           @endif>
                                               @if($item->absence_type=="Annual Leave")
                                                   <td><button class="toggle-button-annal"><i class="fas fa-chevron-down"></i></button></td>
                                               @else
                                                   <td style="color: white; font-size: 15px;"></td>
                                               @endif
                                               <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                               <td style="color: white">{{$item->absence_type}}</td>
                                               <td style="color: white">{{$item->empno}}</td>
                                               <td style="color: white">{{$item->emp_name}}</td>
                                               <td style="color: white">{{$item->absence_start_date}}</td>
                                               <td style="color: white">{{$item->absence_end_date}}</td>
                                               <td style="color: white">{{$item->approval_status}}</td>
                                               @if($item->mgr_approval_status!=$pending_status )
                                                   <td style="color: white"></td>
                                               @else
                                                   <td style="display: flex;justify-content: space-evenly;">
                                                       <a href="#">
                                                           <button
                                                               data-type_person="Manager"

                                                               data-notify_confirm="{{$item->mgr_notif_id}}"
                                                               data-transaction_id="{{$item->transaction_id}}"
                                                               type="button"
                                                               class="btn btn-success confirmationClick"
                                                               data-toggle="modal" data-target="#confirmation"><i
                                                                   class="fas fa-check"></i>
                                                           </button>
                                                       </a>
                                                       <a href="#">
                                                           <button data-type_person="Manager"
                                                                   data-notify_confirm="{{$item->mgr_notif_id}}"
                                                                   data-transaction_id="{{$item->transaction_id}}"
                                                                   type="button"
                                                                   class="btn btn-danger addedNoteClick"
                                                                   data-toggle="modal" data-target="#addedNote"><i
                                                                   class="fas fa-sharp fa-solid fa-x fa-bounce fa-lg"
                                                                   style="color: #c92233;"></i>X
                                                           </button>
                                                       </a>
                                                       @if($item->absence_type=="Annual Leave")
                                                           @php
                                                               $date = \Carbon\Carbon::create($item->absence_start_date);
                                                               $formattedDate = $date->format('d-M-Y');
                                                                $number_accural = $accrued_balance->accrued_balance($item->empno,$formattedDate);
                                                           @endphp
                                                           <a href="#">
                                                               <button
                                                                   data-start_date="{{$item->absence_start_date}}"
                                                                   data-end_date="{{$item->absence_end_date}}"
                                                                   data-accurals_no="{{$number_accural}}"
                                                                   data-taswiath_status="{{$item->taswiath_status}}"
                                                                   data-taswiath_note="{{$item->taswiath_note}}"
                                                                   type="button"
                                                                   class="btn btn-warning showAccutalsDetailsClick"
                                                                   data-toggle="modal"
                                                                   data-target="#showAccutalsDetails">
                                                                   <i class="fa fa-bars fa-bounce fa-lg"></i>
                                                               </button>
                                                           </a>


                                                       @endif
                                                   </td>
                                               @endif
                                           </tr>

                                        @elseif((($item->approval_status==$admin_manger_approved or $item->approval_status==str_contains($item->approval_status,'Delegated')) and  $user_type==$top_user_type ) or $conditions)
                                             <tr data-approval_status="{{$item->approval_status}}" data-absence_type="{{$item->absence_type}}" class="toggleable-row" @if(isset($toggle_unauthorized_annual) and $toggle_unauthorized_annual==1 and $item->absence_type=="Authorized Unpaid Leave")
                                                 style="display: none"
                                                 @endif>
                                                 @if($item->absence_type=="Annual Leave")
                                                     <td><button class="toggle-button-annal"><i class="fas fa-chevron-down"></i></button></td>
                                                 @else
                                                     <td style="color: white; font-size: 15px;"></td>
                                                 @endif
                                                <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                                <td style="color: white">{{$item->absence_type}}</td>
                                                <td style="color: white">{{$item->empno}}</td>
                                                <td style="color: white">{{$item->emp_name}}</td>
                                                <td style="color: white">{{$item->absence_start_date}}</td>
                                                <td style="color: white">{{$item->absence_end_date}}</td>
                                                <td style="color: white">{{$item->approval_status}}</td>
                                                @if($item->top_management_approval_status!=$pending_status )
                                                    <td style="color: white"></td>
                                                @else
                                                    <td style="display: flex;justify-content: space-evenly;">
                                                        <a href="#">
                                                            <button
                                                                data-type_person="TopMgr"

                                                                data-notify_confirm="{{$item->top_mgmt_notif_id}}"
                                                                data-transaction_id="{{$item->transaction_id}}"
                                                                type="button"
                                                                class="btn btn-success confirmationClick"
                                                                data-toggle="modal" data-target="#confirmation"><i
                                                                    class="fas fa-check"></i>
                                                            </button>
                                                        </a>
                                                        <a href="#">
                                                            <button data-type_person="TopMgr"

                                                                    data-notify_confirm="{{$item->top_mgmt_notif_id}}"
                                                                    data-transaction_id="{{$item->transaction_id}}"
                                                                    type="button"
                                                                    class="btn btn-danger addedNoteClick"
                                                                    data-toggle="modal" data-target="#addedNote"><i
                                                                    class="fas fa-sharp fa-solid fa-x fa-bounce fa-lg"
                                                                    style="color: #c92233;"></i>X
                                                            </button>
                                                        </a>
                                                        @if($item->absence_type=="Annual Leave")
                                                            @php
                                                                $date = \Carbon\Carbon::create($item->absence_start_date);
                                                                $formattedDate = $date->format('d-M-Y');
                                                                 $number_accural = $accrued_balance->accrued_balance($item->empno,$formattedDate);
                                                            @endphp
                                                            <a href="#">
                                                                <button
                                                                    data-start_date="{{$item->absence_start_date}}"
                                                                    data-end_date="{{$item->absence_end_date}}"
                                                                    data-accurals_no="{{$number_accural}}"
                                                                    data-taswiath_status="{{$item->taswiath_status}}"
                                                                    data-taswiath_note="{{$item->taswiath_note}}"
                                                                    type="button"
                                                                    class="btn btn-warning showAccutalsDetailsClick"
                                                                    data-toggle="modal"
                                                                    data-target="#showAccutalsDetails">
                                                                    <i class="fa fa-info-circle fa-bounce fa-lg"></i>
                                                                </button>
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>

                                        @elseif((($item->approval_status==$Mgr_approved_pending_req  or $item->approval_status==str_contains($item->approval_status,'Delegated')) and $user_type==$admin_user_type )  or $conditions)
                                             <tr data-approval_status="{{$item->approval_status}}" data-absence_type="{{$item->absence_type}}" class="toggleable-row" @if(isset($toggle_unauthorized_annual) and $toggle_unauthorized_annual==1 and $item->absence_type=="Authorized Unpaid Leave")
                                                 style="display: none"
                                                 @endif>
                                                 @if($item->absence_type=="Annual Leave")
                                                     <td><button class="toggle-button-annal"><i class="fas fa-chevron-down"></i></button></td>
                                                 @else
                                                     <td style="color: white; font-size: 15px;"></td>
                                                 @endif
                                                <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                                <td style="color: white">{{$item->absence_type}}</td>
                                                <td style="color: white">{{$item->empno}}</td>
                                                <td style="color: white">{{$item->emp_name}}</td>
                                                <td style="color: white">{{$item->absence_start_date}}</td>
                                                <td style="color: white">{{$item->absence_end_date}}</td>
                                                <td style="color: white">{{$item->approval_status}}</td>
                                                @if($item->admin_mgr_approval_status!=$pending_status )
                                                    <td style="color: white"></td>
                                                @else
                                                    <td style="display: flex;justify-content: space-evenly;">
                                                        <a href="#">
                                                            <button
                                                                data-type_person="AdminMgr"

                                                                data-notify_confirm="{{$item->admin_mgr_notif_id}}"
                                                                data-transaction_id="{{$item->transaction_id}}"
                                                                type="button"
                                                                class="btn btn-success confirmationClick"
                                                                data-toggle="modal" data-target="#confirmation"><i
                                                                    class="fas fa-check"></i></button>
                                                        </a>
                                                        <a href="#">
                                                            <button data-type_person="AdminMgr"

                                                                    data-notify_confirm="{{$item->admin_mgr_notif_id}}"
                                                                    data-transaction_id="{{$item->transaction_id}}"
                                                                    type="button"
                                                                    class="btn btn-danger addedNoteClick"
                                                                    data-toggle="modal" data-target="#addedNote"><i
                                                                    class="fas fa-sharp fa-solid fa-x fa-bounce fa-lg"
                                                                    style="color: #c92233;"></i>X
                                                            </button>
                                                        </a>
                                                        @if($item->absence_type=="Annual Leave")
                                                            @php
                                                                $date = \Carbon\Carbon::create($item->absence_start_date);
                                                                $formattedDate = $date->format('d-M-Y');
                                                                 $number_accural = $accrued_balance->accrued_balance($item->empno,$formattedDate);
                                                            @endphp
                                                            <a href="#">
                                                                <button
                                                                    data-start_date="{{$item->absence_start_date}}"
                                                                    data-end_date="{{$item->absence_end_date}}"
                                                                    data-accurals_no="{{$number_accural}}"
                                                                    data-taswiath_status="{{$item->taswiath_status}}"
                                                                    data-taswiath_note="{{$item->taswiath_note}}"
                                                                    type="button"
                                                                    class="btn btn-warning showAccutalsDetailsClick"
                                                                    data-toggle="modal"
                                                                    data-target="#showAccutalsDetails">
                                                                    <i class="fa fa-info-circle fa-bounce fa-lg"></i>
                                                                </button>
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endif

                                    @elseif($item->no_of_approvals==2)

                                        @if((($item->approval_status==$Pend_approved_pending_req  or $item->approval_status==str_contains($item->approval_status,'Delegated')) and $user_type==$manger_user_type) or ($item->mgr_person_id == $item->admin_mgr_person_id and  $special_type_user_default==$manger_user_type) or $conditions)
                                          <tr>
                                              <td style="color: white; font-size: 15px;"></td>
                                              <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                              <td style="color: white">{{$item->absence_type}}</td>
                                              <td style="color: white">{{$item->empno}}</td>
                                              <td style="color: white">{{$item->emp_name}}</td>
                                              <td style="color: white">{{$item->absence_start_date}}</td>
                                              <td style="color: white">{{$item->absence_end_date}}</td>
                                              <td style="color: white">{{$item->approval_status}}</td>
                                              @if($item->mgr_approval_status!=$pending_status )
                                                  <td style="color: white"></td>
                                              @else
                                                  <td style="display: flex;justify-content: space-evenly;">
                                                      <a href="#">
                                                          <button
                                                              data-type_person="Manager"

                                                              data-notify_confirm="{{$item->mgr_notif_id}}"
                                                              data-transaction_id="{{$item->transaction_id}}"
                                                              type="button"
                                                              class="btn btn-success confirmationClick"
                                                              data-toggle="modal" data-target="#confirmation"><i
                                                                  class="fas fa-check"></i></button>
                                                      </a>
                                                      <a href="#">
                                                          <button data-type_person="Manager"

                                                                  data-notify_confirm="{{$item->mgr_notif_id}}"
                                                                  data-transaction_id="{{$item->transaction_id}}"
                                                                  type="button"
                                                                  class="btn btn-danger addedNoteClick"
                                                                  data-toggle="modal" data-target="#addedNote"><i
                                                                  class="fas fa-sharp fa-solid fa-x fa-bounce fa-lg"
                                                                  style="color: #c92233;"></i>X
                                                          </button>
                                                      </a>
                                                      <a href="{{route("get-details",['transaction_id'=>$item->transaction_id])}}">
                                                          <button type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
                                                      </a>
                                                  </td>
                                              @endif
                                          </tr>

                                        @elseif((($item->approval_status==$Mgr_approved_pending_req  or $item->approval_status==str_contains($item->approval_status,'Delegated')) and ($user_type==$admin_user_type )) or $conditions)
                                          <tr>
                                              <td style="color: white; font-size: 15px;"></td>
                                              <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                              <td style="color: white">{{$item->absence_type}}</td>
                                              <td style="color: white">{{$item->empno}}</td>
                                              <td style="color: white">{{$item->emp_name}}</td>
                                              <td style="color: white">{{$item->absence_start_date}}</td>
                                              <td style="color: white">{{$item->absence_end_date}}</td>
                                              <td style="color: white">{{$item->approval_status}}</td>
                                              @if($item->admin_mgr_approval_status!=$pending_status )
                                                  <td style="color: white"></td>
                                              @else
                                                  <td style="display: flex;justify-content: space-evenly;">
                                                      <a href="#">
                                                          <button
                                                              data-type_person="AdminMgr"
                                                              data-notify_confirm="{{$item->admin_mgr_notif_id}}"
                                                              data-transaction_id="{{$item->transaction_id}}"
                                                              type="button"
                                                              class="btn btn-success confirmationClick"
                                                              data-toggle="modal" data-target="#confirmation"><i
                                                                  class="fas fa-check"></i></button>
                                                      </a>
                                                      <a href="#">
                                                          <button data-type_person="AdminMgr"
                                                                  data-notify_confirm="{{$item->admin_mgr_notif_id}}"
                                                                  data-transaction_id="{{$item->transaction_id}}"
                                                                  type="button"
                                                                  class="btn btn-danger addedNoteClick"
                                                                  data-toggle="modal" data-target="#addedNote"><i
                                                                  class="fas fa-sharp fa-solid fa-x fa-bounce fa-lg"
                                                                  style="color: #c92233;"></i>X
                                                          </button>
                                                      </a>
                                                      <a href="{{route("get-details",['transaction_id'=>$item->transaction_id])}}">
                                                          <button type="button"> <i class="fa fa-bars" aria-hidden="true"></i></button>
                                                      </a>
                                                  </td>
                                              @endif
                                          </tr>
                                @endif
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="addedNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div>
                    <form action="{{route('reject-request')}}" method="post">
                        @csrf

                        <input type="hidden" name="notify_confirm" id="notify_confirm" value=""/>
                        <input type="hidden" name="transaction_id" id="transaction_id" value=""/>
                        <input type="hidden" name="type_person" id="type_person" value=""/>
                        <input type="hidden" name="user_type" id="user_type" value=""/>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1"
                                   style="float: left;">@lang('messages.Added_Notes')</label>
                            <textarea name="note" class="form-control" id="note" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" style="margin: 17px 166px;width: 163px"
                                    class="btn btn-primary align-content-center">@lang('messages.Yes')</button>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.Close')</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div style="text-align: center">
                    <div>
                        <form action="{{route('approve-request')}}" method="post">
                            @csrf
                            <input type="hidden" name="notify_confirm" id="notify_confirm" value=""/>
                            <input type="hidden" name="transaction_id" id="transaction_id" value=""/>
                            <input type="hidden" name="type_person" id="type_person" value=""/>
                            <input type="hidden" name="user_type" id="user_type" value=""/>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1"
                                       style="float: left;">@lang('messages.Added_Notes')</label>
                                <textarea name="note" class="form-control" id="note" rows="3"></textarea>
                            </div>
                            <div class="form-group">

                                <button type="submit" style="margin: 17px 166px;width: 163px"
                                        class="btn btn-primary">@lang('messages.Yes')</button>
                            </div>

                        </form>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">@lang('messages.Close')</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="showAccutalsDetails" tabindex="-1" role="dialog" aria-labelledby="showAccutalsDetails" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div style="text-align: center">
                    <div>
                        <p><strong>@lang("messages.StartDate"):</strong> <span id="modal-start-date"></span></p>
                        <p><strong>@lang("messages.EndDate"):</strong> <span id="modal-end-date"></span></p>
                        <p><strong>@lang("messages.Accurals"):</strong> <span id="modal-accurals-no"></span></p>
                        <p><strong>@lang("messages.Differnce_Date"):</strong> <span id="modal-differnceDate"></span></p>
                        <p><strong>@lang("messages.taswiath_status"):</strong> <span id="modal-taswiath_status"></span></p>
                        <p><strong>@lang("messages.taswiath_note"):</strong><span id="modal-taswiath_note"></span></p>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.Close')</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


@include('includes._footer')

<script>
    // JavaScript function to handle the filtering on keyup event
    document.getElementById('service-filter').addEventListener('keyup', function () {
        const filterText = this.value.toLowerCase();
        const serviceCards = document.querySelectorAll('.service-card');
        console.log(serviceCards)
        serviceCards.forEach(card => {
            const serviceNameElement = card.querySelector('.mb-0');
            if (serviceNameElement) {
                const serviceName = serviceNameElement.textContent.toLowerCase();
                if (serviceName.includes(filterText)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    });
</script>



</body>
<script>
    setTimeout(function() {
        location.reload();
    }, 1000000);

</script>
<script>
    var today = new Date();
    document.getElementById('out-date').innerText = today;
</script>
</html>
