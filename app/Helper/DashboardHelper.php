<?php

namespace App\Helper;

class DashboardHelper
{

    public function main_procee_images(){

        return [
            ["image"=>"active_session.jfif","url"=>"active_session","name"=>"Active Session"],
            ["image"=>"all_services.png","url"=>"all_services?data_decrypted=kajska656sasnjiujiasdsw58565asmnhwuemxsa32","name"=>"All Services"],
            ["image"=>"non_register_user.png","url"=>"non_register_user","name"=>"Non Register"],
            ["image"=>"otp_different_device.png","url"=>"otp_different_device","name"=>"Kill Sessions"],
            ["image"=>"register_user.png","url"=>"register_user","name"=>"Register Users"],
            ["image"=>"tracking_r.jpg","url"=>"tracking_r","name"=>"Tracking Requests"],
            ["image"=>"continue_process.png","url"=>"manual_add_absence","name"=>"Insert Absence"],
            ["image"=>"feature_new.png","url"=>"feature_new","name"=>"New Feature"],
        ];
    }

}
