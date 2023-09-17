<?php

namespace App\Http\Services;

use App\Http\Repository\EmployeeDetailsRepository;

class DetailsEmployeeService
{
    protected $detailsRepository;
    public function __construct(EmployeeDetailsRepository $detailsRepository)
    {
        $this->detailsRepository = $detailsRepository;
    }

    public  function GetIdFromUserTable($employee_name){
       return  $this->detailsRepository->GetIdFromUserTable($employee_name);
    }
    public  function GetPersonID($employee_number){
        return  $this->detailsRepository->GetPersonID($employee_number);
    }
    public  function GetOccurance($absence_attendance_type_id,$person_id){
        return  $this->detailsRepository->GetOccurance($absence_attendance_type_id,$person_id);
    }
    public  function GetLastRecordFromCustomNotifyWF($emp_number){
        return  $this->detailsRepository->GetLastRecordFromCustomNotifyWF($emp_number);
    }
    public  function getLastSameService($emp_number,$absence_type){
        return  $this->detailsRepository->getLastSameService($emp_number,$absence_type);
    }
    public function accrued_balance($p_empno,$p_vac_start_date)
    {
        return $this->detailsRepository->accrued_balance($p_empno,$p_vac_start_date);
    }

    public function InsertSpecialSeviceTable($data){
         $this->detailsRepository->InsertSpecialSeviceTable($data);
    }
    public  function InsertDataInAbsenceTable($person_id,$employee_number,$date_start,$date_end,$absence_type,$absence_type_id,$comments,$replaced_employee,$timePart_start_date,$timePart_end_date,$difference_hours){
        try {
              $this->detailsRepository->InsertTransctionProcessWorkFlow($person_id,$employee_number,$date_start,$date_end,$absence_type,$absence_type_id,$comments,$replaced_employee,$timePart_start_date,$timePart_end_date,$difference_hours);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }
    public function GetSegmentsOfSpecifTemplate($template_id){
        return $this->detailsRepository->GetSegmentsOfSpecifTemplate($template_id);
    }

    public function GetAdditionalFieldForAbsence($searchTerm){
        return $this->detailsRepository->GetAdditionalFieldForAbsence($searchTerm);
    }
}
