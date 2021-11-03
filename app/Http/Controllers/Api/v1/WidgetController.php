<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Carbon\Carbon;

use DB;
use Helper;

use App\Widget;
use App\FasKes;

class WidgetController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function widgetfaskes(Request $request) {
        try {

            $data = [
                'kuis_id' => (int) $request->kuis_id,
                'header_id' => (int) $request->header_id,
                'jenis' => 'widget',
                'deskripsi' => null,
                'caption' => 'Data MR',
                'pertanyaan' => [
                    [
                        'kuis_id' => (int) $request->kuis_id,
                        'header_id' => (int) $request->header_id,
                        'pertanyaan_id' => 0,
                        'title' => 'Tanggal pemeriksaan',
                        'tipe' => 'tanggal'
                    ],
                    [
                        'kuis_id' => (int) $request->kuis_id,
                        'header_id' => (int) $request->header_id,
                        'pertanyaan_id' => 0,
                        'title' => 'Tempat / Nama Fasilitas Kesehatan',
                        'tipe' => 'autocomplete',
                        'api' => 'http://bkkbn.local/api/v1/faskeslist',
                        'params' => 'nama'
                    ],
                    [
                        'kuis_id' => (int) $request->kuis_id,
                        'header_id' => (int) $request->header_id,
                        'pertanyaan_id' => 0,
                        'title' => 'Unggah / foto hasil kesehatan',
                        'tipe' => 'upload'
                    ]
                ]
            ];

            //echo json_encode($data);

            return response()->json([
                'code' => 200,
                'error' => false,
                'data' => $data
            ], 200);

            /*$faskes = FasKes::whereNull('deleted_by')->select(['id', 'nama'])->get();
            //print_r ($faskes);

            return response()->json([
                'code' => 200,
                'error'   => true,
                'data' => $faskes
            ], 200);*/

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function faskeslist(Request $request) {
        try {

            $faskes = FasKes::whereNull('deleted_by')->select(['id', 'nama'])->where('nama', 'like', '%' . $request->nama . '%')->get();
            //print_r ($faskes);

            return response()->json([
                'code' => 200,
                'error'   => true,
                'data' => $faskes
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
