<?php

namespace App\Http\Repository;

use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeDetailsRepository extends MainOracleQueryRepo
{
    public function login($employee_number){
//        DB::beginTransaction();
        try {
            $employee_data = $this->GetEmployeeUsingFileNumber($employee_number);
            if (isset($employee_data) and count($employee_data)>0){
              return $employee_data;
            }else{
                Alert::error("message,'not exist any user that have employee number");
            }
        }catch (\Exception $exception)
        {
            return $exception->getMessage();
        }

    }
}
