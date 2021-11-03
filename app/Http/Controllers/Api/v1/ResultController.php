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

use App\KuisResult;
use App\KuisResultHeader;
use App\KuisResultDetail;
use App\KuisResultComment;

use App\Kuis;
use App\KuisHeader;
use App\KuisDetail;
use App\KuisBobot;
use App\KuisBobotFile;
use App\KuisSummary;
use App\KuisResultBobotFile;

class ResultController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function resultlist(Request $request) {
        try {

            $data = KuisResult::where('member_id', $request->id)->orderBy('id', 'DESC')->select([
                'id',
                'kuis_title',
                'member_kuis_nilai',
                'kuis_max_nilai',
                'label',
                'deskripsi',
                'rating',
                'rating_color',
                'created_at'
            ])->get();

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

    public function resultdetail(Request $request) {
        try {

            $kuis = [];
            $result = [];
            $komentar = '';

            $base_url = env('BASE_URL_PDF');
            $base_url_kuis = env('BASE_URL_KUIS');
            $kuis = KuisResult::where('id', $request->id)
                ->select([
                    'kuis_id',
                    'kuis_code', 
                    'created_at', 
                    'label',
                    'deskripsi',
                    'rating', 
                    'rating_color', 
                    'kuis_max_nilai', 
                    'member_kuis_nilai', 
                    DB::raw("concat('{$base_url}', filename) AS url")
                ])
                ->first();

            if (!empty($kuis)) {
                $tanggal = explode(' ', $kuis['created_at']);
                $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
                unset ($kuis['created_at']);
                $kuis['tanggal_kuis'] = $tanggal;

                $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                    $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                    $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
                })
                ->where('kuisioner_result_detail.result_id', $request->id)
                ->where('kuisioner_result_header.pertanyaan_header_jenis', '!=', 'widget')
                ->select([
                    'kuisioner_result_header.pertanyaan_header_caption',
                    'kuisioner_result_header.pertanyaan_header_jenis',
                    'kuisioner_result_detail.pertanyaan_bobot_label',
                    'kuisioner_result_detail.value',
                    'kuisioner_result_detail.formula_value',
                    'kuisioner_result_detail.pertanyaan_bobot_id',
                    'kuisioner_result_detail.pertanyaan_rating',
                    'kuisioner_result_detail.pertanyaan_rating_color',
                    'kuisioner_result_detail.pertanyaan_detail_pilihan'
                ])
                ->groupBy('kuisioner_result_detail.header_id')
                ->get()
                ->toArray();

                $result = [];
                foreach ($detail as $key => $row) {
                    $result[$key]['caption'] = $row['pertanyaan_header_caption'];
                    $result[$key]['label'] = $row['pertanyaan_bobot_label'];

                    if ($row['pertanyaan_header_jenis'] == 'combine') {
                        $result[$key]['value'] = $row['formula_value'];
                    } else if ($row['pertanyaan_header_jenis'] == 'single') {
                        if ($row['pertanyaan_detail_pilihan'] == 'radio' || $row['pertanyaan_detail_pilihan'] == 'dropdown') {
                            $result[$key]['value'] = '';
                        } else {
                            $result[$key]['value'] = $row['value'];
                        }
                    } else {
                        $result[$key]['value'] = $row['value'];
                    }
                    $result[$key]['rating'] = $row['pertanyaan_rating'];
                    $result[$key]['rating_color'] = $row['pertanyaan_rating_color'];

                    /*$file = KuisBobotFile::whereNull('deleted_by')->where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select([
                        'name', 
                        DB::raw("concat('{$base_url}', file) AS file")
                    ])->get()->toArray();*/

                    $file = KuisResultBobotFile::where('result_id', $request->id)->where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select([
                        'name', 
                        DB::raw("concat('{$base_url_kuis}', file) AS file")
                    ])->get()->toArray();

                    $result[$key]['file'] = $file;
                }

                $komentar = KuisResultComment::leftJoin('users', function($join) {
                    $join->on('users.id', '=', 'kuisioner_result_comment.created_by');
                })
                ->leftJoin('role_user', function($join) {
                    $join->on('role_user.user_id', '=', 'users.id');
                })
                ->leftJoin('role', function($join) {
                    $join->on('role.id', '=', 'role_user.role_id');
                })
                ->where('kuisioner_result_comment.result_id', $request->id)
                ->select([
                    'kuisioner_result_comment.komentar',
                    'kuisioner_result_comment.created_at',
                    'users.name',
                    'role.name as jabatan'
                ])
                ->first();

                if (!empty($komentar)) {
                    $tanggal = $komentar->created_at;
                    unset ($komentar['created_at']);
                    $komentar['tanggal_ulasan'] = $tanggal;
                }
            }


            //print_r ($detail);
            $data = [
                'header' => $kuis,
                'detail' => $result,
                'ulasan' => $komentar
            ];

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

    public function resultcouple(Request $request) {
        try {

            $base_url = env('BASE_URL') . env('BASE_URL_KUESIONER');

            $member = Member::where('id', $request->id)->first();
            $gender = ['all', $member->gender];

            /*$data = Kuis::whereNull('deleted_by')->where('apv', 'APV300')->whereIn('gender', $gender)->select([
                'id', 
                'title', 
                'created_at', 
            ])->orderBy('id', 'DESC')->get();*/
            if ($member->gender == '2') {
                $data = DB::select("SELECT id, title, created_at FROM kuisioner WHERE deleted_by is null and apv = 'APV300' and gender in ('all', '2') order by id desc");
            } else {
                $data = DB::select("SELECT id, title, created_at FROM kuisioner WHERE deleted_by is null and apv = 'APV300' and gender in ('all', '1') order by id desc");
            }

            $res = [];
            foreach ($data as $key => $row) {
                $get = KuisResult::where('member_id', $request->id)->where('kuis_id', $row->id)->orderBy('id', 'DESC')->first();

                if (!empty($get)) {
                    $result_id = $get->id;
                    $label = $get->label;
                    $rating = $get->rating;
                    $background = $get->rating_color;
                    $point = $get->member_kuis_nilai;
                    $max_point = $get->kuis_max_nilai;
                    $deskripsi = $get->deskripsi;
                    // $created_at = Helper::customDateKuis($get->created_at);
                } else {
                    $result_id = 0;
                    $label = '';
                    $rating = '';
                    $background = '';
                    $point = 0;
                    $max_point = 0;
                    $deskripsi = '';
                }

                if (!empty($get)) {
                    $row->result_id = $result_id;
                    $row->label = $label;
                    $row->rating = $rating;
                    $row->background = $background;
                    $row->point = $point;
                    $row->max_point = $max_point;
                    $row->deskripsi = $deskripsi;
                    // $row->created_at = $created_at;
                } else {
                    $data = [];
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

}
