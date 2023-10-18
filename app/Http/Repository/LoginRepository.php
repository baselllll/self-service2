<?php

namespace App\Http\Repository;

use App\Helper\SmsVerifyHelper;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use RealRashid\SweetAlert\Facades\Alert;

class LoginRepository extends MainOracleQueryRepo
{
    public function __construct(SmsVerifyHelper $smsVerifyHelper)
    {
        $this->smsVerifyHelper = $smsVerifyHelper;
    }

    public function login($employee_number){
        DB::beginTransaction();
        try {
            $employee_data = $this->GetEmployeeUsingFileNumber($employee_number);
            if (isset($employee_data) and count($employee_data)>0){
              return $employee_data;
            }else{
                Alert::error("message,not exist any user that have employee number");
            }
        }catch (\Exception $exception)
        {
            return $exception->getMessage();
        }

    }

    public function SendOtpService($otp,$person_id,$ip,$employee_number,$employee=null){
        $currentDateTime = Carbon::now();
        $flag_check_phone_success=null;
        $flag_check_email_success=null;
        $newDateTime = $currentDateTime->addSeconds(env('SECOND_OTP'));

//        if ($employee->attribute4 !== $ip and $employee->attribute4 !=null and $employee->attribute8 !=null){
//            return "device_is_opend";
//        }else{
//            DB::statement("UPDATE HR.PER_ALL_PEOPLE_F
//                 SET  attribute8='1'
//                 WHERE person_id = $person_id");
//        }

        $result_data = $this->xxajmi_emp_reg_or_not($employee_number);
        if ($result_data->status_req=="0"){
            return "new_user_messsage";
        }
        try {
           $check_count_employee= $this->GetEmpActive($person_id);
           if($check_count_employee[0]->count_data == 0){
               return "emp_not_exist";
           }

           $data_phone= $this->GetPhoneEmpFromPersonId($person_id);
           $email_employee= $this->GetEmailEmployee(request()->emp_number);

            if($data_phone[0]->attribute9 >= env('LoginedTime')){
                DB::statement("BEGIN apps.xxajmi_send_otp_email($person_id, '$otp'); END;");
                $updateQuery2 = "UPDATE HR.PER_ALL_PEOPLE_F
                 SET attribute2 = '$otp', attribute3 ='$newDateTime', attribute4='$ip'
                 WHERE person_id = $person_id";
                 DB::statement($updateQuery2);
                return "exceed_login_time";
            }

            if(isset($data_phone) and count($data_phone) > 0) {
                if ($data_phone[0]->attribute3 < Carbon::now()) {
                    if (!isset($data_phone[0]->phone_number)) {
                        return "emp_not_phone_number";
                    }
                    else {
                        try {
                            $phone_number = $data_phone[0]->phone_number;
                            if (count($data_phone) > 0) {
                                if (substr($phone_number, 0, 1) === '0')
                                    $cur_zero_number = '966' . substr($phone_number, 1);
                                if (strlen($cur_zero_number) == 12) {
                                    $result = $this->smsVerifyHelper->sendSMS(
                                        $cur_zero_number,
                                        trans('messages.OtpSms_Message_before') . "" . trans('messages.OtpSms_Message_after'),$otp
                                    );
                                }
                            }
                            $flag_check_phone_success  = "success_sent_phone";
                        } catch (\Exception $exception) {
                            return "failed_phone";
                        }
                    }
                }  else{
                    return "opt_sent_already";
                }

            }
            if (isset($data_phone) and count($email_employee) > 0){
                try {
                    DB::statement("BEGIN apps.xxajmi_send_otp_email($person_id, '$otp'); END;");
                    $flag_check_email_success  = "success_sent_email";
                }catch (\Exception $exception){
                    return "failed_mail";
                }
            }

            $emp_to_get_attr = DB::table("HR.PER_ALL_PEOPLE_F")->where('person_id',$person_id)->first();
            $times_loginNo = $emp_to_get_attr->attribute9;
            $new_times_login = ++$times_loginNo;
            $updateQuery = "UPDATE HR.PER_ALL_PEOPLE_F
                 SET attribute2 = '$otp', attribute3 ='$newDateTime', attribute4='$ip',attribute9='$new_times_login'
                 WHERE person_id = $person_id";

            if (isset($flag_check_email_success) && isset($flag_check_phone_success)) {
                DB::statement($updateQuery);
                return "both_mail_phone";
            } elseif (isset($flag_check_email_success)) {
                DB::statement($updateQuery);
                return $flag_check_email_success;
            } elseif (isset($flag_check_phone_success)) {
                DB::statement($updateQuery);
                return $flag_check_phone_success;
            }
        }catch (\Exception $exception){
          return $exception->getMessage();
        }
    }

    public function countLoginTimes($person_id,$times){
        try {
            return DB::statement("UPDATE HR.PER_ALL_PEOPLE_F
            SET ATTRIBUTE5='$times'
            WHERE person_id = '$person_id'");
        } catch (QueryException $exception) {
            DB::rollBack();
        }
    }

    public function update_per_people_attr($otp,$newDateTime,$employee_number){

        try {
            return DB::statement("UPDATE HR.PER_ALL_PEOPLE_F
                 SET attribute6 = '$otp', attribute7 ='$newDateTime'
                 WHERE employee_number = '$employee_number'");
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function storeDelegate($data){
        $absence_type = json_encode($data['selectedOptions']);
        try {
            $delegate_id = DB::select("select xxajmi_delegate_s.NEXTVAL from dual")[0]->nextval;
            DB::table('APPS.XXAJMI_DELEGATE_REQUESTS')->insert([
                "delegate_id"=>$delegate_id,
                "delegate_to_date"=>$data['delegate_to_date'],
                "delegate_from_date"=>$data['delegate_from_date'],
                "delegate_to_emp"=>$data['delegate_to_emp'],
                "delegate_from_emp"=>$data['delegate_from_emp'],
                "comments"=>$data['comments'],
                "absence_type_ar" => DB::raw("TO_CLOB('" . $absence_type . "')")
            ]);
            DB::statement("BEGIN xxajmi_delegation_notif($delegate_id); END;");
            return "true";
        }catch (\Exception $exception){
            DB::rollBack();
        }
    }
    public function GetDelegation($employee_number,$type_user){
        return DB::table("XXAJMI_DELEGATE_REQUESTS")
            ->select("DELEGATE_FROM_EMP", "DELEGATE_TO_EMP", "DELEGATE_FROM_DATE", "DELEGATE_TO_DATE", "DELEGATION_STATUS", "DELEGATE_ID", "COMMENTS", DB::raw("TO_CLOB(ABSENCE_TYPE_AR) AS ABSENCE_TYPE_AR"))
            ->where(function ($query) use ($employee_number,$type_user) {
                if ($type_user=="manger"){
                    return $query->where("DELEGATE_FROM_EMP", '=', $employee_number);
                }
                return $query->where("DELEGATE_To_EMP", '=', $employee_number);
            })
            ->get();

    }
    public function deleteDelegate($delegate_id){
      return DB::table("XXAJMI_DELEGATE_REQUESTS")->where("delegate_id",$delegate_id)->delete();
    }
    public function getSpecificDelegate($delegate_id){
      return DB::table("XXAJMI_DELEGATE_REQUESTS")->where("delegate_id",$delegate_id)->first();
    }
    public function updateDelegateData($delegate_id,$data){
        return   DB::table('XXAJMI_DELEGATE_REQUESTS')
            ->where('delegate_id', $delegate_id)
            ->update($data);
    }

    public function GetOtherMangerDepartment($person_id){
        return DB::select("SELECT DISTINCT ppx.employee_number, ppx.full_name
FROM per_assignments_x pax
JOIN per_people_x ppx ON ppx.person_id = pax.person_id
JOIN per_person_analyses ppa ON ppa.person_id = ppx.person_id
JOIN per_analysis_criteria pac ON ppa.analysis_criteria_id = pac.analysis_criteria_id
JOIN fnd_flex_values ffv ON ffv.flex_value = pac.segment1
WHERE pac.id_flex_num = '50348'
  AND ffv.attribute10 IS NOT NULL
  AND pax.job_id = 1104
  AND ffv.attribute10 IN (
    SELECT DISTINCT attribute10
    FROM fnd_flex_values
    WHERE attribute10 IS NOT NULL
      AND flex_value = (
        SELECT pac.segment1
        FROM per_person_analyses ppa
        JOIN per_analysis_criteria pac ON ppa.analysis_criteria_id = pac.analysis_criteria_id
        WHERE pac.id_flex_num = '50348'
          AND ppa.person_id = '$person_id'
      )
  )");

    }
}
