<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Kelurahan;

use App\User;
use App\Role;
use App\UserRole;

use App\MemberCouple;
use App\MemberDelegate;
use App\MemberDelegateLog;

use App\ChatHeader;
use App\Config;
use App\KuisResult;
use App\KuisResultComment;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $this->authorize('access', [\App\User::class, Auth::user()->role, 'index']);

        $auth = Auth::user();
        $role = Auth::user()->role;

        $user = User::leftJoin('users as us', function($join) {
            $join->on('us.id', '=', 'users.created_by');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'users.provinsi_id');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'users.kabupaten_id');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'users.kecamatan_id');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'users.kelurahan_id');
        })
        ->leftJoin('role_user', function($join) {
            $join->on('role_user.user_id', '=', 'users.id');
        })
        ->leftJoin('role', function($join) {
            $join->on('role.id', '=', 'role_user.role_id');
        })
        ->leftJoin('member_delegate', function($join) {
            $join->on('member_delegate.user_id', '=', 'users.id');
        })
        ->whereNull('users.deleted_by');

        if ($role == '1') {
            $kelurahan = [];
            $kecamatan = [];
            $kabupaten = [];
            $provinsi = Provinsi::whereNull('deleted_by')->get();

            if($request->provinsi != '') $kabupaten = Kabupaten::where('provinsi_kode', $request->provinsi)->whereNull('deleted_by')->orderBy('nama')->get();
            if($request->kabupaten != '') $kecamatan = Kecamatan::where('kabupaten_kode', $request->kabupaten)->whereNull('deleted_by')->orderBy('nama')->get();
            if($request->kecamatan != '') $kelurahan = Kelurahan::where('kecamatan_kode', $request->kecamatan)->whereNull('deleted_by')->orderBy('nama')->get();
        }
        else if ($role == '2') {
            $user = $user->where('users.provinsi_id', $auth->provinsi_id);
            // ->where('role.id', '>', $role);

            $provinsi = Provinsi::where('provinsi_kode', $auth->provinsi_id)->get();
            $kabupaten = Kabupaten::where('provinsi_kode', $auth->provinsi_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = [];//Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
            $kelurahan = [];

            if($request->kabupaten != '') $kecamatan = Kecamatan::where('kabupaten_kode', $request->kabupaten)->whereNull('deleted_by')->orderBy('nama')->get();
            if($request->kecamatan != '') $kelurahan = Kelurahan::where('kecamatan_kode', $request->kecamatan)->whereNull('deleted_by')->orderBy('nama')->get();
        }
        else if ($role == '3') {
            $user = $user->where('users.provinsi_id', $auth->provinsi_id)->where('users.kabupaten_id', $auth->kabupaten_id);
            // ->where('role.id', '>', $role);

            $provinsi = Provinsi::where('provinsi_kode', $auth->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $auth->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kabupaten_kode', $auth->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = [];

            if($request->kecamatan != '') $kelurahan = Kelurahan::where('kecamatan_kode', $request->kecamatan)->whereNull('deleted_by')->orderBy('nama')->get();
        }
        else if ($role == '4') {
            $user = $user->where('users.provinsi_id', $auth->provinsi_id)->where('users.kabupaten_id', $auth->kabupaten_id)->where('users.kecamatan_id', $auth->kecamatan_id);
            // ->where('role.id', '>', $role);

            $provinsi = Provinsi::where('provinsi_kode', $auth->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $auth->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $auth->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kecamatan_kode', $auth->kecamatan_id)->orderBy('nama')->get();
        }
        else if ($role == '5') {
            $user = $user->where('users.provinsi_id', $auth->provinsi_id)->where('users.kabupaten_id', $auth->kabupaten_id)->where('users.kecamatan_id', $auth->kecamatan_id)->where('users.kelurahan_id', $auth->kelurahan_id);
            // ->where('role.id', '>', $role);

            $provinsi = Provinsi::where('provinsi_kode', $auth->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $auth->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $auth->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kelurahan_kode', $auth->kelurahan_id)->orderBy('nama')->get();
        }else{
            $user = $user->where('users.provinsi_id', $auth->provinsi_id)->where('users.kabupaten_id', $auth->kabupaten_id)->where('users.kecamatan_id', $auth->kecamatan_id)->where('users.kelurahan_id', $auth->kelurahan_id);

            $provinsi = Provinsi::where('provinsi_kode', $auth->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $auth->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $auth->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kelurahan_kode', $auth->kelurahan_id)->orderBy('nama')->get();
        }

        $keyword = $role_id = '';
        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            $user = $user->where('users.name', 'like', '%' . $request->keyword . '%');
        }
        if (isset($request->role_id)) {
            $role_id = $request->role_id;
            $user = $user->where('role.id', $role_id);
        }
        if($request->status != '') $user->where('users.is_active', $request->status);
        if($request->provinsi != '') $user->where('users.provinsi_id', $request->provinsi);
        if($request->kabupaten != '') $user->where('users.kabupaten_id', $request->kabupaten);
        if($request->kecamatan != '') $user->where('users.kecamatan_id', $request->kecamatan);
        if($request->kelurahan != '') $user->where('users.kelurahan_id', $request->kelurahan);
        if($request->tanggal != '') {
            $tanggal_arr = explode('-', $request->tanggal);
            $start_date = explode('/',$tanggal_arr[0]);
            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];
            $end_date = explode('/',$tanggal_arr[1]);
            $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];

            $user->whereBetween(DB::raw('date(users.created_at)'), [$start_date, $end_date]);
        }
        if($request->member_sum != '') $user->having('total', $request->member_sum);

        $user = $user->select([
                'users.*',
                'us.name as nama',
                'adms_provinsi.nama as provinsi',
                'adms_kabupaten.nama as kabupaten',
                'adms_kecamatan.nama as kecamatan',
                'adms_kelurahan.nama as kelurahan',
                'role.name as roles',
                'role.id as role_id',
                DB::raw("count(member_delegate.id) AS total")
            ])
            ->groupBy('users.id')
            ->orderBy('users.id', 'DESC');
        
        $paginate = $user->paginate(10);
        $user = $paginate->items();

        $status_list = Helper::statusAdmin();
        $role_list = Role::whereNull('deleted_by')
            ->where('id', '>=', $role)
            ->get();
            
        $selected_region = [
            'prov' => $request->provinsi,
            'kab' => $request->kabupaten,
            'kec' => $request->kecamatan,
            'kel' => $request->kelurahan
        ];
        $status_pendamping = $request->status_pendamping;
        $status = $request->status;
        $tanggal = $request->tanggal;
        $member_sum = $request->member_sum;

        // $user = $user->get();
        return view('user.index', compact(
            'user','paginate', 'keyword', 'role', 'role_id',
            'provinsi', 'kecamatan','kelurahan', 'kabupaten',
            'selected_region', 'status', 'member_sum',
            'status_list', 'role_list', 'tanggal'
        ));
    }

    public function show($id) {
        $this->authorize('access', [\App\User::class, Auth::user()->role, 'show']);

        $user = User::leftJoin('users as us', function($join) {
            $join->on('us.id', '=', 'users.created_by');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'users.provinsi_id');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'users.kabupaten_id');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'users.kecamatan_id');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'users.kelurahan_id');
        })
        ->leftJoin('role_user', function($join) {
            $join->on('role_user.user_id', '=', 'users.id');
        })
        ->leftJoin('role', function($join) {
            $join->on('role.id', '=', 'role_user.role_id');
        })
        ->select([
            'users.*',
            'us.name as nama',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'role.name as roles'
        ])
        ->where('users.id', $id)
        ->first();

        return view('user.show', compact('user'));
    }

    public function edit($id) {
        $this->authorize('access', [\App\User::class, Auth::user()->role, 'edit']);
        $role = Auth::user()->role;

        $user = User::leftJoin('users as us', function($join) {
            $join->on('us.id', '=', 'users.created_by');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'users.provinsi_id');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'users.kabupaten_id');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'users.kecamatan_id');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'users.kelurahan_id');
        })
        ->leftJoin('role_user', function($join) {
            $join->on('role_user.user_id', '=', 'users.id');
        })
        ->leftJoin('role', function($join) {
            $join->on('role.id', '=', 'role_user.role_id');
        })
        ->select([
            'users.*',
            'us.name as nama',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'role.name as role',
            'role_user.role_id',
            'role_user.role_child_id'
        ])
        ->where('users.id', $id)
        ->first();

        if(!$user) return redirect()->back()->withErrors(['error' => 'Gagal', 'keterangan' => 'User tidak dapat ditemukan.']);

        $status = Helper::statusAdmin();
        
        $roles = Role::whereNull('deleted_by');

        if($user->id == Auth::id()) $roles->where('id', '>=', $role);
        else if(Auth::user()->role != 1) $roles->where('id', '>', $role);
            
        $roles = $roles->get();

        $role_childs = Config::where('code', 'role_child_'.$user->role_id)
            ->get();

        return view('user.edit', compact('user', 'status', 'roles', 'role_childs'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function($q) use($id) {
                    $q->whereRaw('deleted_at is null')
                        ->where('id', '<>', $id);
                }), 
            ],
            'password' => ['nullable', 'required_with:password_confirmation', 'same:password_confirmation'],
            'password_confirmation' =>  ['nullable']
        ], [
            'unique' => ':attribute sudah terdaftar.',
            'required' => ':attribute harus diisi.',
            'same' => ':attribute harus sama.',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors(['error' => 'Gagal', 'keterangan' => $validator->errors()->first()]);
        }

        //cek nomor telp
        $current_phone = User::where('no_telp', Helper::phoneNumber($request->no_telp))
            ->whereNotIn('id', [$id])
            ->whereRaw('deleted_at is null')
            ->first();

        if($current_phone) return Redirect::back()->withErrors(['error' => 'Gagal', 'keterangan' => 'Nomor telepon sudah terdaftar.']);

        $update = User::where('id', $id)->update([
            'email' => $request->email,
            'name' => $request->name,
            'no_telp' => Helper::phoneNumber($request->no_telp),
            'no_sk' => $request->no_sk,
            'sertifikat' => $request->sertifikat,
            'is_active' => $request->status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id()
        ]);

        //update password
        if($request->has('is_change_password') || $request->is_change_password == 'on'){
            User::where('id', $id)->update([
                'password' => Hash::make($request->password)
            ]);
        }

        $check = UserRole::where('user_id', $id)->first();
        if ($check) {
            $updaterole = UserRole::where('user_id', $id)->update([
                'role_id' => $request->role,
                'role_child_id' => $request->rolechild
            ]);
        } else {
            //mapping role child
            $insertrole = new UserRole;
            $insertrole->role_id = $request->role;
            $insertrole->role_child_id = $request->rolechild;
            $insertrole->user_id = $id;

            $insertrole->save();
        }

        $user = User::where('id', $id)->first();

        if ($user->is_active != '2' && $user->is_active != '3') {
            // Helper::sendMail([
            //     'id' => $user->id, 
            //     'tipe' => 1, 
            //     'name' => $user->name, 
            //     'email' => $user->email, 
            //     'content' => $user->is_active,
            //     'url' => 'lgn'
            // ]);
        }

        $msg = 'Pengubahan data petugas CMS berhasil';
        return redirect()->route('admin.user.index')->with('success', $msg);
    }

    public function delete(Request $request) {
        $update = User::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            //delete member delegasi
            $member_delegates = MemberDelegate::where('user_id', $request->id)->get()->toArray();
            $member_delegate_arr = array_column($member_delegates, 'member_id');
            MemberDelegate::where('user_id', $request->id)
                ->delete();

            foreach ($member_delegate_arr as $value) {
                MemberDelegateLog::insert([
                    'member_id' => $value,
                    'user_id' => $request->id,
                    'deleted_by' => Auth::id(),
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);
    
            }

            $msg = 'User admin CMS berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'User admin CMS gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function delegasi($id) {

        $auth = Auth::user();
        $role = Auth::user()->role;

        $user = User::leftJoin('member_delegate', function($join) {
            $join->on('member_delegate.user_id', '=', 'users.id');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'users.provinsi_id');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'users.kabupaten_id');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'users.kecamatan_id');
        })
        ->join('role_user as role', 'role.user_id', 'users.id')
        ->where('users.id', $id)
        ->select([
            'users.id',
            'users.name',
            'users.provinsi_id',
            'adms_provinsi.nama as provinsi',
            'users.kabupaten_id',
            'adms_kabupaten.nama as kabupaten',
            'users.kecamatan_id',
            'adms_kecamatan.nama as kecamatan',
            'role.role_child_id',
            'role.role_id as role_id',
            DB::raw("count(member_delegate.id) AS total_member")
        ])
        ->first();

        //echo $user;

        $users = User::leftJoin('member_delegate', function($join) {
            $join->on('member_delegate.user_id', '=', 'users.id');
        })
        ->join('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'users.provinsi_id');
        })
        ->join('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'users.kabupaten_id');
        })
        ->join('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'users.kecamatan_id');
        })
        ->join('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'users.kelurahan_id');
        })
        ->join('role_user as role', 'role.user_id', 'users.id')
        ->where('users.provinsi_id', $user->provinsi_id)
        ->where('users.kabupaten_id', $user->kabupaten_id)
        ->where('users.kecamatan_id', $user->kecamatan_id)
        ->where('users.id', '!=', $user->id)
        ->where('role.role_child_id', $user->role_child_id)
        ->whereNull('users.deleted_by')
        ->select([
            'users.id as user_id',
            'users.name as nama',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            DB::raw("count(member_delegate.id) AS total")
        ])
        ->get();

        /*if ($role == '1') {
            $provinsi = $kabupaten = $kecamatan = [];
        }*/

        $oldmember = MemberDelegate::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'member_delegate.member_id');
        })
        ->where('user_id', $id)
        ->select(['members.id', 'members.name'])
        ->get();

        $valold = '';
        if ($oldmember->isNotEmpty()) {
            foreach ($oldmember as $key => $row) {
                $valold .= $row->id . ', ';
            }
        }
        $valold = substr($valold, 0, -2);

        return view('user.delegasi', compact('user', 'users', 'valold', 'oldmember'));
    }

    public function submit(Request $request) {
        //echo '<pre>'; 
        //print_r ($request->all()); //die;

        if (!empty($request->oldmember)) {
            $check = MemberDelegate::where('user_id', $request->cid)->whereNotIn('member_id', $request->oldmember)->get();

            if ($check->isNotEmpty()) {
                foreach ($check as $key => $row) {
                    $couple = MemberCouple::select(['couple_id'])->where('member_id', $row->member_id)->where('status', 'APM200')->get();

                    if ($couple->isNotEmpty()) {
                        foreach ($couple as $keys => $rows) {
                            $delete = MemberDelegate::where('member_id', $rows->couple_id)->delete();

                            $updateChat = ChatHeader::where('member_id', $rows->couple_id)->update([
                                'responder_id' => null,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::id()
                            ]);

                            $updateKuis = KuisResult::where('member_id', $rows->couple_id)->where('status', 1)->update([
                                'responder_id' => null,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::id()
                            ]);
                        }
                    }

                    $del = MemberDelegate::where('member_id', $row->member_id)->delete();

                    $updateChat = ChatHeader::where('member_id', $row->member_id)->update([
                        'responder_id' => null,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);

                    $updateKuis = KuisResult::where('member_id', $row->member_id)->where('status', 1)->update([
                        'responder_id' => null,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);
                }
            }
        } else {
            $check = MemberDelegate::where('user_id', $request->cid)->get();

            if ($check->isNotEmpty()) {
                foreach ($check as $key => $row) {
                    $del = MemberDelegate::where('member_id', $row->member_id)->delete();

                    $updateChat = ChatHeader::where('member_id', $row->member_id)->update([
                        'responder_id' => null,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);

                    $updateKuis = KuisResult::where('member_id', $row->member_id)->where('status', 1)->update([
                        'responder_id' => null,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);
                }
            }
        }

        //die;

        if (!empty($request->newmember)) {
            foreach ($request->newmember as $row) {

                $simpan = new MemberDelegate;
                $simpan->member_id = $row;
                $simpan->user_id = $request->cid;
                $simpan->created_at = date('Y-m-d H:i:s');
                $simpan->created_by = Auth::id();

                $simpan->save();

                $log = new MemberDelegateLog;
                $log->member_id = $row;
                $log->user_id = $request->cid;
                $log->created_at = date('Y-m-d H:i:s');
                $log->created_by = Auth::id();

                $log->save();

                $update = ChatHeader::where('member_id', $row)->update([
                    'responder_id' => $request->cid,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::id()
                ]);

                $get = KuisResult::where('member_id', $row)->where('status', 1)->first();

                $updated = KuisResult::where('member_id', $row)->where('status', 1)->update([
                    'responder_id' => $request->cid,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::id()
                ]);


                $check = MemberCouple::select(['couple_id'])->where('member_id', $row)->where('status', 'APM200')->get();

                if ($check->isNotEmpty()) {
                    foreach ($check as $keys => $rows) {
                        $cekdelegate = MemberDelegate::where('member_id', $rows->couple_id)->first();

                        if ($cekdelegate) {
                            $updateCouple = MemberDelegate::where('member_id', $rows->couple_id)->update([
                                'user_id' => $request->cid,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::id()
                            ]);
                        } else {
                            $simpanCouple = new MemberDelegate;
                            $simpanCouple->member_id = $rows->couple_id;
                            $simpanCouple->user_id = $request->cid;
                            $simpanCouple->created_at = date('Y-m-d H:i:s');
                            $simpanCouple->created_by = Auth::id();

                            $simpanCouple->save();
                        }

                        $logCouple = new MemberDelegateLog;
                        $logCouple->member_id = $rows->couple_id;
                        $logCouple->user_id = $request->cid;
                        $logCouple->created_at = date('Y-m-d H:i:s');
                        $logCouple->created_by = Auth::id();

                        $logCouple->save();

                        $updateChatCouple = ChatHeader::where('member_id', $rows->couple_id)->update([
                            'responder_id' => $request->cid,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::id()
                        ]);

                        $updateKuisCouple = KuisResult::where('member_id', $rows->couple_id)->where('status', 1)->update([
                            'responder_id' => $request->cid,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::id()
                        ]);
                    }
                }

            }
        }

        $msg = 'Delegasi member berhasil';
        return redirect()->route('admin.user.index')->with('success', $msg);
    }

    public function move(Request $request) {
        $select = MemberDelegate::select(['member_id'])->where('user_id', $request->cid)->get()->toArray();

        $update = MemberDelegate::where('user_id', $request->cid)->update([
            'user_id' => $request->user,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id()
        ]);

        foreach ($select as $row) {
            $simpan = new MemberDelegateLog;
            $simpan->member_id = $row['member_id'];
            $simpan->user_id = $request->user;
            $simpan->created_at = date('Y-m-d H:i:s');
            $simpan->created_by = Auth::id();

            $simpan->save();

            $update = ChatHeader::where('member_id', $row['member_id'])->update([
                'responder_id' => $request->user,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $updateKuisCouple = KuisResult::where('member_id', $row['member_id'])->where('status', 1)->update([
                'responder_id' => $request->user,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

        }

        $msg = 'Delegasi member berhasil dipindahkan';
        return redirect()->route('admin.user.index')->with('success', $msg);
    }
}
