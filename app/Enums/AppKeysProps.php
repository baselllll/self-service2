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
        ];
    }
}

