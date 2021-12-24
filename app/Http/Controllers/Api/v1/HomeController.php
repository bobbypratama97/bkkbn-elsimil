<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use DB;
use Helper;

use App\Member;
use App\MemberCouple;
use App\News;
use App\Kuis;
use App\KuisResult;

class HomeController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function index(Request $request) {
        try {

            $base_url = env('BASE_URL') . env('BASE_URL_VRF');
            $pic_url = env('BASE_URL') . env('BASE_URL_PROFILE');
            $kuis_url = env('BASE_URL_NEWS') . env('NEWS_THUMB_MD');

            $id = $request->id;

            $output = [];

            $user = Member::leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->where('members.id', $id)
            ->select([
                'members.id', 
                'name', 
                'tgl_lahir', 
                'gender',
                'profile_code as profile_id', 
                DB::raw("concat('{$pic_url}', members.foto_pic) AS pic"),
                'adms_kabupaten.nama as kota'
            ])
            ->first();

            $now = date('Y-m-d');
            $user->tgl_lahir = ucwords(Helper::diffDate($now, $user->tgl_lahir, 'y'));

            $pasangan = MemberCouple::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'member_couple.couple_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->where('member_id', $id)
            ->select([
                'members.id',
                'members.name',
                'members.tgl_lahir',
                DB::raw("concat('{$pic_url}', members.foto_pic) AS pic"),
                'adms_kabupaten.nama as kota'
            ])
            ->orderBy('member_couple.id', 'DESC')
            ->get();

            $couple = [];
            if ($pasangan->isNotEmpty()) {
                foreach ($pasangan as $key => $val) {
                    $couple[] = [
                        'id' => $val->id,
                        'name' => $val->name,
                        'tgl_lahir' => ucwords(Helper::diffDate($now, $val->tgl_lahir, 'y')),
                        'pic' => $val->pic,
                        'kota' => $val->kota
                    ];
                }
            }

            //$gen = ['all', $user->gender];
            //$kuis = Kuis::whereNull('deleted_by')->orderBy('id', 'DESC')->get();


            if ($user->gender == '1') {
                $kuis = DB::select("SELECT * FROM kuisioner WHERE deleted_by IS NULL AND apv = 'APV300' AND gender in ('all', '1') ORDER BY id DESC");    
            } else {
                $kuis = DB::select("SELECT * FROM kuisioner WHERE deleted_by IS NULL AND apv = 'APV300' AND gender in ('all', '2') ORDER BY id DESC");
            }
            
            $result = [];
            foreach ($kuis as $key => $row) {
                //if ($row->gender == $user->gender || $row->gender == 'all') {
                    $res = KuisResult::where('kuis_id', $row->id)->where('member_id', $request->id)->where('status', 1)->orderBy('id', 'DESC')->select([
                        'id',
                        'rating',
                        'rating_color',
                        'member_kuis_nilai',
                        'kuis_max_nilai',
                        'label',
                        'kuis_title'
                    ])->first();

                    if (!empty($res)) {
                        $result[] = [
                            'kuis_id' => (int) $row->id,
                            'result_id' => $res->id,
                            'kuis_title' => $row->title,
                            'rating' => $res->rating,
                            'rating_color' => $res->rating_color,
                            'member_kuis_nilai' => $res->member_kuis_nilai,
                            'kuis_max_nilai' => $res->kuis_max_nilai,
                            'label' => $res->label,
                            'tgl_kuis' => $res->created_at
                        ];
                    } else {
                        $result[] = [
                            'kuis_id' => (int) $row->id,
                            'result_id' => 0,
                            'kuis_title' => $row->title,
                            'rating' => '',
                            'rating_color' => '',
                            'member_kuis_nilai' => '',
                            'kuis_max_nilai' => '',
                            'label' => '',
                            'tgl_kuis' => ''
                        ];
                    }
                //}
            }

            $news = News::leftJoin('news_kategori', function($join) {
                $join->on('news_kategori.id', '=', 'news.kategori_id');
            })
            ->select([
                'news.id',
                'news.title',
                DB::raw("concat('{$kuis_url}', news.thumbnail) AS image"),
                'news_kategori.name as kategori'
            ])
            ->whereNull('news.deleted_by')
            ->whereNull('news_kategori.deleted_by')
            ->orderBy('news.id', 'DESC')
            ->limit(5)
            ->get();

            $infos = Member::where('members.id', $id)->select(['is_active', 'email'])->first();

            $info = [];
            if ($infos->is_active == '4') {
                $info[] = [
                    'content' => 'Silahkan aktivasi akun Anda melalui email yang kami kirim. Klik <u>disini</u> untuk mengirim ulang.',
                    'link' => $base_url,
                    'additional' => [
                        [
                            'params' => 'email',
                            'value' => $infos->email
                        ],
                        [
                            'params' => 'tipe',
                            'value' => '2'
                        ]
                    ]
                ];
            }

            $output = [
                'own' => $user,
                'couple' => $couple,
                'result' => $result,
                'info' => $info,
                'edukasi' => $news
            ];

            return response()->json([
                'code' => 200,
                'error'   => false,
                'data' => $output
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
