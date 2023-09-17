<?php

namespace App\Http\Repository;

use App\Enums\AppKeysProps;
use App\Helper\SmsVerifyHelper;
use App\Helper\SpecialSpecifService;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;

class MainOracleQueryRepo
{
    private $conn;
    protected $admin_mngSigniture;
    protected $top_mngSigniture;
    protected $specialSpecifService;
    protected $sms_send;
    public function __construct()
    {
        $this->conn = oci_connect('apps', 'apps', '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.15.227)(PORT=1571))(CONNECT_DATA=(SERVICE_NAME=sysdev)))');
        $this->admin_mngSigniture = AppKeysProps::AdminManger()->value;
        $this->top_mngSigniture = AppKeysProps::TopManger()->value;
        $this->specialSpecifService = new SpecialSpecifService();
        $this->sms_send = new SmsVerifyHelper();
    }

    public function GetEmployeeUsingFileNumber($employee_number)
    {
        return DB::select("SELECT ppf.person_id,
       paaf.assignment_id,
       ppf.employee_number,
       ppf.full_name employee_name,
       hr.name department,
       jbt.name job,
       pp.name position,
       hl.description location,
       stat.user_status assignment_status
FROM hr.per_all_people_f ppf,
     hr.per_all_assignments_f paaf,
     hr.per_jobs_tl jbt,
     apps.per_positions pp,
     apps.hr_locations hl,
     hr.per_assignment_status_types_tl stat,
     hr_organization_units hr
WHERE     ppf.person_id = paaf.person_id
  AND SYSDATE BETWEEN ppf.effective_start_date
    AND ppf.effective_end_date
  AND SYSDATE BETWEEN paaf.effective_start_date
    AND paaf.effective_end_date
  AND paaf.job_id = jbt.job_id(+)
  AND  paaf.position_id=pp.position_id(+)
  AND   paaf.location_id =hl.location_id(+)
  AND stat.assignment_status_type_id = paaf.assignment_status_type_id
  AND jbt.language = 'US'
  AND stat.language = 'US'
  AND ppf.current_employee_flag = 'Y'
  and paaf.organization_id = hr.organization_id
  AND ppf.employee_number = '$employee_number'
");
    }

    public function update_role_for_same_manger_admin( $status,$admin_mgr_status,$transaction_id)
    {
        DB::beginTransaction();
        try {

            DB::select("UPDATE xxajmi_notif SET approval_status = '$status' ,mgr_approval_status='$admin_mgr_status', admin_mgr_approval_status='$admin_mgr_status'  WHERE TRANSACTION_ID=$transaction_id");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

    }
    public function GetAbsenceAttendaceTypeID()
    {
        $leaves_replaced = $this->specialSpecifService->ExecptThatLeaves();
        return DB::table('PER_ABS_ATTENDANCE_TYPES_TL')
            ->select('ABSENCE_ATTENDANCE_TYPE_ID', 'name','last_update_date')
            ->where('source_lang', '=', 'US')
            ->whereNotIn('name', $leaves_replaced)
            ->get();
    }


    public function GetIdFromUserTable($employee_name)
    {
        return DB::table('fnd_user')
            ->select('user_id')
            ->where('user_name', '=', $employee_name)->first();
    }

    public function GetPersonID($employee_number)
    {
        return DB::select("
           select *
           from hr.per_all_people_f
           where employee_number =  '$employee_number'
           and sysdate between effective_start_date and effective_end_date")[0];
    }
    public function GetPersonNATIONALITY($employee_number)
    {

        return DB::select("
         SELECT ppf.employee_number,
       ppf.full_name,
       apps.hr_general.decode_lookup ('NATIONALITY', ppf.nationality)
           AS Nationality,
       pcx.duration
           contract_duration,
       ppf.start_date
           hire_date
  FROM apps.per_all_people_f ppf,
       per_contracts_x pcx
WHERE     SYSDATE BETWEEN ppf.effective_start_date
                       AND ppf.effective_end_date
       AND ppf.person_id = pcx.person_id
       AND ppf.employee_number = '$employee_number'
")[0];
    }
    // latest rejoin date
    public function GetPersonLastRejoin($person_id)
    {
        return DB::select("SELECT  apps.FND_DATE.CANONICAL_TO_DATE(pac.segment2) Last_Rejoin_Date
  FROM per_person_analyses ppa, per_analysis_criteria pac
WHERE     ppa.analysis_criteria_id = pac.analysis_criteria_id
       AND ppa.person_id = $person_id
       AND pac.id_flex_num = '50401'
       AND pac.segment1 =
           (SELECT MAX (segment1)
              FROM per_analysis_criteria pac2, per_person_analyses ppa2
             WHERE pac2.id_flex_num = '50401'
             and ppa2.analysis_criteria_id = pac2.analysis_criteria_id
             and ppa2.person_id = ppa.person_id)
");
    }
//employee finished years months days
    public function GetPersonAvailableCompanyDate($employee_number)
    {
        return DB::select("WITH ddd AS (
        SELECT TRUNC(SYSDATE) - (
            SELECT (TRUNC(SYSDATE) - TRUNC(start_date))
            FROM per_people_x
            WHERE employee_number = '$employee_number'
        ) AS dtforcomp
        FROM dual
    )
    SELECT
        TRUNC(MONTHS_BETWEEN(TRUNC(SYSDATE), dtforcomp) / 12) AS years,
        MOD(TRUNC(MONTHS_BETWEEN(TRUNC(SYSDATE), dtforcomp)), 12) AS months,
        ROUND(TRUNC(SYSDATE) - ADD_MONTHS(dtforcomp, TRUNC(MONTHS_BETWEEN(TRUNC(SYSDATE), dtforcomp))), 0) AS days
    FROM ddd")[0];
    }


    public function GetOccurance($absence_attendance_type_id, $person_id)
    {
        return DB::table('hr.per_absence_attendances')
            ->selectRaw('count(*) + 1 as count')
            ->where('absence_attendance_type_id', '=', $absence_attendance_type_id)
            ->where('person_id', '=', $person_id)
            ->orderByDesc('creation_date')
            ->get()[0]->count;

    }

    public function GetLastRecordFromCustomNotifyWF($emp_number)
    {
        return DB::table('xxajmi_notif')
            ->where('EMPNO', '=', $emp_number)
            ->orderByDesc('CREATION_DATE')
            ->first();
    }

    public function GetReplacmentDetailsSpecificDepartment($person_id)
    {
        /*
         * //       xx_fnd_custom_pkg.get_flex_value_desc (1014957, pac.segment1)
//          cost_center_desc
         */
        return DB::select("SELECT ppx.employee_number,
       ppx.person_id,
       ppx.full_name,
       pac.segment1 cost_center
  FROM hr.per_all_people_f ppx,
       hr.per_person_analyses ppa,
       hr.per_analysis_criteria pac
WHERE     ppx.person_id = ppa.person_id
       AND ppa.analysis_criteria_id = pac.analysis_criteria_id
       AND SYSDATE BETWEEN ppx.effective_start_date
                       AND ppx.effective_end_date
       AND pac.id_flex_num = '50348'
       AND ppx.person_id <> $person_id
       AND ppx.current_employee_flag = 'Y'
       AND pac.segment1 =
              (SELECT pac1.segment1
                FROM hr.per_person_analyses ppa1,
                      hr.per_analysis_criteria pac1,
                      hr.per_all_people_f ppx1
                WHERE     ppa1.analysis_criteria_id =
                             pac1.analysis_criteria_id
                      AND SYSDATE BETWEEN ppx1.effective_start_date
                                      AND ppx1.effective_end_date
                      AND pac1.id_flex_num = '50348'
                      AND ppx1.person_id = ppa1.person_id
                      AND ppx1.person_id = '$person_id')
");
    }


    public function InsertDataInAbsenceTable($absence_attendance_type_id, $person_id, $date_start, $date_end, $occurrence, $comments, $replaced_employee, $authorising_person_id, $date_notification, $attribute_category, $attribute1, $additional_data,$timePart_start_date,$timePart_end_date,$difference_hours)
    {
        $status = (array_key_exists('ATTRIBUTE12', $additional_data) && $additional_data['ATTRIBUTE12'] === 'N') ? 'Rejected' : 'Approved';

        if ($absence_attendance_type_id == "2061" || $absence_attendance_type_id == "2062") {
            $calculate_differnce_day = null;
        }else{
            $calculate_differnce_day = DB::raw("TO_DATE('$date_end') - TO_DATE('$date_start')+1");
        }

        return DB::table('hr.per_absence_attendances')->insert([
            'absence_attendance_id' => DB::raw('hr.per_absence_attendances_s.NEXTVAL'),
            'business_group_id' => 0,//static not changed
            'absence_attendance_type_id' => $absence_attendance_type_id,
            'person_id' => $person_id,
            'authorising_person_id' => $authorising_person_id,
            'replacement_person_id' => $replaced_employee,
            'date_notification' => $date_notification,
            'attribute_category' => $attribute_category,
            'date_start' => DB::raw("TO_DATE('$date_start')"),
            'date_end' => DB::raw("TO_DATE('$date_end')"),
            'absence_days' => $calculate_differnce_day,
            'occurrence' => $occurrence,
            'object_version_number' => 800,//static not changed
            'creation_date' => DB::raw('SYSDATE'),
            'created_by' => $person_id,
            'last_update_login' => -1,//static not changed
            'last_updated_by' => $person_id,
            'last_update_date' => DB::raw('SYSDATE'),
            'comments' => $comments,
            'approval_status' => $status,
            'time_start'=>$timePart_start_date,
            'time_end'=>$timePart_end_date,
            'absence_hours'=>$difference_hours,
            'PREGNANCY_RELATED_ILLNESS' => (array_key_exists('ATTRIBUTE12', $additional_data)) ? $additional_data['ATTRIBUTE12'] : null,
            'ACCEPT_LATE_NOTIFICATION_FLAG' => (array_key_exists('ATTRIBUTE12', $additional_data)) ? $additional_data['ATTRIBUTE12'] : null,
            'attribute1' => $attribute1, // not colunm is reserved to proccessing time
            "attribute2" => (array_key_exists('ATTRIBUTE2', $additional_data)) ? $additional_data['ATTRIBUTE2'] : null,
            "attribute3" => (array_key_exists('ATTRIBUTE3', $additional_data)) ? $additional_data['ATTRIBUTE3'] : null,
            "attribute4" => (array_key_exists('ATTRIBUTE4', $additional_data)) ? $additional_data['ATTRIBUTE4'] : null,
            "attribute5" => (array_key_exists('ATTRIBUTE5', $additional_data)) ? $additional_data['ATTRIBUTE5'] : null,
            "attribute6" => (array_key_exists('ATTRIBUTE6', $additional_data)) ? $additional_data['ATTRIBUTE6'] : null,
            "attribute7" => (array_key_exists('ATTRIBUTE7', $additional_data)) ? $additional_data['ATTRIBUTE7'] : null,
            "attribute8" => (array_key_exists('ATTRIBUTE8', $additional_data)) ? $additional_data['ATTRIBUTE8'] : null,
            "attribute9" => (array_key_exists('ATTRIBUTE9', $additional_data)) ? $additional_data['ATTRIBUTE9'] : null,
            "attribute10" => (array_key_exists('ATTRIBUTE10', $additional_data)) ? $additional_data['ATTRIBUTE10'] : null,
            "attribute11" => (array_key_exists('ATTRIBUTE11', $additional_data)) ? $additional_data['ATTRIBUTE11'] : null,
            "attribute12" => (array_key_exists('ATTRIBUTE12', $additional_data)) ? $additional_data['ATTRIBUTE12'] : null,
            "attribute13" => (array_key_exists('ATTRIBUTE13', $additional_data)) ? $additional_data['ATTRIBUTE13'] : null,
            "attribute14" => (array_key_exists('ATTRIBUTE14', $additional_data)) ? $additional_data['ATTRIBUTE14'] : null,
            "attribute15" => (array_key_exists('ATTRIBUTE15', $additional_data)) ? $additional_data['ATTRIBUTE15'] : null,
            "attribute16" => (array_key_exists('ATTRIBUTE16', $additional_data)) ? $additional_data['ATTRIBUTE16'] : null,
            "attribute17" => (array_key_exists('ATTRIBUTE17', $additional_data)) ? $additional_data['ATTRIBUTE17'] : null,
            "attribute18" => (array_key_exists('ATTRIBUTE18', $additional_data)) ? $additional_data['ATTRIBUTE18'] : null,
            "attribute19" => (array_key_exists('ATTRIBUTE19', $additional_data)) ? $additional_data['ATTRIBUTE19'] : null,
            "attribute20" => (array_key_exists('ATTRIBUTE20', $additional_data)) ? $additional_data['ATTRIBUTE20'] : null,
            ]);
    }
    public function GetSegmentsOfSpecifTemplate($template_id)
    {
        return DB::select("SELECT SEGMENT_NAME,
         DESCRIPTION,
         ENABLED_FLAG,
         APPLICATION_COLUMN_NAME,
         SEGMENT_NUM,
         DISPLAY_FLAG,
         APPLICATION_COLUMN_INDEX_FLAG,
         DEFAULT_VALUE,
         RUNTIME_PROPERTY_FUNCTION,
         ADDITIONAL_WHERE_CLAUSE,
         REQUIRED_FLAG,
         SECURITY_ENABLED_FLAG,
         DISPLAY_SIZE,
         MAXIMUM_DESCRIPTION_LEN,
         CONCATENATION_DESCRIPTION_LEN,
         FORM_ABOVE_PROMPT,
         FORM_LEFT_PROMPT,
         RANGE_CODE,
         FLEX_VALUE_SET_ID,
         DEFAULT_TYPE,
         LAST_UPDATE_DATE,
         LAST_UPDATED_BY,
         CREATION_DATE,
         CREATED_BY,
         LAST_UPDATE_LOGIN,
         ID_FLEX_NUM,
         ID_FLEX_CODE,
         APPLICATION_ID
    FROM FND_ID_FLEX_SEGMENTS_VL
   WHERE     (ID_FLEX_NUM = $template_id)
         AND (ID_FLEX_CODE = 'PEA')
         AND (APPLICATION_ID = 800)
ORDER BY application_id,
         id_flex_code,
         id_flex_num,
         DECODE (enabled_flag,  'Y', 1,  'N', 2),
         segment_num");
    }
    public function InsertSpecialSeviceTable($data){
        $employee_number = session()->get('employee')->employee_number;
        $person_id = session()->get('employee')->person_id;
        $transaction_id_unique = DB::select("select transaction_seq.NEXTVAL from dual")[0]->nextval;
        $newItemKey = $this->GetLastItemKey();
        $assignmentId = $this->GetAssignmentId($employee_number);
        $object_identifier = DB::table("hr_api_transaction_steps")->select("*")->orderByDesc('creation_date')->first()->object_identifier;
        $transaction_document = $this->generteXML($object_identifier, $person_id, null, null, $transaction_id_unique, $assignmentId->assignment_id, DB::raw('TRUNC(SYSDATE)'), null);
        try {
            DB::beginTransaction();
            DB::table('hr_api_transactions')->insert([
                'transaction_id' => $transaction_id_unique,
                'creator_person_id' => $person_id,
                'transaction_privilege' => 'PRIVATE',
                'created_by' => $person_id,
                'creation_date' => DB::raw('SYSDATE'),
                'last_update_date' => DB::raw('SYSDATE'),
                'last_updated_by' => $person_id,
                'last_update_login' => 40235588,
                'product_code' => 'PER',
                'status' => 'Y',
                'function_id' => 12238,
                'transaction_ref_table' => 'PER_ABSENCE_ATTENDANCES',
                'transaction_ref_id' => DB::raw('per_absence_attendances_s.NEXTVAL'),
                'transaction_type' => 'WF',
                'assignment_id' => $assignmentId->assignment_id,
                'selected_person_id' => $person_id,
                'item_type' => 'HRSSA',
                'item_key' => $newItemKey,
                'transaction_effective_date' => DB::raw('TRUNC(SYSDATE)'),
                'process_name' => 'HR_GENERIC_APPROVAL_PRC',
                'relaunch_function' => 'HR_ABS_ENTRY_PAGE_SS',
                'transaction_group' => 'ABSENCE_MGMT',
                'transaction_identifier' => 'ABSENCES',
                'creator_role' => 'PER:14311',
                'last_update_role' => 'PER:14311',
                'transaction_document' => $transaction_document
            ]);
            DB::table('hr_api_transaction_steps')->insert([
                'transaction_step_id' => $transaction_id_unique,
                'transaction_id' => $transaction_id_unique,
                'api_name' => 'HR_PERSON_ABSENCE_SWI.PROCESS_API',
                'processing_order' => 0,
                'item_type' => 'HRSSA',
                'creator_person_id' => $person_id,
                'update_person_id' => $person_id,
                'object_version_number' => 1,
                'created_by' => 1984,
                'creation_date' => DB::raw('SYSDATE'),
                'last_update_date' => DB::raw('SYSDATE'),
                'last_updated_by' => 1984,
                'last_update_login' => 40235410,
                'object_type' => 'ENTITY',
                'object_name' => 'oracle.apps.per.schema.server.PerAbsenceAttendancesEO',
                'object_identifier' => $object_identifier,
                'pk1' => 1662336,
                'object_state' => 0,
                'information1' => (array_key_exists('information1', $data)) ? $data['information1'] : null,
                'information2' => (array_key_exists('information2', $data)) ? $data['information2'] : null,
                'information5' => (array_key_exists('information5', $data)) ? $data['information5'] : null,
                'information6' => (array_key_exists('information6', $data)) ? $data['information6'] : null,
                'information8' => 3,
                'information9' => 'CONFIRMED',
                'information10' => (array_key_exists('information10', $data)) ? $data['information10'] : null,
                'information11' => (array_key_exists('information11', $data)) ? $data['information11'] : null,//Confirmed or Planned
                'information12' => (array_key_exists('information12', $data)) ? $data['information12'] : null,
                'information13' => (array_key_exists('information13', $data)) ? $data['information13'] : null,
                'information14' => (array_key_exists('information14', $data)) ? $data['information14'] : null,
                'information20' => (array_key_exists('service_type', $data)) ? $data['service_type'] : null,
                'information21' => (array_key_exists('flex_id', $data)) ? $data['flex_id'] : null,
                'information22' => (array_key_exists('flex_name', $data)) ? $data['flex_name'] : null,
                'data_attribute_form' => (array_key_exists('data_attribute_form', $data)) ? $data['data_attribute_form'] : null,
                'information30' => 'ATT',
            ]);

            DB::commit();
            $this->FireCustomWorkflowOfSSHR($transaction_id_unique);
        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }
    public function GetALLDynamicformTemplate()
    {
        return DB::select("select  id_flex_structure_name,id_flex_num
                     from apps.fnd_id_flex_structures_vl fns
                     where id_flex_code ='PEA'
                     and application_id = 800
                     and created_by  not in (1,2)
               ");
    }

    public function GetEmolyeeDataFromPersonId($person_id)
    {
        return DB::select("
           select *
           from hr.per_all_people_f
           where person_id =  '$person_id'
           and sysdate between effective_start_date and effective_end_date")[0];
    }
 public function GetPhoneEmpFromPersonId($person_id)
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
               ");
    }
    public function GetEmpActive($person_id)
    {
        return DB::select("
        select count(*) as count_data
from hr.per_all_people_f ppf,
     hr.per_all_assignments_f paaf
where ppf.person_id = paaf.person_id
and ppf.current_employee_flag ='Y'
and paaf.ASSIGNMENT_STATUS_TYPE_ID =1
and sysdate between ppf.effective_start_date and ppf.effective_end_date
and sysdate between paaf.effective_start_date and paaf.effective_end_date
and ppf.person_id = $person_id
               ");
    }

public function getLastSameService($employee_number,$absence_type){
        return DB::select("SELECT *
  FROM xxajmi_notif xxnotif
WHERE     xxnotif.absence_type = '$absence_type'
       AND xxnotif.empno = '$employee_number'
       AND xxnotif.absence_end_date =
              (SELECT MAX (absence_end_date)
                 FROM xxajmi_notif
                WHERE absence_type = '$absence_type' AND empno = xxnotif.empno)");
}

    public function GetEmailEmployee($emp_number)
    {
        return DB::select("SELECT ppx.employee_number,
       ppx.person_id,
       ppx.full_name,
       pac.segment1 email
  FROM hr.per_all_people_f ppx,
       hr.per_person_analyses ppa,
       hr.per_analysis_criteria pac
WHERE     ppx.person_id = ppa.person_id
       AND ppa.analysis_criteria_id = pac.analysis_criteria_id
       AND SYSDATE BETWEEN ppx.effective_start_date
                       AND ppx.effective_end_date
       AND pac.id_flex_num = '50590'
       AND ppx.employee_number = '$emp_number'
");
    }
    //supervior validation
    public function GetCountSupervisor($employee_number)
    {
        return DB::select("select count(*) as count_data
from hr.per_all_assignments_f paaf,hr.per_all_people_f ppf
where sysdate between paaf.effective_start_date and paaf.effective_end_date
and sysdate between ppf.effective_start_date and ppf.effective_end_date
and ppf.person_id = paaf.person_id
and ppf.employee_number ='$employee_number'
and paaf.supervisor_id is not null
");
    }


    public function GetEmolyeeDataFromEmployee($emp_number)
    {

        return DB::select("
           select *
           from hr.per_all_people_f
           where employee_number =  '$emp_number'
           and sysdate between effective_start_date and effective_end_date")[0];

    }

    public function GetPeriodTime($service_type_id)
    {
        return DB::select("select PERIOD_NAME,TIME_PERIOD_ID
               from hr.PER_TIME_PERIODS
               where TIME_PERIOD_ID > 379
               AND PAYROLL_ID = $service_type_id
               ORDER BY TIME_PERIOD_ID");
    }

    public function GetAdditionalFieldForAbsence($service_type)
    {
        return DB::select("SELECT APPLICATION_COLUMN_NAME, END_USER_COLUMN_NAME,
         ROWIDTOCHAR(ROWID)
    FROM apps.FND_DESCR_FLEX_COL_USAGE_VL fb
   WHERE     (APPLICATION_ID = 800)
         AND (DESCRIPTIVE_FLEXFIELD_NAME LIKE 'PER_ABSENCE_ATTENDANCES')
         AND DESCRIPTIVE_FLEX_CONTEXT_CODE = '$service_type'
ORDER BY column_seq_num");

        //'ANNUAL','EMERGENCY','UNAUTHOUNPA'
    }

    public function createUniqueTransctionId()
    {
        return DB::table('hr_api_transaction_steps')
            ->where('item_type', 'HRSSA')
            ->max(DB::raw('transaction_id+1'));
    }

    public function GetLastItemKey()
    {
        $maxItemKey = DB::table('hr_api_transactions')
            ->where('item_type', 'HRSSA')
            ->max('item_key');

        $newItemKey = $maxItemKey + 1;
        return $newItemKey;

    }

    public function GetAssignmentId($employee_number)
    {
        return DB::table('per_people_x as ppx')
            ->join('per_assignments_x as pax', 'ppx.person_id', '=', 'pax.person_id')
            ->where('ppx.employee_number', $employee_number)
            ->select('pax.assignment_id')
            ->first();
    }

    public function GetManagerPersonId($emp_person_id)
    {
        //here
        return DB::select("SELECT employee_id
        FROM fnd_user
       WHERE employee_id = (SELECT supervisor_id
                              FROM per_assignments_x
                             WHERE person_id = $emp_person_id))");
    }

    public function generteXML($object_identifier, $person_id, $date_Start, $date_end, $transcation_id, $assignment_id, $current_date, $abbsenc_attendance_id)
    {
        return <<<EOD
 <Transaction>
           <TransCtx>
              <TransactionGroup>ABSENCE_MGMT</TransactionGroup>
              <PrsnJobName>Oracle Specialist</PrsnJobName>
              <PrsnAsgFlag>Y</PrsnAsgFlag>
              <LoginPrsnNpwFlag>M</LoginPrsnNpwFlag>
              <PrsnBgId>0</PrsnBgId>
              <EmployeeGeneration>A</EmployeeGeneration>
              <PrsnNpwFlag>M</PrsnNpwFlag
              <ItemType>HRSSA</ItemType>
              <PrsnJobId>$person_id</PrsnJobId>
              <AsgStartDate dataType="d">$date_Start</AsgStartDate>
              <TransactionRefId dataType="n">$transcation_id</TransactionRefId>
              <LoginPrsnLegCode>SA</LoginPrsnLegCode>
              <PrsnMgrId>24521</PrsnMgrId>
              <PrsnAssignmentId>$assignment_id</PrsnAssignmentId>
              <PrsnLegCode>SA</PrsnLegCode>
              <LoginPrsnId>$person_id</LoginPrsnId>
              <LoginPrsnContextSet dataType="b">true</LoginPrsnContextSet>
              <pNtfSubMsg>HR_ABS_NTF_SUB_MSG</pNtfSubMsg>
              <ProductCode>PER</ProductCode>
              <EffectiveDate dataType="d">2023-05-25</EffectiveDate>
              <LoginPrsnEmpFlag>A</LoginPrsnEmpFlag>
              <LoginPrsnType>E</LoginPrsnType>
              <pCalledId dataType="n">12238</pCalledId>
              <ReviewTemplateRNAttr>HR_ABS_NTF_SS</ReviewTemplateRNAttr>
               <TransactionId>$transcation_id</TransactionId>
              <PrsnLocationId>162</PrsnLocationId>
              <PrsnKflexStructCode>PEOPLE_GROUP_FLEXFIELD</PrsnKflexStructCode>
              <LoginPrsnMgrName>ALOMARI, MAN</LoginPrsnMgrName>
              <PrsnContextSet dataType="b">true</PrsnContextSet>
              <PrsnPositionName>Oracle Application Developer</PrsnPositionName>
              <PrsnEmpFlag>A</PrsnEmpFlag>
              <PrsnMgrName>ALOMARI, MAN</PrsnMgrName>
              <PrsnOrganizationId>134</PrsnOrganizationId>
              <PrsnPositionId>50170</PrsnPositionId>
              <LoginWorkerNumber>17961</LoginWorkerNumber>
              <PrsnBgCurrencyCode>SAR</PrsnBgCurrencyCode>
              <HeaderType>PER_HEADER</HeaderType>
              <SSHR_WF_BASED dataType="b">true</SSHR_WF_BASED>
              <PrsnSecurityGroupId>0</PrsnSecurityGroupId>
              <LoginPrsnMgrId>24521</LoginPrsnMgrId>
              <PrsnType>E</PrsnType>
              <PerzOrganizationId>0</PerzOrganizationId>
              <LoginPrsnName>BOJANALA, PRADEEP</LoginPrsnName>
              <CreatorPrsnId dataType="n">$person_id</CreatorPrsnId>
              <PrsnId>14311</PrsnId>
              <TransactionType>WF</TransactionType>
              <NtfAttachAttr>FND:entity=PQH_SS_ATTACHMENT&amp;pk1name=TransactionId&amp;pk1value=$transcation_id</NtfAttachAttr>
              <pApprovalReqd>YD</pApprovalReqd>
              <pAMETranType>SSHRMS</pAMETranType>
              <RelaunchFunction>HR_ABS_ENTRY_PAGE_SS</RelaunchFunction>
              <PerzLocalizationCode>SA</PerzLocalizationCode>
              <TransactionRefTable>PER_ABSENCE_ATTENDANCES</TransactionRefTable>
              <TransactionIdentifier>ABSENCES</TransactionIdentifier>
              <pCalledFrom>HR_LOA_SS</pCalledFrom>
              <LoginPrsnBgId>0</LoginPrsnBgId>
              <PerzFunctionName>HR_LOA_SS</PerzFunctionName>
              <ProcessName>HR_GENERIC_APPROVAL_PRC</ProcessName>
              <PrsnPayrollId>81</PrsnPayrollId>
              <PrsnName>BOJANALA, PRADEEP</PrsnName>
              <CNode name="AbsenceParams" type="Ht">
                 <AbsenceAction>CreateMode</AbsenceAction>
                 <AbsenceAttdId>$abbsenc_attendance_id</AbsenceAttdId>
              </CNode>
              <pAMEAppId>800</pAMEAppId>
           </TransCtx>
           <EoApiMap>
              <EO Name="oracle.apps.per.schema.server.PerAbsenceAttendancesEO">HR_PERSON_ABSENCE_SWI.PROCESS_API</EO>
           </EoApiMap>
           <TransCache>
              <AM MomVer="1044362310593">
                 <cd/>
                 <TXN Def="0" New="0" Lok="2" pcid="158">
                    <EO Name="oracle.apps.per.schema.server.PerAbsenceAttendancesEO">
                       <![CDATA[$object_identifier]]>
                       <PerAbsenceAttendancesEORow PS="0" PK="Y">
                          <AbsenceAttendanceId>$abbsenc_attendance_id</AbsenceAttendanceId>
                          <BusinessGroupId>0</BusinessGroupId>
                          <PersonId>$current_date</PersonId>
                         <AbsenceDays>3</AbsenceDays>
                          <AbsenceHours null="true"/>
                          <DateEnd>$date_end</DateEnd>
                          <DateNotification>2023-05-25 15:13:58.0</DateNotification>
                          <DateProjectedEnd null="true"/>
                          <DateProjectedStart null="true"/>
                          <DateStart>$date_Start</DateStart>
                          <TimeProjectedEnd null="true"/>
                          <TimeProjectedStart null="true"/>
                          <LastUpdateDate>2023-05-25 15:40:08.0</LastUpdateDate>
                          <LastUpdatedBy>1984</LastUpdatedBy>
                          <LastUpdateLogin>40236493</LastUpdateLogin>
                          <CreatedBy>1984</CreatedBy>
                          <CreationDate>2023-05-25 15:40:08.0</CreationDate>
                          <ObjectVersionNumber null="true"/>
                       </PerAbsenceAttendancesEORow>
                    </EO>
                 </TXN>
              </AM>
           </TransCache>
        </Transaction>
EOD;
    }

    public function InsertTransctionProcessWorkFlow($person_id, $employee_number, $date_start, $date_end, $absence_type, $absence_type_id, $comments, $replaced_employee,$timePart_start_date,$timePart_end_date,$difference_hours)
    {
//        $transaction_id_unique = DB::select("select transaction_seq.NEXTVAL from dual")[0]->nextval;
//        $newItemKey = $this->GetLastItemKey();
//        $assignmentId = $this->GetAssignmentId($employee_number);
//        $object_identifier = DB::table("hr_api_transaction_steps")->select("*")->orderByDesc('creation_date')->first()->object_identifier;
//        $transaction_document = $this->generteXML($object_identifier, $person_id, $date_start, $date_end, $transaction_id_unique, $assignmentId->assignment_id, DB::raw('TRUNC(SYSDATE)'), $absence_type_id);
//
        $transaction_id_unique = DB::select("select xxajmi_trxn_s.NEXTVAL from dual")[0]->nextval;
        //$newItemKey = $this->GetLastItemKey();
       // $assignmentId = $this->GetAssignmentId($employee_number);
        //$object_identifier = DB::table("hr_api_transaction_steps")->select("*")->orderByDesc('creation_date')->first()->object_identifier;
       // $transaction_document = $this->generteXML($object_identifier, $person_id, $date_start, $date_end, $transaction_id_unique, $assignmentId->assignment_id, DB::raw('TRUNC(SYSDATE)'), $absence_type_id);

        try {
            DB::beginTransaction();
            DB::table('xxajmi_ss_transactions')->insert([
                'transaction_id' => $transaction_id_unique,
                'creator_person_id' => $person_id,
                'created_by' => $person_id,
                'creation_date' => DB::raw('SYSDATE'),
                'last_update_date' => DB::raw('SYSDATE'),
                'last_updated_by' => $person_id,
                'information1' => $date_start,
                'information2' => $date_end,
                'information5' => $absence_type_id,
                'information6' => $absence_type,//Unpaid Leave || paid Leave
                'information8' => 3,
                'information9' => 'CONFIRMED',//Confirmed or Planned
                'information10' => $replaced_employee,
                'information11' => $comments,//Confirmed or Planned
                'information12' => $timePart_start_date,
                'information13' => $timePart_end_date,
                'information14' => $difference_hours,
                'information20' => 'absence',
            ]);

//            DB::table('hr_api_transaction_steps')->insert([
//                'transaction_step_id' => $transaction_id_unique,
//                'transaction_id' => $transaction_id_unique,
//                'api_name' => 'HR_PERSON_ABSENCE_SWI.PROCESS_API',
//                'processing_order' => 0,
//                'item_type' => 'HRSSA',
//                'creator_person_id' => $person_id,
//                'update_person_id' => $person_id,
//                'object_version_number' => 1,
//                'created_by' => 1984,
//                'creation_date' => DB::raw('SYSDATE'),
//                'last_update_date' => DB::raw('SYSDATE'),
//                'last_updated_by' => 1984,
//                'last_update_login' => 40235410,
//                'object_type' => 'ENTITY',
//                'object_name' => 'oracle.apps.per.schema.server.PerAbsenceAttendancesEO',
//                'object_identifier' => $object_identifier,
//                'pk1' => 1662336,
//                'object_state' => 0,
//                'information1' => $date_start,
//                'information2' => $date_end,
//                'information5' => $absence_type_id,
//                'information6' => $absence_type,//Unpaid Leave || paid Leave
//                'information8' => 3,
//                'information9' => 'CONFIRMED',//Confirmed or Planned
//                'information10' => $replaced_employee,
//                'information11' => $comments,//Confirmed or Planned
//                'information12' => $timePart_start_date,
//                'information13' => $timePart_end_date,
//                'information14' => $difference_hours,
//                'information20' => 'absence',
//                'information30' => 'ATT',
//            ]);
            DB::commit();
           // lanuch the custom workflow
            $this->FireCustomWorkflowOfSSHR($transaction_id_unique);

        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }


    public function LAUNCH_WORKFLOW_ADMIN($transaction_id, $l_mgr_person_id)
    {

        try {
            return  DB::statement("Begin  XX_CUSTOM_PKG_MGR1.LAUNCH_WORKFLOW_ADMIN($transaction_id,$l_mgr_person_id);End;");
//            $conn3 = oci_connect('apps', 'apps', '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.15.227)(PORT=1571))(CONNECT_DATA=(SERVICE_NAME=sysdev)))');
//            $statement3 = oci_parse($conn3, "Begin  XX_CUSTOM_PKG_MGR1.LAUNCH_WORKFLOW_ADMIN($transaction_id,$l_mgr_person_id);End;");
//            oci_execute($statement3, OCI_DEFAULT);
//            oci_close($conn3);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function accrued_balance($p_empno,$p_vac_start_date){

        try {

            DB::statement("ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MON-RRRR'");
            return  DB::select("SELECT xxajmi_vac_accrued_balance('$p_empno', TO_DATE('$p_vac_start_date', 'DD-MON-RR')) acc_days FROM dual")[0]->acc_days;
        }catch (\Exception $exception){

        }

    }

    public function LAUNCH_WORKFLOW_TopManger($transaction_id, $admin_mgr_person_id)
    {

        try {
            return  DB::statement("Begin XX_CUSTOM_PKG_MGR1.LAUNCH_WORKFLOW_TOP($transaction_id,$admin_mgr_person_id);End;");
//            $conn4 = oci_connect('apps', 'apps', '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.15.227)(PORT=1571))(CONNECT_DATA=(SERVICE_NAME=sysdev)))');
//            $statement4 = oci_parse($conn4, "Begin XX_CUSTOM_PKG_MGR1.LAUNCH_WORKFLOW_TOP($transaction_id,$admin_mgr_person_id);End;");
//            oci_execute($statement4, OCI_DEFAULT);
//            oci_close($conn4);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function FireCustomWorkflowOfSSHR($transaction_id_unique)
    {
        return DB::statement("BEGIN XX_CUSTOM_PKG_MGR1.LAUNCH_WORKFLOW_MGR($transaction_id_unique); END;");
//        $conn = oci_connect('apps', 'apps', '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.15.227)(PORT=1571))(CONNECT_DATA=(SERVICE_NAME=sysdev)))');
//        $statement = oci_parse($conn, "BEGIN XX_CUSTOM_PKG_MGR1.LAUNCH_WORKFLOW_MGR($transaction_id_unique); END;");
//        oci_execute($statement, OCI_DEFAULT);
//        oci_close($conn);
    }


    public function GetDetailsOfCustom($transaction_id)
    {
        return DB::table('xxajmi_notif')
            ->where('transaction_id', $transaction_id)->first();
    }
    public function reg_users()
    {
        return DB::select("SELECT ppx.employee_number,
         ppx.full_name,
         apps.xx_fnd_custom_pkg.get_lookup_desc ('NATIONALITY',
                                                 ppx.nationality,
                                                 'M',
                                                 'US')
            nationality,
         'Registered' AS registration_status
    FROM selfservice.sshr_user_reg sshr, apps.per_people_x ppx
   WHERE sshr.employee_number = ppx.employee_number AND sshr.reg_status = 'Y'
ORDER BY TO_NUMBER (ppx.employee_number)
");
    }
    public function non_reg_users()
    {
        return DB::select("  SELECT ppx.employee_number,
         ppx.full_name,
         apps.xx_fnd_custom_pkg.get_lookup_desc ('NATIONALITY',
                                                 ppx.nationality,
                                                 'M',
                                                 'US')
            nationality,
         'Not Registered' AS registration_status
    FROM apps.per_people_x ppx
   WHERE     ppx.current_employee_flag = 'Y'
         AND ppx.employee_number NOT IN (SELECT employee_number
                                           FROM selfservice.sshr_user_reg sshr
                                          WHERE sshr.reg_status = 'Y')
ORDER BY TO_NUMBER (employee_number)
");
    }

    public function count_register_user(){
        return DB::select("SELECT COUNT (*) as no_user
  FROM selfservice.sshr_user_reg sshr, apps.per_people_x ppx
WHERE sshr.employee_number = ppx.employee_number AND sshr.reg_status = 'Y'
")[0];
    }
    public function count_not_register_user(){
        return DB::select("SELECT COUNT (*) as no_user
  FROM apps.per_people_x ppx
WHERE     ppx.current_employee_flag = 'Y'
       AND ppx.employee_number NOT IN (SELECT employee_number
                                         FROM selfservice.sshr_user_reg sshr
                                        WHERE sshr.reg_status = 'Y')
")[0];
    }
    public function checkElgibalityOfAnnul($person_id){
        return DB::select("select selfservice.xxajmi_next_vac_start_date('$person_id') as next_vac_start_date  from dual")[0];
    }

    public function CheckUsingPersonId($person_id){
        return DB::table('per_people_x')
            ->select('*')
            ->where('person_id', '=', $person_id);
    }
    public function MangerInterface($mgr_person_id)
    {
        try {
            // Ajmi Managers View  only supervisor
            return  DB::table('xxajmi_mgr_v')
                ->select('*')
                ->where('person_id', '=', $mgr_person_id);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function UpdateUserData($data)
    {
        $p_empno = $data['emp_number'];
        $p_mobile = $data['phone_number'];
        $p_email = $data['email_employee'];
        $p_iqama = $data['iqama_number'];
        try {
            return DB::statement("BEGIN xxajmi_new_user_reg ('$p_empno', '$p_mobile', '$p_email','$p_iqama'); END;");
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }


    }
    public function deleteServiceData($transaction_id)
    {
        try {
            return DB::table('xxajmi_notif')
                ->where('transaction_id', $transaction_id)->delete();
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function emp_quama_verified($emp_employee,$quama_number){
        try {
            return  DB::select("
select employee_number,full_name,national_identifier ,count(*) as emp_count
from hr.per_all_people_f
where sysdate between effective_start_date and effective_end_date
and current_employee_flag='Y'
and employee_number = '$emp_employee'
and national_identifier = '$quama_number'
group by employee_number,full_name,national_identifier
");
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function xxajmi_emp_reg_or_not($employee_number){
        try {
            return  DB::select("select count(*) as status_req
from selfservice.sshr_user_reg
where employee_number = '$employee_number' and reg_status ='Y'
")[0];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
    public function xxajmi_register_new_user($p_empno,$p_mobile,$p_email,$p_iqama){
        try {
            return DB::statement("BEGIN xxajmi_new_user_reg ('$p_empno', '$p_mobile', '$p_email','$p_iqama); END;");
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function AdminMangerInterface($admin_mgr_person_id)
    {
        try {
            return  DB::table('xxajmi_2_level_approvers_v')
                ->select('*')
                ->where('person_id', '=', $admin_mgr_person_id);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function TopMangInterface($top_mgmt_person_id)
    {
        try {
            return  DB::table('xxajmi_3_level_approvers_v')
                ->select('*')
                ->where('person_id', '=', $top_mgmt_person_id);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function EmployeeInterface($EMPNO)
    {
        try {
           return DB::select("
           select *
           from hr.per_all_people_f
           where employee_number =  $EMPNO
           and sysdate between effective_start_date and effective_end_date");
;

        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function DelegationViewChecking()
    {
        return DB::table('xxajmi_delegate_emp')->select("*");
    }
    public function GetFromXXXnotifaBy()
    {
        return DB::table('xxajmi_notif')->select("*");
    }

    public function GetnotificationOfManger($mgr_person_id, $emp_number = null)
    {
        return DB::table('xxajmi_notif')
            ->orWhere('mgr_person_id', $mgr_person_id)
            ->orWhere('admin_mgr_person_id', $mgr_person_id)
            ->orWhere('top_mgmt_person_id', $mgr_person_id)
            ->orWhere('delegate_to_emp', $emp_number)
            ->orderByDesc('transaction_id')
            ->get();
    }

    public function GetnotificationOfEmployee($emp_number = null, $person_id = null,$user_type=null)
    {
        return DB::table('xxajmi_notif')
            ->where('empno', $emp_number)
            ->orWhere('mgr_person_id', $person_id)
            ->orWhere('admin_mgr_person_id', $person_id)
            ->orWhere('top_mgmt_person_id', $person_id)
            ->orWhere('delegate_to_emp', $emp_number)
            ->get();

    }
    public function GetAnnualServiceForTawsaya()
    {
        return DB::table('xxajmi_notif')
            ->where('absence_type', "=","Annual Leave")
            ->get();

    }

    public function GetDelegatedToPerson($emp_number = null)
    {
        return DB::table('xxajmi_notif')
            ->where('delegate_to_emp', $emp_number)
            ->get();
    }

    public function GetMangerNameSpecificManger($manger_name_of_employee)
    {
        return DB::table('per_people_x')
            ->select('*')
            ->where('employee_number', '=', $manger_name_of_employee)->first()->first_name;
    }

    public function CallNotoficationAfterChangeStaus($transaction_id, $status, $person_type)
    {
        return DB::statement("BEGIN xxajmi_approve_reject_notif($transaction_id, '$status', '$person_type'); END;");
    }
    public function CallToBackupAfterUpdate($transaction_id)
    {
        return DB::statement("BEGIN xxajmi_hr_ss_trxn_update_bkp ($transaction_id); END;");
    }

    public function ApproveRequest($notif_id, $transaction_id, $note, $type)
    {
        $xxajmi_notif = DB::table("xxajmi_notif")->select('*')->where('transaction_id', '=', $transaction_id)->first();
        if ($xxajmi_notif->no_of_approvals == 2) {
            if ($type == "Manager") {
                try {
            if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Manager Approved',
                     mgr_action_date=SYSDATE,
                     mgr_approval_status ='Delegated Assistant Approved'  , mgr_approve_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
            }else{
                DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Manager Approved',
                      mgr_action_date=SYSDATE,
                     mgr_approval_status ='Approved'  , mgr_approve_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
            }
                    $l_mgr_person_id = DB::select("select MGR_PERSON_ID from xxajmi_notif where transaction_id=$transaction_id")[0]->mgr_person_id;
//                    $this->LAUNCH_WORKFLOW_ADMIN($transaction_id, $l_mgr_person_id);
                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Approved", "EMP_MGR");

                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->mgr_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $manger_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->mgr_person_id);
                    $manager_name = $manger_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$manager_name you Approved Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
            if ($type == "AdminMgr") {
                try {
                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                            DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Admin Mgr Approved',
                      admin_mgr_action_date=SYSDATE,
                     admin_mgr_approval_status ='Admin Mgr Approved'  , admin_mgr_approval_note = '$note' , update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }else{
                            DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Admin Mgr Approved',
                      admin_mgr_action_date=SYSDATE,
                     admin_mgr_approval_status ='Approved'  , admin_mgr_approval_note = '$note' , update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }
                    $this->XjmRecordProcess($transaction_id, 'Y');
                    $this->sms_send->EditOnTemplate($transaction_id);


                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Approved", "ADMIN_MGR");
                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->admin_mgr_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $admin_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->admin_mgr_person_id);
                    $admin_name = $admin_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$admin_name you Approved Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);


                } catch (\Exception $e) {
                    DB::rollback();
                }
            }

        }
        elseif ($xxajmi_notif->no_of_approvals == 3) {
            if ($type == "Manager") {
                try {

                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                        DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Manager Approved',
                      mgr_action_date=SYSDATE,
                     mgr_approval_status ='Delegated Assistant Approved'  , mgr_approve_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }else{
                        DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Manager Approved',
                      mgr_action_date=SYSDATE,
                     mgr_approval_status ='Approved'  , mgr_approve_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }
                    $l_mgr_person_id = DB::select("select MGR_PERSON_ID from xxajmi_notif where transaction_id=$transaction_id")[0]->mgr_person_id;
//                    $this->LAUNCH_WORKFLOW_ADMIN($transaction_id, $l_mgr_person_id);
                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Approved", "EMP_MGR");

                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->mgr_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $manger_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->mgr_person_id);
                    $manager_name = $manger_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$manager_name you Approved Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
            if ($type == "AdminMgr") {
                try {
                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                        DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Admin Mgr Approved',
                      admin_mgr_action_date=SYSDATE,
                     admin_mgr_approval_status ='Admin Mgr Approved'  , admin_mgr_approval_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }else{
                        DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Admin Mgr Approved',
                      admin_mgr_action_date=SYSDATE,
                     admin_mgr_approval_status ='Approved' , admin_mgr_approval_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }
                    $admin_mgr_person_id = DB::table('xxajmi_notif')->select("ADMIN_MGR_PERSON_ID")->where('transaction_id', $transaction_id)->first()->admin_mgr_person_id;
//                    $this->LAUNCH_WORKFLOW_TopManger($transaction_id, $admin_mgr_person_id);
                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Approved", "ADMIN_MGR");


                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->admin_mgr_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $admin_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->admin_mgr_person_id);
                    $admin_name = $admin_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$admin_name you Approved Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
            if ($type == "TopMgr") {
                try {

                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                        DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Approved',
                      top_mgmt_action_date=SYSDATE,
                     top_management_approval_status ='Approved' , top_mgmt_approval_note = '$note', update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }else{
                        DB::statement("UPDATE xxajmi_notif
                 SET approval_status = 'Approved',
                      top_mgmt_action_date=SYSDATE,
                     top_management_approval_status ='Approved' , top_mgmt_approval_note = '$note' , update_date=SYSDATE
               WHERE transaction_id = $transaction_id");
                    }

                    $this->XjmRecordProcess($transaction_id, 'Y');
                    $this->sms_send->EditOnTemplate($transaction_id);

                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Approved", "TOP_MGMT");


                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->top_mgmt_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $top_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->top_mgmt_person_id);
                    $top_name = $top_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$top_name you Approved Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);

                    return 'true';
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
        }
        $this->CallToBackupAfterUpdate($transaction_id);
        return 'true';
    }

    public function RejectRequest($notif_id, $transaction_id, $note, $type)
    {
        try {
            $xxajmi_notif = DB::select("select * from xxajmi_notif where transaction_id=$transaction_id")[0];

            if ($type == "Manager") {
                try {
                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                        DB::statement("UPDATE xxajmi_notif
    SET APPROVAL_STATUS = 'Delegated Assistant Rejected', mgr_reject_note = '$note', update_date=SYSDATE,
           mgr_action_date=SYSDATE,
    MGR_APPROVAL_STATUS = 'Delegated Assistant Rejected'
    WHERE transaction_id = $transaction_id");
                    }else{
                        DB::statement("UPDATE xxajmi_notif
    SET APPROVAL_STATUS = 'Manager Rejected', mgr_reject_note = '$note', update_date=SYSDATE,
          mgr_action_date=SYSDATE,
    MGR_APPROVAL_STATUS = 'Rejected'
    WHERE transaction_id = $transaction_id");
                    }
                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Rejected", "EMP_MGR");


                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->mgr_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $manger_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->mgr_person_id);
                    $manager_name = $manger_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$manager_name you Rejected Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);

                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
            if ($type == "AdminMgr") {
                try {
                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                        DB::statement("UPDATE xxajmi_notif
    SET APPROVAL_STATUS = 'Delegated Assistant Rejected', admin_mgr_reject_note = '$note',update_date=SYSDATE,
          admin_mgr_action_date=SYSDATE,
        ADMIN_MGR_APPROVAL_STATUS = 'Delegated Assistant Rejected'
    WHERE transaction_id = $transaction_id");
                    }else{
                        DB::statement("UPDATE xxajmi_notif
    SET APPROVAL_STATUS = 'Admin Manager Rejected', admin_mgr_reject_note = '$note',update_date=SYSDATE,
          admin_mgr_action_date=SYSDATE,
        ADMIN_MGR_APPROVAL_STATUS = 'Rejected'
    WHERE transaction_id = $transaction_id");
                    }
                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Rejected", "ADMIN_MGR");

                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->admin_mgr_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $admin_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->admin_mgr_person_id);
                    $admin_name = $admin_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$admin_name you Rejected Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);

                } catch (\Exception $e) {
                    DB::rollback();
                }

            }
            if ($type == "TopMgr") {
                try {
                    if ($xxajmi_notif->delegate_to_emp and \Carbon\Carbon::now() >= $xxajmi_notif->delegate_from_date and \Carbon\Carbon::now() <= $xxajmi_notif->delegate_to_date){
                        DB::statement("UPDATE xxajmi_notif
    SET APPROVAL_STATUS = 'Delegated Assistant Rejected', top_mgmt_reject_note = '$note' ,update_date=SYSDATE,
         top_mgmt_action_date=SYSDATE,
        TOP_MANAGEMENT_APPROVAL_STATUS='Delegated Assistant Rejected'
    WHERE transaction_id = $transaction_id");
                    }else{
                        DB::statement("UPDATE xxajmi_notif
    SET APPROVAL_STATUS = 'Top Manager Rejected', top_mgmt_reject_note = '$note' ,update_date=SYSDATE,
         top_mgmt_action_date=SYSDATE,
        TOP_MANAGEMENT_APPROVAL_STATUS='Rejected'
    WHERE transaction_id = $transaction_id");
                    }
                    //send notification
                    $this->CallNotoficationAfterChangeStaus($transaction_id, "Rejected", "TOP_MGMT");

                    $phone_number = $this->GetPhoneEmpFromPersonId($xxajmi_notif->top_mgmt_person_id)[0]->phone_number;
                    $phone_number = $this->sms_send->filterPhoneNumber($phone_number);
                    $top_data = $this->GetEmolyeeDataFromPersonId($xxajmi_notif->top_mgmt_person_id);
                    $top_name = $top_data->full_name;
                    $emp_requested_number = $xxajmi_notif->empno;
                    $transaction_id = $xxajmi_notif->transaction_id;
                    $absence_type = $xxajmi_notif->absence_type;
                    $message = "Dear Mr.$top_name you Rejected Request from Employee File Number ($emp_requested_number) with Transaction Id ($transaction_id) of Service($absence_type) ";
                    $this->sms_send->sendSMS($phone_number,$message);

                    return 'true';
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
            $this->CallToBackupAfterUpdate($transaction_id);
            return true;
        }catch (\Exception $exception){
            DB::rollBack();
        }

    }


    public function XjmRecordProcess($transaction_id, $status)
    {
        //delete from xxajmi_ss_transactions,hr_api_transactions
        $xxajmi_notif_record = DB::table("xxajmi_notif")->select('*')->where('transaction_id', '=', $transaction_id)->first();
        $hr_api_transactions_record = DB::table("xxajmi_ss_transactions")->select('*')->where('transaction_id', $transaction_id)->first();
        $this->InsertDataInAbsenceTable(
            $hr_api_transactions_record->information5,
            $hr_api_transactions_record->creator_person_id,
            $hr_api_transactions_record->information1,
            $hr_api_transactions_record->information2,
            null,
            null,
            $xxajmi_notif_record->replacement_no,
            $xxajmi_notif_record->mgr_person_id,
            DB::raw('SYSDATE'),
            $hr_api_transactions_record->information6,
            null, ['ATTRIBUTE12' => $status, 'ATTRIBUTE13' => $status],
            $xxajmi_notif_record->time_start,
            $xxajmi_notif_record->time_end,
            $xxajmi_notif_record->absence_hours,
        );
        DB::table('xxajmi_ss_transactions')->where('transaction_id', $xxajmi_notif_record->transaction_id)->delete();
        DB::table('xxajmi_ss_transactions')->where('transaction_id', $xxajmi_notif_record->transaction_id)->delete();
        return 'true';

    }

    public function getRecordOfHRTransactionStep($transaction_id)
    {
        return DB::table('xxajmi_ss_transactions')->where('TRANSACTION_ID', $transaction_id)->first();
    }

    public function update_reason_note($note, $transaction_id)
    {
        return DB::select("UPDATE xxajmi_notif
SET MGR_REJECT_NOTE = $note
WHERE TRANSACTION_ID=$transaction_id");
    }




    public function GetRestDelegationServiceThatNotChoose($employee_number)
    {
        return DB::select("
SELECT
    DISTINCT absence_attendance_type_id,
    name
FROM
    per_abs_attendance_types_tl
WHERE
    source_lang = 'US'
    AND name NOT IN (
        'Tickets Adults',
        'Delay - Partial Unpaid Leave',
        'Absence',
        'Ajmi Late Deduction',
        'AJMI Casual Leave',
        'Tickets Childs',
        'Tickets Infants',
        'Compassionate Leave',
        'Authorized Unpaid Leave'
    )
    AND absence_attendance_type_id NOT IN (
        SELECT
            DISTINCT paat.absence_attendance_type_id
        FROM
            per_abs_attendance_types_tl paat,
            xxajmi_delegate_requests del
        WHERE
            paat.source_lang = 'US'
            AND paat.name NOT IN (
                'Tickets Adults',
                'Delay - Partial Unpaid Leave',
                'Absence',
                'Ajmi Late Deduction',
                'AJMI Casual Leave',
                'Tickets Childs',
                'Tickets Infants',
                'Compassionate Leave',
                'Authorized Unpaid Leave'
            )
            AND del.absence_type_ar LIKE '%' || paat.absence_attendance_type_id || '%'
            AND del.delegate_from_emp = '$employee_number'
            AND TRUNC (SYSDATE) BETWEEN del.delegate_from_date
            AND del.delegate_to_date
    )");
    }

    public function LoanServices(){
        return DB::select("select fifs.ID_FLEX_STRUCTURE_NAME structure_name,fifs.id_flex_num
FROM FND_ID_FLEX_STRUCTURES_VL fifs
WHERE fifs.id_flex_num IN ('50397', '50610', '50402')");
    }
    public function get_LoanRequest_service($flex_id){
        return DB::select("SELECT fifs.id_flex_num,
       fifs.application_column_name,
       fifs.segment_name
  FROM fnd_id_flex_segments_vl fifs
WHERE fifs.id_flex_num ='$flex_id'");
    }
    public function taswaya_status_change($transaction_id,$note,$type){
        try {
            if ($type=="approve"){
                return  DB::statement("UPDATE xxajmi_notif
                 SET taswiath_status =1 , taswiath_note  = '$note'
               WHERE transaction_id = $transaction_id");
            }elseif ($type=="reject"){
                return  DB::statement("UPDATE xxajmi_notif
                 SET taswiath_status =0 , taswiath_note  = '$note'
               WHERE transaction_id = $transaction_id");
            }
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function delete_taswaya($transaction_id){
        try {
            DB::table('xxajmi_notif')->where('transaction_id',$transaction_id)->delete();
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
}
