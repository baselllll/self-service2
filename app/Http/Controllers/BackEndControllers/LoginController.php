<?php

namespace App\Http\Controllers\BackEndControllers;

use App\Enums\AppKeysProps;
use App\Helper\SmsVerifyHelper;
use App\Helper\TaswayaLayer;
use App\Helper\UploadDocumnetAcrchive;
use App\Http\Controllers\Controller;
use App\Http\Repository\EmployeeDetailsRepository;
use App\Http\Repository\MainOracleQueryRepo;
use App\Http\Requests\LoginRequest;
use App\Http\Services\DetailsEmployeeService;
use App\Http\Services\LoginService;
use App\Http\Services\MangerLogicService;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Knp\Snappy\Pdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;


class LoginController extends Controller
{
    protected $loginService;
    protected $user_type_manger;
    protected $user_type_admin_manger;
    protected $user_type_top_manger;
    protected $user_type_employee;
    protected $smsVerifyHelper;
    protected $mangerLogicService;
    public function __construct(TaswayaLayer $taswayaLayer,LoginService $loginService,SmsVerifyHelper $smsVerifyHelper,MangerLogicService $mangerLogicService)
    {
        $this->loginService = $loginService;
        $this->smsVerifyHelper = $smsVerifyHelper;
        $this->mangerLogicService = $mangerLogicService;
        $this->user_type_manger = AppKeysProps::Manger();
        $this->user_type_admin_manger = AppKeysProps::AdminManger();
        $this->user_type_top_manger = AppKeysProps::TopManger();
        $this->user_type_employee = AppKeysProps::UserTypeEmployee();
        $this->taswayaEmp = $taswayaLayer;
    }



    public function delegation_check_manager_or_not($person_id){
        $mangers=  $this->loginService->DelegationViewChecking()->get();
        $filteredArray = array_filter($mangers->toArray(), function ($person) use($person_id) {
            return $person->person_id == $person_id;
        });
        if (count($filteredArray)>0) {
            return true;
        } else {
            return false;
        }



    }

    public  function Login(LoginRequest $request)
    {
        try {
            $out_modal_information = 1;
            $employee = $this->loginService->Login($request->employee_number);
            $employee_full_data = $this->loginService->GetPersonID($request->employee_number);
            //get the user Ip to check that user send otp from your device or what
            $current_ip = $this->loginService->getUserIpAddr();
            // check otp that sent to employee expire or not and same device (ip)
            if(isset($employee_full_data->attribute2) and  $employee_full_data->attribute2 == $request->verification_code and $employee_full_data->attribute3 > Carbon::now() and  ($employee_full_data->attribute4 == $current_ip or $employee_full_data->attribute4 ==null)){
                if(isset($employee)){
                    // $out_modal_information  that to detect show information for user login first

                    // open 4 interface employee,manager ,admin_mgr , top_manger
                    $manger =  $this->loginService->MangerInterface($employee[0]->person_id)->first();
                    $admin_mgr =  $this->loginService->AdminMangerInterface($employee[0]->person_id)->first();
                    $top_mgmt =  $this->loginService->TopMangInterface($employee[0]->person_id)->first();
                    $employee = $employee[0];
                    $employee_data = $employee;
                    Session::put('employee',  $employee);
                    Session::put('employee_data',  $employee_data);
                    request()->session()->save();

                    if(isset($manger)){
                        $check_supervisor_requesdted = $this->loginService->CheckSuperVisorToRequestService($request->employee_number)->super_status;
                        if ($check_supervisor_requesdted== "Y"){
                            Session::put('super_visor_can_request',  true);
                        }
                        if(in_array($employee->employee_number,$this->taswayaEmp->Employee_Avalabile())==true){
                            Session::put('taswaya_emp',  true);
                            request()->session()->save();
                        }else{
                            Session::forget('taswaya_emp');
                        }
                        if (isset($admin_mgr)){
                            $user_type = $this->user_type_admin_manger->value;
                            //that put because if same manger is admin_mng   ( yhaa -- emp(hr) ) same supervisor same admin_manager
                            //cause of same manger_person_id == admin_person_id
                            if ($check_supervisor_requesdted== "Y"){
                                Session::put('super_visor_can_request_admin_manger',  true);
                            }
                            $special_type_user_default =  $this->user_type_manger->value;
                            Session::put('user_type',  $user_type);
                            Session::put('delegated_type',  "delegated");
                            Session::put('special_type_user_default',  $special_type_user_default);
                            request()->session()->save();
                            return redirect()->route('home',['isFirstLogin'=>$out_modal_information]);
                        }else{
                            $user_type = $this->user_type_manger->value;
                            Session::put('user_type',  $user_type);
                            Session::put('delegated_type',  "delegated");
                            return redirect()->route('home',['isFirstLogin'=>$out_modal_information]);
                        }

                    }
                    elseif (isset($admin_mgr)){
                        $user_type = $this->user_type_admin_manger->value;
                        $check_supervisor_requesdted = $this->loginService->CheckSuperVisorToRequestService($request->employee_number)->super_status;
                        if ($check_supervisor_requesdted== "Y"){
                            Session::put('super_visor_can_request_admin_manger',  true);
                        }
                        Session::put('user_type',  $user_type);
                        Session::put('delegated_type',  "delegated");
                       if(in_array($employee->employee_number,$this->taswayaEmp->Employee_Avalabile())==true){
                            Session::put('taswaya_emp',  true);
                        }else{
                            Session::forget('taswaya_emp');
                        }
                        request()->session()->save();
                        return redirect()->route('home',['isFirstLogin'=>$out_modal_information]);
                    }
                    elseif (isset($top_mgmt)){
                        if(in_array($employee->employee_number,$this->taswayaEmp->Employee_Avalabile())==true){
                            Session::put('taswaya_emp',  true);
                            request()->session()->save();
                        }else{
                            Session::forget('taswaya_emp');
                            Session::forget('special_type_user_default');
                        }
                        $user_type = $this->user_type_top_manger->value;
                        Session::put('user_type',  $user_type);
                        Session::put('delegated_type',  "delegated");
                        Session::forget('special_type_user_default');
                        request()->session()->save();

                        return redirect()->route('home',['isFirstLogin'=>$out_modal_information]);
                    }
                    else {
                        if(in_array($employee->employee_number,$this->taswayaEmp->Employee_Avalabile())==true){
                            Session::put('taswaya_emp',  true);
                            request()->session()->save();
                        }else{
                            Session::forget('taswaya_emp');
                        }


                        $user_type = $this->user_type_employee->value;
                        Session::put('user_type',  $user_type);
                        Session::put('delegated_type',  "not_delegated");
                        request()->session()->save();
                        return redirect()->route('home',['isFirstLogin'=>$out_modal_information]);
                    }

                }
            }
            else{
                Alert::error("ERROR",__('messages.login_error'));
                return redirect()->back();
            }

        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.login_error'));
            return redirect()->route('login-page');
        }
    }

    public function index(){
        $user_type =  session()->get('user_type');
        $employee = session()->get('employee');
        if(isset(session()->get('employee')->person_id)){
            Alert::success("SUCCESS",__('messages.logined_now'));
            return redirect('home');
        }
        return view('frontend.login');
    }
    public function send_otp_for_check_before(Request $request){
        $currentDateTime = Carbon::now();
        $newDateTime = $currentDateTime->addSeconds(env('SECOND_OTP'));

        $employee_full_data = $this->loginService->GetPersonID($request->employee_number);
        $sms = new SmsVerifyHelper();
        $randomNumbers = array();
        for ($i = 0; $i < 5; $i++) {
            $randomNumbers[] = rand(1, 9);
        }
        $randomNumbers = implode($randomNumbers);
        $otp = $randomNumbers;
        if (substr($request->phone_number, 0, 1) === '0') {
            $request['phone_number'] = '966' . substr($request->phone_number, 1);
        }
        $message = "Verification is";
        if (strlen($request['phone_number']) == 12) {
            $sms->sendSMS($request['phone_number'], $message, $otp);
            $this->loginService->update_per_people_attr($otp,$newDateTime,$request->employee_number);
        }
        return response()->json(['sended'=>1]);
    }
    public function send_email_for_check_before(Request $request){
        $currentDateTime = Carbon::now();
        $newDateTime = $currentDateTime->addSeconds(env('SECOND_OTP'));

        $randomNumbers = array();
        for ($i = 0; $i < 5; $i++) {
            $randomNumbers[] = rand(1, 9);
        }
        $randomNumbers = implode($randomNumbers);
        $otp = $randomNumbers;
        DB::statement("BEGIN apps.xxajmi_send_otp_email_direct('$request->employee_number','$request->email_employee', '$otp'); END;");
        $this->loginService->update_per_people_attr($otp,$newDateTime,$request->employee_number);
        return response()->json(['sended'=>1]);
    }


    public function send_otp_for_check_otp(Request $request){
        $employee_full_data = $this->loginService->GetPersonID($request->employee_number);
        $expire_date = $employee_full_data->attribute7;
        if(isset($employee_full_data->attribute6) and $employee_full_data->attribute6==$request->otp and $expire_date > Carbon::now()) {
            return response()->json(['verified'=>'1']);
        }else{
            return response()->json(['verified'=>'0']);
        }
    }
    public function send_otp_email_for_check_otp(Request $request){
        $employee_full_data = $this->loginService->GetPersonID($request->employee_number);
        $expire_date = $employee_full_data->attribute7;
        if(isset($employee_full_data->attribute6) and $employee_full_data->attribute6==$request->otp and $expire_date > Carbon::now()) {
            return response()->json(['verified'=>'1']);
        }else{
            return response()->json(['verified'=>'0']);
        }
    }
    public function send_otp_for_register(Request $request){
        try {

            $currentDateTime = Carbon::now();
            $newDateTime = $currentDateTime->addSeconds(env('SECOND_OTP'));

            $employee_full_data = $this->loginService->GetPersonID($request->employee_number);
            $person_id = $employee_full_data->person_id;
            if (substr($request->phone_number, 0, 1) === '0') {
                $request['phone_number'] = '966' . substr($request->phone_number, 1);
            }
            $sms = new SmsVerifyHelper();
            $randomNumbers = array();
            for ($i = 0; $i < 5; $i++) {
                $randomNumbers[] = rand(1, 9);
            }
            $randomNumbers = implode($randomNumbers);
            $otp = $randomNumbers;
            $message = "Verification is";
            if(isset($employee_full_data->attribute7) and $employee_full_data->attribute7 > Carbon::now()) {
                return response()->json(['results'=>__('messages.verified_phone_number'),'verified'=>0]);
            }else{
                if (strlen($request['phone_number']) == 12) {
                    $sms->sendSMS($request['phone_number'], $message, $otp);
                }
                if(isset($request->email)){
                    DB::statement("BEGIN apps.xxajmi_send_otp_email_direct('$request->employee_number','$request->email', '$otp'); END;");
                }
                $this->loginService->update_per_people_attr($otp,$newDateTime,$request->employee_number);
                return response()->json(['results'=>__('messages.verified_phone_number'),'verified'=>1]);
            }
        }catch (\Exception $exception){
            return response()->json(['results'=>__('messages.verified_phone_number'),'verified'=>0]);
        }

    }
    public function UpdateUser_view(){
        return view('frontend.update_user_data');
    }
    public function UpdateUser(Request $request){
        try {
            $email_pattern = '/^[a-zA-Z0-9._%+-]+@(ajmi\.com|alajmicompany\.com)$/';
            if (preg_match($email_pattern, $request->email)) {
                Alert::warning("WARNING",__('messages.personnal_validate'));
                return redirect('login');
            }
            $result_data = $this->loginService->xxajmi_emp_reg_or_not($request->emp_number);
            if ($result_data->status_req=="1"){
                Alert::warning("WARNING",__('messages.register_before'));
                return redirect('login');
            }else{
//                $mainRepo = new MainOracleQueryRepo();
                $employee_full_data = $this->loginService->GetPersonID($request->emp_number);
//                $employee_phone_number =  $mainRepo->GetPhoneEmpFromPersonId($employee_full_data->person_id)[0]->phone_number;
//                $request['phone_number']=$employee_phone_number;
                $this->loginService->UpdateUserData($request->all());
                Alert::success("SUCCESS", __('messages.update_success'));
                return redirect('login');
//                $expire_date = Carbon::createFromFormat("Y-m-d H:i:s",$employee_full_data->attribute7);
//                if(isset($employee_full_data->attribute6) and $employee_full_data->attribute6==$request->verification_code_number and $expire_date > Carbon::now()) {
//                    $this->loginService->UpdateUserData($request->all());
//                    Alert::success("SUCCESS", __('messages.update_success'));
//                    return redirect('login');
//                }else{
//                    Alert::error("ERROR",__('messages.login_error'));
//                    return redirect('login');
//                }
            }

        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.login_error'));
            return redirect('login');
        }
    }
    public function emp_quama_verified(Request $request){
        try {
            $emp_number = $request->emp_number_input;
            $iqama_number = $request->iqama_number_input;
            $data =  $this->loginService->emp_quama_verified($emp_number,$iqama_number);
            $result_data = $this->loginService->xxajmi_emp_reg_or_not($emp_number)->status_req;
            if (count($data) === 1){
                return response()->json(['results'=>__('messages.verified_iqam_emp'),'verified'=>1,'registered_before'=>$result_data]);
            }else{
                return response()->json(['results'=>__('messages.out_not_verifed_iqama'),'verified'=>0,'registered_before'=>$result_data]);
            }

        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.login_error'));
        }
    }
    public function DeleteService($transaction_id){
        try {
            $manger =  $this->loginService->deleteServiceData($transaction_id);
            Alert::success("SUCCESS",__('messages.deleted_success'));
            return redirect()->to("home");
        }catch (\Exception $exception){
            Alert::error("ERROR",__('messages.login_error'));
        }
    }


    public function logout(){
        $employee = session()->get('employee');
          $this->loginService->updateOnPerPeopleIp($employee->person_id);
          session()->flush();
          session()->invalidate();

          return redirect()->route('login-page');
    }

    public function SendOtp(Request $request){
        if(is_null($request->emp_number)){
            return response()->json([
                'results' => "Must Enter Employee File Number",
            ]);
        }
        $employee_number = $request->emp_number;
        try {
            $employee = $this->loginService->GetPersonID($request->emp_number);
            $person_id = $employee->person_id;
            //create otp random
            $randomNumbers = array();
            for ($i = 0; $i < 5; $i++) {
                $randomNumbers[] = rand(1, 9); // Generates a random number between 1 and 100
            }
            $otp = $randomNumbers;
            $result = $this->loginService->SendOtpService($otp,$person_id,$employee_number,$employee);
            //more validation emp have phone or email or existEmp
            if ($result == 'device_is_opend') {
                return response()->json([
                    'results' => __('messages.device_is_opend')
                ]);
            }
            if ($result == 'device_is_opend') {
                return response()->json([
                    'results' => __('messages.success_sent_email')
                ]);
            }
            if ($result == 'new_user_messsage') {
                return response()->json([
                    'results' => __('messages.new_user_messsage')
                ]);
            }
            if ($result == 'both_mail_phone') {
                return response()->json([
                    'results' => __('messages.both_mail_phone')
                ]);
            }
            if ($result == 'success_sent_phone') {
                return response()->json([
                    'results' => __('messages.otp_sent_success')
                ]);
            } elseif ($result == 'emp_not_exist') {
                return response()->json([
                    'results' => __('messages.emp_not_exist')
                ]);
            } elseif ($result == 'emp_not_phone_number' || $result == 'failed_phone') {
                return response()->json([
                    'results' => __('messages.emp_not_phone_number')
                ]);
            } elseif ($result == 'failed_mail') {
                return response()->json([
                    'results' => __('messages.emp_not_mail')
                ]);
            } elseif ($result == 'opt_sent_already') {
                return response()->json([
                    'results' => __('messages.otp_already_sent'),
                    'hide_button' => 1

                ]);
            } else {
                return response()->json([
                    'results' => __('messages.kindly_return_to_it')
                ]);
            }


        }catch (\Exception $exception){
            return response()->json([
                'results' => __('messages.emp_not_exist')
            ]);
        }
    }
    public function GetEmployeeInformation(){
        $employee = session()->get('employee_data');
        $user_type =  session()->get('user_type');
        if($user_type!=$this->user_type_employee->value){
            $requested_notification =  $this->mangerLogicService->GetnotificationOfManger($employee->person_id);
        }else{
            $requested_notification =  $this->mangerLogicService->GetnotificationOfEmployee($employee->employee_number);

        }
        return view('frontend.employee-information',compact('employee','requested_notification'));
    }


    public function generatePdf($transaction_id){
        $getDetailsOfCustom = $this->loginService->GetDetailsOfCustom($transaction_id);
        return view('frontend.report-pdf',['data' => $getDetailsOfCustom]);

    }
    public function generateReportDisplayData(Request $request){
        $encryptedData = $request->data_decrypted;
        if(isset($encryptedData) and  $encryptedData === "kajska656sasnjiujiasdsw58565asmnhwuemxsa32"){
            $reg_users = $this->loginService->reg_users();
            $non_reg_users= $this->loginService->non_reg_users();
            $count_register = $this->loginService->count_register_user()->no_user;
            $count_not_register =$this->loginService->count_not_register_user()->no_user;
            return view('frontend.report-register-user',compact('reg_users','non_reg_users','count_register','count_not_register'));
        }else{
            return view('frontend.NotAllowedLocation');
        }
    }
    public function EditOnTemplate($transition_id){
        try {
            $mainRepo = new MainOracleQueryRepo();
            $UploadDocumnetAcrchive = new UploadDocumnetAcrchive();
            $EmployeeDetailsRepository = new EmployeeDetailsRepository();
            $record_approved =  \DB::table("xxajmi_notif")->where('transaction_id',$transition_id)->where('no_of_approvals', '=', 3)->where('approval_status','Approved')->first();
            $lastRecordApproved_Two_Approvals   =  \DB::table("xxajmi_notif")->where('transaction_id',$transition_id)->where('no_of_approvals', '=', 2)->where('Approval_status', '=', "Admin Mgr Approved")->first();
            if (isset($record_approved)){
                $record_approved = $record_approved;
            }
            if (isset($lastRecordApproved_Two_Approvals)){
                $record_approved = $lastRecordApproved_Two_Approvals;
            }
            $employee_data = $mainRepo->GetPersonID($record_approved->empno);

            $GetPersonNATIONALITY_Contract_hire = $mainRepo->GetPersonNATIONALITY($record_approved->empno);

            $GetPersonLastRejoinDate = $mainRepo->GetPersonLastRejoin($employee_data->person_id);
            if (isset($GetPersonLastRejoinDate[0])){
                $GetPersonLastRejoinDate  = $GetPersonLastRejoinDate[0]->last_rejoin_date;
            }else{
                $GetPersonLastRejoinDate = '';
            }
            $GetPersonAvailableCompanyDate = $mainRepo->GetPersonAvailableCompanyDate($record_approved->empno);
            $employee_data_details = $mainRepo->GetEmployeeUsingFileNumber($record_approved->empno)[0];
            $employee_phone_number = $mainRepo->GetPhoneEmpFromPersonId($employee_data->person_id)[0]->phone_number;
            $manger_data = $mainRepo->GetEmolyeeDataFromPersonId($record_approved->mgr_person_id);
            $admin_mgr_data = $mainRepo->GetEmolyeeDataFromPersonId($record_approved->admin_mgr_person_id);
            $top_mgr_data = $mainRepo->GetEmolyeeDataFromPersonId($record_approved->top_mgmt_person_id);
            $absence_start_date = $record_approved->absence_start_date;
            $absence_end_date = $record_approved->absence_end_date;
            $creation_date = $record_approved->creation_date;
            $admin_mgr_action_date = $record_approved->admin_mgr_action_date;
            $hire_date = $GetPersonNATIONALITY_Contract_hire->hire_date;
            $absence_start_date = strtotime($absence_start_date);
            $absence_end_date = strtotime($absence_end_date);
            $creation_date = strtotime($creation_date);
            $hire_date = strtotime($hire_date);
            $GetPersonLastRejoinDate = strtotime($GetPersonLastRejoinDate);
            $admin_mgr_action_date = strtotime($admin_mgr_action_date);
            $cleanDateString = substr($absence_start_date, 0, strpos($absence_start_date, 'GMT'));
            $originalDate = Carbon::parse($cleanDateString);
            $formattedDate = $originalDate->format('d-M-Y');
            $formattedDate = Carbon::createFromFormat('d-M-Y', $formattedDate)->format('d-M-Y');
            $number_accural = $EmployeeDetailsRepository->accrued_balance($record_approved->empno,$formattedDate);
            if($record_approved->absence_type == "Emergency Leave"){
                $service_type_emr="*";
                $service_type_regular="";
            }else{
                $service_type_regular="*";
                $service_type_emr="";
            }


            $template = new TemplateProcessor("vacation3.docx");


            // Replace placeholders with data
            $data = [
                'full_name' => $record_approved->requestor,
                'nationality' => $GetPersonNATIONALITY_Contract_hire->nationality,
                'emp_number' => $employee_data->employee_number,
                'VFD' => date("d", $absence_start_date),
                'VFM' => date("m", $absence_start_date),
                'VDY' => date("Y", $absence_start_date),
                'VED' => date("d", $absence_end_date),
                'VEM' => date("m", $absence_end_date),
                'VEY' => date("Y", $absence_end_date),
                'Re' => $service_type_regular,//regular
                'Em' => $service_type_emr,//emergancy
                'Ot' => '',//other
                'address' => $employee_data->country_of_birth,
                'telephone' => $employee_phone_number,
                'work_location' => $employee_data_details->location,
                'job_title' => $employee_data_details->position,
                'note' => 'congats',
                'crD' => date("d", $creation_date), //creation_date day
                'crM' => date("m", $creation_date),//creation_date month
                'cDY' => date("Y", $creation_date),//creation_date year
                'emp_name' => explode(" ",$record_approved->requestor)[0]  . " " . explode(" ",$record_approved->requestor)[1],
                'replacement_name_number' =>explode(" ",$record_approved->replacement_name)[0]  . " " .explode(" ",$record_approved->replacement_name)[1]  . ' - ' . $record_approved->replacement_no,
                'manager_name' => explode(" ",$record_approved->approver)[0] . " " . explode(" ",$record_approved->approver)[1],
                'mA' => ($record_approved->mgr_approval_status =="Approved" ) ? '*':'' ,//manager approve
                'mR' => ($record_approved->mgr_approval_status =="Rejected" ) ? '*':'',//manager reject
                'rA' => '*',//replacment approve
                'rR' => '',
                'Dy' => ($record_approved->absence_type =="Annual Leave" ) && ($record_approved->admin_mgr_approval_status=="Approved") ? '*':'',//requested vacation due yes
                'Dn' => isset($record_approved->record_approved) ? '*' : '',
                'Rn' => isset($record_approved->replacement_name) ? '*' : '',
                'Ry' => isset($record_approved->replacement_name) ? '*' : '',
                'Contract_per' => $GetPersonNATIONALITY_Contract_hire->contract_duration,
                'accNo' => ($record_approved->absence_type =="Annual Leave" )  ? $number_accural:'',
                'adminMng_name' => explode(" ",$admin_mgr_data->full_name)[0]  . " " .explode(" ",$admin_mgr_data->full_name)[1],
                'adminMng_note' => '',
                'AmD' =>  date("d", $admin_mgr_action_date),//duration
                'AmM' =>  date("m", $admin_mgr_action_date),
                'AmY' =>  date("Y", $admin_mgr_action_date),
                'DD' =>  $GetPersonAvailableCompanyDate->days,
                'DM' =>  $GetPersonAvailableCompanyDate->months,
                'DY' =>  $GetPersonAvailableCompanyDate->years,
                'HD' =>  date("d", $hire_date),
                'HM' =>  date("m", $hire_date),
                'HY' =>  date("Y", $hire_date),
                'LD' =>  date("d", $GetPersonLastRejoinDate),
                'LM' =>  date("m", $GetPersonLastRejoinDate),
                'LY' =>  date("Y", $GetPersonLastRejoinDate),
                'topMng_name' => explode(" ",$top_mgr_data->full_name)[0] . " " . explode(" ",$top_mgr_data->full_name)[1],
                'TA' => ($record_approved->top_management_approval_status =="Approved" ) ? '*':'',
                'TR' => ($record_approved->top_management_approval_status =="Rejected" ) ? '*':''
            ];
            foreach ($data as $key => $value) {
                $template->setValue($key, $value);
            }
            $employeeNumber = $record_approved->empno;
            $folderPath = "documents/$employeeNumber/";

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $Current_date = Carbon::now()->format('d-m-Y');
            $outputFileName_local = "{$folderPath}template_sshr_{$Current_date}.html";
            $file_output = "template_sshr_{$Current_date}.html";
            $file_output_pdf= "template_sshr_{$Current_date}.pdf";
            $file_output_pdf_path= "{$folderPath}template_sshr_{$Current_date}.pdf";

            $template->saveAs($outputFileName_local);
            $phpWord = IOFactory::load($outputFileName_local);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            $htmlContent = $htmlWriter->getContent();

            // Modify the HTML content to set the table border color to white
            $htmlContent = str_replace('td {border: 1px solid black;}', 'td {border-color: white;}', $htmlContent);
            $htmlContent = str_replace('* {font-family: Arial; font-size: 11pt;}', '* {font-family: cursive; font-size: 8pt;}', $htmlContent);


// Save the sanitized HTML back to the file
            file_put_contents($outputFileName_local, $htmlContent);

// Generate PDF from the sanitized HTML
            $pdf = SnappyPdf::loadfile($outputFileName_local);
            $pdf->save($file_output_pdf_path);
// Assuming $record_approved->empno, $file_output_pdf_path, and $file_output_pdf are defined elsewhere
            $UploadDocumnetAcrchive->upload($record_approved->empno, $file_output_pdf_path, $file_output_pdf);

        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
}
