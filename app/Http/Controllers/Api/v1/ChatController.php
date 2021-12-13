<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Carbon\Carbon;

use DB;
use Helper;

use App\Member;
use App\ChatHeader;
use App\ChatMessage;
use App\Config;
use App\MemberDelegate;

class ChatController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function list(Request $request) {
        try {

            $url = env('BASE_URL') . env('BASE_URL_PROFILE');

            $id = $request->id;
            $limit = $request->has('limit') ? $request->limit : 15;
            $page = $request->has('paging') ? $request->paging : 0;
            $page = $page * 15;
            $type = request('type');

            /*$res = ChatMessage::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'chat_message.member_id');
            })
            ->leftJoin('role_user', function($join) {
                $join->on('role_user.user_id', '=', 'chat_message.response_id');
            })
            ->leftJoin('role', function($join) {
                $join->on('role.id', '=', 'role_user.role_id');
            })
            ->where('chat_message.member_id', $request->id)
            //->orderBy('chat_message.id', 'ASC')
            ->select([
                'chat_message.member_id',
                'chat_message.message',
                'role.name as jabatan',
                'chat_message.created_at',
                'chat_message.status'
            ])
            ->orderBy('chat_message.id', 'ASC')
            ->limit($limit)->offset($page)
            ->get();*/

            $condition = '';
            if($type) $condition = 'AND chat_header.type = '.$type;
            
            $sql = "
                SELECT a.*, members.foto_pic AS pic,
                    if(configs.id is not null, configs.name, role.name) as jabatan
                    FROM (SELECT chat_message.id, chat_message.chat_id, chat_message.member_id, chat_message.response_id, chat_message.message, chat_message.created_at, chat_message.status FROM chat_message WHERE member_id = {$id} ORDER BY id DESC LIMIT {$limit} OFFSET {$page}) a 
                JOIN members ON members.id = a.member_id
                JOIN role_user ON role_user.user_id = a.response_id
                JOIN role ON role.id = role_user.role_id
                JOIN chat_header ON chat_header.id = a.chat_id
                LEFT JOIN configs ON configs.value = role_user.role_child_id
                WHERE 1 = 1 {$condition}
                ORDER BY a.id
            ";

            $res = DB::select($sql);
            
            $fin = [];
            foreach ($res as $key => $row) {
                $tanggal = explode(' ', $row->created_at);
                $date = Helper::customDateMember($tanggal[0]);

                $time = explode(':', $tanggal[1]);
                $time = $time[0] . ':' . $time[1];

                $fin[] = [
                    'member_id' => $row->member_id,
                    'pic' => ($row->status == 'send') ? $url . $row->pic : '',
                    'message' => $row->message,
                    'jabatan' => ($row->status == 'send') ? '' : $row->jabatan,
                    'tanggal' => $date,
                    'jam' => $time,
                    'action' => $row->status
                ];
            }

            $group = [];
            foreach ($fin as $key => $row) {
                $group[$row['tanggal']][$key] = $row;
            }

            $data = [];
            $i = 0;
            foreach ($group as $key => $row) {
                $data[$i]['header'] = $key;
                $data[$i]['child'] = array_values($row);
                $i++;
            }

            return response()->json([
                'code' => 200,
                'error'   => false,
                'data' => $data
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function submit(Request $request) {
        $stat = 0;
        $chat_role_id = request('type') ?? $this->role_child_id;

        $check = ChatHeader::where('member_id', $request->id)->where('type', $chat_role_id)->first();
        $get = MemberDelegate::join('role_user as role', 'role.user_id', 'member_delegate.user_id')
            ->where('role.role_child_id', $chat_role_id)
            ->where('member_id', $request->id)
            ->first();
        $responder_id = ($get) ? $get->user_id : null;

        if ($check) {

            $update = ChatMessage::where('chat_id', $check->id)->update([
                'last' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $request->id
            ]);

            $insert = new ChatMessage;
            $insert->chat_id = $check->id;
            $insert->member_id = $request->id;
            $insert->response_id = $responder_id;
            $insert->message = $request->message;
            $insert->status = 'send';
            $insert->last = 1;
            $insert->created_at = date('Y-m-d H:i:s');
            $insert->created_by = $request->id;

            if ($insert->save()) {
                $stat = 1;
            } else {
                $stat = 0;
            }

        } else {

            $member = Member::where('id', $request->id)->first();

            $insertHeader = new ChatHeader;
            $insertHeader->member_id = $request->id;
            $insertHeader->responder_id = $responder_id;
            $insertHeader->provinsi_kode = $member->provinsi_id;
            $insertHeader->kabupaten_kode = $member->kabupaten_id;
            $insertHeader->kecamatan_kode = $member->kecamatan_id;
            $insertHeader->kelurahan_kode = $member->kelurahan_id;
            $insertHeader->status = 0;
            $insertHeader->type = $chat_role_id;
            $insertHeader->created_at = date('Y-m-d H:i:s');
            $insertHeader->created_by = $request->id;

            if ($insertHeader->save()) {
                $chatId = $insertHeader->id;

                $insert = new ChatMessage;
                $insert->chat_id = $insertHeader->id;
                $insert->member_id = $request->id;
                $insert->response_id = $responder_id;
                $insert->message = $request->message;
                $insert->status = 'send';
                $insert->created_at = date('Y-m-d H:i:s');
                $insert->created_by = $request->id;

                $stat = 1;

                if ($insert->save()) {
                    $stat = 1;
                } else {
                    $stat = 0;
                }
            } else {
                $stat = 0;
            }

        }

        if ($stat == '1') {
            return response()->json([
                'code' => 200,
                'error'   => false,
                'message' => 'Chat berhasil dikirim'
            ], 200);
        } else {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => 'Chat gagal dikirim'
            ], 200);
        }
    }

    public function type(Request $request){
        $role_childs = Config::select('configs.name', 'configs.value as type', DB::Raw('if(md.id is not null, 1, 0) as status'))
            ->join('role_user as role', 'role.role_child_id', 'configs.value')
            ->leftJoin('member_delegate as md', function($q) use($request){
                $q->on('md.user_id', 'role.user_id')
                    ->where('md.status', 1)
                    ->where('md.member_id', $request->id);
            })
            ->where('code', 'LIKE', '%role_child_%')
            ->orderBy('configs.value', 'desc')
            ->groupBy('configs.value')
            ->get();

        return response()->json([
            'code' => 200,
            'error'   => false,
            'message' => 'List Chat type',
            'data' => $role_childs
        ], 200);
    }

}
