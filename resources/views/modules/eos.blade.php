
    @php
        $conditions =   ($item->delegate_to_emp==session()->get('employee')->employee_number  and \Carbon\Carbon::now() >= $item->delegate_from_date and \Carbon\Carbon::now() <= $item->delegate_to_date);
    @endphp
    <tr style="background-color: #181d38">
    @if($item->no_of_approvals==3)
        @if((($item->approval_status==$Pend_approved_pending_req or $item->approval_status==str_contains($item->approval_status,'Delegated')) and $user_type==$manger_user_type) or ($item->mgr_person_id == $item->admin_mgr_person_id and  $special_type_user_default==$manger_user_type and $item->admin_mgr_approval_status ==$pending_status ) or $conditions)
            <tr data-approval_status="{{$item->approval_status}}" data-absence_type="{{$item->absence_type}}" class="toggleable-row" @if(isset($toggle_unauthorized_annual) and $toggle_unauthorized_annual==1 and $item->absence_type=="Authorized Unpaid Leave")
                style="display: none"
                @endif>
                <td style="color: white">{{$item->absence_type}}</td>
                <td style="color: white">{{$item->empno}}</td>
                <td style="color: white">{{$item->emp_name}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->notified_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->actual_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{$item->notice_period_days}}</td>
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
                        <a href="{{route("get-details",['transaction_id'=>$item->transaction_id])}}">
                            <button  type="button"><i class="fa fa-bars" aria-hidden="true" style="font-size: 25px;"></i></button>
                        </a>
                    </td>
                @endif
            </tr>

        @elseif((($item->approval_status==$admin_manger_approved or $item->approval_status==str_contains($item->approval_status,'Delegated')) and  $user_type==$top_user_type ) or $conditions)
            <tr data-approval_status="{{$item->approval_status}}" data-absence_type="{{$item->absence_type}}" class="toggleable-row" @if(isset($toggle_unauthorized_annual) and $toggle_unauthorized_annual==1 and $item->absence_type=="Authorized Unpaid Leave")
                style="display: none"
                @endif>
                <td style="color: white">{{$item->absence_type}}</td>
                <td style="color: white">{{$item->empno}}</td>
                <td style="color: white">{{$item->emp_name}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->notified_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->actual_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{$item->notice_period_days}}</td>
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
                        <a href="{{route("get-details",['transaction_id'=>$item->transaction_id])}}">
                            <button  type="button"><i class="fa fa-bars" aria-hidden="true" style="font-size: 25px;"></i></button>
                        </a>
                    </td>
                @endif
            </tr>

        @elseif((($item->approval_status==$Mgr_approved_pending_req  or $item->approval_status==str_contains($item->approval_status,'Delegated')) and $user_type==$admin_user_type )  or $conditions)
            <tr data-approval_status="{{$item->approval_status}}" data-absence_type="{{$item->absence_type}}" class="toggleable-row" @if(isset($toggle_unauthorized_annual) and $toggle_unauthorized_annual==1 and $item->absence_type=="Authorized Unpaid Leave")
                style="display: none"
                @endif>
                <td style="color: white">{{$item->absence_type}}</td>
                <td style="color: white">{{$item->empno}}</td>
                <td style="color: white">{{$item->emp_name}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->notified_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->actual_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{$item->notice_period_days}}</td>
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
                            <button  type="button"><i class="fa fa-bars" aria-hidden="true" style="font-size: 25px;"></i></button>
                        </a>
                    </td>
                @endif
            </tr>
        @endif

    @elseif($item->no_of_approvals==2)
      @if((($item->approval_status==$Pend_approved_pending_req  or $item->approval_status==str_contains($item->approval_status,'Delegated')) and $user_type==$manger_user_type) or ($item->mgr_person_id == $item->admin_mgr_person_id and  $special_type_user_default==$manger_user_type) or $conditions)

          <tr>
                <td style="color: white">{{$item->absence_type}}</td>
                <td style="color: white">{{$item->empno}}</td>
                <td style="color: white">{{$item->emp_name}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->notified_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->actual_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{$item->notice_period_days}}</td>
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
                            <button  type="button"><i class="fa fa-bars" aria-hidden="true" style="font-size: 28px;"></i></button>
                        </a>
                    </td>
                @endif
            </tr>

      @elseif((($item->approval_status==$Mgr_approved_pending_req  or $item->approval_status==str_contains($item->approval_status,'Delegated')) and ($user_type==$admin_user_type )) or $conditions)
            <tr>
                <td style="color: white">{{$item->absence_type}}</td>
                <td style="color: white">{{$item->empno}}</td>
                <td style="color: white">{{$item->emp_name}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->notified_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{\Carbon\Carbon::create($item->actual_eos_date)->format('d-M-Y')}}</td>
                <td style="color: white">{{$item->notice_period_days}}</td>
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
                            <button type="button"> <i class="fa fa-bars" aria-hidden="true" style="font-size: 28px;"></i></button>
                        </a>
                    </td>
                @endif
            </tr>
        @endif
    @endif
