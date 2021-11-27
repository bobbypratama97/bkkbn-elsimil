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
use App\KuisHamil16Minggu;

class KuisHamilController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    private function _getKontakAwalResult($id)
    {
        $today = date("Y-m-d");
        $base_url = env('BASE_URL_PDF');
        #kontak-awal
        $dataKontakAwal = KuisHamilKontakAwal::where('id_user',$id)->first();
        $answerKontakAwal= array();
        $pdfKontakAwal = '20210316154708 - 96RCJH4N - Pencegahan Stunting - oncom.pdf';

        if($dataKontakAwal != null){
            foreach( $dataKontakAwal->toArray() as $key => $value )
            {
                switch($key)
                {
                    case 'nama' :
                    case 'nik' :
                    case 'alamat' :
                    case 'hari_pertama_haid_terakhir' :
                            $singleData = [
                                                    "question" => $key,
                                                    "answer" => $value,
                                                    "isRisky" => "-"
                            ];
                            array_push($answerKontakAwal,$singleData);
                            break;

                    case 'usia'                             :    if($value>=20 && $value<=35){
                                                                        $isRisky = false;
                                                                      }else if($value<20 || $value>35){
                                                                        $isRisky = true;
                                                                      }
                                                                      $singleData = [
                                                                        "question" => $key,
                                                                        "answer" => $value,
                                                                        "isRisky" => $isRisky
                                                                      ];
                                                                     array_push($answerKontakAwal,$singleData);
                                                                     break;

                    case 'jumlah_anak'              :    if($value>=0 && $value<=2){
                                                                        $isRisky = false;
                                                                    }else if($value>2){
                                                                        $isRisky = true;
                                                                    }
                                                                    $singleData = [
                                                                        "question" => $key,
                                                                        "answer" => $value,
                                                                        "isRisky" => $isRisky
                                                                    ];
                                                                    array_push($answerKontakAwal,$singleData);
                                                                    break;

                    case 'usia_anak_terakhir'     :      if($value>=0 && $value<=2){
                                                                           $isRisky = false;
                                                                        }else if($value>2){
                                                                           $isRisky = true;
                                                                        }
                                                                        $singleData = [
                                                                            "question" => $key,
                                                                            "answer" => $value,
                                                                            "isRisky" => $isRisky
                                                                        ];
                                                                        array_push($answerKontakAwal,$singleData);
                                                                        break;

                    case 'anak_stunting'            :       if($value == 0){
                                                                            $isRisky = false;
                                                                        }else if($value == 1){
                                                                            $isRisky = true;
                                                                        }
                                                                        $singleData = [
                                                                            "question" => $key,
                                                                            "answer" => $value,
                                                                            "isRisky" => $isRisky
                                                                        ];
                                                                        array_push($answerKontakAwal,$singleData);
                                                                        break;

                    case 'sumber_air_bersih'      :     if($value == 0){
                                                                            $isRisky = false;
                                                                        }else if($value == 1){
                                                                            $isRisky = true;
                                                                        }
                                                                        $singleData = [
                                                                        "question" => $key,
                                                                        "answer" => $value,
                                                                        "isRisky" => $isRisky
                                                                        ];
                                                                        array_push($answerKontakAwal,$singleData);
                                                                        break;

                    case 'jamban_sehat'      :             if($value == 0){
                                                                            $isRisky = false;
                                                                        }else if($value == 1){
                                                                            $isRisky = true;
                                                                        }
                                                                        $singleData = [
                                                                        "question" => $key,
                                                                        "answer" => $value,
                                                                        "isRisky" => $isRisky
                                                                        ];
                                                                        array_push($answerKontakAwal,$singleData);
                                                                        break;

                    case 'rumah_layak_huni'      :      if($value == 0){
                                                                            $isRisky = false;
                                                                            }else if($value == 1){
                                                                            $isRisky = true;
                                                                            }
                                                                            $singleData = [
                                                                            "question" => $key,
                                                                            "answer" => $value,
                                                                            "isRisky" => $isRisky
                                                                            ];
                                                                            array_push($answerKontakAwal,$singleData);
                                                                            break;

                    case 'bansos'      :                         if($value == 0){
                                                                            $isRisky = true;
                                                                            }else if($value == 1){
                                                                            $isRisky = false;
                                                                            }
                                                                            $singleData = [
                                                                            "question" => $key,
                                                                            "answer" => $value,
                                                                            "isRisky" => $isRisky
                                                                            ];
                                                                            array_push($answerKontakAwal,$singleData);
                                                                            break;
                }

            }
            $arrayKontakAwal = array(
                "id" => 'kontak-awal',
                "answerDate" => \Carbon\Carbon::parse($dataKontakAwal->created_at)->isoFormat('YYYY-MM-DD'),
                "pdfUrl" =>  $base_url.$pdfKontakAwal,
                "answers" => $answerKontakAwal
            );
            return $arrayKontakAwal;
        }
    }

    private function _get16MingguResult($id)
    {
        $today = date("Y-m-d");
        $base_url = env('BASE_URL_PDF');
         #kontak-16-minggu
         $data16Minggu = KuisHamil16Minggu::where('id_user',$id)->first();
         $answer16Minggu= array();
         $pdf16Minggu = '20210316154708 - 96RCJH4N - Pencegahan Stunting - oncom.pdf';
         if($data16Minggu != null){
             foreach( $data16Minggu->toArray() as $key => $value )
             {
                 switch($key) {
                     case 'hemoglobin' : if($value >= 11){
                                                       $isRisky = false;
                                                     }else if($value < 11){
                                                       $isRisky = true;
                                                     }
                                                     $singleData = [
                                                     "question" => $key,
                                                     "answer" => $value,
                                                     "isRisky" => $isRisky
                                                     ];
                                                     array_push($answer16Minggu,$singleData);
                                                     break;
                       case 'tensi_darah' : if($value <= 90){
                                                         $isRisky = false;
                                                       }else if($value > 90){
                                                         $isRisky = true;
                                                       }
                                                       $singleData = [
                                                       "question" => $key,
                                                       "answer" => $value,
                                                       "isRisky" => $isRisky
                                                       ];
                                                       array_push($answer16Minggu,$singleData);
                                                       break;

                       case 'gula_darah_sewaktu' : if($value >= 95 && $value <= 200){
                                                         $isRisky = false;
                                                       }else if($value < 95 || $value > 200 ){
                                                         $isRisky = true;
                                                       }
                                                       $singleData = [
                                                       "question" => $key,
                                                       "answer" => $value,
                                                       "isRisky" => $isRisky
                                                       ];
                                                       array_push($answer16Minggu,$singleData);
                                                       break;
                 }
             }
             $array16Minggu = array(
               "id" => '16-minggu',
               "answerDate" => \Carbon\Carbon::parse($data16Minggu->created_at)->isoFormat('YYYY-MM-DD'),
               "pdfUrl" =>  $base_url.$pdf16Minggu,
               "answers" => $answer16Minggu
             );
             return $array16Minggu;
         }
    }



    public function getKuesionerHamilResult($id)
    {
        #generic data
        $today = date("Y-m-d");
        $base_url = env('BASE_URL_PDF');
        $finalData = array();

        #kontak awal
        $arrayKontakAwal = $this->_getKontakAwalResult($id);
        array_push($finalData,$arrayKontakAwal);

        #16 minggu
        $array16Minggu = $this->_get16MingguResult($id);
        array_push($finalData,$array16Minggu);

        $finalResult = [
          "data" => $finalData
        ];
        return $finalResult;
    }

}
