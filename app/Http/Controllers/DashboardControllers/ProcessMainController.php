<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Helper\DashboardHelper;
use App\Http\Controllers\Controller;
use App\Http\Repository\MainOracleQueryRepo;
use App\Http\Services\LoginService;

class ProcessMainController extends Controller
{
    public function __construct(DashboardHelper $dashboardHelper , LoginService $loginService)
    {
        $this->dashboardHelper = $dashboardHelper;
        $this->loginService = $loginService;
    }

    public function home(){
        $main_process =  json_encode($this->dashboardHelper->main_procee_images());
        $main_process = json_decode($main_process);
        return view('dashboard.home',compact('main_process'));
    }
    public function active_session(){
        $activeSession= $this->loginService->activeSession();
        foreach ($activeSession as &$newItem){
            $main_repo = new MainOracleQueryRepo();
            $newItem->full_name = $main_repo->GetEmployeeUsingFileNumber($newItem->employee_number)[0]->employee_name;
        }
        return view('dashboard.active_session',compact("activeSession"));
    }
    public function non_register_user(){
        $non_reg_users= $this->loginService->non_reg_users();
        return view('dashboard.non_register_user',compact("non_reg_users"));
    }
    public function otp_different_device(){
        $count_register = $this->loginService->count_register_user()->no_user;
        $count_not_register =$this->loginService->count_not_register_user()->no_user;
        return view('dashboard.otp_different_device',compact('count_register','count_not_register'));
    }
    public function register_user(){
        $reg_users = $this->loginService->reg_users();
        return view('dashboard.register_user',compact("reg_users"));
    }
    public function manual_add_absence(){
        return view('dashboard.manule_add_absence');
    }

    public function tracking_r(){
                $tracking_users = $this->loginService->tracking_users();
                foreach ($tracking_users as &$item) {
            $get_users_from_userReq = $this->loginService->get_users_from_userReq($item->empno);
            if (isset($get_users_from_userReq)){
                $item->phone  = $get_users_from_userReq->mobile_no;
                $item->email_address  = $get_users_from_userReq->email_address;
            }else{
                $item->phone  = '';
                $item->email_address  = '';
            }

            $item->mgr_emp_number = $this->loginService->CheckUsingPersonId($item->mgr_person_id);
            $item->admin_emp_number = $this->loginService->CheckUsingPersonId($item->admin_mgr_person_id);
            $item->top_emp_number = $this->loginService->CheckUsingPersonId($item->top_mgmt_person_id);

    }
        return view('dashboard.tracking_r',compact('tracking_users'));
    }

}
