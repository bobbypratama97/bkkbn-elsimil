<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\User;
use App\Module;

class KelurahanPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function access(User $user, $roleId, $permit) {
        $permission = 'admin.kelurahan.' . $permit;

        $module = Module::
            leftJoin('module_role', function($join) {
                $join->on('module_role.module_id', '=', 'module.id');
            })
            ->where('module.name', $permission)
            ->where('module_role.role_id', $roleId)
            ->first();

        if ($module) {
            return true;
        } else {
            return false;
        }
    }

}
