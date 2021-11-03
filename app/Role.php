<?php

namespace App;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Role extends Model
{
    protected $table = 'role';
    protected $fillable = ['name', 'deskripsi', 'status', 'created_by', 'updated_by'];

    public static function complex($id) {
		$sql = "
			SELECT role.id, role.name, role.deskripsi, role.status, 
			(SELECT GROUP_CONCAT(module_role.module_id) FROM module_role WHERE module_role.role_id = role.id) AS combined,
			(SELECT GROUP_CONCAT(role_user.user_id) FROM role_user WHERE role_user.role_id = role.id) AS roles
			FROM role
			WHERE role.id = '{$id}'
		";

		$role = DB::select($sql);
		$role[0]->combined = explode(',', $role[0]->combined);

		return $role;
    }

    public function getCreatedAtAttribute($date) {
    	return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
	}
}
