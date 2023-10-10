<?php

namespace App\Helper;

use App\Http\Services\DetailsEmployeeService;

class SpecialSpecifService
{
    protected $collect_services = [];

    public function __construct(DetailsEmployeeService $detailsEmployeeService=null)
    {
        $this->detailsEmployeeService = $detailsEmployeeService;
    }

    public function SpecialService()
    {
        return ['Bonus','Employment Certificate','Loan Request','Medical Insurance','Overtime Payment','Tickets Request','Employee Evaluation','Test_12345'];
    }

    public function mainAdditionalFiledsAvailableService($name){
        $ReservedAdditinalTypeAvailable = ['ANNUAL','EMERGENCY','UNAUTHOUNPA'];
        $attribute_category = null;
        $additional_field = null;;
        foreach ($ReservedAdditinalTypeAvailable as $AvailableType){
            if (\Str::contains($name, $AvailableType,true)) {
                $additional_field = $this->detailsEmployeeService->GetAdditionalFieldForAbsence($AvailableType);
                array_shift($additional_field);
                $attribute_category=$AvailableType;
            }
        }
        return [$attribute_category,$additional_field];
    }


    public function CallingReservedImages(){
        return   [
            "1061"=>'emergency.jpg',
            "62"=>'Annual Leave.jpg',
            "64"=>'baby.jpg',
            "66"=>'exist.jpg',
            "67"=>'haji.png',
            "68"=>'marige.png',
            "70"=>'Sick Leave.jpg',
            "2068"=>'Permission Work.png',
            "2067"=>'Personal Permission.jpg',
            "2064"=>'death idah.jpg',
            "2063"=>'death.jpg',
            "2065"=>'omoma.jpg',
        ];
    }

    public function ExecptThatLeaves(){
        return ['Tickets Adults', 'Delay - Partial Unpaid Leave', 'Absence', 'Ajmi Late Deduction', 'AJMI Casual Leave', 'Tickets Childs', 'Tickets Infants', 'Compassionate Leave', 'Authorized Unpaid Leave'];
    }

    public function GetAllServiceDifferent(){
        return  [
            'Absence_Services'=>['id'=>'AS5','name'=>"Absence Services","image"=>"absence_service.png",'condition'=>''],
            'Certificate_Services'=>['id'=>'CS6','name'=>"Certificate Services","image"=>"certificate_service.png",'condition'=>'disabled'],
            'LoanServices'=>['id'=>'Lo7','name'=>"Loan Services","image"=>"loan.jpg",'condition'=>'disabled'],
            'Letter Services'=>['id'=>'LS8','name'=>"Letter Services","image"=>"letter.png",'condition'=>'disabled'],
            'Insurance'=>['id'=>'I9','name'=>"Insurance","image"=>"insurance.jpg",'condition'=>'disabled'],
            'Other'=>['id'=>'Other10','name'=>"Other","image"=>"other.png",'condition'=>'disabled'],
        ];
    }
}
