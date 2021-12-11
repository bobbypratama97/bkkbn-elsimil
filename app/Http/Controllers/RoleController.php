<?php

namespace App\Http\Controllers;

use App\Config;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

use Auth;
use DB;
use Helper;
use Session;

use App\Role;
use App\Module;
use App\User;
use App\RoleModule;
use App\UserRole;

class RoleController extends Controller
{
	public function __construct()
	{
		//$this->middleware('admin')->only('store', 'edit', 'update', 'destroy');
	}

	public function index()
	{
		$this->authorize('access', [\App\Role::class, Auth::user()->role, 'index']);

		$role = Role::select(['role.*', 'users.name AS nama'])->leftJoin('users', function($join) {
			$join->on('users.id', '=', 'role.created_by');
		})->get();

		return view('role.index', ['role' => $role]);
	}

	public function setting() {
		$this->authorize('access', [\App\Role::class, Auth::user()->role, 'setting']);

		$process = Helper::storeRoute();

		return redirect()->route('admin.role.index')->with(
			'success', 'Berhasil menambahkan setting role'
		);
	}

	public function create()
	{
		$this->authorize('access', [\App\Role::class, Auth::user()->role, 'create']);

		$status = Helper::status();
		$tree = Module::hierarchy();

		return view('role.create', ['status' => $status, 'tree' => $tree]);
	}

	public function store(Request $request)
	{
		$output = [];
		$insert = Role::create([
			'name' => $request->input('name'),
			'deskripsi' => $request->input('deskripsi'),
			'status' => $request->input('status'),
			'created_by' => Auth::id(),
			'created_at' => date('Y-m-d H:i:s')
		]);

		if ($insert) {
			foreach ($request->input('module') as $row) {
				$rolmod = RoleModule::create([
					'role_id' => $insert->id,
					'module_id' => $row
				]);
			}

			foreach ($request->input('user') as $row) {
				$roluser = UserRole::create([
					'role_id' => $insert->id,
					'user_id' => $row
				]);
			}

			$msg = 'Role berhasil ditambahkan';
			$output = Helper::successResponse($msg);
		} else {
			$msg = 'Role gagal ditambahkan';
			$output = Helper::successResponse($msg);
		}

		return json_encode($output);

		die();
	}

	public function show($id)
	{
		$this->authorize('access', [\App\Role::class, Auth::user()->role, 'show']);

		$role = Role::complex($id);
		$tree = Module::hierarchy($role[0]->combined);
		$user = UserRole::leftJoin('users', function($join) {
			$join->on('users.id', '=', 'role_user.user_id');
		})
		->select(['users.name'])
		->where('role_user.role_id', $id)
		->get();

		return view('role.show', ['role' => $role, 'tree' => $tree, 'user' => $user]);
	}

	public function edit($id)
	{
		$this->authorize('access', [\App\Role::class, Auth::user()->role, 'edit']);

		$role = Role::complex($id);
		$tree = Module::hierarchy($role[0]->combined);
		$existTree = implode(', ', $role[0]->combined);
		$status = Helper::status();

		return view('role.edit', ['role' => $role, 'tree' => $tree, 'status' => $status, 'exist' => $existTree]);
	}

	public function update(Request $request, $id)
	{
		$module = RoleModule::select(['module_id'])->where('role_id', $id)->get()->toArray();
		$module = array_column($module, 'module_id');

		$remModule = array_diff($module, $request->module);
		$insModule = array_diff($request->module, $module);

		$users = UserRole::select(['user_id'])->where('role_id', $id)->get()->toArray();
		$users = array_column($users, 'user_id');

		if (!empty($request->user)) {
			$remUser = array_diff($users, $request->user);
			$insUser = array_diff($request->user, $users);
		}

		$update = Role::where('id', $id)
			->update([
				'name' => $request->name,
				'deskripsi' => $request->deskripsi,
				'status' => $request->status,
				'updated_by' => Auth::id(),
				'updated_at' => date('Y-m-d H:i:s')
			]);

		if ($update) {
			if (!empty($remModule)) {
				RoleModule::where('role_id', $id)->whereIn('module_id', $remModule)->delete(); 
			}
			
			if (!empty($insModule)) {
				foreach ($insModule as $row) {
					RoleModule::create([
						'role_id' => $id,
						'module_id' => $row
					]);
				}
			}

			if (!empty($remUser)) {
				UserRole::where('role_id', $id)->whereIn('user_id', $remUser)->delete();
			}

			if (!empty($insUser)) {
				foreach ($insUser as $row) {
					UserRole::create([
						'role_id' => $id,
						'user_id' => $row
					]);
				}
			}

			$msg = 'Role berhasil diubah';
			$output = Helper::successResponse($msg);
		} else {
			$msg = 'Role gagal diubah';
			$output = Helper::successResponse($msg);
		}

		return json_encode($output);
		die();
	}

	public function userList(Request $request) {
		$id = $request->role_id;

		$list['data'] = [];

		if (empty($id)) {

			$exist = UserRole::select(['user_id'])->get()->toArray();
			$exist = array_column($exist, 'user_id');

			if(!empty($exist)) {
				$user = User::select(['id', 'name', 'created_at'])->whereNotIn('id', $exist)->orderBy('name')->get();
			} else {
				$user = User::select(['id', 'name', 'created_at'])->orderBy('name')->get();
			}

			if (!empty($user)) {
				foreach ($user as $row) {
					$tanggal = date('d M Y', strtotime($row->created_at));
					$list['data'][] = [
						$row->id,
						$row->name,
						$tanggal,
						0
					];
				}
			}
		} else {
			$exist = UserRole::select(['user_id'])->get()->toArray();
			$exist = array_column($exist, 'user_id');

			$exclude = UserRole::select(['user_id'])->where('role_id', '!=', $id)->get()->toArray();
			$exclude = array_column($exclude, 'user_id');

			$user = User::select(['id', 'name', 'created_at'])->whereNotIn('id', $exclude)->orderBy('name')->get();

			foreach ($user as $row) {
				$tanggal = date('d M Y', strtotime($row->created_at));
				$selected = (in_array($row->id, $exist)) ? 1 : 0;

				$list['data'][] = [
					$row->id,
					$row->name,
					$tanggal,
					$selected
				];
			}
		}

		return json_encode($list);

		exit();
	}

	public function delete(Request $request) {
		$update = Role::where('id', $request['id'])->update([
			'status' => 3, 
			'deleted_at' => date('Y-m-d H:i:s'), 
			'deleted_by' => Auth::id()
		]);

		$output = [];
		if ($update) {
			$msg = 'Role berhasil dihapus';
			$output = Helper::successResponse($msg);
		} else {
			$msg = 'Role gagal dihapus';
			$output = Helper::successResponse($msg);
		}

		return json_encode($output);

		die();
	}

	public function getChild($roleid){
		$role_child = Config::select('configs.value as id', 'configs.name')
			->where('code', 'role_child_'.$roleid)
			->get();

		return $role_child;
	}
}
