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
use App\KuisHamil12Minggu;
use App\KuisHamil16Minggu;
use App\KuisHamilIbuJanin;

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

    private function _get12MingguResult($id)
    {
         $base_url = env('BASE_URL_PDF');
         #kontak-16-minggu
         $data12Minggu = KuisHamil12Minggu::where('id_user',$id)->first();
         $answer12Minggu= array();
         $pdf12Minggu = '20210316154708 - 96RCJH4N - Pencegahan Stunting - oncom.pdf';
         if($data12Minggu != null){
             $imtCalculation = $data12Minggu->berat_badan / ($data12Minggu->tinggi_badan ^ 2);
             foreach( $data12Minggu->toArray() as $key => $value )
             {
                 switch($key) {
                     case 'berat_badan' : if($imtCalculation >= 19 && $imtCalculation <= 29){
                                                       $isRisky = false;
                                                     }else if($imtCalculation < 19 || $imtCalculation > 29){
                                                       $isRisky = true;
                                                     }
                                                     $singleData = [
                                                     "question" => $key,
                                                     "answer" => $value,
                                                     "isRisky" => $isRisky
                                                     ];
                                                     array_push($answer12Minggu,$singleData);
                                                     break;
                       case 'tinggi_badan' : if($value >= 145){
                                                         $isRisky = false;
                                                       }else if($value < 145){
                                                         $isRisky = true;
                                                       }
                                                       $singleData = [
                                                       "question" => $key,
                                                       "answer" => $value,
                                                       "isRisky" => $isRisky
                                                       ];
                                                       array_push($answer12Minggu,$singleData);
                                                       break;

                       case 'lingkar_lengan_atas' : if($value >= 23.5){
                                                                        $isRisky = false;
                                                                    }else if($value < 23.5){
                                                                        $isRisky = true;
                                                                    }
                                                                    $singleData = [
                                                                    "question" => $key,
                                                                    "answer" => $value,
                                                                    "isRisky" => $isRisky
                                                                    ];
                                                                    array_push($answer12Minggu,$singleData);
                                                                    break;

                        case 'hemoglobin' :             if($value >= 11){
                                                                        $isRisky = false;
                                                                    }else if($value < 11){
                                                                        $isRisky = true;
                                                                    }
                                                                    $singleData = [
                                                                    "question" => $key,
                                                                    "answer" => $value,
                                                                    "isRisky" => $isRisky
                                                                    ];
                                                                    array_push($answer12Minggu,$singleData);
                                                                    break;

                        case 'tensi_darah' :             if($value <= 90){
                                                                        $isRisky = false;
                                                                    }else if($value > 90){
                                                                        $isRisky = true;
                                                                    }
                                                                    $singleData = [
                                                                    "question" => $key,
                                                                    "answer" => $value,
                                                                    "isRisky" => $isRisky
                                                                    ];
                                                                    array_push($answer12Minggu,$singleData);
                                                                    break;

                         case 'gula_darah_sewaktu' :       if($value >= 95 && $value <= 200){
                                                                                $isRisky = false;
                                                                            }else if($value < 95 || $value > 200){
                                                                                $isRisky = true;
                                                                            }
                                                                            $singleData = [
                                                                            "question" => $key,
                                                                            "answer" => $value,
                                                                            "isRisky" => $isRisky
                                                                            ];
                                                                            array_push($answer12Minggu,$singleData);
                                                                            break;

                            case 'riwayat_sakit_kronik' :       if($value == 1){
                                                                                   $isRisky = true;
                                                                                }else if($value == 0){
                                                                                    $isRisky = false;
                                                                                }
                                                                                $singleData = [
                                                                                "question" => $key,
                                                                                "answer" => $value,
                                                                                "isRisky" => $isRisky
                                                                                ];
                                                                                array_push($answer12Minggu,$singleData);
                                                                                break;
                 }
             }
             $array12Minggu = array(
               "id" => '12-minggu',
               "answerDate" => \Carbon\Carbon::parse($data12Minggu->created_at)->isoFormat('YYYY-MM-DD'),
               "pdfUrl" =>  $base_url.$pdf12Minggu,
               "answers" => $answer12Minggu
             );
             return $array12Minggu;
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

    private function _getIbuJaninResult($id,$periode)
    {
        $today = date("Y-m-d");
        $base_url = env('BASE_URL_PDF');
        $dataIbuJanin = KuisHamilIbuJanin::where('id_user',$id)->where('periode',$periode)->first();
        $answerIbuJanin = array();
        $pdfIbuJanin = '20210316154708 - 96RCJH4N - Pencegahan Stunting - oncom.pdf';
        if($pdfIbuJanin != null){
            foreach( $dataIbuJanin->toArray() as $key => $value )
            {
                switch($key) {
                    case 'kenaikan_berat_badan' : $singleData = [
                                                                        "question" => $key,
                                                                        "answer" => $value,
                                                                        "isRisky" => "-"
                                                                    ];
                                                                    array_push($answerIbuJanin,$singleData);
                                                                    break;
                    case 'hemoglobin'                :  if($value >= 11){
                                                                       $isRisky = false;
                                                                    }else if($value < 11){
                                                                        $isRisky = true;
                                                                    };
                                                                    $singleData = [
                                                                        "question" => $key,
                                                                        "answer" => $value,
                                                                        "isRisky" => $isRisky
                                                                    ];
                                                                    array_push($answerIbuJanin,$singleData);
                                                                    break;
                     case 'tensi_darah'                :  if($value <= 90){
                                                                        $isRisky = false;
                                                                     }else if($value > 90){
                                                                         $isRisky = true;
                                                                     };
                                                                     $singleData = [
                                                                         "question" => $key,
                                                                         "answer" => $value,
                                                                         "isRisky" => $isRisky
                                                                     ];
                                                                     array_push($answerIbuJanin,$singleData);
                                                                     break;

                    case 'gula_darah'                :   if($value >= 95 && $value <= 200){
                                                                        $isRisky = false;
                                                                     }else if($value < 95 || $value > 200){
                                                                         $isRisky = true;
                                                                     };
                                                                     $singleData = [
                                                                         "question" => $key,
                                                                         "answer" => $value,
                                                                         "isRisky" => $isRisky
                                                                     ];
                                                                     array_push($answerIbuJanin,$singleData);
                                                                     break;

                    case 'proteinuria'                :   if($value == "1"){
                                                                        $isRisky = true;
                                                                     }else if($value == "0"){
                                                                         $isRisky = false;
                                                                     };
                                                                     $singleData = [
                                                                         "question" => $key,
                                                                         "answer" => $value,
                                                                         "isRisky" => $isRisky
                                                                     ];
                                                                     array_push($answerIbuJanin,$singleData);
                                                                     break;

                     case 'denyut_jantung'          :   if($value >= 100 && $value <= 160){
                                                                        $isRisky = false;
                                                                      }else if($value < 100 || $value > 160){
                                                                        $isRisky = true;
                                                                      };
                                                                     $singleData = [
                                                                         "question" => $key,
                                                                         "answer" => $value,
                                                                         "isRisky" => $isRisky
                                                                     ];
                                                                     array_push($answerIbuJanin,$singleData);
                                                                     break;

                      case 'tinggi_fundus_uteri'          :   if($periode == 20)
                                                                            {
                                                                                if($value >= 17 && $value <= 23){
                                                                                    $isRisky = false;
                                                                                }else if($value < 17 || $value > 23){
                                                                                    $isRisky = true;
                                                                                };
                                                                                $singleData = [
                                                                                    "question" => $key,
                                                                                    "answer" => $value,
                                                                                    "isRisky" => $isRisky
                                                                                ];
                                                                            }else if($periode == 24){
                                                                                if($value >= 20 && $value <= 26){
                                                                                    $isRisky = false;
                                                                                }else if($value < 20 || $value > 26){
                                                                                    $isRisky = true;
                                                                                };
                                                                                $singleData = [
                                                                                    "question" => $key,
                                                                                    "answer" => $value,
                                                                                    "isRisky" => $isRisky
                                                                                ];
                                                                            }else if($periode == 28){
                                                                                if($value >= 24 && $value <= 30){
                                                                                    $isRisky = false;
                                                                                }else if($value < 24 || $value > 30){
                                                                                    $isRisky = true;
                                                                                };
                                                                                $singleData = [
                                                                                    "question" => $key,
                                                                                    "answer" => $value,
                                                                                    "isRisky" => $isRisky
                                                                                ];
                                                                            }else if($periode == 32){
                                                                                if($value >= 27 && $value <= 33){
                                                                                    $isRisky = false;
                                                                                }else if($value < 27 || $value > 33){
                                                                                    $isRisky = true;
                                                                                };
                                                                                $singleData = [
                                                                                    "question" => $key,
                                                                                    "answer" => $value,
                                                                                    "isRisky" => $isRisky
                                                                                ];
                                                                            }else if($periode == 36){
                                                                                if($value >= 31 && $value <= 37){
                                                                                    $isRisky = false;
                                                                                }else if($value < 31 || $value > 37){
                                                                                    $isRisky = true;
                                                                                };
                                                                                $singleData = [
                                                                                    "question" => $key,
                                                                                    "answer" => $value,
                                                                                    "isRisky" => $isRisky
                                                                                ];
                                                                            }
                                                                            array_push($answerIbuJanin,$singleData);
                                                                            break;

                        case 'taksiran_berat_janin'          :   if($periode == 20)
                                                                                {
                                                                                    if($value >= 300 && $value <= 325){
                                                                                        $isRisky = false;
                                                                                    }else if($value < 300 || $value > 325){
                                                                                        $isRisky = true;
                                                                                    };
                                                                                    $singleData = [
                                                                                        "question" => $key,
                                                                                        "answer" => $value,
                                                                                        "isRisky" => $isRisky
                                                                                    ];
                                                                                }else if($periode == 24){
                                                                                    if($value >= 550 && $value <= 685){
                                                                                        $isRisky = false;
                                                                                    }else if($value < 550 || $value > 685){
                                                                                        $isRisky = true;
                                                                                    };
                                                                                    $singleData = [
                                                                                        "question" => $key,
                                                                                        "answer" => $value,
                                                                                        "isRisky" => $isRisky
                                                                                    ];
                                                                                }else if($periode == 28){
                                                                                    if($value >= 1000 && $value <= 1150){
                                                                                        $isRisky = false;
                                                                                    }else if($value < 1000 || $value > 1150){
                                                                                        $isRisky = true;
                                                                                    };
                                                                                    $singleData = [
                                                                                        "question" => $key,
                                                                                        "answer" => $value,
                                                                                        "isRisky" => $isRisky
                                                                                    ];
                                                                                }else if($periode == 32){
                                                                                    if($value >= 1610 && $value <= 1810){
                                                                                        $isRisky = false;
                                                                                    }else if($value < 1610 || $value > 1810){
                                                                                        $isRisky = true;
                                                                                    };
                                                                                    $singleData = [
                                                                                        "question" => $key,
                                                                                        "answer" => $value,
                                                                                        "isRisky" => $isRisky
                                                                                    ];
                                                                                }else if($periode == 36){
                                                                                    if($value >= 2500 && $value <= 2690){
                                                                                        $isRisky = false;
                                                                                    }else if($value < 2500 || $value > 2690){
                                                                                        $isRisky = true;
                                                                                    };
                                                                                    $singleData = [
                                                                                        "question" => $key,
                                                                                        "answer" => $value,
                                                                                        "isRisky" => $isRisky
                                                                                    ];
                                                                                }
                                                                                array_push($answerIbuJanin,$singleData);
                                                                                break;

                            case 'gerak_janin'                      : if($value == "1"){
                                                                                     $isRisky = false;
                                                                                 }else if($value == "0"){
                                                                                     $isRisky = true;
                                                                                 }
                                                                                 $singleData = [
                                                                                    "question" => $key,
                                                                                    "answer" => $value,
                                                                                    "isRisky" => $isRisky
                                                                                ];
                                                                                array_push($answerIbuJanin,$singleData);
                                                                                break;

                            case 'gerak_janin'                      : if($value == 1){
                                                                                    $isRisky = false;
                                                                                }else if($value > 1){
                                                                                    $isRisky = true;
                                                                                }
                                                                                $singleData = [
                                                                                   "question" => $key,
                                                                                   "answer" => $value,
                                                                                   "isRisky" => $isRisky
                                                                               ];
                                                                               array_push($answerIbuJanin,$singleData);
                                                                               break;


                }
            }

            $arrayIbuJanin = array(
                "id" => $periode .'-minggu',
                "answerDate" => \Carbon\Carbon::parse($dataIbuJanin->created_at)->isoFormat('YYYY-MM-DD'),
                "pdfUrl" =>  $base_url.$pdfIbuJanin,
                "answers" => $answerIbuJanin
            );
            return $arrayIbuJanin;
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

        #12 minggu
        $array12Minggu = $this->_get12MingguResult($id);
        array_push($finalData,$array12Minggu);

        #16 minggu
        $array16Minggu = $this->_get16MingguResult($id);
        array_push($finalData,$array16Minggu);

        #20 minggu
        $array20Minggu = $this->_getIbuJaninResult($id,20);
        array_push($finalData,$array20Minggu);

        #24 minggu
        $array24Minggu = $this->_getIbuJaninResult($id,24);
        array_push($finalData,$array24Minggu);

        #28 minggu
        $array28Minggu = $this->_getIbuJaninResult($id,28);
        array_push($finalData,$array28Minggu);

        #32 minggu
        $array32Minggu = $this->_getIbuJaninResult($id,32);
        array_push($finalData,$array32Minggu);

         #36 minggu
        $array36Minggu = $this->_getIbuJaninResult($id,36);
        array_push($finalData,$array36Minggu);

        $finalResult = [
          "data" => $finalData
        ];
        return $finalResult;
    }

}
