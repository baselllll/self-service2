<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self PendingStatus()
 * @method static self Manger()
 * @method static self AdminManger()
 * @method static self TopManger()
 * @method static self AdminMgrApproved()
 * @method static self UserTypeEmployee()
 * @method static self Pend_approved_pending_req()
 * @method static self Mgr_approved_pending_req()
 * @method static self MuslimReligion()
 * @method static self TopManger_Approved()
 * @method static self Omoma_absence_type_id()
 * @method static self Idah_absence_type_id()
 * @method static self MargieLeave_absence_type_id()
 * @method static self AnnuLeave_absence_type_id()
 * @method static self ChildLeave_absence_type_id()
 * @method static self PersonnalPremission_absence_type_id()
 * @method static self WorkPremission_absence_type_id()
 * @method static self Sickleave_absence_type_id()
 * @method static self Emergency_absence_type_id()
 * @method static self Absence_Services()
 * @method static self Certificate_Services()
 * @method static self Letter_Services()
 * @method static self Loan_Services()
 * @method static self Insurance()
 * @method static self Other()
 * @method static self EOS()
 * @method static self Resignation()
 * @method static self Non_Renewal_contr()
 * @method static self Annual_Leave()
 * @method static self Emergency_Leave()
 * @method static self Sick_Leave()
 */
class AppKeysProps extends Enum
{
    protected static function values(): array
    {
        return [
            'PendingStatus' => "Pending",
            'Manger' => "manger",
            'AdminManger' => "admin_manger",
            'TopManger' => "top_mng",
            'AdminMgrApproved' => "Admin Mgr Approved",
            'UserTypeEmployee' => "employee",
            'Pend_approved_pending_req'=>'Pending Approval',
            'Mgr_approved_pending_req'=>'Manager Approved',
            'MuslimReligion'=>'MUSLIM',
            'TopManger_Approved'=>'Approved',
            'Omoma_absence_type_id'=>2065,
            'Idah_absence_type_id'=>2064,
            'MargieLeave_absence_type_id'=>68,
            'AnnuLeave_absence_type_id'=>62,
            'ChildLeave_absence_type_id'=>64,
            'PersonnalPremission_absence_type_id'=>2068,
            'WorkPremission_absence_type_id'=>2063,
            'Sickleave_absence_type_id'=>70,
            'Emergency_absence_type_id'=>1061,
            'Absence_Services'=>'Absence Services',
            'Certificate_Services'=>'Certificate Services',
            'Letter_Services'=>'Letter Services',
            'Loan_Services'=>'Loan Services',
            'Insurance'=>'Insurance',
            'Other'=>'Other',
            'EOS'=>'EOS',
            'Resignation'=>'Resignation',
            'Non_Renewal_contr'=>'Non_Renewal_contr',
            'Annual_Leave'=>'Annual Leave',
            'Emergency_Leave'=>'Emergency Leave',
            'Sick_Leave'=>'Sick Leave',
        ];
    }
}

