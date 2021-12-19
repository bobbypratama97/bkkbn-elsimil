<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;

use Str;
use File;
use DB;
use Helper;

use Auth;

use App\Member;
use App\ChatHeader;
use App\ChatMessage;
use App\UserRole;

use App\MemberDelegate;
use App\MemberDelegateLog;

class ChatController extends Controller
{
    /**
     * Authinticate the connection for pusher
     *
     * @param Request $request
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {

        $this->authorize('access', [\App\Chat::class, Auth::user()->role, 'index']);

        //echo '<pre>';

        $baseurlmember = env('BASE_URL') . env('BASE_URL_PROFILE');
        $baseurlavatar = env('BASE_URL') . env('BASE_URL_CHAT');

        $user = Auth::user();
        $role = Auth::user()->role;

        $where = '';
        if ($role == '1') {
            $condition = "";
        }

        if ($role == '2') {
            $where = "
                chat_header.provinsi_kode = '{$user->provinsi_id}' AND 
            ";
            $condition = "AND {$where} (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL))";
        }

        if ($role == '3') {
            $where = "
                chat_header.provinsi_kode = '{$user->provinsi_id}' AND 
                chat_header.kabupaten_kode = '{$user->kabupaten_id}' AND 
            ";
            $condition = "AND {$where} (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL))";
        }

        if ($role == '4') {
            $where = "
                chat_header.provinsi_kode = '{$user->provinsi_id}' AND 
                chat_header.kabupaten_kode = '{$user->kabupaten_id}' AND 
                chat_header.kecamatan_kode = '{$user->kecamatan_id}' AND 
            ";
            $condition = "AND {$where} (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL))";
        }

        if ($role == '5') {
            $where = "
                chat_header.provinsi_kode = '{$user->provinsi_id}' AND 
                chat_header.kabupaten_kode = '{$user->kabupaten_id}' AND 
                chat_header.kecamatan_kode = '{$user->kecamatan_id}' AND 
                chat_header.kelurahan_kode = '{$user->kelurahan_id}' AND 
            ";
            $condition = "AND {$where} ((responder_id = {$user->id}))";
        }

        // $sql = "
        //     SELECT 
        //         members.id,
        //         members.name,
        //         members.gender,
        //         members.foto_pic,
        //         adms_provinsi.nama AS provinsi,
        //         adms_kabupaten.nama AS kabupaten,
        //         adms_kecamatan.nama AS kecamatan,
        //         adms_kelurahan.nama AS kelurahan,
        //         chat_header.id AS chatid,
        //         chat_header.responder_id AS header_responder_id,
        //         chat_header.type,
        //         role_user.role_id AS header_role_id,
        //         (SELECT COUNT(*) FROM chat_message WHERE member_id = members.id) AS total,
        //         chat_message.message,
        //         chat_message.status,
        //         chat_message.response_id AS child_responder_id,
        //         chat_message.created_at,
        //         users.name AS petugas
        //     FROM chat_header 
        //     LEFT JOIN members ON members.id = chat_header.member_id
        //     LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = members.provinsi_id
        //     LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = members.kabupaten_id
        //     LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
        //     LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = members.kelurahan_id
        //     LEFT JOIN role_user ON role_user.user_id = chat_header.responder_id
        //     LEFT JOIN chat_message ON chat_message.chat_id = chat_header.id AND chat_message.last = 1
        //     LEFT JOIN users ON users.id = chat_header.responder_id
        //     WHERE 1 = 1 AND chat_header.type = '".Auth::user()->roleChild."' $condition
        //     ORDER BY chat_message.id DESC LIMIT 100
        // ";
        $query = ChatHeader::selectRaw('
                members.id,
                members.name,
                members.gender,
                members.foto_pic,
                adms_provinsi.nama AS provinsi,
                adms_kabupaten.nama AS kabupaten,
                adms_kecamatan.nama AS kecamatan,
                adms_kelurahan.nama AS kelurahan,
                chat_header.id AS chatid,
                chat_header.responder_id AS header_responder_id,
                chat_header.type,
                role_user.role_id AS header_role_id,
                (SELECT COUNT(*) FROM chat_message WHERE member_id = members.id) AS total,
                chat_message.message,
                chat_message.status,
                chat_message.response_id AS child_responder_id,
                chat_message.created_at,
                users.name AS petugas
            ')
            ->join('members', 'members.id', 'chat_header.member_id')
            ->join('adms_provinsi', 'adms_provinsi.provinsi_kode', 'members.provinsi_id')
            ->join('adms_kabupaten', 'adms_kabupaten.kabupaten_kode', 'members.kabupaten_id')
            ->join('adms_kecamatan', 'adms_kecamatan.kecamatan_kode', 'members.kecamatan_id')
            ->join('adms_kelurahan', 'adms_kelurahan.kelurahan_kode', 'members.kelurahan_id')
            ->leftJoin('role_user', 'role_user.user_id', 'chat_header.responder_id')
            ->join('chat_message', function($q) {
                $q->on('chat_message.chat_id', 'chat_header.id')
                    ->where('chat_message.last', 1);
            })
            ->leftJoin('users', 'users.id', 'chat_header.responder_id')
            ->where('chat_header.type', Auth::user()->roleChild)
            ->whereRaw('responder_id = '.$user->id);
            // ->whereRaw('(role_user.role_id = 1 OR (responder_id = '.$user->id.' OR responder_id IS NULL))');

        if($condition != '') $query->whereRaw('1=1 '.$condition);
            
        // $list = DB::select($sql)->paginate(10);
        $paginate = $query->paginate(10);
        $list = $paginate->items(); 
        // return $list;
        
        if (!empty($list)) {
            $curr = '';
            foreach ($list as $key => $row) {
                $curr .= $row->id . ', ';
            }

            $curr = substr($curr, 0, -2);


            // $conditions = "AND (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL))";
            $conditions = "AND responder_id = {$user->id}";

            $sql1 = "
                SELECT 
                    members.id,
                    members.name,
                    members.gender,
                    members.foto_pic,
                    adms_provinsi.nama AS provinsi,
                    adms_kabupaten.nama AS kabupaten,
                    adms_kecamatan.nama AS kecamatan,
                    adms_kelurahan.nama AS kelurahan,
                    chat_header.id AS chatid,
                    chat_header.responder_id AS header_responder_id,
                    role_user.role_id AS header_role_id,
                    (SELECT COUNT(*) FROM chat_message WHERE member_id = members.id) AS total,
                    chat_message.message,
                    chat_message.status,
                    chat_message.response_id AS child_responder_id,
                    chat_message.created_at,
                    users.name AS petugas
                FROM chat_header 
                LEFT JOIN members ON members.id = chat_header.member_id
                LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = members.provinsi_id
                LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = members.kabupaten_id
                LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
                LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = members.kelurahan_id
                LEFT JOIN role_user ON role_user.user_id = chat_header.responder_id
                LEFT JOIN chat_message ON chat_message.chat_id = chat_header.id AND chat_message.last = 1
                LEFT JOIN users ON users.id = chat_header.responder_id
                LEFT JOIN member_delegate ON member_delegate.member_id = members.id
                WHERE 1 = 1 AND member_delegate.member_id not in ({$curr}) AND member_delegate.user_id = {$user->id} {$conditions}
                ORDER BY chat_message.id DESC LIMIT 100
            ";

            $couple = DB::select($sql1);

            $list = array_merge($list, $couple);
        }


        $res = [];
        foreach ($list as $key => $row) {
            $row->lokasi = $row->kelurahan . ', ' . $row->kecamatan . ', ' . $row->kabupaten . ', ' . $row->provinsi;
            $row->waktu = Carbon::parse($row->created_at)->diffForHumans();

            if (!empty($row->foto_pic)) {
                if ($row->foto_pic == 'noimage.png') {
                    if ($row->gender == '2') {
                        $row->gambar = $baseurlavatar . '018-girl-9.svg';
                    } else if ($row->gender == '1') {
                        $row->gambar = $baseurlavatar . '009-boy-4.svg';
                    } else {
                        $row->gambar = $baseurlavatar . '024-boy-9.svg';
                    }
                } else {
                    $row->gambar = $baseurlmember . $row->foto_pic;
                }
            } else {
                if ($row->gender == '2') {
                    $row->gambar = $baseurlavatar . '018-girl-9.svg';
                } else if ($row->gender == '1') {
                    $row->gambar = $baseurlavatar . '009-boy-4.svg';
                } else {
                    $row->gambar = $baseurlavatar . '024-boy-9.svg';
                }
            }

            if ($row->status == 'send') {
                $row->label = 'New';
                $row->background = 'success';
                $row->aktif = 1;
            } else if ($row->header_responder_id != $row->child_responder_id) {
                $row->label = 'Open';
                $row->background = 'warning';
                $row->aktif = 1;
            } else if ($row->header_responder_id == $row->child_responder_id) {
                $row->label = ($role == '1') ? 'Aktif' : 'Aktif';
                $row->background = 'primary';
                $row->aktif = 1;
            }

        }

        $selected = 'mine';

        return view ('chat.index', compact('list', 'selected', 'paginate'));
    }

    public function search(Request $request) {

        $this->authorize('access', [\App\Chat::class, Auth::user()->role, 'index']);

        // echo '<pre>';
        // print_r($request->all());die;

        $baseurlmember = env('BASE_URL') . env('BASE_URL_PROFILE');
        $baseurlavatar = env('BASE_URL') . env('BASE_URL_CHAT');

        $user = Auth::user();
        $role = Auth::user()->role;
        $roleChild = Auth::user()->roleChild;

        if ($request->search == 'mine') {
            // $filter = " AND (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL)) AND type = {$roleChild}";
            $filter = " AND responder_id = {$user->id}";
        } else if ($request->search == 'other') {
            $filter = " AND (role_user.role_id = 1 OR (responder_id != {$user->id}))";
        } else if ($request->search == 'all' || $request->search == '') {
            //$filter = "(role_user.role_id = 1 OR (responder_id IS NULL))";
            $filter = "";
        } else if ($request->search == 'nh') {
            $filter = " AND (role_user.role_id = 1 OR (responder_id IS NULL))";
        //} else if ($request->search == 'new') {
        //    $filter = " AND (role_user.role_id = 1 OR (responder_id IS NULL))";
        } 
        // else {
        //     $filter = " AND (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL))";
        // }

        if($request->keyword != ''){
            $keyword = $request->keyword;
            $filter .= ' and (users.name like "%'.$keyword.'%"';
            $filter .= ' or members.name like "%'.$keyword.'%"';
            $filter .= ' or chat_message.message like "%'.$keyword.'%"';
            $filter .= ' or chat_message.status like "%'.$keyword.'%")';
        }

        $where = '';
        if ($role == '1') {
            $condition = "{$filter}";
        }

        if ($role == '2') {
            $where = "
                AND chat_header.provinsi_kode = '{$user->provinsi_id}'
            ";
            $condition = "{$where} {$filter}";
        }

        if ($role == '3') {
            $where = "
                AND chat_header.provinsi_kode = '{$user->provinsi_id}' 
                AND chat_header.kabupaten_kode = '{$user->kabupaten_id}'
            ";
            $condition = "{$where} {$filter}";
        }

        if ($role == '4') {
            $where = "
                AND chat_header.provinsi_kode = '{$user->provinsi_id}' 
                AND chat_header.kabupaten_kode = '{$user->kabupaten_id}' 
                AND chat_header.kecamatan_kode = '{$user->kecamatan_id}'
            ";
            $condition = "{$where} {$filter}";
        }

        if ($role == '5') {
            $where = "
                AND chat_header.provinsi_kode = '{$user->provinsi_id}' 
                AND chat_header.kabupaten_kode = '{$user->kabupaten_id}' 
                AND chat_header.kecamatan_kode = '{$user->kecamatan_id}'
                AND chat_header.kelurahan_kode = '{$user->kelurahan_id}'
            ";
            $condition = "{$where} {$filter}";
        }

        // $sql = "
        //     SELECT 
        //         members.id,
        //         members.name,
        //         members.gender,
        //         members.foto_pic,
        //         adms_provinsi.nama AS provinsi,
        //         adms_kabupaten.nama AS kabupaten,
        //         adms_kecamatan.nama AS kecamatan,
        //         adms_kelurahan.nama AS kelurahan,
        //         chat_header.id AS chatid,
        //         chat_header.responder_id AS header_responder_id,
        //         role_user.role_id AS header_role_id,
        //         (SELECT COUNT(*) FROM chat_message WHERE member_id = members.id) AS total,
        //         chat_message.message,
        //         chat_message.status,
        //         chat_message.response_id AS child_responder_id,
        //         chat_message.created_at,
        //         users.name AS petugas
        //     FROM chat_header 
        //     LEFT JOIN members ON members.id = chat_header.member_id
        //     LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = members.provinsi_id
        //     LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = members.kabupaten_id
        //     LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
        //     LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = members.kelurahan_id
        //     LEFT JOIN role_user ON role_user.user_id = chat_header.responder_id
        //     LEFT JOIN chat_message ON chat_message.chat_id = chat_header.id AND chat_message.last = 1
        //     LEFT JOIN users ON users.id = chat_header.responder_id
        //     WHERE 1 = 1 {$condition}
        //     ORDER BY chat_message.id DESC LIMIT 100
        // ";


        // $list = DB::select($sql);
        $query = ChatHeader::selectRaw('
                members.id,
                members.name,
                members.gender,
                members.foto_pic,
                adms_provinsi.nama AS provinsi,
                adms_kabupaten.nama AS kabupaten,
                adms_kecamatan.nama AS kecamatan,
                adms_kelurahan.nama AS kelurahan,
                chat_header.id AS chatid,
                chat_header.responder_id AS header_responder_id,
                chat_header.type,
                role_user.role_id AS header_role_id,
                (SELECT COUNT(*) FROM chat_message WHERE member_id = members.id) AS total,
                chat_message.message,
                chat_message.status,
                chat_message.response_id AS child_responder_id,
                chat_message.created_at,
                users.name AS petugas
            ')
            ->join('members', 'members.id', 'chat_header.member_id')
            ->join('adms_provinsi', 'adms_provinsi.provinsi_kode', 'members.provinsi_id')
            ->join('adms_kabupaten', 'adms_kabupaten.kabupaten_kode', 'members.kabupaten_id')
            ->join('adms_kecamatan', 'adms_kecamatan.kecamatan_kode', 'members.kecamatan_id')
            ->join('adms_kelurahan', 'adms_kelurahan.kelurahan_kode', 'members.kelurahan_id')
            ->leftJoin('role_user', 'role_user.user_id', 'chat_header.responder_id')
            ->join('chat_message', function($q) {
                $q->on('chat_message.chat_id', 'chat_header.id')
                    ->where('chat_message.last', 1);
            })
            ->leftJoin('users', 'users.id', 'chat_header.responder_id');

        if($condition != '') $query->whereRaw('1=1 '.$condition);
            
        $paginate = $query->orderBy('chat_message.id', 'DESC')->paginate(10);
        $list = $paginate->items(); 
        // echo '<pre>';
        // print_r($list);die;

        if (!empty($list)) {
            $curr = '';
            foreach ($list as $key => $row) {
                $curr .= $row->id . ', ';
            }
            $curr = substr($curr, 0, -2);

            $conditions = "AND (role_user.role_id = 1 OR (responder_id = {$user->id} OR responder_id IS NULL))";

            $sql1 = "
                SELECT 
                    members.id,
                    members.name,
                    members.gender,
                    members.foto_pic,
                    adms_provinsi.nama AS provinsi,
                    adms_kabupaten.nama AS kabupaten,
                    adms_kecamatan.nama AS kecamatan,
                    adms_kelurahan.nama AS kelurahan,
                    chat_header.id AS chatid,
                    chat_header.responder_id AS header_responder_id,
                    role_user.role_id AS header_role_id,
                    (SELECT COUNT(*) FROM chat_message WHERE member_id = members.id) AS total,
                    chat_message.message,
                    chat_message.status,
                    chat_message.response_id AS child_responder_id,
                    chat_message.created_at,
                    users.name AS petugas
                FROM chat_header 
                LEFT JOIN members ON members.id = chat_header.member_id
                LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = members.provinsi_id
                LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = members.kabupaten_id
                LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
                LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = members.kelurahan_id
                LEFT JOIN role_user ON role_user.user_id = chat_header.responder_id
                LEFT JOIN chat_message ON chat_message.chat_id = chat_header.id AND chat_message.last = 1
                LEFT JOIN users ON users.id = chat_header.responder_id
                LEFT JOIN member_delegate ON member_delegate.member_id = members.id
                WHERE 1 = 1 AND member_delegate.member_id not in ({$curr}) AND member_delegate.user_id = {$user->id} {$conditions}
                ORDER BY chat_message.id DESC LIMIT 100
            ";

            $couple = DB::select($sql1);

            $list = array_merge($list, $couple);
        }

        $res = [];
        foreach ($list as $key => $row) {
            $row->lokasi = $row->kelurahan . ', ' . $row->kecamatan . ', ' . $row->kabupaten . ', ' . $row->provinsi;
            $row->waktu = Carbon::parse($row->created_at)->diffForHumans();

            if (!empty($row->foto_pic)) {
                if ($row->foto_pic == 'noimage.png') {
                    if ($row->gender == '2') {
                        $row->gambar = $baseurlavatar . '018-girl-9.svg';
                    } else if ($row->gender == '1') {
                        $row->gambar = $baseurlavatar . '009-boy-4.svg';
                    } else {
                        $row->gambar = $baseurlavatar . '024-boy-9.svg';
                    }
                } else {
                    $row->gambar = $baseurlmember . $row->foto_pic;
                }
            } else {
                if ($row->gender == '2') {
                    $row->gambar = $baseurlavatar . '018-girl-9.svg';
                } else if ($row->gender == '1') {
                    $row->gambar = $baseurlavatar . '009-boy-4.svg';
                } else {
                    $row->gambar = $baseurlavatar . '024-boy-9.svg';
                }
            }

            if ($row->status == 'send') {
                $row->label = 'New';
                $row->background = 'success';
                $row->aktif = 1;
            } else if ($row->header_responder_id != $row->child_responder_id) {
                $row->label = 'Open';
                $row->background = 'warning';
                $row->aktif = 1;
            } else if ($row->header_responder_id == $row->child_responder_id) {
                $row->label = ($role == '1') ? 'Aktif' : 'Aktif';
                $row->background = 'primary';
                $row->aktif = 1;
            }

        }

        $selected = $request->search;

        return view ('chat.index', compact('list', 'selected', 'paginate'));
    }

    public function show($id) {
        return view('chat.show', compact('id'));
    }

    public function detail(Request $request) {

        /*$list = ChatMessage::whereNull('deleted_by')
            ->where('chat_id', $request->id)
            ->orderBy('id', 'ASC')
            ->limit(12)
            ->get();*/

        $get = ChatMessage::select(['member_id'])->where('chat_id', $request->id)->first();
        $header = ChatHeader::select(['responder_id'])->where('id', $request->id)->first();
        $member = Member::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
        })
        ->where('members.id', $get->member_id)
        ->select([
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'members.name',
            'members.id'
        ])
        ->first();

        $member->lokasi = $member->kelurahan . ', ' . $member->kecamatan . ', ' . $member->kabupaten . ', ' . $member->provinsi;

        $sql = "
            SELECT * FROM (
                SELECT * FROM chat_message WHERE chat_id = '{$request->id}' AND deleted_by IS NULL ORDER BY id DESC LIMIT 12
            ) 
            a ORDER BY a.id
        ";

        $list = DB::select($sql);

        $output = '';
        foreach ($list as $key => $row) {
            $waktu = Carbon::parse($row->created_at)->diffForHumans();
            if ($row->status == 'send') {
                $output .= '
                    <div class="d-flex flex-column mb-5 align-items-start">
                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                            '. $row->message .'
                            <span class="text-muted font-size-sm float-right ml-5 mt-1">' . $waktu . '</span>
                        </div>
                    </div>
                ';
            } else {
                $output .= '
                    <div class="d-flex flex-column mb-5 align-items-end">
                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
                            '.$row->message.'
                            <span class="text-muted font-size-sm float-right ml-5 mt-1">' . $waktu . '</span>
                        </div>
                    </div>
                ';
            }
        }

        return response()->json([
            'current' => Auth::id(),
            'user' => $header->responder_id,
            'locked' => (Auth::user()->role == '1' || Auth::user()->role == '2' || Auth::user()->role == '3') ? 0 : 1,
            'member' => $member,
            'detail' => $output
        ]);

        die();
    }

    public function send(Request $request) {

        $insert = new ChatMessage;
        $insert->chat_id = $request->chatid;
        $insert->member_id = $request->member;
        $insert->response_id = Auth::id();
        $insert->message = $request->message;
        $insert->status = 'reply';
        $insert->last = 1;
        $insert->created_at = date('Y-m-d H:i:s');
        $insert->created_by = Auth::id();

        if ($insert->save()) {

            $update = ChatMessage::where('chat_id', $request->chatid)->where('id', '!=', $insert->id)->update([
                'last' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            //if (Auth::user()->role != '1' || Auth::user()->role != '2' || Auth::user()->role != '3') {
                $updateheader = ChatHeader::where('id', $request->chatid)->update([
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::id()
                ]);
            //}

            /*$update = ChatMessage::where('chat_id', $request->chatid)->whereNull('response_id')->update([
                'response_id' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => Auth::id()
            ]);*/

            $msg = 'Success';
        } else {
            $msg = 'Failed';
        }

        return response()->json([
            'msg' => $msg
        ]);

        die();
    }

    public function history(Request $request) {

        $list = ChatMessage::whereNull('deleted_by')
            ->where('chat_id', $request->id)
            ->orderBy('id', 'ASC')
            ->get();

        $output = '';
        foreach ($list as $key => $row) {
            $waktu = Carbon::parse($row->created_at)->diffForHumans();
            if ($row->status == 'send') {
                $output .= '
                    <div class="d-flex flex-column mb-5 align-items-start">
                        <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                            '. $row->message .'
                            <span class="text-muted font-size-sm float-right ml-5 mt-1">' . $waktu . '</span>
                        </div>
                    </div>
                ';
            } else {
                $output .= '
                    <div class="d-flex flex-column mb-5 align-items-end">
                        <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
                            '.$row->message.'
                            <span class="text-muted font-size-sm float-right ml-5 mt-1">' . $waktu . '</span>
                        </div>
                    </div>
                ';
            }
        }

        return response()->json([
            'detail' => $output
        ]);

        die();
    }

    public function refresh(Request $request) {
        $baseurlmember = env('BASE_URL') . env('BASE_URL_PROFILE');
        $baseurlavatar = env('BASE_URL') . env('BASE_URL_CHAT');

        $id = Auth::id();
        $user = Auth::user();

        $role = UserRole::where('user_id', $id)->first();

        $where = '';
        if ($role->role_id != '1') {
            $where = "
                chat_header.provinsi_kode = '{$user->provinsi_id}' AND 
                chat_header.kabupaten_kode = '{$user->kabupaten_id}' AND 
                chat_header.kecamatan_kode = '{$user->kecamatan_id}' AND 
            ";
        }

        $sql = "
            SELECT 
                members.id,
                members.name,
                members.gender,
                members.foto_pic,
                adms_provinsi.nama AS provinsi,
                adms_kabupaten.nama AS kabupaten,
                adms_kecamatan.nama AS kecamatan,
                adms_kelurahan.nama AS kelurahan,
                chat_header.id AS chat_id,
                chat_header.responder_id AS header_responder_id,
                role_user.role_id AS header_role_id
            FROM chat_header 
            LEFT JOIN members ON members.id = chat_header.member_id
            LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = chat_header.provinsi_kode
            LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = chat_header.kabupaten_kode
            LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = chat_header.kecamatan_kode
            LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = chat_header.kelurahan_kode
            LEFT JOIN role_user ON role_user.user_id = chat_header.responder_id
            WHERE {$where} (role_user.role_id = 1 OR (responder_id = {$id} OR responder_id IS NULL))
        ";

        $list = DB::select($sql);

        $res = [];
        $chatid = [];
        foreach ($list as $key => $row) {
            $detail = ChatMessage::where('chat_id', $row->chat_id)->orderBy('id', 'DESC')->select(['id', 'message', 'status', 'created_at', 'response_id'])->first();

            $row->lokasi = $row->kelurahan . ', ' . $row->kecamatan . ', ' . $row->kabupaten . ', ' . $row->provinsi;
            $row->message = $detail->message;
            $row->chat_status = $detail->status;
            $row->waktu = $detail->created_at;

            if (!empty($row->foto_pic)) {
                if ($row->foto_pic == 'noimage.jpg') {
                    if ($row->gender == '2') {
                        $row->avatar = $baseurlavatar . '018-girl-9.svg';
                    } else if ($row->gender == '1') {
                        $row->avatar = $baseurlavatar . '009-boy-4.svg';
                    } else {
                        $row->avatar = $baseurlavatar . '024-boy-9.svg';
                    }
                } else {
                    $row->avatar = $baseurlmember . $row->foto_pic;
                }
            } else {
                if ($row->gender == '2') {
                    $row->avatar = $baseurlavatar . '018-girl-9.svg';
                } else if ($row->gender == '1') {
                    $row->avatar = $baseurlavatar . '009-boy-4.svg';
                } else {
                    $row->avatar = $baseurlavatar . '024-boy-9.svg';
                }
            }


            $rolemsg = '';
            if (!empty($detail->response_id)) {
                $detailrole = UserRole::where('user_id', $detail->response_id)->first();
                $rolemsg = $detailrole->role_id;
            }

            if (!empty($row->chat_status) && $row->chat_status == 'reply') {
                if ($rolemsg == '1') {

                    if ($detail->response_id == $id) {
                        $row->naming = 'Anda : ';
                    } else {
                        $row->naming = 'Super Admin : ';
                    }

                } else {
                    if (!empty($detail->response_id)) {

                        if ($detail->response_id == $id) {
                            $row->naming = 'Anda : ';
                        } else {
                            $row->naming = 'Admin : ';
                        }
                    } else {
                        $row->naming = '';
                    }
                }
            } else {
                $row->naming = '';
            }

            if ($row->header_responder_id == $id) {
                $row->class = 'm-list-active';
                $chatid[] = $row->chat_id;
            } else {
                $row->class = '';
                $chatid[] = '';
            }
        }

        $count = count($list);
        $chatid = array_filter($chatid);
        if (!empty($chatid)) {
            $chatid = $chatid[0];

            $user = ChatHeader::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'chat_header.member_id');
            })
            ->leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            })
            ->leftJoin('adms_kelurahan', function($join) {
                $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
            })
            ->select([
                'members.id as member_id',
                'members.name',
                'adms_provinsi.provinsi_kode as provinsi',
                'adms_kabupaten.kabupaten_kode as kabupaten',
                'adms_kecamatan.kecamatan_kode as kecamatan',
                'adms_kelurahan.kelurahan_kode as kelurahan'
            ])
            ->where('chat_header.id', $chatid)
            ->first();

            $memberid = $user->member_id;
            $name = $user->name;
            $lokasi = $user->kelurahan . ', ' . $user->kecamatan . ', ' . $user->kabupaten . ', ' . $user->provinsi;
        } else {
            $chatid = '';
            $memberid = '';
            $name = '';
            $lokasi = '';
        }


        $output = '';
        foreach ($list as $keys => $rows) {

            $jam = Carbon::parse($rows->waktu)->diffForHumans();

            $arr = explode(" ", $rows->lokasi);
            $arr = array_splice($arr, 0, 5);
            $lokasi = implode(" ", $arr) . ' ...';

            $arrs = explode(" ", $rows->message);
            $arrs = array_splice($arrs, 0, 9);
            $message = implode(" ", $arrs) . ' ...';

            $output .= '
                <table class="messenger-list-item ' . $rows->class . '" 
                    data-id="' . $rows->chat_id . '" 
                    data-member="' . $rows->id . '" 
                    data-responder="' . $rows->header_responder_id . '" 
                    data-user="' . $rows->name . '" 
                    data-lokasi="' . $rows->lokasi . '" 
                    id="memberchat-' . $rows->chat_id . '" >
                    <tbody>
                        <tr data-action="'.$keys.'">
                            <td style="position: relative">
                                <div class="avatar av-m" style="background-image: url(' . $rows->avatar . ');"></div>
                            </td>
                            <td>
                                <p class="text-primary mb-0" data-id="user_6">' . $rows->name . '<span>' . $jam . '</span></p>
                                <p class="text-dark-50 mb-2">
                                    <small>' . $lokasi . '</small>
                                </p>
                                <span class="text-dark-75"><strong><i class="flaticon2-menu-1 icon-sm mr-1 text-success"></i> ' . $rows->naming . '</strong>' . $message . '</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            ';
        }

        return response()->json(['count' => $count, 'output' => $output, 'chatid' => $chatid, 'memberid' => $memberid, 'nama' => $name, 'lokasi' => $lokasi]);

        die();

    }

    /*public function search(Request $request) {
        $baseurlmember = env('BASE_URL') . env('BASE_URL_PROFILE');
        $baseurlavatar = env('BASE_URL') . env('BASE_URL_CHAT');

        $id = Auth::id();
        $user = Auth::user();

        $role = UserRole::where('user_id', $id)->first();

        $where = '';
        if ($role->role_id != '1') {
            $where = "
                chat_header.provinsi_kode = '{$user->provinsi_id}' AND 
                chat_header.kabupaten_kode = '{$user->kabupaten_id}' AND 
                chat_header.kecamatan_kode = '{$user->kecamatan_id}' AND 
            ";
        }

        $sql = "
            SELECT 
                members.id,
                members.name,
                members.gender,
                members.foto_pic,
                adms_provinsi.nama AS provinsi,
                adms_kabupaten.nama AS kabupaten,
                adms_kecamatan.nama AS kecamatan,
                adms_kelurahan.nama AS kelurahan,
                chat_header.id AS chat_id,
                chat_header.responder_id AS header_responder_id,
                role_user.role_id AS header_role_id
            FROM chat_header 
            LEFT JOIN members ON members.id = chat_header.member_id
            LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = chat_header.provinsi_kode
            LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = chat_header.kabupaten_kode
            LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = chat_header.kecamatan_kode
            LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = chat_header.kelurahan_kode
            LEFT JOIN role_user ON role_user.user_id = chat_header.responder_id
            WHERE {$where} members.name LIKE '%{$request->nama}%' AND (role_user.role_id = 1 OR (responder_id = {$id} OR responder_id IS NULL))
        ";

        $list = DB::select($sql);

        $res = [];
        $chatid = [];
        foreach ($list as $key => $row) {
            $detail = ChatMessage::where('chat_id', $row->chat_id)->orderBy('id', 'DESC')->select(['id', 'message', 'status', 'created_at', 'response_id'])->first();

            $row->lokasi = $row->kelurahan . ', ' . $row->kecamatan . ', ' . $row->kabupaten . ', ' . $row->provinsi;
            $row->message = $detail->message;
            $row->chat_status = $detail->status;
            $row->waktu = $detail->created_at;

            if (!empty($row->foto_pic)) {
                if ($row->foto_pic == 'noimage.jpg') {
                    if ($row->gender == '2') {
                        $row->avatar = $baseurlavatar . '018-girl-9.svg';
                    } else if ($row->gender == '1') {
                        $row->avatar = $baseurlavatar . '009-boy-4.svg';
                    } else {
                        $row->avatar = $baseurlavatar . '024-boy-9.svg';
                    }
                } else {
                    $row->avatar = $baseurlmember . $row->foto_pic;
                }
            } else {
                if ($row->gender == '2') {
                    $row->avatar = $baseurlavatar . '018-girl-9.svg';
                } else if ($row->gender == '1') {
                    $row->avatar = $baseurlavatar . '009-boy-4.svg';
                } else {
                    $row->avatar = $baseurlavatar . '024-boy-9.svg';
                }
            }

            $rolemsg = '';
            if (!empty($detail->response_id)) {
                $detailrole = UserRole::where('user_id', $detail->response_id)->first();
                $rolemsg = $detailrole->role_id;
            }

            if (!empty($row->chat_status) && $row->chat_status == 'reply') {
                if ($rolemsg == '1') {

                    if ($detail->response_id == $id) {
                        $row->naming = 'Anda : ';
                    } else {
                        $row->naming = 'Super Admin : ';
                    }

                } else {
                    if (!empty($detail->response_id)) {

                        if ($detail->response_id == $id) {
                            $row->naming = 'Anda : ';
                        } else {
                            $row->naming = 'Admin : ';
                        }
                    } else {
                        $row->naming = '';
                    }
                }
            } else {
                $row->naming = '';
            }

            if ($row->header_responder_id == $id) {
                $row->class = 'm-list-active';
                $chatid[] = $row->chat_id;
            } else {
                $row->class = '';
                $chatid[] = '';
            }
        }



        $count = count($list);
        $chatid = array_filter($chatid);
        if (!empty($chatid)) {
            $chatid = $chatid[0];

            $user = ChatHeader::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'chat_header.member_id');
            })
            ->leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            })
            ->leftJoin('adms_kelurahan', function($join) {
                $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
            })
            ->select([
                'members.id as member_id',
                'members.name',
                'adms_provinsi.provinsi_kode as provinsi',
                'adms_kabupaten.kabupaten_kode as kabupaten',
                'adms_kecamatan.kecamatan_kode as kecamatan',
                'adms_kelurahan.kelurahan_kode as kelurahan'
            ])
            ->where('chat_header.id', $chatid)
            ->first();

            $memberid = $user->member_id;
            $name = $user->name;
            $lokasi = $user->kelurahan . ', ' . $user->kecamatan . ', ' . $user->kabupaten . ', ' . $user->provinsi;
        } else {
            $chatid = '';
            $memberid = '';
            $name = '';
            $lokasi = '';
        }


        $output = '';
        foreach ($list as $keys => $rows) {

            $jam = Carbon::parse($rows->waktu)->diffForHumans();

            $arr = explode(" ", $rows->lokasi);
            $arr = array_splice($arr, 0, 5);
            $lokasi = implode(" ", $arr) . ' ...';

            $arrs = explode(" ", $rows->message);
            $arrs = array_splice($arrs, 0, 9);
            $message = implode(" ", $arrs) . ' ...';

            $output .= '
                <table class="messenger-list-item ' . $rows->class . '" 
                    data-id="' . $rows->chat_id . '" 
                    data-member="' . $rows->id . '" 
                    data-responder="' . $rows->header_responder_id . '" 
                    data-user="' . $rows->name . '" 
                    data-lokasi="' . $rows->lokasi . '" 
                    id="memberchat-' . $rows->chat_id . '" >
                    <tbody>
                        <tr data-action="'.$keys.'">
                            <td style="position: relative">
                                <div class="avatar av-m" style="background-image: url(' . $rows->avatar . ');"></div>
                            </td>
                            <td>
                                <p class="text-primary mb-0" data-id="user_6">' . $rows->name . '<span>' . $jam . '</span></p>
                                <p class="text-dark-50 mb-2">
                                    <small>' . $lokasi . '</small>
                                </p>
                                <span class="text-dark-75"><strong><i class="flaticon2-menu-1 icon-sm mr-1 text-success"></i> ' . $rows->naming . '</strong>' . $message . '</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            ';
        }

        return response()->json(['count' => $count, 'output' => $output, 'chatid' => $chatid, 'memberid' => $memberid, 'nama' => $name, 'lokasi' => $lokasi]);

        die();

    }*/

    public function active(Request $request) {
        $update = ChatHeader::where('id', $request->id)->update([
            'responder_id' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id()
        ]);

        if ($update) {
            $other = ChatHeader::where('id', '!=', $request->id)->where('responder_id', Auth::id())->update([
                'responder_id' => null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $msg = 'Success';
        } else {
            $msg = 'Failed';
        }

        return response()->json([
            'msg' => $msg
        ]);

        die();
    }

    public function leave(Request $request) {
        $check = ChatHeader::where('responder_id', Auth::id())->first();

        if ($check) {
            $update = ChatHeader::where('responder_id', Auth::id())->update([
                'responder_id' => null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);
        }

        return response()->json([
            'msg' => 'success'
        ]);

        die();
    }

}
