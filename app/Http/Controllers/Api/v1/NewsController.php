<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Carbon\Carbon;

use DB;
use Helper;

use App\NewsKategori;
use App\News;

class NewsController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function listkategori(Request $request) {
        try {

            $base_url = env('BASE_URL_KAT');

            $data = NewsKategori::leftJoin('users', function($join) {
                $join->on('users.id', '=', 'news_kategori.created_by');
            })
            ->whereNull('news_kategori.deleted_by')
            ->where('news_kategori.status', 2)
            ->orderBy('news_kategori.position')
            ->select([
                'news_kategori.id',
                'news_kategori.name as kategori',
                DB::raw("concat('{$base_url}', thumbnail) AS image"),
                DB::raw("concat('#', color) AS background"),
                'news_kategori.deskripsi',
                'news_kategori.created_at',
                'users.name as creator'
            ])
            ->get();

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

    public function newslist(Request $request) {
        try {

            $base_url = env('BASE_URL_NEWS') . env('NEWS_ORI');

            $data = News::leftJoin('news_kategori', function($join) {
                $join->on('news_kategori.id', '=', 'news.kategori_id');
            })
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'news.created_by');
            })
            ->select([
                'news_kategori.name as kategori',
                'news.id',
                'news.title as judul',
                'news.deskripsi',
                'news.created_at as tgl_publish',
                DB::raw("concat('{$base_url}', news.thumbnail) AS image"),
                'users.name as creator'
            ])
            ->whereNull('news.deleted_by')
            ->where('news_kategori.id', $request->kategori_id)
            ->where('news.status', 2)
            ->orderBy('news.id', 'DESC')
            ->get();

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

    public function newsdetail(Request $request) {
        try {

            $base_url = env('BASE_URL_NEWS') . env('NEWS_ORI');

            $data = News::leftJoin('news_kategori', function($join) {
                $join->on('news_kategori.id', '=', 'news.kategori_id');
            })
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'news.created_by');
            })
            ->select([
                'news_kategori.name as kategori',
                'news.id',
                'news.title as judul',
                'news.deskripsi',
                'news.content',
                'news.created_at as tgl_publish',
                DB::raw("concat('{$base_url}', news.gambar) AS image"),
                'users.name as creator'
            ])
            ->where('news.id', $request->news_id)
            ->where('news.status', 2)
            ->orderBy('news.id', 'DESC')
            ->first();

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

    public function newsrelated(Request $request) {
        try {

            $base_url = env('BASE_URL_NEWS') . env('NEWS_ORI');

            $data = News::leftJoin('news_kategori', function($join) {
                $join->on('news_kategori.id', '=', 'news.kategori_id');
            })
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'news.created_by');
            })
            ->select([
                'news_kategori.name as kategori',
                'news.id',
                'news.title as judul',
                'news.deskripsi',
                'news.created_at as tgl_publish',
                DB::raw("concat('{$base_url}', news.thumbnail) AS image"),
                'users.name as creator'
            ])
            ->where('news.status', 2)
            ->where('news_kategori.status', 2)
            ->whereNull('news_kategori.deleted_by')
            ->inRandomOrder()
            ->limit(2)
            ->get();

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
