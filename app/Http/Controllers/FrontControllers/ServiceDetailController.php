<?php

namespace App\Http\Controllers\FrontControllers;

use App\Enums\AppKeysProps;
use App\Helper\SpecialSpecifService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddServiceValidRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Services\DetailsEmployeeService;
use App\Http\Services\LoginService;
use App\Http\Services\MangerLogicService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ServiceDetailController extends Controller
{
    protected $detailsEmployeeService;
    protected $loginService;
    protected $mangerLogicService;
    protected $employeeSigniture;
    public function __construct(MangerLogicService $mangerLogicService,DetailsEmployeeService $detailsEmployeeService,LoginService $loginService,SpecialSpecifService $specialSpecifService)
    {
        $this->detailsEmployeeService = $detailsEmployeeService;
        $this->loginService = $loginService;
        $this->specialSpecifService = $specialSpecifService;
        $this->mangerLogicService = $mangerLogicService;
        $this->employeeSigniture = AppKeysProps::UserTypeEmployee()->value;
    }
    public function index($service_type,$absence_attendance_type_id,$name){
        if (session()->get('employee_not_assign')==true){
            Alert::warning(__('messages.employee_not_Assign_title'),__('messages.employee_not_Assign_content'));
        }
        session()->put("absence_attendance_type_id",$absence_attendance_type_id);
        session()->save();
        $authorizedLeaveFlag = request()->query('authorizedLeaveFlag');
        $diffInDays = request()->query('diffInDays');
        if(!isset($authorizedLeaveFlag)){
            $authorizedLeaveFlag = false;
        }else{
            $authorizedLeaveFlag = true;
        }
            $employee = session()->get('employee');
           $user_type =  session()->get('user_type');

            $occurrence = $this->detailsEmployeeService->GetOccurance($absence_attendance_type_id,$employee->person_id);
            $lastRecord = $this->detailsEmployeeService->GetLastRecordFromCustomNotifyWF($employee->employee_number);
            //check not able employee enter two service at same time
//           if(isset($lastRecord)){
//               if($lastRecord->absence_type==$name and $lastRecord->approval_status=="Pending Approval"){
//                   Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
//                   return redirect()->to('profile-employee');
//               }
//           }


            $time_period = $this->loginService->GetPeriodTime($absence_attendance_type_id);
            $emp_department = $this->loginService->GetReplacmentDetailsSpecificDepartment($employee->person_id);
            $additionalUsedData =  $this->specialSpecifService->mainAdditionalFiledsAvailableService($name);
            $attribute_category = $additionalUsedData[0];
            $additional_field = $additionalUsedData[1];

        if($user_type!=$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);

        }



        if(isset($lastRecord)){
            if ($lastRecord->no_of_approvals== 3 and ($lastRecord->approval_status == "Pending Approval" or $lastRecord->approval_status== "Manager Approved" or $lastRecord->approval_status== "Admin Mgr Approved")){
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                return redirect()->route('profile-employee');
            }

            if ($lastRecord->no_of_approvals== 2 and ($lastRecord->approval_status == "Pending Approval" or $lastRecord->approval_status== "Manager Approved")){
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                return redirect()->route('profile-employee');
            }

        }

        return view('frontend.service-details',compact('requested_notification','diffInDays','authorizedLeaveFlag','emp_department','attribute_category','additional_field','service_type','time_period','service_type','absence_attendance_type_id','name','employee','occurrence'));
    }

    public function CalculateAccruals(Request $request)
    {
        $employee_number = session()->get('employee')->employee_number;
        if (isset($request->start_date)){
            $cleanDateString = substr($request->start_date, 0, strpos($request->start_date, 'GMT'));
            $originalDate = Carbon::parse($cleanDateString);
            $formattedDate = $originalDate->format('d-M-Y');
            $formattedDate = Carbon::createFromFormat('d-M-Y', $formattedDate)->format('d-M-Y');
            $number_accural = $this->detailsEmployeeService->accrued_balance($employee_number,$formattedDate);
            return response()->json(['results'=>$number_accural]);
        }
    }
    public function AddServiceDetail(AddServiceValidRequest $request){
        $start_date_absence = $request->start_date;
        $end_date_absence = $request->end_date;
        $datePattern = '/^\d{4}-\d{2}-\d{2}$/';

        if (preg_match($datePattern, $start_date_absence) && preg_match($datePattern, $end_date_absence)){
            $request['start_date']=$start_date_absence;
            $request['end_date']=$end_date_absence;
            $timePart_start_date = null;
            $timePart_end_date = null;
        }else{
            [$datePart_start_date, $timePart_start_date] = explode('T', $start_date_absence);
            [$datePart_end_date, $timePart_end_date] = explode('T', $end_date_absence);


            $request['start_date']=$datePart_start_date;
            $request['end_date']=$datePart_end_date;

            $timePart_end_date_to_diff = Carbon::parse($timePart_end_date);
            $timePart_start_date_to_diff = Carbon::parse($timePart_start_date);

            $diffInMinutes = $timePart_end_date_to_diff->diffInMinutes($timePart_start_date_to_diff);

            $diffHours = floor($diffInMinutes / 60);

            $request['difference_hours'] = (int)  $diffHours;

        }

        if($request->absence_attendance_type_id == 62){
            $accrauls_available = $request->get_Accruals_data;
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $diffInDays = $end->diffInDays($start);
            if ($diffInDays <= 10){
                Alert::warning('ERROR',__('messages.not_allowed_less'));
                return redirect()->to('profile-employee');
            }

            if(!isset($request->start_date_unathorized) or !isset($request->end_date_unathorized)){
                if($diffInDays >= $accrauls_available){
                    Alert::warning(__('messages.accurals_title'),__('messages.accurals_message'));
                    return redirect()->route('service-details',[
                        'service_type'=>'Absence',
                        'absence_attendance_type_id'=>$request->absence_attendance_type_id,
                        'name'=>$request->absence_type
                    ]);
                }
            }else{
                $request['unauthorized_absence_attendance_type_id'] = 63;
                $request['unauthorized_absence_name' ] = 'Authorized Unpaid Leave';
            }
        }

        $employee = session()->get('employee');
        $lastRecordSameService = $this->detailsEmployeeService->getLastSameService($employee->employee_number,$request->absence_type);
        if (isset($lastRecordSameService[0])){
            if ($lastRecordSameService[0]->absence_end_date > Carbon::now()->format('Y-m-d') and str_contains($lastRecordSameService[0]->approval_status,'Rejected') == false){
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate2_message'));
                return redirect()->to('profile-employee');
            }
        }
        $lastRecord = $this->detailsEmployeeService->GetLastRecordFromCustomNotifyWF($employee->employee_number);
        $lastRecordApproved = $this->detailsEmployeeService->GetLastRecordApprovedFromCustomNotifyWF($employee->employee_number);
        $lastRecordApproved_Two_Approvals = $this->detailsEmployeeService->GetLastRecordApprovedForTwoApprovalsFromCustomNotifyWF($employee->employee_number);
        if (isset($lastRecordApproved)){
            if ($request->start_date <= $lastRecordApproved->absence_end_date && $request->start_date >= $lastRecordApproved->absence_start_date) {
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate2_message'));
                return redirect()->to('profile-employee');
            }
        }
        if (isset($lastRecordApproved_Two_Approvals)){
            if ($request->start_date <= $lastRecordApproved_Two_Approvals->absence_end_date && $request->start_date >= $lastRecordApproved_Two_Approvals->absence_start_date) {
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate2_message'));
                return redirect()->to('profile-employee');
            }
        }




        if(isset($lastRecord)){
            if (isset($lastRecordSameService[0])){
                if($request->absence_type == $lastRecord->absence_type and str_contains($lastRecordSameService[0]->approval_status,'Rejected') == false){

                    if($request->start_date < $lastRecord->absence_end_date or $request->start_date < $lastRecord->absence_start_date){
                        Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate2_message'));
                        return redirect()->to('profile-employee');
                    }
                    if($request->start_date > $lastRecord->absence_end_date and $request->start_date < $lastRecord->absence_end_date){
                        Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate2_message'));
                        return redirect()->to('profile-employee');
                    }
                }
            }
          if($request->absence_type == $lastRecord->absence_type and ($lastRecord->approval_status == "Pending Approval" or $lastRecord->approval_status== "Manager Approved")){
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                return redirect()->to('profile-employee');
            }
           if ($lastRecord->no_of_approvals== 3 and ($lastRecord->approval_status == "Pending Approval" or $lastRecord->approval_status== "Manager Approved" or $lastRecord->approval_status== "Admin Mgr Approved")){
               Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                return redirect()->to('profile-employee');
            }

            if ($lastRecord->no_of_approvals== 2 and ($lastRecord->approval_status == "Pending Approval" or $lastRecord->approval_status== "Manager Approved")){
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                return redirect()->to('profile-employee');
            }
            if (isset($lastRecordSameService[0])){
                if(($request->start_date < $lastRecord->absence_end_date or $request->start_date < $lastRecord->absence_start_date) and str_contains($lastRecordSameService[0]->approval_status,'Rejected') == false ){
                    Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                    return redirect()->to('profile-employee');
                }
            }


        }

        //add validation here
        $absence_attendance_type_id =$request->absence_attendance_type_id;
        $absence_type =$request->absence_type;
        $employee_number =session()->get('employee')->employee_number;
        $person_id = $request->person_id;
        $occurrence = $request->person_id;
        $comments = $request->comments;
        $replacement_employee_number = $request->replacement_employee_number;
        $additional_data = $request->except(['_token','occurrence','start_date','end_date','absence_attendance_type_id','person_id','comments','manager_employee_number','attribute_category','date_notification','replacement_person_id','time_period','attribue1']);
        $replaced_employee = null;
        if(isset($replacement_employee_number)){
            $employee_replaced = $this->loginService->GetPersonID($replacement_employee_number);
//            dd($employee_replaced);
            $replaced_employee = (isset($employee_replaced)) ? $employee_replaced->person_id:null;
        }

        if($absence_type and isset( $request->unauthorized_absence_name)){
            $this->detailsEmployeeService->InsertDataInAbsenceTable(
                $person_id,$employee_number,$request->start_date_unathorized,$request->end_date_unathorized,$request->unauthorized_absence_name,$request->unauthorized_absence_attendance_type_id,$comments,$replaced_employee,$timePart_start_date,$timePart_end_date,$request->difference_hours
            );

            $this->detailsEmployeeService->InsertDataInAbsenceTable(
                $person_id,$employee_number,$request->start_date,$request->end_date,$absence_type,$absence_attendance_type_id,$comments,$replaced_employee,$timePart_start_date,$timePart_end_date,$request->difference_hours
            );
        }else{
            $this->detailsEmployeeService->InsertDataInAbsenceTable(
                $person_id,$employee_number,$request->start_date,$request->end_date,$absence_type,$absence_attendance_type_id,$comments,$replaced_employee,$timePart_start_date,$timePart_end_date,$request->difference_hours
            );
        }

         Alert::success("SUCCESS",__('messages.added_service_success'));
        return redirect('home');
    }
    public function AddSpecialServiceDetail(Request $request){
        $employee =session()->get('employee');
        if(isset($request->service_type)){
            $request['service_type'] = 'loan';
        }
        $request['data_attribute_form'] =json_encode( $request->except(['_token','service_type']));
        $this->detailsEmployeeService->InsertSpecialSeviceTable($request->all());
        Alert::success("Employee Name: {$employee->employee_name}  ",__('messages.added_service_success'));
        return redirect('home');
    }
    public function SpecialService($id_flex_num,$name){
        $segments_form = $this->detailsEmployeeService->GetSegmentsOfSpecifTemplate($id_flex_num);
        return view('frontend.service-details',compact('name','segments_form'));
    }

    public function specialcaseunauthorized($service_type,$absence_attendance_type_id,$name){
        $start_date_days = request()->query('start_date_days');
        $start_date_unauthorized = Carbon::parse($start_date_days);

        $employee = session()->get('employee');
        $user_type =  session()->get('user_type');

        $occurrence = $this->detailsEmployeeService->GetOccurance($absence_attendance_type_id,$employee->person_id);
        $lastRecord = $this->detailsEmployeeService->GetLastRecordFromCustomNotifyWF($employee->employee_number);
        //check not able employee enter two service at same time
        if(isset($lastRecord)){
            if($lastRecord->absence_type==$name and $lastRecord->approval_status=="Pending Approval"){
                Alert::warning(__('messages.added_service_validate1_title'),__('messages.added_service_validate1_message'));
                return redirect()->to('profile-employee');
            }
        }
        $time_period = $this->loginService->GetPeriodTime($absence_attendance_type_id);
        $emp_department = $this->loginService->GetReplacmentDetailsSpecificDepartment($employee->person_id);
        $additionalUsedData =  $this->specialSpecifService->mainAdditionalFiledsAvailableService($name);
        $attribute_category = $additionalUsedData[0];
        $additional_field = $additionalUsedData[1];

        if($user_type!=$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);

        }
        return view('frontend.service-details',compact('requested_notification','start_date_unauthorized','emp_department','attribute_category','additional_field','service_type','time_period','service_type','absence_attendance_type_id','name','employee','occurrence'));
    }
}
