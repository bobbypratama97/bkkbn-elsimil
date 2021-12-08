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

use App\KuesionerHamil;
use App\KuisHamilKontakAwal;
use App\KuisHamil12Minggu;
use App\KuisHamil16Minggu;
use App\KuisHamilIbuJanin;
use App\KuisHamilPersalinan;
use App\KuisHamilNifas;

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
        $dataKontakAwal = KuesionerHamil::where([['id_member','=',$id],['periode','=',1]])
        ->select(['nama', 'nik', 'usia',
                'alamat', 'jumlah_anak','usia_anak_terakhir',
                'anak_stunting', 'hari_pertama_haid_terakhir','sumber_air_bersih','jamban_sehat',
                'rumah_layak_huni', 'bansos','created_at','updated_at'])->first();
        $answerKontakAwal= array();
        $oriPath = public_path('uploads/pdf');
        $filename = 'files51990Flyer_ibu hamil_15x21cm.pdf';
        $finalUrl = $oriPath."/".$filename;

        if($dataKontakAwal != null){
            foreach( $dataKontakAwal->toArray() as $key => $value )
            {
                switch($key)
                {

                    // case 'nama' :
                    // case 'alamat' :
                    // case 'hari_pertama_haid_terakhir' :
										// 		$singleData = [
										// 			"question" => "Hari Pertama Haid Terakhir",
										// 			"answer" => $value,
										// 			"isRisky" => "-"
										// 		];
										// 		array_push($answerKontakAwal,$singleData);
										// 		break;

                    // case 'nik' :
										// 		$singleData = [
										// 			"question" => "NIK",
										// 			"answer" => Helper::decryptNik($value),
										// 			"isRisky" => "-"
										// 		];
										// 		array_push($answerKontakAwal,$singleData);
										// 		break;
                    case 'usia' :
											if($value>=20 && $value<=35){
												$isRisky = false;
											}else if($value<20 || $value>35){
												$isRisky = true;
											}
											$singleData = [
												"question" => "Usia",
												"answer" => $value . " Tahun",
												"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;
                    case 'jumlah_anak'              :
											if($value>=0 && $value<=2){
													$isRisky = false;
											}else if($value>2){
													$isRisky = true;
											}
											$singleData = [
													"question" => "Jumlah Anak",
													"answer" => $value,
													"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;

                    case 'usia_anak_terakhir'     :
											if($value>=4){
													$isRisky = false;
											}else if($value<4){
													$isRisky = true;
											}
											$singleData = [
													"question" => "Usia Anak Terakhir",
													"answer" => $value . " Tahun",
													"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;

                    case 'anak_stunting':
											if($value == "Tidak"){
													$isRisky = false;
											}else if($value == "Ya"){
													$isRisky = true;
											}
											$singleData = [
													"question" => "Memiliki Anak Stunting",
													"answer" => $value,
													"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;

                    case 'sumber_air_bersih'      :
											if($value == "Ya"){
													$isRisky = false;
											}else if($value == "Tidak"){
													$isRisky = true;
											}
											$singleData = [
											"question" => "Memiliki Sumber Air Bersih",
											"answer" => $value,
											"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;

                    case 'jamban_sehat'      :
											if($value == "Ya"){
													$isRisky = false;
											}else if($value == "Tidak"){
													$isRisky = true;
											}
											$singleData = [
											"question" => "Memiliki Jamban Sehat",
											"answer" => $value,
											"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;

                    case 'rumah_layak_huni' :
											if($value == "Ya"){
												$isRisky = false;
											}else if($value == "Tidak"){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Memiliki Rumah Layak Huni",
											"answer" => $value,
											"isRisky" => $isRisky
											];
											array_push($answerKontakAwal,$singleData);
											break;

                    case 'bansos' :
											if($value == "Ya"){
													$isRisky = true;
											}else if($value == "Tidak"){
													$isRisky = false;
											}
											$singleData = [
											"question" => "Menerima Bansos",
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
                "pdfUrl" =>  $base_url.$filename,
                "answers" => $answerKontakAwal
            );
            return $arrayKontakAwal;
        }else{
            $arrayKontakAwal = array(
                "id" => 'kontak-awal',
                "answerDate" => null,
                "pdfUrl" =>  null,
                "answers" => null
            );
            return $arrayKontakAwal;
        }
    }

    private function _get12MingguResult($id)
    {
         $base_url = env('BASE_URL_PDF');
         #kontak-16-minggu
         $data12Minggu = KuesionerHamil::where([['id_member','=',$id],['periode','=',2]])
         ->select(['berat_badan','tinggi_badan','lingkar_lengan_atas',
         'hemoglobin','tensi_darah','gula_darah_sewaktu','riwayat_sakit_kronik'])->first();
         $answer12Minggu= array();
         $oriPath = public_path('uploads/pdf');
         $filename = 'files51990Flyer_ibu hamil_15x21cm.pdf';
         $finalUrl = $oriPath."/".$filename;
         if($data12Minggu != null){
            $tinggiBadanMeter = $data12Minggu->tinggi_badan / 100;
            $imtCalculation = $data12Minggu->berat_badan / ($tinggiBadanMeter ^ 2);
            foreach( $data12Minggu->toArray() as $key => $value )
             {
                 switch($key) {
                    case 'berat_badan' :
											if($imtCalculation >= 19 && $imtCalculation <= 29){
												$isRisky = false;
											}else if($imtCalculation < 19 || $imtCalculation > 29){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Berat Badan",
											"answer" => $value . " Kg",
											"isRisky" => $isRisky
											];
											array_push($answer12Minggu,$singleData);
											break;
                    case 'tinggi_badan' :
											if($value >= 145){
												$isRisky = false;
											}else if($value < 145){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Tinggi Badan",
											"answer" => $value . " cm",
											"isRisky" => $isRisky
											];
											array_push($answer12Minggu,$singleData);
											break;

                    case 'lingkar_lengan_atas' :
											if($value >= 23.5){
													$isRisky = false;
											}else if($value < 23.5){
													$isRisky = true;
											}
											$singleData = [
											"question" => "Lingkar Lengan Atas",
											"answer" => $value . " cm",
											"isRisky" => $isRisky
											];
											array_push($answer12Minggu,$singleData);
											break;

										case 'hemoglobin' :
											if($value >= 11){
												$isRisky = false;
											}else if($value < 11){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Kadar Hemoglobin",
											"answer" => $value . " gr/dl",
											"isRisky" => $isRisky
											];
											array_push($answer12Minggu,$singleData);
											break;

                    case 'tensi_darah' :
											if($value <= 90){
												$isRisky = false;
											}else if($value > 90){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Tensi Darah",
											"answer" => $value . " mmHg",
											"isRisky" => $isRisky
											];
											array_push($answer12Minggu,$singleData);
											break;

										case 'gula_darah_sewaktu' :
											if($value >= 95 && $value <= 200){
												$isRisky = false;
											}else if($value < 95 || $value > 200){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Gula Darah Sewaktu",
											"answer" => $value . " mg/dl",
											"isRisky" => $isRisky
											];
											array_push($answer12Minggu,$singleData);
											break;

										case 'riwayat_sakit_kronik' :
											if($value == "Ada"){
												$isRisky = true;
											}else if($value == "Tidak Ada"){
												$isRisky = false;
											}
											$singleData = [
											"question" => "Riwayat Sakit Kronik",
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
               "pdfUrl" =>  $base_url.$filename,
               "answers" => $answer12Minggu
             );
             return $array12Minggu;
         }else{
            $array12Minggu = array(
                "id" => '12-minggu',
                "answerDate" => null,
                "pdfUrl" =>  null,
                "answers" => null
            );
            return $array12Minggu;
         }
    }

    private function _get16MingguResult($id)
    {
         $today = date("Y-m-d");
         $base_url = env('BASE_URL_PDF');
         #kontak-16-minggu
         $data16Minggu = KuesionerHamil::where([['id_member','=',$id],['periode','=',3]])
         ->select(['hemoglobin','tensi_darah','gula_darah_sewaktu'])->first();
         $answer16Minggu= array();
         $oriPath = public_path('uploads/pdf');
         $filename = 'files51990Flyer_ibu hamil_15x21cm.pdf';
         $finalUrl = $oriPath."/".$filename;
         if($data16Minggu != null){
             foreach( $data16Minggu->toArray() as $key => $value )
             {
								switch($key) {
									case 'hemoglobin' :
										if($value >= 11){
											$isRisky = false;
										}else if($value < 11){
											$isRisky = true;
										}
										$singleData = [
										"question" => "Kadar Hemoglobin",
										"answer" => $value . " gr/dl",
										"isRisky" => $isRisky
										];
										array_push($answer16Minggu,$singleData);
										break;
									case 'tensi_darah' :
										if($value <= 90){
											$isRisky = false;
										}else if($value > 90){
											$isRisky = true;
										}
										$singleData = [
										"question" => "Tensi Darah",
										"answer" => $value . " mmHg",
										"isRisky" => $isRisky
										];
										array_push($answer16Minggu,$singleData);
										break;

										case 'gula_darah_sewaktu' :
											if($value >= 95 && $value <= 200){
												$isRisky = false;
											}else if($value < 95 || $value > 200 ){
												$isRisky = true;
											}
											$singleData = [
											"question" => "Gula Darah Sewaktu",
											"answer" => $value . " mg/dl",
											"isRisky" => $isRisky
											];
											array_push($answer16Minggu,$singleData);
											break;
                 }
             }
             $array16Minggu = array(
               "id" => '16-minggu',
               "answerDate" => \Carbon\Carbon::parse($data16Minggu->created_at)->isoFormat('YYYY-MM-DD'),
               "pdfUrl" =>  $base_url.$filename,
               "answers" => $answer16Minggu
             );
             return $array16Minggu;
         }else{
            $array16Minggu = array(
                "id" => '16-minggu',
                "answerDate" => null,
                "pdfUrl" => null,
                "answers" => null
              );
              return $array16Minggu;
         }
    }

    private function _getPeriodeID($periode)
    {
        if($periode == 20){
            $id = 4;
        }else if($periode == 24){
            $id = 5;
        }else if($periode == 28){
            $id = 6;
        }else if($periode == 32){
            $id = 7;
        }else if($periode == 36){
            $id = 8;
        }

        return $id;
    }

    private function _getIbuJaninResult($id,$periode)
    {
        $today = date("Y-m-d");
        $base_url = env('BASE_URL_PDF');
        $periode_id = $this->_getPeriodeID($periode);
        $dataIbuJanin = KuesionerHamil::where([['id_member','=',$id],['periode','=',$periode_id]])
        ->select(['kenaikan_berat_badan','hemoglobin','tensi_darah','gula_darah_sewaktu',
        'proteinuria','denyut_jantung','tinggi_fundus_uteri','taksiran_berat_janin','gerak_janin','jumlah_janin'
        ])->first();
        $answerIbuJanin = array();
        $oriPath = public_path('uploads/pdf');
        $filename = 'files51990Flyer_ibu hamil_15x21cm.pdf';
        $finalUrl = $oriPath."/".$filename;
        if($dataIbuJanin != null){
            foreach( $dataIbuJanin->toArray() as $key => $value )
            {
                switch($key) {
                    case 'kenaikan_berat_badan' :
											$singleData = [
												"question" => "Kenaikan Berat Badan",
												"answer" => $value . " kg",
												"isRisky" => "-"
											];
											array_push($answerIbuJanin,$singleData);
											break;
                    case 'hemoglobin'   :
											if($value >= 11){
												$isRisky = false;
											}else if($value < 11){
												$isRisky = true;
											};
											$singleData = [
												"question" => "Kadar Hemoglobin",
												"answer" => $value . " gr/dl",
												"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;
                    case 'tensi_darah'  :
											if($value <= 90){
												$isRisky = false;
											}else if($value > 90){
													$isRisky = true;
											};
											$singleData = [
													"question" => "Tensi Darah",
													"answer" => $value . " mmHg",
													"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;
                    case 'gula_darah' :
											if($value >= 95 && $value <= 200){
												$isRisky = false;
											}else if($value < 95 || $value > 200){
													$isRisky = true;
											};
											$singleData = [
													"question" => "Kadar Gula Darah",
													"answer" => $value . " mg/dl",
													"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;
                    case 'proteinuria'                :
											if($value == "Positif"){
												$isRisky = true;
											}else if($value == "Negatif"){
													$isRisky = false;
											};
											$singleData = [
													"question" => "Proteinuria",
													"answer" => $value,
													"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;

                    case 'denyut_jantung'          :
											if($value >= 100 && $value <= 160){
												$isRisky = false;
											}else if($value < 100 || $value > 160){
												$isRisky = true;
											};
											$singleData = [
													"question" => "Tingkat Denyut Jantung",
													"answer" => $value . " bpm",
													"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;

                    case 'tinggi_fundus_uteri' :
											if($periode == 20)
											{
												if($value >= 17 && $value <= 23){
														$isRisky = false;
												}else if($value < 17 || $value > 23){
														$isRisky = true;
												};

											}else if($periode == 24){
													if($value >= 20 && $value <= 26){
															$isRisky = false;
													}else if($value < 20 || $value > 26){
															$isRisky = true;
													};

											}else if($periode == 28){
													if($value >= 24 && $value <= 30){
															$isRisky = false;
													}else if($value < 24 || $value > 30){
															$isRisky = true;
													};

											}else if($periode == 32){
													if($value >= 27 && $value <= 33){
															$isRisky = false;
													}else if($value < 27 || $value > 33){
															$isRisky = true;
													};

											}else if($periode == 36){
													if($value >= 31 && $value <= 37){
															$isRisky = false;
													}else if($value < 31 || $value > 37){
															$isRisky = true;
													};
											}
											$singleData = [
												"question" => "Tinggi Fundus Uteri",
												"answer" => $value . " cm",
												"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;

                    case 'taksiran_berat_janin'          :
											if($periode == 20)
											{
													if($value >= 300 && $value <= 325){
															$isRisky = false;
													}else if($value < 300 || $value > 325){
															$isRisky = true;
													};

											}else if($periode == 24){
													if($value >= 550 && $value <= 685){
															$isRisky = false;
													}else if($value < 550 || $value > 685){
															$isRisky = true;
													};

											}else if($periode == 28){
													if($value >= 1000 && $value <= 1150){
															$isRisky = false;
													}else if($value < 1000 || $value > 1150){
															$isRisky = true;
													};

											}else if($periode == 32){
													if($value >= 1610 && $value <= 1810){
															$isRisky = false;
													}else if($value < 1610 || $value > 1810){
															$isRisky = true;
													};

											}else if($periode == 36){
													if($value >= 2500 && $value <= 2690){
															$isRisky = false;
													}else if($value < 2500 || $value > 2690){
															$isRisky = true;
													};

											}
											$singleData = [
												"question" => "Taksiran Berat Janin",
												"answer" => $value . " gr",
												"isRisky" => $isRisky
											];
											array_push($answerIbuJanin,$singleData);
											break;

										case 'gerak_janin'  :
											if($value == "Positif"){
												$isRisky = false;
											}else if($value == "Negatif"){
												$isRisky = true;
											}
											$singleData = [
												"question" => "Gerak Janin",
												"answer" => $value,
												"isRisky" => $isRisky
										];
										array_push($answerIbuJanin,$singleData);
										break;

										case 'jumlah_janin'   :
											if($value == 1){
												$isRisky = false;
											}else if($value > 1){
												$isRisky = true;
											}
											$singleData = [
													"question" => "Jumlah Janin",
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
                "pdfUrl" =>  $base_url.$filename,
                "answers" => $answerIbuJanin
            );
            return $arrayIbuJanin;
        }else{
            $arrayIbuJanin = array(
                "id" => $periode .'-minggu',
                "answerDate" => null,
                "pdfUrl" =>  null,
                "answers" => null
            );
            return $arrayIbuJanin;
        }
    }

    private function _getPersalinanResult($id)
    {
         $today = date("Y-m-d");
         $base_url = env('BASE_URL_PDF');
         #kontak-16-minggu
         $dataPersalinan = KuesionerHamil::where([['id_member','=',$id],['periode','=',9]])
         ->select(['tanggal_persalinan','kb','usia_janin','berat_janin','panjang_badan_janin','jumlah_bayi'
         ])->first();
         $answerPersalinan= array();
         $oriPath = public_path('uploads/pdf');
         $filename = 'files51990Flyer_ibu hamil_15x21cm.pdf';
         $finalUrl = $oriPath."/".$filename;
         if($dataPersalinan != null){
             foreach( $dataPersalinan->toArray() as $key => $value )
             {
                 switch($key) {
                    //  case 'tanggal_persalinan' : $singleData = [
                    //                                             "question" => $key,
                    //                                             "answer" => $value,
                    //                                             "isRisky" => "-"
                    //                                             ];
                    //                                             array_push($answerPersalinan,$singleData);
                    //                                             break;

                    case 'kb'   :
											$isRisky = false;
											if($value == "Ya"){
												$isRisky = false;
											}else if($value == "Tidak"){
													$isRisky = true;
											}
											$singleData = [
												"question" => "Menggunakan KB",
												"answer" => $value,
												"isRisky" => $isRisky
											];
											array_push($answerPersalinan,$singleData);
											break;

                    case 'usia_janin'    :
											if($value >= 37 && $value <= 42){
												$isRisky = false;
											}else if($value < 37 || $value > 42){
												$isRisky = true;
											}
											$singleData = [
													"question" => "Usia Janin",
													"answer" => $value .' minggu',
													"isRisky" => $isRisky
											];
											array_push($answerPersalinan,$singleData);
											break;

                    case 'berat_janin'                            :
											if($value >= 2500 && $value <= 3900){
												$isRisky = false;
											}else if($value < 2500 || $value > 3900){
													$isRisky = true;
											}
											$singleData = [
													"question" => "Berat Janin",
													"answer" => $value . ' gr',
													"isRisky" => $isRisky
											];
											array_push($answerPersalinan,$singleData);
											break;

                   case 'panjang_badan_janin'                :
										if($value >= 48 && $value <= 53){
											$isRisky = false;
										}else if($value < 48 || $value > 53){
												$isRisky = true;
										}
										$singleData = [
												"question" => "Panjang Badan Janin",
												"answer" => $value . ' cm',
												"isRisky" => $isRisky
										];
										array_push($answerPersalinan,$singleData);
										break;

                  case 'jumlah_bayi'  :
										if($value == 1){
											$isRisky = false;
										}else if($value > 1){
												$isRisky = true;
										}
										$singleData = [
												"question" => "Jumlah Bayi",
												"answer" => $value,
												"isRisky" => $isRisky
										];
										array_push($answerPersalinan,$singleData);
										break;

                 }
             }
             $arrayPersalinan = array(
               "id" => 'persalinan',
               "answerDate" => \Carbon\Carbon::parse($dataPersalinan->created_at)->isoFormat('YYYY-MM-DD'),
               "pdfUrl" =>  $base_url.$filename,
               "answers" => $answerPersalinan
             );
             return $arrayPersalinan;
         }else{
             $arrayPersalinan = array(
                "id" => 'persalinan',
                "answerDate" => null,
                "pdfUrl" =>  null,
                "answers" => null
              );
              return $arrayPersalinan;
         }
    }


    private function _getNifasResult($id)
    {
        $today = date("Y-m-d");
        $base_url = env('BASE_URL_PDF');
        #kontak-16-minggu
        $dataNifas = KuesionerHamil::where([['id_member','=',$id],['periode','=',10]])
        ->select(['komplikasi','asi','kbpp_mkjp'
        ])->first();
        $answerNifas= array();
        $oriPath = public_path('uploads/pdf');
        $filename = 'files51990Flyer_ibu hamil_15x21cm.pdf';
        $finalUrl = $oriPath."/".$filename;
        if($dataNifas != null){
            foreach( $dataNifas->toArray() as $key => $value )
            {
                switch($key) {
                    case 'komplikasi' :
											$isRisky= false;
											if($value == "Ya"){
												$isRisky = true;
											}else if($value == "Tidak"){
												$isRisky = false;
											}
											$singleData = [
											"question" => "Ada Komplikasi",
											"answer" => $value,
											"isRisky" => $isRisky
											];
											array_push($answerNifas,$singleData);
											break;

										case 'asi' :
											if($value == "Ya"){
												$isRisky = false;
											}else if($value == "Tidak"){
												$isRisky = true;
											}
											$singleData = [
											"question" => "ASI",
											"answer" => $value,
											"isRisky" => $isRisky
											];
											array_push($answerNifas,$singleData);
											break;

										case 'kbpp_mkjp' :
											if($value == "MKJP"){
												$isRisky = false;
											}else if($value == "Tidak"){
												$isRisky = true;
											}
											$singleData = [
											"question" => "KBPP MKJP",
											"answer" => $value,
											"isRisky" => $isRisky
											];
											array_push($answerNifas,$singleData);
											break;

                }
            }
            $arrayNifas = array(
              "id" => 'nifas',
              "answerDate" => \Carbon\Carbon::parse($dataNifas->created_at)->isoFormat('YYYY-MM-DD'),
              "pdfUrl" =>  $base_url.$filename,
              "answers" => $answerNifas
            );
            return $arrayNifas;
        }else{
            $arrayNifas = array(
                "id" => 'nifas',
                "answerDate" => null,
                "pdfUrl" =>  null,
                "answers" => null
              );
              return $arrayNifas;
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

        #persalinan
        $arrayPersalinan = $this->_getPersalinanResult($id);
        array_push($finalData,$arrayPersalinan);

        #nifas
        $arrayNifas = $this->_getNifasResult($id);
        array_push($finalData,$arrayNifas);

        $finalResult = [
          "data" => $finalData
        ];
        return $finalResult;
    }

}
