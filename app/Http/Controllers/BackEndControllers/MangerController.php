<?php

namespace App\Http\Controllers\BackEndControllers;

use App\Enums\AppKeysProps;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddDelegationRequest;
use App\Http\Services\LoginService;
use App\Http\Services\MangerLogicService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MangerController extends Controller
{
    protected $employeeSigniture;
    public function __construct(MangerLogicService $mangerLogicService,LoginService $loginService)
    {
        $this->mangerLogicService = $mangerLogicService;
        $this->loginService = $loginService;
        $this->employeeSigniture = AppKeysProps::UserTypeEmployee()->value;
    }

    public function ApproveRequest(Request $request){
        try {
            $user_type = session()->get('user_type');
            $this->mangerLogicService->ApproveRequest($request->notify_confirm,$request->transaction_id,$request->note,$request->type_person);
            Alert::success("SUCCESS",__('messages.mr_Approve'));
            return redirect()->route('profile-employee',['user_type'=>$user_type]);
        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.login_error'));
            return redirect()->route('profile-employee');
        }

    }
    public function RejectRequest(Request $request){
        try {
            $this->mangerLogicService->RejectRequest($request->notify_confirm,$request->transaction_id,$request->note,$request->type_person);
            Alert::warning("WARNING",__('messages.mr_Reject'));
            return redirect()->route('profile-employee');
        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.login_error'));
            return redirect()->route('profile-employee');
        }
    }
    public function Delegation(Request $request){
        $employee = session()->get('employee');
        $user_type = session()->get('user_type');
        if ($user_type=="employee"){
            $get_delegetion = $this->loginService->GetDelegation($employee->employee_number,"employee");
            foreach ($get_delegetion as $item){
                $item->delegate_from_emp =  $this->loginService->GetEmolyeeDataFromEmployee($item->delegate_from_emp)->full_name;
                $item->delegate_to_emp =  $this->loginService->GetEmolyeeDataFromEmployee($item->delegate_to_emp)->full_name;
                $item->absence_type_ar =  json_decode($item->absence_type_ar);
            }

        }else{
            $get_delegetion = $this->loginService->GetDelegation($employee->employee_number,"manger");
            foreach ($get_delegetion as $item){
                $item->delegate_from_emp =  $this->loginService->GetEmolyeeDataFromEmployee($item->delegate_from_emp)->full_name;
                $item->delegate_to_emp =  $this->loginService->GetEmolyeeDataFromEmployee($item->delegate_to_emp)->full_name;
                $item->absence_type_ar =  json_decode($item->absence_type_ar);
            }
        }
        if($user_type!=$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);
        }
        return view('frontend.delegation',compact('get_delegetion','user_type','requested_notification'));

    }

  public function createDelegate(){

      $employee = session()->get('employee');
      $all_services = $this->loginService->GetRestDelegationServiceThatNotChoose($employee->employee_number);
      $emp_department = $this->loginService->GetReplacmentDetailsSpecificDepartment($employee->person_id);
      $GetOtherMangerDepartment = $this->loginService->GetOtherMangerDepartment($employee->person_id);

      return view('frontend.create_delegation',compact('emp_department','all_services','GetOtherMangerDepartment'));
    }
    public function storeDelegate(AddDelegationRequest $request){
        $employee = session()->get('employee');
        $all_services = $this->loginService->GetAbsenceAttendaceTypeID();
        $selectedOptions = $request->selectedOptions;

        $matchingItems = array_filter($all_services, function ($item) use ($selectedOptions) {
            return in_array($item->absence_attendance_type_id, $selectedOptions);
        });

        $request['selectedOptions'] = $matchingItems;

       isset($request->delegate_to_emp_other_department)  ?
           $request['delegate_to_emp'] =  $request->delegate_to_emp_other_department :
           $request->delegate_to_emp;

        $stored_delegated = $this->loginService->storeDelegate($request->all());
        Alert::success("SUCCESS",__('messages.created_Deleted'));
        return redirect()->route('delegation-view');
    }
    public function updateDelegate(Request $request){
        try {
           $this->loginService->updateDelegateData($request->delegate_id,
                [
                'delegate_from_emp'=>$request->delegate_from_emp,
                'delegate_to_emp'=>$request->delegate_to_emp,
                'delegate_from_date'=>$request->delegate_from_date,
                'delegate_to_date'=>$request->delegate_to_date,
                'delegation_status'=>$request->delegation_status,
            ]);
            Alert::warning("WARNING",__('messages.success_updated_delegated'));
            return redirect()->route('delegation-view');
        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.failed_updated'));
            return redirect()->route('delegation-view');
        }
    }
    public function updateDelegateView(Request $request,$delegated_id){
        $employee = session()->get('employee');
        $delegate_record = $this->loginService->getSpecificDelegate($delegated_id);
        $emp_department = $this->loginService->GetReplacmentDetailsSpecificDepartment($employee->person_id);
        return view('frontend.update_delegation',compact('delegate_record','emp_department'));
    }
    public function deleteDelegate($delegate_id){
        try {
            $this->loginService->deleteDelegate($delegate_id);
            Alert::warning("WARNING",__('messages.success_Delegated_deleted'));
            return redirect()->route('delegation-view');
        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.failed_Deleted'));
            return redirect()->route('delegation-view');
        }
    }
}
