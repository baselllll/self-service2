<?php

namespace App\Helper;

class EosDetails
{

    public function GetEOSSerivices(){
        return [
            ["id_flex_num"=>20231,"structure_name"=>"Resignation","service_type"=>"EOS","image"=>"resignation.jpg"],
            ["id_flex_num"=>20232,"structure_name"=>"Non_Renewal_contr","service_type"=>"EOS","image"=>"probation_period.jpg"],
//            ["id_flex_num"=>20233,"structure_name"=>"Probation_Continiue","service_type"=>"EOS","image"=>"non_renewable.jpg"]
        ];
    }

    public function GetEOSAttr(){
        return [
            ["type"=>"text","segment_name"=>"Resignation Reason",'ar_text'=>'سبب الاستقالة'],
            ["type"=>"date","segment_name"=>"Notified EOS Date",'ar_text'=>'تاريخ إخطار نهاية الخدمة '],
            ["type"=>"date","segment_name"=>"Actual EOS Date",'ar_text'=>'تاريخ نهاية الخدمة الفعلي'],
            ["type"=>"number","segment_name"=>"Notice Period",'ar_text'=>'فترة الإشعار'],
        ];
    }
}
