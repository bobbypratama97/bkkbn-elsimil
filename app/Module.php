<?php

namespace App;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use DB;
use Helper;

/**
 * @mixin Eloquent
 * @mixin Builder
 */

class Module extends Model
{
    protected $table = 'module';
    public $timestamps = false;
    protected $fillable = ['name', 'value'];

    public static function hierarchy($data=array()) {
		$sql = "
			SELECT
				p.id as parent_id, p.name as parent_name, c1.id as child_id, c1.name as child_name, c1.value as child_value
			FROM module p
			LEFT JOIN module c1 ON c1.parent_id = p.id
			WHERE p.parent_id IS NULL ORDER BY c1.parent_id ASC, c1.level ASC, c1.value ASC
		";

		$result = DB::select($sql);

		$module = [];
		foreach ($result as $key => $val) {
			$module[$val->parent_name][] = json_decode(json_encode($val), true);
		}

		$tree = [];
		foreach ($module as $key => $val) {
			foreach ($val as $keys => $vals) {
				$parent_checked = $child_checked = '';
				if (!empty($data)) {
					$parent_checked = (in_array($vals['parent_id'], $data)) ? 'checked' : '';
					$child_checked = (in_array($vals['child_id'], $data)) ? 'checked' : '';
				}
				if ($keys == 0) {
					$tree[] = [
						'id' => $vals['parent_id'],
						'pid' => '',
						'name' => $key,
						'open' => 'open',
						'checked' => $parent_checked
					];
				}

				$tree[] = [
					'id' => $vals['child_id'],
					'pid' => $vals['parent_id'],
					'name' => Helper::separateText($vals['child_value']),
					'checked' => $child_checked
				];
			}
		}

		return $tree;

    }

    public function roles() {
    	return $this->belongsToMany(Role::class, 'module_role');
    }

}
