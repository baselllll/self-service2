<?php

namespace App\Http\Services;

use App\Http\Repository\EmployeeDetailsRepository;
use App\Http\Repository\LoginRepository;
use App\Http\Repository\MainOracleQueryRepo;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class LoginService
{
    protected $loginRepo;
    protected $detailsRepository;
    protected $mainOracleQueryRepo;

    public function __construct(LoginRepository $loginRepo, EmployeeDetailsRepository $detailsRepository , MainOracleQueryRepo $mainOracleQueryRepo)
    {
        $this->loginRepo = $loginRepo;
        $this->detailsRepository = $detailsRepository;
        $this->mainOracleQueryRepo = $mainOracleQueryRepo;
    }

    public function Login($employee_number)
    {
        return $this->loginRepo->login($employee_number);
    }


    public function getUserIpAddr(){
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    public function SendOtpService($otp,$person_id,$employee_number)
    {
        $otp = implode($otp);
        $ip = $this->getUserIpAddr();
        return $this->loginRepo->SendOtpService($otp,$person_id,$ip,$employee_number);
    }
    public function countLoginTimes($person_id,$times)
    {
        return $this->loginRepo->countLoginTimes($person_id,$times);
    }
    public function update_per_people_attr($otp,$newDateTime,$employee_number)
    {
        return $this->loginRepo->update_per_people_attr($otp,$newDateTime,$employee_number);
    }

    public function GetPersonID($employee_number)
    {
        return $this->loginRepo->GetPersonID($employee_number);
    }
    public function GetDetailsOfCustom($transaction_id)
    {
        return $this->loginRepo->GetDetailsOfCustom($transaction_id);
    }
     public function reg_users()
     {
            return $this->loginRepo->reg_users();
     }
    public function non_reg_users()
     {
            return $this->loginRepo->non_reg_users();
     }
    public function count_register_user()
     {
            return $this->loginRepo->count_register_user();
     }
     public function count_not_register_user()
     {
            return $this->loginRepo->count_not_register_user();
     }


    public function MangerInterface($mgr_person_id){
        return $this->mainOracleQueryRepo->MangerInterface($mgr_person_id);
    }
    public function UpdateUserData($data){
        return $this->mainOracleQueryRepo->UpdateUserData($data);
    }
    public function emp_quama_verified($emp_number,$quama_number){
        return $this->mainOracleQueryRepo->emp_quama_verified($emp_number,$quama_number);
    }
    public function deleteServiceData($transaction_id){
        return $this->mainOracleQueryRepo->deleteServiceData($transaction_id);
    }
    public function xxajmi_emp_reg_or_not($employee_number){
        return $this->mainOracleQueryRepo->xxajmi_emp_reg_or_not($employee_number);
    }
    public function xxajmi_register_new_user($p_empno,$p_mobile,$p_email,$p_iqama){
        return $this->mainOracleQueryRepo->xxajmi_register_new_user($p_empno,$p_mobile,$p_email,$p_iqama);
    }
    public function DelegationViewChecking(){
        return $this->mainOracleQueryRepo->DelegationViewChecking();
    }
    public function AdminMangerInterface($admin_mgr_person_id){
        return $this->mainOracleQueryRepo->AdminMangerInterface($admin_mgr_person_id);
    }
    public function TopMangInterface($top_mgmt_person_id){
        return $this->mainOracleQueryRepo->TopMangInterface($top_mgmt_person_id);
    }
    public function EmployeeInterface($EMPNO){
        return $this->mainOracleQueryRepo->EmployeeInterface($EMPNO);
    }
    public function GetReplacmentDetailsSpecificDepartment($person_id)
    {
        return $this->loginRepo->GetReplacmentDetailsSpecificDepartment($person_id);
    }
    public function storeDelegate($data)
    {
        return $this->loginRepo->storeDelegate($data);
    }
    public function deleteDelegate($delegate_id)
    {
        return $this->loginRepo->deleteDelegate($delegate_id);
    }
    public function getSpecificDelegate($delegate_id)
    {
        return $this->loginRepo->getSpecificDelegate($delegate_id);
    }
    public function updateDelegateData($delegate_id,$data)
    {
        return $this->loginRepo->updateDelegateData($delegate_id,$data);
    }
    public function GetDelegation($employee_number,$type_user)
    {
        return $this->loginRepo->GetDelegation($employee_number,$type_user);
    }

    public function GetOtherMangerDepartment($person_id)
    {
        return $this->loginRepo->GetOtherMangerDepartment($person_id);
    }
 public function GetRestDelegationServiceThatNotChoose($employee_number)
    {
        return $this->loginRepo->GetRestDelegationServiceThatNotChoose($employee_number);
    }

    public function GetAbsenceAttendaceTypeID()
    {
        try {
            $services = $this->detailsRepository->GetAbsenceAttendaceTypeID();
            $uniqueArray = [];
           foreach ($services as $item) {
                if (!in_array($item->absence_attendance_type_id, array_column($uniqueArray, "absence_attendance_type_id"))) {
                    $uniqueArray[] = $item;
                }
            }
          $services = $uniqueArray;
            if (isset($services) and count($services) > 0) {
                return $services;
            } else {
                Alert::error("message,'not exist any user that have employee number");
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }


    public function GetAbsenceManagment($person_id,$employee_number,$user_type=null)
    {
        return $this->detailsRepository->GetnotificationOfEmployee($person_id,$employee_number,$user_type);
    }
    public function GetAnnualServiceForTawsaya()
    {
        return $this->detailsRepository->GetAnnualServiceForTawsaya();
    }
    public function taswaya_status_change($transaction_id,$note,$type)
    {
        return $this->detailsRepository->taswaya_status_change($transaction_id,$note,$type);
    }
    public function delete_taswaya($transaction_id)
    {
        return $this->detailsRepository->delete_taswaya($transaction_id);
    }
    public function checkElgibalityOfAnnul($person_id)
    {
        return $this->detailsRepository->checkElgibalityOfAnnul($person_id);
    }

public function getRecordOfHRTransactionStep($transaction_id)
    {
        return $this->detailsRepository->getRecordOfHRTransactionStep($transaction_id);
    }

    public function GetALLDynamicformTemplate()
    {
        return $this->detailsRepository->GetALLDynamicformTemplate();
    }

    public function GetEmolyeeDataFromPersonId($person_id){
        return $this->detailsRepository->GetEmolyeeDataFromPersonId($person_id);
    }
    public function GetCountSupervisor($employee_number){
        return $this->detailsRepository->GetCountSupervisor($employee_number);
    }
    public function update_role_for_same_manger_admin($status,$admin_mgr_status,$transaction_id){
         $this->detailsRepository->update_role_for_same_manger_admin($status,$admin_mgr_status,$transaction_id);
    }
    public function GetEmolyeeDataFromEmployee($emp_number){
        return $this->detailsRepository->GetEmolyeeDataFromEmployee($emp_number);
    }

    public function GetPeriodTime($service_type_id){

        return $this->detailsRepository->GetPeriodTime($service_type_id);
    }
}
