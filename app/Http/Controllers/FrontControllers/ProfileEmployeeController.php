<?php

namespace App\Http\Controllers\FrontControllers;

use App\Enums\AppKeysProps;
use App\Helper\SpecialSpecifService;
use App\Http\Controllers\BackEndControllers\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Services\DetailsEmployeeService;
use App\Http\Services\LoginService;
use App\Http\Services\MangerLogicService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileEmployeeController extends Controller
{
    protected $loginService;
    protected $specialSpecifhelper;
    protected $detailsEmployeeService;
    protected $muslimReligion;
    protected $Manger;
    protected $employeeSigniture;

    public function __construct(DetailsEmployeeService $detailsEmployeeService,LoginService $loginService,SpecialSpecifService $specialSpecifhelper,MangerLogicService $mangerLogicService)
    {
        $this->loginService = $loginService;
        $this->specialSpecifhelper = $specialSpecifhelper;
        $this->mangerLogicService = $mangerLogicService;
        $this->detailsEmployeeService = $detailsEmployeeService;
        $this->muslimReligion = AppKeysProps::MuslimReligion();
        $this->employeeSigniture = AppKeysProps::UserTypeEmployee()->value;
        $this->pend_approved_pending_req = AppKeysProps::Pend_approved_pending_req()->value;
        $this->Manger= AppKeysProps::Manger()->value;
    }

    public function index(Request $request){


        $user_type =  session()->get('user_type');

        $employee = session()->get('employee');
        $special_type_user_default = session()->get('special_type_user_default');
        $this->loginService->updateOnPerPeople($employee->person_id);
        $status_request = $request->status_request;
        if(!isset($employee)){
            $login_service = App::make(LoginController::class);
            $login_service->logout();
            return redirect()->route("home");
        }
        //static services with static form
        $all_services = $this->loginService->GetAbsenceAttendaceTypeID();
        $specail_services = $this->loginService->GetALLDynamicformTemplate();
        $array_special = $this->specialSpecifhelper->SpecialService();
        $absence_requests= $this->loginService->GetAbsenceManagment($employee->person_id,$employee->employee_number);

        foreach ($absence_requests as $absence){
            if(isset($absence->replacement_person_id)){
                $absence->replacement_person_id =  $this->loginService->GetEmolyeeDataFromPersonId($absence->replacement_person_id)->full_name;
            }
        }




        $updated_specail_services = [];
        foreach ($specail_services as $item){
            if(in_array($item->id_flex_structure_name,$array_special)){
                array_push($updated_specail_services,$item);
            }
        }
        $specail_services = $updated_specail_services;


        if($user_type!==$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id,$employee->employee_number);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number,$employee->person_id);
        }
        $full_data_user = $this->detailsEmployeeService->GetPersonID($employee->employee_number);
        $images =  $this->specialSpecifhelper->CallingReservedImages();



        // show to user that exist "Authorized Unpaid Leave" founded related "Annual leave"
        foreach ($requested_notification as $requested_notify){
            if($requested_notify->absence_type=="Authorized Unpaid Leave" and $requested_notify->approval_status == $this->pend_approved_pending_req and !isset($_GET['status_request']) and $user_type=='manger' and $requested_notify->taswiath_status == 1){
                Alert::warning('WARNING',__('messages.warning_content_unauthorized'));
            }
        }

        foreach ($all_services as $key => $record) {
            $absence_attendance_type_id = $record->absence_attendance_type_id;
            if (isset($images[$absence_attendance_type_id])) {
                $record->image = $images[$absence_attendance_type_id];
            } else {
                $record->image = null; // Or any default image value you prefer
            }

            $all_services[$key] = $record;
        }
        $searchTerm = 67;
        // check user have muslim or not to out service haji
        if($full_data_user->per_information7 !== $this->muslimReligion->value or is_null($full_data_user->per_information7)){
            foreach ($all_services as $key => $absence) {
                if ($absence->absence_attendance_type_id == $searchTerm) {
                    array_splice($all_services, $key, 1);
                    break;
                }
            }
        }
//else{
//
//            $keysToRemove = [];
//
//            foreach ($all_services as $key => $absence) {
//                if ($absence->absence_attendance_type_id == $searchTerm  and isset($absence->last_update_date)  ) {
//                   //add 5 year
//                    $lastUpdateDate = Carbon::parse($absence->last_update_date);
//                    $updatedDate = $lastUpdateDate->addYears(5);
//                    $updatedDateString = $updatedDate->format('Y-m-d H:i:s');
//                    if($updatedDateString >= Carbon::now()){
//                        $keysToRemove[] = $key;
//                    }
//
//                }
//            }
//            foreach ($keysToRemove as $key) {
//                unset($all_services[$key]);
//            }
//            $all_services = array_values($all_services);
//        }

        //eligibal annula
        $status = $this->loginService->checkElgibalityOfAnnul($employee->person_id)->next_vac_start_date;

        if($status=="N"){
            $annual_leave = AppKeysProps::AnnuLeave_absence_type_id()->value;

            $keysToRemove = [];

            foreach ($all_services as $key => $absence) {
                if ($absence->absence_attendance_type_id == $annual_leave ) {
                    $keysToRemove[] = $key;
                }
            }
            foreach ($keysToRemove as $key) {
                unset($all_services[$key]);
            }
            $all_services = array_values($all_services);
        }
        //maragie_leave
        if($full_data_user->marital_status=="S"){
            $child_leave = AppKeysProps::ChildLeave_absence_type_id()->value;

            $keysToRemove = [];

            foreach ($all_services as $key => $absence) {
                if ($absence->absence_attendance_type_id == $child_leave ) {
                    $keysToRemove[] = $key;
                }
            }
            foreach ($keysToRemove as $key) {
                unset($all_services[$key]);
            }
            $all_services = array_values($all_services);
        }

        if($full_data_user->marital_status=="M"){
            $marige_leave = AppKeysProps::MargieLeave_absence_type_id()->value;

            $keysToRemove = [];

            foreach ($all_services as $key => $absence) {

                if ($absence->absence_attendance_type_id == $marige_leave ) {
                    if($absence->last_update_date  < Carbon::now()){
                        $keysToRemove[] = $key;
                    }

                }
            }
            foreach ($keysToRemove as $key) {
                unset($all_services[$key]);
            }
            $all_services = array_values($all_services);
        }

        // show services specific woman employee
        if ($full_data_user->sex =="F"){

        }else{
            $omoma = AppKeysProps::Omoma_absence_type_id()->value;
            $death_idah = AppKeysProps::Idah_absence_type_id()->value;

            $keysToRemove = [];

            foreach ($all_services as $key => $absence) {
                if ($absence->absence_attendance_type_id == $omoma || $absence->absence_attendance_type_id == $death_idah) {
                    $keysToRemove[] = $key;
                }
            }
            foreach ($keysToRemove as $key) {
                unset($all_services[$key]);
            }
            $all_services = array_values($all_services);
        }
        // if manager equal same admin_manger  special case
        foreach ($requested_notification as $key => $item) {


            if ( $item->approval_status== 'Approved' or  ($item->approval_status== 'Admin Mgr Approved' and $item->no_of_approvals=="2" ) or str_contains($item->approval_status,"Rejected")) {
                unset($requested_notification[$key]);
            }

            if ($item->admin_mgr_person_id == $item->mgr_person_id && $special_type_user_default == $this->Manger) {
                $special_type_user_default = $this->Manger;
            } else {
                $user_type = session()->get('user_type');
            }

        }

        $last_requested_to_play_notify =  $requested_notification->first();
        $requested_notification = array_values($requested_notification->toArray());
        $filtered_notification = array_filter($requested_notification, function ($item) {
            return !($item->absence_type == "Annual Leave" && $item->approval_status == "Pending Approval" && $item->taswiath_status != 1) && $item->empno;
        });
        $requested_notification = array_values($filtered_notification);

        $toggle_unauthorized_annual=null;
        foreach ($requested_notification as  $item) {
            if (isset($item->absence_type) && $item->absence_type === "Authorized Unpaid Leave" && $item->approval_status == "Pending Approval" ) {
                $toggle_unauthorized_annual = 1;
            }
        }
        $filtered_notifications = array_filter($requested_notification, function($item) use ($employee) {
            return !($item->mgr_person_id == $employee->person_id && $employee->employee_number == $item->empno);
        });

        $requested_notification = array_values($filtered_notifications);
        return view('frontend.profile-employee',compact('toggle_unauthorized_annual','special_type_user_default','last_requested_to_play_notify','requested_notification','status_request','user_type','absence_requests','employee','all_services','specail_services'));
    }

    public function servicesCategory(){
        $user_type =  session()->get('user_type');
        $employee = session()->get('employee');
        if($user_type!=$this->employeeSigniture){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);
        }

        $all_services_category =$this->specialSpecifhelper->GetAllServiceDifferent();

        return view('frontend.services-category',compact('requested_notification','all_services_category'));

    }
    public function certificateService(){}
    public function loanService(){
        $loan_services =  $this->mangerLogicService->LoanServices();
        foreach ($loan_services as $item){
            $item->service_type = 'loan service';
        }
        return view('frontend.services-sub_category',compact('loan_services'));
    }
    public function getAttributeSpecialService($flex_id,$service_type,$main_service_request_sub){
        $loan_attr =  $this->mangerLogicService->get_LoanRequest_service($flex_id);


// Translation array for segment_name
        $segmentTranslations = [
            "Bank Name" => "اسم البنك",
            "Loan Date" => "تاريخ القرض",
            "Issued Document" => "المستند المصدر",
            "Clearance Document from Bank" => "مستند التصفية من البنك",
            "Notes" => "ملاحظات"
        ];

        foreach ($loan_attr as $item) {
            if (isset($segmentTranslations[$item->segment_name])) {
                $item->ar_segment_name = $segmentTranslations[$item->segment_name];
            }
        }
        return view('frontend.services-sub_category_attribute',compact('loan_attr','service_type','flex_id','main_service_request_sub'));
    }


    public function InsuranceService(){}
    public function OtherService(){}
    public function LetterService(){}
}
