<?php

namespace App\Http\Services;

use App\Http\Repository\EmployeeDetailsRepository;
use App\Http\Repository\LoginRepository;
use App\Http\Repository\MainOracleQueryRepo;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class MangerLogicService
{
    public function __construct(MainOracleQueryRepo $mainOracleQueryRepo)
    {
        $this->mainOracleQueryRepo = $mainOracleQueryRepo;
    }

    public function GetnotificationOfManger($mgr_person_id,$emp_number=null){
        return $this->mainOracleQueryRepo->GetnotificationOfManger($mgr_person_id,$emp_number);
    }
    public function LoanServices(){
        return $this->mainOracleQueryRepo->LoanServices();
    }
    public function get_LoanRequest_service($flex_id){
        return $this->mainOracleQueryRepo->get_LoanRequest_service($flex_id);
    }
 public function GetnotificationOfEmployee($emp_number=null,$person_id=null){
        return $this->mainOracleQueryRepo->GetnotificationOfEmployee($emp_number,$person_id);
    }

    public function GetReplacmentDetailsSpecificDepartment($person_id=null){
        return $this->mainOracleQueryRepo->GetReplacmentDetailsSpecificDepartment($person_id);
    }
    public function GetPhoneEmpFromPersonId($person_id=null){
        return $this->mainOracleQueryRepo->GetPhoneEmpFromPersonId($person_id);
    }


    public function ApproveRequest($notif_id,$transaction_id,$note,$type){
        return $this->mainOracleQueryRepo->ApproveRequest($notif_id,$transaction_id,$note,$type);

    }
    public function RejectRequest($notif_id,$transaction_id,$note,$type){
        return    $this->mainOracleQueryRepo->RejectRequest($notif_id,$transaction_id,$note,$type);
    }
    public function GetEOS(){
        return $this->mainOracleQueryRepo->GetEOS();
    }
}
