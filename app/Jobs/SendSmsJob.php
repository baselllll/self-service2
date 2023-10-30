<?php

namespace App\Jobs;

use App\Helper\SmsVerifyHelper;
use Dflydev\DotAccessData\Data;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function sendsms($name, $emp_requested_number, $absence_type, $transaction_id, $phone_number)
    {

        $sms = new SmsVerifyHelper();
        $name = explode(" ", $name);
        try {
            $message = "Mr.$name[0] Check system, Emp:$emp_requested_number Request:$transaction_id $absence_type";

            if (substr($phone_number, 0, 1) === '0')
                $cur_zero_number = '966' . substr($phone_number, 1);

            if (strlen($cur_zero_number) == 12) {
                $sms->sendSMS($cur_zero_number, $message);
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getPhone($person_id)
    {
        return DB::select("SELECT pp.phone_number , ppx.*
  FROM hr.per_all_people_f ppx, hr.per_phones pp
WHERE     ppx.person_id = pp.parent_id
       AND pp.date_to IS NULL
       AND pp.phone_number IS NOT NULL
       AND ppx.current_employee_flag = 'Y'
       AND ROWNUM = 1
       AND ppx.person_id = '$person_id'
       AND sysdate between ppx.effective_start_date and ppx.effective_end_date
               ")[0];
    }

    public function logicGroupCode()
    {
        $mangrs_items = DB::select("SELECT
              MGR_PERSON_ID,
              MAX(EMPNO) AS EMPNO,
              TIMES_CALL AS TIMES_CALL,
              MAX(TRANSACTION_ID) AS TRANSACTION_ID,
              MAX(ABSENCE_TYPE) AS ABSENCE_TYPE,
              MAX(APPROVAL_STATUS) AS APPROVAL_STATUS,
              MAX(ADMIN_MGR_PERSON_ID) AS ADMIN_MGR_PERSON_ID,
              MAX(TOP_MGMT_PERSON_ID) AS TOP_MGMT_PERSON_ID
            FROM XXAJMI_NOTIF
            WHERE APPROVAL_STATUS = 'Pending Approval'
            AND TIMES_CALL=0
            GROUP BY MGR_PERSON_ID,TIMES_CALL");

        $admin_mangrs_items = DB::select("SELECT
              admin_mgr_person_id,
              MAX(empno) AS empno,
              TIMES_CALL AS TIMES_CALL,
              MAX(transaction_id) AS transaction_id,
              MAX(absence_type) AS absence_type,
              MAX(approval_status) AS approval_status,
              MAX(admin_mgr_person_id) AS admin_mgr_person_id,
              MAX(top_mgmt_person_id) AS top_mgmt_person_id
            FROM XXAJMI_NOTIF
            WHERE approval_status = 'Manager Approved'
            AND TIMES_CALL=0
            GROUP BY admin_mgr_person_id,TIMES_CALL
");
        $top_mangrs_items = DB::select("SELECT
              top_mgmt_person_id,
              MAX(empno) AS empno,
               TIMES_CALL AS TIMES_CALL,
              MAX(transaction_id) AS transaction_id,
              MAX(absence_type) AS absence_type,
              MAX(approval_status) AS approval_status,
              MAX(admin_mgr_person_id) AS admin_mgr_person_id,
              MAX(top_mgmt_person_id) AS top_mgmt_person_id
            FROM XXAJMI_NOTIF
            WHERE approval_status = 'Admin Mgr Approved'
             AND TIMES_CALL=0
            GROUP BY top_mgmt_person_id,TIMES_CALL
");
        return [$mangrs_items, $admin_mangrs_items, $top_mangrs_items];
    }

    public function handle()
    {
        $result = $this->logicGroupCode();
        $manger_items = $result[0];
        $admin_manger_items = $result[1];
        $top_manger_items = $result[2];

        if (count($manger_items) > 0) {
            $item = $manger_items[0];
            $data = $this->getPhone($item->mgr_person_id);
            $phone_number = $data->phone_number;
            $name = $data->full_name;
            $emp_requested_number = $item->empno;
            $transaction_id = $item->transaction_id;
            $absence_type = $item->absence_type;
            $this->sendsms($name, $emp_requested_number, $absence_type, $transaction_id, $phone_number);
            try {
                DB::statement("UPDATE xxajmi_notif
                      SET times_call = '1'
                      WHERE transaction_id = $item->transaction_id");
            } catch (\Exception $exception) {
                DB::rollBack();
            }
        } elseif (count($admin_manger_items) > 0) {
            $item = $admin_manger_items[0];
            $data = $this->getPhone($item->admin_mgr_person_id);
            $phone_number = $data->phone_number;
            $name = $data->full_name;
            $emp_requested_number = $item->empno;
            $transaction_id = $item->transaction_id;
            $absence_type = $item->absence_type;
            $this->sendsms($name, $emp_requested_number, $absence_type, $transaction_id, $phone_number);
            try {
                DB::statement("UPDATE xxajmi_notif
                      SET times_call = 1
                      WHERE transaction_id = $item->transaction_id");
            } catch (\Exception $exception) {
                DB::rollBack();
            }
        } elseif (count($top_manger_items) > 0) {
            $item = $top_manger_items[0];
            $data = $this->getPhone($item->top_mgmt_person_id);
            $phone_number = $data->phone_number;
            $name = $data->full_name;
            $emp_requested_number = $item->empno;
            $transaction_id = $item->transaction_id;
            $absence_type = $item->absence_type;
            $this->sendsms($name, $emp_requested_number, $absence_type, $transaction_id, $phone_number);
            try {
                DB::statement("UPDATE xxajmi_notif
                      SET times_call = 1
                      WHERE transaction_id = $item->transaction_id");
            } catch (\Exception $exception) {

                DB::rollBack();
            }

        }
    }
}
