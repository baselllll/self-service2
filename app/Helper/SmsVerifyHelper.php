<?php

namespace App\Helper;


use App\Http\Repository\EmployeeDetailsRepository;
use App\Http\Repository\MainOracleQueryRepo;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class SmsVerifyHelper
{
    function isArabicMessage($message) {
        // Regular expression pattern to match Arabic characters
        $pattern = '/\p{Arabic}/u';

        // Check if the message contains Arabic characters
        return preg_match($pattern, $message) === 1;
    }
    function sendSMS($phone_number, $message,$otp=null)
    {

        if ($this->isArabicMessage($message)){
            $message = "رمز التحقق  من الحساب هو لتسجيل الدخول إلى منصة العجمي للخدمة الذاتية لا تشاركها :  ".$otp;
        }else{
            if (isset($otp)){
                $message = "$message : $otp";
            }

        }

        $base_url = "http://192.168.211.150/cgi/WebCGI";
        $api_key = "1500101=account=apiuser&password=apipass&port=3";
        $destination = "&destination=" . rawurlencode($phone_number);
        $content = "&content=" . rawurlencode($message);
        $url = $base_url . "?" . $api_key . $destination . $content;
        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function filterPhoneNumber($phone_number){
        $cur_zero_number = null;
        if (strlen($phone_number) > 0) {
            if (substr($phone_number, 0, 1) === '0')
                $cur_zero_number = '966' . substr($phone_number, 1);
            if (strlen($cur_zero_number) == 12) {
                 return $cur_zero_number;
            }
        }
    }

    public function EditOnTemplate($transition_id){
        try {
            $mainRepo = new MainOracleQueryRepo();
            $UploadDocumnetAcrchive = new UploadDocumnetAcrchive();
            $EmployeeDetailsRepository = new EmployeeDetailsRepository();
            $record_approved =  \DB::table("xxajmi_notif")->where('transaction_id',$transition_id)->where('approval_status','Approved')->first();
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

            $customTempDir = "tempDir";
            if (!file_exists(public_path($customTempDir))) {
                mkdir(public_path($customTempDir), 0777, true);
            }
            $tempFile = tempnam($customTempDir, 'prefix');
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

// Clean up temporary files
            unlink($tempFile);
            unlink($outputFileName_local);

        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }


}
