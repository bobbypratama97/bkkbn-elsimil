<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Carbon\Carbon;

use DB;
use PDF;
use Helper;

use App\Member;

use App\KuisHamilKontakAwal;

class KuisHamilController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function getKontakAwalResult($id)
    {
        $data = KuisHamilKontakAwal::where('id_user',$id)->first();
        $dataArray = [
            // "id" => "kontak-awal",
            "answerDate" => date_format($data->created_at,"Y-m-d"),
            "pdfUrl" => "www.pdf.com",
            "answers" => [
            [
                "question" => "Usia",
                "answer" => $data->usia,
                "isRisky" => false,
            ],
            [
                "question" => "Alamat",
                "answer" => $data->alamat,
                "isRisky" => "-",
            ],

            ]
        ];

        return $dataArray;
    }

}
