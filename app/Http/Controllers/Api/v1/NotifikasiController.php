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
use App\NotificationLog;

class NotifikasiController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function notiflist(Request $request) {
        try {

            $data = [];

            $member = Member::select('created_at as tanggal')->where('id', $request->id)->first();

            if ($member) {
                $max = date('Y-m-d',strtotime("-14 days"));

                $res = NotificationLog::where('member_id', $request->id)->orWhereNull('member_id')
                    ->whereRaw("DATE_FORMAT( created_at, '%Y-%m-%d') >= ?", array($max))
                    ->whereRaw("DATE_FORMAT( created_at, '%Y-%m-%d') >= ?", array($member->tanggal))
                    ->select([
                        'jenis as title',
                        'member_id',
                        'content',
                        'created_at as waktu'
                ])->orderBy('id', 'DESC')->get();

                if ($res->isNotEmpty()) {
                    foreach ($res as $key => $row) {
                        $data[$key] = [
                            'tipe' => (empty($member_id)) ? 'global' : 'member',
                            'title' => $row->title,
                            'content' => $row->content,
                            'waktu' => Carbon::parse($row->waktu)->diffForHumans()
                        ];
                    }
                }
            }

            return response()->json([
                'error' => false,
                'code' => 200,
                'message' => 'Success',
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

    public function notifdelete(Request $request) {
        try {

            $check = NotificationLog::where('member_id', $request->id)->first();

            if ($check) {
                $delete = NotificationLog::where('member_id', $request->id)->delete();

                if ($delete) {
                    return response()->json([
                        'code' => 200,
                        'error'   => false,
                        'message' => 'Notifikasi telah dibersihkan'
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 401,
                        'error'   => true,
                        'message' => 'Penghapusan notifikasi gagal dilakukan. Silahkan coba beberapa saat lagi ya'
                    ], 401);
                }
            } else {
                    return response()->json([
                        'code' => 401,
                        'error'   => true,
                        'message' => 'Notifikasi sudah kosong. Tidak ada notifikasi yang akan dihapus'
                    ], 401);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function notifinsert(Request $request) {
        try {

            $insert = new NotificationLog;
            $insert->member_id = 10;
            $insert->jenis = 'Pasangan';
            $insert->content = 'Seseorang mengirimkan kamu permintaan sebagai pasangan';
            $insert->created_at = date('Y-m-d H:i:s');
            $insert->created_by = 10;
            $insert->save();

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

}
