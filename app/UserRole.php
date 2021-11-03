<?php

namespace App;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use DB;

use App\UserRole;
use App\RoleModule;
use App\Module;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class UserRole extends Model
{
	protected $table = 'role_user';
	public $timestamps = false;
	protected $fillable = ['role_id', 'user_id'];

	public static function getACL($id='') {
		$role = UserRole::select([
			'role_user.role_id',
			'module_role.module_id',
			'module.parent_id',
			'module.name',
			'module.value',
			'module.uri',
			'module.method',
			'module.menu_parent',
			'module.menu',
			'module.menu_sub'
		])
		->leftJoin('module_role', function($join) {
			$join->on('module_role.role_id', '=', 'role_user.role_id');
		})
		->leftJoin('module', function($join) {
			$join->on('module.id', '=', 'module_role.module_id');
		})
		->where('role_user.user_id', $id)
		->orderBy('module.level')
		->orderBy('module.id')
		->get()
		->toArray();

		$menu = [];
		$submenu = [];
		foreach ($role as $key => $val) {
			if (empty($val['parent_id'])) {
				$menu[] = $val['menu_parent'];
			} else {
				$submenu[] = $val['name'];
			}
		}

		/*$finRole = [];
		foreach ($role as $key => $val) {
			$finRole[$val['menu_parent']][] = $val;
		}

		$menu = [];
		foreach ($finRole as $key => $row) {

		}*/

		$finRole = [
			'menu' => $menu,
			'submenu' => $submenu
		];

		return $finRole;

    }
}
