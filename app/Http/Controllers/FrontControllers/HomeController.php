<?php

namespace App\Http\Controllers\FrontControllers;


use App\Enums\AppKeysProps;
use App\Http\Controllers\Controller;
use App\Http\Services\LoginService;
use App\Http\Services\MangerLogicService;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $employeeSigniture;
    public function __construct(LoginService $loginService,MangerLogicService $mangerLogicService)
    {
        $this->loginService = $loginService;
        $this->mangerLogicService = $mangerLogicService;
        $this->employeeSigniture = AppKeysProps::UserTypeEmployee()->value;
    }

    public function index(){

        $employee = session()->get('employee');
        $user_type =  session()->get('user_type');
        // check if employee not have supervisor show message and disabled newRequest (item)
        $is_user_supervisor= $this->loginService->GetCountSupervisor($employee->employee_number);
        if(isset($is_user_supervisor)){
           if ($is_user_supervisor->assigned_supvsr_status != "Y"){
                Session::put('employee_not_assign',  true);
               request()->session()->save();
            }
        }
       if(isset($employee)){
           // get all requested services
           $absence_requests= $this->loginService->GetAbsenceManagment($employee->employee_number,$employee->person_id,$user_type);
           foreach ($absence_requests as $absence){
               // get the replacement name
               $replacement_no = $this->loginService->getRecordOfHRTransactionStep($absence->transaction_id);
               if(isset($replacement_no->information10)){
                   $absence->replacement_no =  $this->loginService->GetEmolyeeDataFromPersonId($replacement_no->information10)->full_name;
               }
           }
           // specific notification
           if($user_type!=$this->employeeSigniture){
               $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
           }else{
               $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);

           }
           $last_requested_to_play_notify =  $requested_notification->first();

           return view('frontend.home',compact('user_type','last_requested_to_play_notify','absence_requests','requested_notification'));
       }else{
           return redirect('login');
       }
    }
    public function getDetails($transaction_id){
        $employee = session()->get('employee');
        $user_type =  session()->get('user_type');
        $custom_details_employee = $this->loginService->GetDetailsOfCustom($transaction_id);
        if($user_type!=$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);
        }

        $directory = public_path('documents');

        $files = glob("$directory/*");
        $filePath=null;
        foreach ($files as $file) {
            $fileName = pathinfo($file, PATHINFO_FILENAME);
            if ($fileName === $employee->employee_number) {
                $filePath = str_replace(public_path(), '', $file);
                break;
            }
        }
        return view('frontend.employee-details-transacation',compact('requested_notification','filePath','custom_details_employee'));
    }
    public function help(){
        $employee = session()->get('employee');
        $user_type =  session()->get('user_type');
        if($user_type!=$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);
        }
        return view('frontend.help',compact('requested_notification'));
    }

    public function taswaya(){
        $absence_requests= $this->loginService->GetAnnualServiceForTawsaya();
        $filtered_notification = array_filter($absence_requests->toArray(), function ($item) {
            return ($item->absence_type == "Annual Leave" && $item->approval_status == "Pending Approval" && $item->taswiath_status == null);
        });
        $absence_requests = array_values($filtered_notification);
        return view('frontend.taswaya',compact('absence_requests'));
    }
    public function Approvetaswaya(Request $request){
        $transaction_id = $request->transaction_id;
        $note = $request->note;
         $this->loginService->taswaya_status_change($transaction_id,$note,'approve');
        Alert::warning('SUCCESS',__('messages.update_success'));
        return back();
    }
    public function Rejecttaswaya(Request $request){
        $transaction_id = $request->transaction_id;
        $note = $request->note;
        $this->loginService->taswaya_status_change($transaction_id,$note,'reject');
        Alert::warning('SUCCESS',__('messages.update_success'));
        return back();
    }
    public function clearance(Request $request){
        $clearance_initialed = $this->loginService->getAnnualApprovedForClearance();
        return view('frontend.clearance',compact("clearance_initialed"));
    }
}
