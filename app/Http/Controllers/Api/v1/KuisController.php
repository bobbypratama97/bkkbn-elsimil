<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use DB;
use PDF;
use Helper;

use App\Member;

use App\Kuis;
use App\KuisHeader;
use App\KuisDetail;
use App\KuisBobot;
use App\KuisBobotFile;
use App\KuisSummary;

use App\WidgetComponentDetail;

use App\KuisResult;
use App\KuisResultHeader;
use App\KuisResultDetail;
use App\KuisResultComment;
use App\KuisResultBobotFile;

use App\LogbookHistory;

use App\MemberDelegate;

class KuisController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function kuislist(Request $request) {
        try {

            $base_url = env('BASE_URL') . env('BASE_URL_KUESIONER');

            $member = Member::where('id', $request->id)->first();
            $gender = ['all', $member->gender];

            $data = Kuis::whereNull('deleted_by')->where('apv', 'APV300')->whereIn('gender', $gender)->select([
                'id', 
                'title', 
                'created_at', 
                DB::raw("concat('{$base_url}', thumbnail) AS thumbnail"),
            ])->orderBy('id', 'DESC')->get();

            $res = [];
            foreach ($data as $key => $row) {
                $get = KuisResult::where('member_id', $request->id)->where('kuis_id', $row->id)->where('status', 1)->orderBy('id', 'DESC')->first();

                if (!empty($get)) {
                    $count = KuisResultDetail::where('result_id', $get->id)->get();

                    $answered = 0;
                    foreach ($count as $keys => $rows) {
                        $answered = (!empty($rows->value)) ? $answered + 1 : $answered;
                    }

                    $comment = KuisResultComment::where('result_id', $get->id)->orderBy('id', 'DESC')->first();

                    $result_id = $get->id;
                    $rating = $get->rating;
                    $background = $get->rating_color;
                    $action = 'result';
                    $total = count($count);
                    $answered = $answered;
                    $ulasan = (!empty($comment)) ? 1 : 0;
                } else {
                    $result_id = 0;
                    $rating = '';
                    $background = '';
                    $action = 'start';
                    $total = 0;
                    $answered = 0;
                    $ulasan = 0;
                }

                $row->result_id = $result_id;
                $row->rating = $rating;
                $row->background = $background;
                $row->action = $action;
                $row->total_pertanyaan = $total;
                $row->answered = $answered;
                $row->ulasan = $ulasan;
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

    public function kuisintro(Request $request) {
        try {

            $base_url = env('BASE_URL') . env('BASE_URL_KUESIONER');

            $data = Kuis::where('id', $request->id)->select([
                'id', 
                'title', 
                'deskripsi', 
                DB::raw("concat('{$base_url}', image) AS image")
            ])->first();

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

    public function pertanyaan(Request $request) {
        try {

            $header = KuisHeader::whereNull('deleted_by')->where('kuis_id', $request->id)->select(['kuis_id', 'id as header_id', 'jenis', 'deskripsi', 'caption'])->orderBy('position')->get();

            $data = [];
            foreach ($header as $key => $row) {
                if ($row['jenis'] == 'widget') {
                    $row['caption'] = '';
                    $data[$key] = $row;


                    $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['header_id'])->get()->toArray();
                    $datapertanyaan = [];
                    foreach ($detail as $keys => $vals) {
                        $pertanyaan = [
                            'kuis_id' => $row['kuis_id'],
                            'header_id' => $vals['header_id'],
                            'pertanyaan_id' => $vals['id'],
                            'title' => $vals['title'],
                            'satuan' => $vals['satuan'],
                            'tipe' => $vals['pilihan']
                        ];

                        $get = WidgetComponentDetail::where('component_id', $vals['komponen_id'])->get()->toArray();
                        if (!empty($get)) {
                            foreach ($get as $keyz => $valz) {
                                $pertanyaan[$valz['name']] = $valz['value'];
                            }
                        }
                        $datapertanyaan[$keys] = $pertanyaan;
                    }

                    $data[$key]['pertanyaan'] = $datapertanyaan;
                } else {
                    $data[$key] = $row;

                    $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['header_id'])->get();
                    $datapertanyaan = [];
                    foreach ($detail as $keys => $vals) {
                        $pertanyaan = [
                            'kuis_id' => $row['kuis_id'],
                            'header_id' => $vals['header_id'],
                            'pertanyaan_id' => $vals['id'],
                            'title' => $vals['title'],
                            'satuan' => $vals['satuan'],
                            'tipe' => $vals['pilihan']
                        ];

                        $element = [];
                        if ($vals['pilihan'] == 'radio' || $vals['pilihan'] == 'dropdown' || $vals['pilihan'] == 'ganda') {
                            $label = KuisBobot::where('header_id', $vals['header_id'])->get();
                            $dataelement = [];
                            foreach ($label as $keyz => $valz) {
                                $elements = [
                                    'id' => $valz['id'],
                                    'option' => $valz['label']
                                ];

                                array_push($dataelement, $elements);
                            }

                            $pertanyaan['element'] = $dataelement;
                        }

                        array_push($datapertanyaan, $pertanyaan);
                    }
                    $data[$key]['pertanyaan'] = $datapertanyaan;

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

    public function submitkuis(Request $request) {
        $data = $request->all();

        //validasi input 
        $validators = Validator::make($request->all(), [
            'user_id' => ['required'],
            'data.*.kuis_id' => ['required'],
            'data.*.header_id' => ['required'],
            'data.*.jenis' => ['required'],
            'data.*.pertanyaan.*.kuis_id' => ['required'],
            'data.*.pertanyaan.*.header_id' => ['required'],
            'data.*.pertanyaan.*.pertanyaan_id' => ['required'],
            'data.*.pertanyaan.*.tipe' => ['required'],
            'data.*.pertanyaan.*.value' => ['required'],
            'data.*.pertanyaan.*.file_name' => ['nullable'],
        ], [
            'required' => 'Data yang anda masukan tidak lengkap (:attribute)'
        ]);

        if ($validators->fails()) {
            return response()->json([
                'code' => 401,
                'error' => true,
                'title' => 'Perhatian',
                'message' => $validators->errors()->first(),
            ], 401);
        }

        // print_r ($data); die;
        $header = $data['data'][0];
        // if(!$header) {
        //     return response()->json([
        //         'code' => 401,
        //         'error' => true,
        //         'title' => 'Perhatian',
        //         'message' => 'Data yang dimasukan belum lengkap.'
        //     ], 401);
        // }

        $kuis = Kuis::where('id', $header['kuis_id'])->select(['gender', 'title', 'max_point'])->first();

        $code = Helper::randomString(8);
        $check = KuisResult::where('kuis_code', $code)->first();

        if ($check) {
            $code = Helper::randomString(8);
        }

        $simpan = new KuisResult;
        $simpan->kuis_code = strtoupper($code);
        $simpan->member_id = $request->user_id;
        $simpan->kuis_id = $header['kuis_id'];
        $simpan->kuis_title = $kuis->title;
        $simpan->kuis_gender = $kuis->gender;
        $simpan->member_kuis_nilai = 0;
        $simpan->status = 0;
        $simpan->kuis_max_nilai = $kuis->max_point;
        $simpan->created_at = date('Y-m-d H:i:s');
        $simpan->created_by = $request->user_id;

        $simpan->save();

        $update = KuisResult::where('id', '!=', $simpan->id)->where('member_id', $request->user_id)->where('kuis_id', $header['kuis_id'])->update([
            'status' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $request->user_id
        ]);

        foreach ($data['data'] as $key => $row) {
            //print_r ($row['jenis']);
            if ($row['jenis'] == 'widget') {
                $find = KuisHeader::where('id', $row['header_id'])->first();

                $saveHeader = new KuisResultHeader;
                $saveHeader->result_id = $simpan->id;
                $saveHeader->header_id = $row['header_id'];
                $saveHeader->pertanyaan_header_jenis = $find->jenis;
                $saveHeader->pertanyaan_header_caption = $find->caption;
                $saveHeader->pertanyaan_header_formula = $find->formula;
                $saveHeader->created_at = date('Y-m-d H:i:s');
                $saveHeader->created_by = $request->user_id;

                if ($saveHeader->save()) {
                    $bobotQ = [
                        'id' => 0,
                        'kondisi' => '',
                        'label' => '',
                        'nilai' => '',
                        'bobot' => '',
                        'value' => '',
                        'rating' => '',
                        'color' => '',
                        'formula_value' => ''
                    ];

                    $mapping_widget_question = [];  //ammad-2021-06-21 0635
                    foreach ($row['pertanyaan'] as $keys => $rows) {
                        $mapping_widget_question[$rows['tipe']] = $rows;    //ammad-2021-06-21 0635
                        $findQ = KuisDetail::where('id', $rows['pertanyaan_id'])->first();

                        if ($rows['tipe'] == 'upload') {

                            $oriPath = public_path('uploads/memberfile');
                            $filename = date('YmdHis') . $rows['file_name'];
                            $file = base64_decode($rows['value']);

                            file_put_contents($oriPath . '/' . $filename, $file);

                            $value = $filename;

                        } else {
                            $value = $rows['value'];
                        }

                        $pertanyaan = KuisDetail::where('id', $rows['pertanyaan_id'])->first();

                        $saveDetail = new KuisResultDetail;
                        $saveDetail->result_id = $simpan->id;
                        $saveDetail->value = $value;
                        $saveDetail->formula_value = $bobotQ['formula_value'];
                        $saveDetail->header_id = $rows['header_id'];
                        $saveDetail->pertanyaan_id = $rows['pertanyaan_id'];
                        $saveDetail->pertanyaan_detail_title = $pertanyaan->title;
                        $saveDetail->pertanyaan_detail_pilihan = $pertanyaan->pilihan;
                        $saveDetail->pertanyaan_detail_bobot = $pertanyaan->bobot;
                        $saveDetail->pertanyaan_bobot_id = $bobotQ['id'];
                        $saveDetail->pertanyaan_bobot_kondisi = $bobotQ['kondisi'];
                        $saveDetail->pertanyaan_bobot_label = $bobotQ['label'];
                        $saveDetail->pertanyaan_bobot_nilai = $bobotQ['nilai'];
                        $saveDetail->pertanyaan_bobot = $bobotQ['bobot'];
                        $saveDetail->pertanyaan_rating = $bobotQ['rating'];
                        $saveDetail->pertanyaan_rating_color = $bobotQ['color'];
                        $saveDetail->created_at = date('Y-m-d H:i:s');
                        $saveDetail->created_by = $request->user_id;

                        $saveDetail->save();
                    }

                }

            }

            if ($row['jenis'] == 'combine') {
                $bobotQ = [
                    'id' => 0,
                    'kondisi' => '',
                    'label' => '',
                    'nilai' => '',
                    'bobot' => '',
                    'value' => '',
                    'rating' => '',
                    'color' => '',
                    'formula_value' => ''
                ];

                $find = KuisHeader::where('id', $row['header_id'])->first();

                $saveHeader = new KuisResultHeader;
                $saveHeader->result_id = $simpan->id;
                $saveHeader->header_id = $row['header_id'];
                $saveHeader->pertanyaan_header_jenis = $find->jenis;
                $saveHeader->pertanyaan_header_caption = $find->caption;
                $saveHeader->pertanyaan_header_formula = $find->formula;
                $saveHeader->created_at = date('Y-m-d H:i:s');
                $saveHeader->created_by = $request->user_id;

                if ($saveHeader->save()) {
                    $value1 = (double)$row['pertanyaan'][0]['value'] ?? 0;
                    $value2 = (double)$row['pertanyaan'][1]['value'] ?? 0;

                    $result = 0;
                    if (!empty($find->formula)) {
                        $formula = $find->formula;
                        $formula = str_replace(['hasil_pertanyaan_1', 'hasil_pertanyaan_2'], [$value1, $value2], $formula);

                        eval( '$result = (' . $formula. ');' );
                    }

                    $bobotid = '';
                    foreach ($row['pertanyaan'] as $keys => $rows) {
                        $findQ = KuisDetail::where('id', $rows['pertanyaan_id'])->first();
                        if ($findQ->bobot == '1') {
                            $value = round($result, 1);

                            $findB = KuisBobot::where('header_id', $row['header_id'])->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])->get()->toArray();

                            $compare = [];
                            foreach ($findB as $k => $r) {
                                if ($findB[$k]['kondisi'] == '1') { $compare[$findB[$k]['kondisi']] = '0 - ' . $r['nilai']; }
                                if ($findB[$k]['kondisi'] == '2') { $compare[$findB[$k]['kondisi']] = $r['nilai'] . ' - ' . $r['nilai']; }
                                if ($findB[$k]['kondisi'] == '3') { $compare[$findB[$k]['kondisi']] = $r['nilai']; }
                                if ($findB[$k]['kondisi'] == '4') { $compare[$findB[$k]['kondisi']] = $r['nilai'] . ' - 10000000'; }
                            }

                            foreach ($compare as $z => $y) {
                                $exp = explode('-', $y);
                                //if ( in_array($value, range($exp[0], $exp[1])) ) {
                                if (is_numeric($value) && $value > trim($exp[0]) && $value <= trim($exp[1])) {
                                    $key = array_search($z, array_column($findB, 'kondisi'));

                                    $bobotQ = [
                                        'id' => $findB[$key]['id'],
                                        'kondisi' => $findB[$key]['kondisi'],
                                        'label' => $findB[$key]['label'],
                                        'nilai' => $findB[$key]['nilai'],
                                        'bobot' => $findB[$key]['bobot'],
                                        'value' => $rows['value'],
                                        'rating' => $findB[$key]['rating'],
                                        'color' => $findB[$key]['rating_color'],
                                        'formula_value' => $value
                                    ];
                                } else if (is_numeric($value) && $value > $exp[0] && $value <= $exp[1]) {
                                    $key = array_search($z, array_column($findB, 'kondisi'));

                                    $bobotQ = [
                                        'id' => $findB[$key]['id'],
                                        'kondisi' => $findB[$key]['kondisi'],
                                        'label' => $findB[$key]['label'],
                                        'nilai' => $findB[$key]['nilai'],
                                        'bobot' => $findB[$key]['bobot'],
                                        'value' => $rows['value'],
                                        'rating' => $findB[$key]['rating'],
                                        'color' => $findB[$key]['rating_color'],
                                        'formula_value' => $value
                                    ];
                                }
                            }
                        } else {
                            $bobotQ = [
                                'id' => 0,
                                'kondisi' => '',
                                'label' => '',
                                'nilai' => '',
                                'bobot' => '',
                                'value' => '',
                                'rating' => '',
                                'color' => '',
                                'formula_value' => ''
                            ];
                        }

                        // print_r ($bobotQ);die;

                        $pertanyaan = KuisDetail::where('id', $rows['pertanyaan_id'])->first();

                        $saveDetail = new KuisResultDetail;
                        $saveDetail->result_id = $simpan->id;
                        $saveDetail->value = $bobotQ['value'];
                        $saveDetail->formula_value = $bobotQ['formula_value'];
                        $saveDetail->header_id = $rows['header_id'];
                        $saveDetail->pertanyaan_id = $rows['pertanyaan_id'];
                        $saveDetail->pertanyaan_detail_title = $pertanyaan->title;
                        $saveDetail->pertanyaan_detail_pilihan = $pertanyaan->pilihan;
                        $saveDetail->pertanyaan_detail_bobot = $pertanyaan->bobot;
                        $saveDetail->pertanyaan_bobot_id = $bobotQ['id'];
                        $saveDetail->pertanyaan_bobot_kondisi = $bobotQ['kondisi'];
                        $saveDetail->pertanyaan_bobot_label = $bobotQ['label'];
                        $saveDetail->pertanyaan_bobot_nilai = $bobotQ['nilai'];
                        $saveDetail->pertanyaan_bobot = $bobotQ['bobot'];
                        $saveDetail->pertanyaan_rating = $bobotQ['rating'];
                        $saveDetail->pertanyaan_rating_color = $bobotQ['color'];
                        $saveDetail->created_at = date('Y-m-d H:i:s');
                        $saveDetail->created_by = $request->user_id;

                        $saveDetail->save();

                        $bobotid = $bobotQ['id'];

                    }

                    $filebobot = KuisBobotFile::whereNull('deleted_by')
                        ->where('pertanyaan_bobot_id', $bobotid)
                        ->get();

                    if ($filebobot->isNotEmpty()) {
                        foreach ($filebobot as $key => $row) {
                            $saveBobotFile = new KuisResultBobotFile;
                            $saveBobotFile->result_id = $simpan->id;
                            $saveBobotFile->pertanyaan_bobot_id = $bobotQ['id'];
                            $saveBobotFile->name = $row->name;
                            $saveBobotFile->file = $row->file;
                            $saveBobotFile->created_at = date('Y-m-d H:i:s');
                            $saveBobotFile->created_by = $request->user_id;

                            $saveBobotFile->save();
                        }
                    }
                }
            }

            //die;

            if ($row['jenis'] == 'single') {
                $find = KuisHeader::where('id', $row['header_id'])->first();
                //print_r ($find);

                $saveHeader = new KuisResultHeader;
                $saveHeader->result_id = $simpan->id;
                $saveHeader->header_id = $row['header_id'];
                $saveHeader->pertanyaan_header_jenis = $find->jenis;
                $saveHeader->pertanyaan_header_caption = $find->caption;
                $saveHeader->pertanyaan_header_formula = $find->formula;
                $saveHeader->created_at = date('Y-m-d H:i:s');
                $saveHeader->created_by = $request->user_id;

                if ($saveHeader->save()) {

                    $bobotQ = [
                        'id' => 0,
                        'kondisi' => '',
                        'label' => '',
                        'nilai' => '',
                        'bobot' => '',
                        'value' => '',
                        'rating' => '',
                        'color' => ''
                    ];

                    foreach ($row['pertanyaan'] as $keys => $rows) {
                        if ($rows['tipe'] == 'angka') {
                            $findQ = KuisDetail::where('header_id', $row['header_id'])->first();
                            if ($findQ->bobot == '1') {
                                $value = $rows['value'];

                                $findB = KuisBobot::where('header_id', $row['header_id'])->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])->get()->toArray();

                                $compare = [];
                                foreach ($findB as $k => $r) {
                                    if ($findB[$k]['kondisi'] == '1') { $compare[$findB[$k]['kondisi']] = '0 - ' . $r['nilai']; }
                                    if ($findB[$k]['kondisi'] == '2') { $compare[$findB[$k]['kondisi']] = $r['nilai'] . ' - ' . $r['nilai']; }
                                    if ($findB[$k]['kondisi'] == '3') { $compare[$findB[$k]['kondisi']] = $r['nilai']; }
                                    if ($findB[$k]['kondisi'] == '4') { $compare[$findB[$k]['kondisi']] = $r['nilai'] . ' - 10000000'; }
                                }

                                //print_r ($compare);
                                foreach ($compare as $z => $y) {
                                    $exp = explode('-', $y);
                                    //if ( in_array($value, range($exp[0], $exp[1])) ) {
                                    if (is_numeric($value) && $value > trim($exp[0]) && $value <= trim($exp[1])) {
                                        $key = array_search($z, array_column($findB, 'kondisi'));

                                        $bobotQ = [
                                            'id' => $findB[$key]['id'],
                                            'kondisi' => $findB[$key]['kondisi'],
                                            'label' => $findB[$key]['label'],
                                            'nilai' => $findB[$key]['nilai'],
                                            'bobot' => $findB[$key]['bobot'],
                                            'value' => $rows['value'],
                                            'rating' => $findB[$key]['rating'],
                                            'color' => $findB[$key]['rating_color']
                                        ];
                                    } else if (is_numeric($value) && $value >= trim($exp[0]) && $value <= trim($exp[1])) {
                                        $key = array_search($z, array_column($findB, 'kondisi'));

                                        $bobotQ = [
                                            'id' => $findB[$key]['id'],
                                            'kondisi' => $findB[$key]['kondisi'],
                                            'label' => $findB[$key]['label'],
                                            'nilai' => $findB[$key]['nilai'],
                                            'bobot' => $findB[$key]['bobot'],
                                            'value' => $rows['value'],
                                            'rating' => $findB[$key]['rating'],
                                            'color' => $findB[$key]['rating_color']
                                        ];
                                    }
                                }
                            } else {
                                $bobotQ = [
                                    'id' => 0,
                                    'kondisi' => '',
                                    'label' => '',
                                    'nilai' => '',
                                    'bobot' => '',
                                    'value' => '',
                                    'rating' => '',
                                    'color' => ''
                                ];
                            }

                            //print_r ($bobotQ);
                            //die;

                            $pertanyaan = KuisDetail::where('header_id', $row['header_id'])->first();

                            $saveDetail = new KuisResultDetail;
                            $saveDetail->result_id = $simpan->id;
                            $saveDetail->value = $bobotQ['value'];
                            $saveDetail->header_id = $rows['header_id'];
                            $saveDetail->pertanyaan_id = $rows['pertanyaan_id'];
                            $saveDetail->pertanyaan_detail_title = $pertanyaan->title;
                            $saveDetail->pertanyaan_detail_pilihan = $pertanyaan->pilihan;
                            $saveDetail->pertanyaan_detail_bobot = $pertanyaan->bobot;
                            $saveDetail->pertanyaan_bobot_id = $bobotQ['id'];
                            $saveDetail->pertanyaan_bobot_kondisi = $bobotQ['kondisi'];
                            $saveDetail->pertanyaan_bobot_label = $bobotQ['label'];
                            $saveDetail->pertanyaan_bobot_nilai = $bobotQ['nilai'];
                            $saveDetail->pertanyaan_bobot = $bobotQ['bobot'];
                            $saveDetail->pertanyaan_rating = $bobotQ['rating'];
                            $saveDetail->pertanyaan_rating_color = $bobotQ['color'];
                            $saveDetail->created_at = date('Y-m-d H:i:s');
                            $saveDetail->created_by = $request->user_id;

                            $saveDetail->save();

                            $filebobot = KuisBobotFile::whereNull('deleted_by')
                                ->where('pertanyaan_bobot_id', $bobotQ['id'])
                                ->get();

                            if ($filebobot->isNotEmpty()) {
                                foreach ($filebobot as $key => $row) {
                                    $saveBobotFile = new KuisResultBobotFile;
                                    $saveBobotFile->result_id = $simpan->id;
                                    $saveBobotFile->pertanyaan_bobot_id = $bobotQ['id'];
                                    $saveBobotFile->name = $row->name;
                                    $saveBobotFile->file = $row->file;
                                    $saveBobotFile->created_at = date('Y-m-d H:i:s');
                                    $saveBobotFile->created_by = $request->user_id;

                                    $saveBobotFile->save();
                                }
                            }
                        }

                        if ($rows['tipe'] == 'radio' || $rows['tipe'] == 'dropdown') {
                            $findQ = KuisDetail::where('header_id', $row['header_id'])->first();
                            //print_r ($findQ);
                            if ($findQ->bobot == '1') {
                                $value = $rows['value'];
                                $findB = KuisBobot::where('header_id', $row['header_id'])->where('id', $value)->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])->first()->toArray();
                                //print_r ($findB);

                                $bobotQ = [
                                    'id' => $findB['id'],
                                    'kondisi' => $findB['kondisi'],
                                    'label' => $findB['label'],
                                    'nilai' => $findB['nilai'],
                                    'bobot' => $findB['bobot'],
                                    'value' => $rows['value'],
                                    'rating' => $findB['rating'],
                                    'color' => $findB['rating_color']
                                ];
                            } else {
                                $bobotQ = [
                                    'id' => 0,
                                    'kondisi' => '',
                                    'label' => '',
                                    'nilai' => '',
                                    'bobot' => '',
                                    'value' => '',
                                    'rating' => '',
                                    'color' => ''
                                ];
                            }

                            $pertanyaan = KuisDetail::where('header_id', $row['header_id'])->first();

                            $saveDetail = new KuisResultDetail;
                            $saveDetail->result_id = $simpan->id;
                            $saveDetail->value = $bobotQ['value'];
                            $saveDetail->header_id = $rows['header_id'];
                            $saveDetail->pertanyaan_id = $rows['pertanyaan_id'];
                            $saveDetail->pertanyaan_detail_title = $pertanyaan->title;
                            $saveDetail->pertanyaan_detail_pilihan = $pertanyaan->pilihan;
                            $saveDetail->pertanyaan_detail_bobot = $pertanyaan->bobot;
                            $saveDetail->pertanyaan_bobot_id = $bobotQ['id'];
                            $saveDetail->pertanyaan_bobot_kondisi = $bobotQ['kondisi'];
                            $saveDetail->pertanyaan_bobot_label = $bobotQ['label'];
                            $saveDetail->pertanyaan_bobot_nilai = $bobotQ['nilai'];
                            $saveDetail->pertanyaan_bobot = $bobotQ['bobot'];
                            $saveDetail->pertanyaan_rating = $bobotQ['rating'];
                            $saveDetail->pertanyaan_rating_color = $bobotQ['color'];
                            $saveDetail->created_at = date('Y-m-d H:i:s');
                            $saveDetail->created_by = $request->user_id;

                            $saveDetail->save();

                            $filebobot = KuisBobotFile::whereNull('deleted_by')
                                ->where('pertanyaan_bobot_id', $bobotQ['id'])
                                ->get();

                            if ($filebobot->isNotEmpty()) {
                                foreach ($filebobot as $key => $row) {
                                    $saveBobotFile = new KuisResultBobotFile;
                                    $saveBobotFile->result_id = $simpan->id;
                                    $saveBobotFile->pertanyaan_bobot_id = $bobotQ['id'];
                                    $saveBobotFile->name = $row->name;
                                    $saveBobotFile->file = $row->file;
                                    $saveBobotFile->created_at = date('Y-m-d H:i:s');
                                    $saveBobotFile->created_by = $request->user_id;

                                    $saveBobotFile->save();
                                }
                            }
                        }
                    }
                }
            }

        }

        $count = KuisResultDetail::select(['pertanyaan_bobot'])->where('result_id', $simpan->id)->groupBy('header_id')->get();
        $total = 0;
        foreach ($count as $row) {
            $total = $total + $row->pertanyaan_bobot;
        }


        $findB = KuisSummary::whereNull('deleted_by')->where('kuis_id', $simpan->kuis_id)->get()->toArray();
        //print_r ($findB);

        $compare = [];
        foreach ($findB as $k => $r) {
            if ($findB[$k]['kondisi'] == '1') { $nilais = $r['nilai'] - 1; $compare[$findB[$k]['kondisi'].'|'.$findB[$k]['nilai']] = '0 - ' . $nilais; }
            if ($findB[$k]['kondisi'] == '2') { $compare[$findB[$k]['kondisi'].'|'.$findB[$k]['nilai']] = $r['nilai'] . ' - ' . $r['nilai']; }
            if ($findB[$k]['kondisi'] == '3') { $compare[$findB[$k]['kondisi'].'|'.$findB[$k]['nilai']] = $r['nilai']; }
            if ($findB[$k]['kondisi'] == '4') { $nilais = $r['nilai'] + 1; $compare[$findB[$k]['kondisi'].'|'.$findB[$k]['nilai']] = $nilais . ' - 10000000'; }
        }
        //echo $total;
        //print_r ($compare);

        $bobotQ = ['id' => 0, 'rating' => '', 'color' => ''];
        foreach ($compare as $z => $y) {
            $exp = explode('-', $y);
            $konds = explode('|', $z);
            //if ( in_array($value, range($exp[0], $exp[1])) ) {
            if (is_numeric($total) && $total == trim($exp[0]) && $total == trim($exp[1])) {
                //$key = array_search($total, array_column($findB, 'nilai'));

                $search = Helper::multi_array_search($findB, ['nilai' => $total, 'kondisi' => $konds[0]]);

                $bobotQ = [
                    'id' => $findB[$search[0]]['id'],
                    'label' => $findB[$search[0]]['label'],
                    'deskripsi' => $findB[$search[0]]['deskripsi'],
                    'rating' => $findB[$search[0]]['rating'],
                    'color' => $findB[$search[0]]['rating_color']
                ];
            } else if (is_numeric($total) && $total > trim($exp[0]) && $total <= trim($exp[1])) {
                $key = array_search($konds[0], array_column($findB, 'kondisi'));

                $bobotQ = [
                    'id' => $findB[$key]['id'],
                    'label' => $findB[$key]['label'],
                    'deskripsi' => $findB[$key]['deskripsi'],
                    'rating' => $findB[$key]['rating'],
                    'color' => $findB[$key]['rating_color']
                ];
            } else if (is_numeric($total) && $total >= trim($exp[0]) && $total <= trim($exp[1])) {
                $key = array_search($konds[0], array_column($findB, 'kondisi'));

                $bobotQ = [
                    'id' => $findB[$key]['id'],
                    'label' => $findB[$key]['label'],
                    'deskripsi' => $findB[$key]['deskripsi'],
                    'rating' => $findB[$key]['rating'],
                    'color' => $findB[$key]['rating_color']
                ];
            }
        }

        $checkresponder = KuisResult::where('id', $simpan->id)->select(['member_id'])->first();

        if ($checkresponder) {
            $responderId = $checkresponder->member_id;
            $dataresponder = MemberDelegate::where('member_id', $checkresponder->member_id)->first();

            if ($dataresponder) {
                $responderId = $dataresponder->user_id;
            } else {
                $responderId = null;
            }
        } else {
            $responderId = null;
        }



        $update = KuisResult::where('id', $simpan->id)->update([
            'member_kuis_nilai' => $total,
            'summary_id' => $bobotQ['id'],
            'responder_id' => $responderId,
            'label' => $bobotQ['label'],
            'deskripsi' => $bobotQ['deskripsi'],
            'rating' => $bobotQ['rating'],
            'rating_color' => $bobotQ['color'],
            'status' => 1
        ]);

        // Generate PDF
        $filename = '';
        $select = KuisResult::leftJoin('members', function($join) {
            $join->on('members.id', 'kuisioner_result.member_id');
        })
        ->leftJoin('adms_provinsi', 'adms_provinsi.provinsi_kode', '=', 'members.provinsi_id')
        ->where('kuisioner_result.id', $simpan->id)
        ->select(['members.name', 'kuisioner_result.*', 'adms_provinsi.nama as nama_provinsi'])
        ->first();

        $summary = KuisSummary::whereNull('deleted_by')->where('id', $select->summary_id)->select(['label', 'deskripsi', 'template'])->first();
        // return $select;

        //ammad-2021-06-25 1819
        // return hasil
        $base_url = env('BASE_URL_PDF');
        $base_url_kuis = env('BASE_URL_KUIS');
        $kuis = KuisResult::where('id', $simpan->id)
            ->select([
                'kuis_id',
                'id as result_id',
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
            ->first()
            ->toArray();

        $tanggal = explode(' ', $kuis['created_at']);
        $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
        $kuis['created_at'] = $tanggal; 

        $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
            $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
        })
        ->where('kuisioner_result_detail.result_id', $simpan->id)
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

            $file = KuisResultBobotFile::where('result_id', $simpan->id)->where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select([
                'name', 
                DB::raw("concat('{$base_url_kuis}', file) AS file")
            ])->get()->toArray();
            $result[$key]['file'] = $file;
        }
        //ammad-2021-06-25 1819

        if (!empty($summary->template)) {
            $title = $select->kuis_title;
            $template = $summary->template;
            
            //ammad-2021-06-21 0635
            $logo_elsimil_height_str = $logo_elsimil = '';
            $logo_elsimil_position = stripos($template, '[logo_elsimil_');
            $logo_elsimil_key = substr($template, $logo_elsimil_position, 25);
            $logo_elsimil_arr = explode('_', $logo_elsimil_key);
            
            if($logo_elsimil_arr[0] == '[logo' && $logo_elsimil_arr[1] == 'elsimil'){
                $logo_elsimil_height = explode('x',$logo_elsimil_arr[2])[0];
                $logo_elsimil_width = (int)explode('x',$logo_elsimil_arr[2])[1];
                $logo_elsimil_height_str = $logo_elsimil_height.'x'.$logo_elsimil_width;
                
                $logo_elsimil = '<img src="assets/media/logos/logo-new.png" style="width:'.$logo_elsimil_height.'; height:auto">';
            }
			
			//rizki-8/2/21
			
			$logo_bkkbn_height_str = $logo_bkkbn = '';
            $logo_bkkbn_position = stripos($template, '[logo_bkkbn_');
            $logo_bkkbn_key = substr($template, $logo_bkkbn_position, 20);
            $logo_bkkbn_arr = explode('_', $logo_bkkbn_key);
            
            if($logo_bkkbn_arr[0] == '[logo' && $logo_bkkbn_arr[1] == 'bkkbn'){
                $logo_bkkbn_height = explode('x',$logo_bkkbn_arr[2])[0];
                $logo_bkkbn_width = (int)explode('x',$logo_bkkbn_arr[2])[1];
                $logo_bkkbn_height_str = $logo_bkkbn_height.'x'.$logo_bkkbn_width;
                
                $logo_bkkbn = '<img src="assets/media/logos/logo.png" style="width:'.$logo_bkkbn_height.'; height:auto">';
            }
			

			
            //get result kuis
            $rekap_hasil = '<table style="border-collapse: collapse; width: 100%;" border="1">
			<thead>
			<tr>
			<td align="center"><strong>INDIKATOR</strong></td>
			<td align="center"><strong>HASIL</strong></td>
			<td align="center"><strong>KETERANGAN</strong></td>
			</tr>
			</thead>
			
            <tbody>';
            foreach ($result as $row) {
                $rekap_hasil .= '<tr>';
                $rekap_hasil .= '<td style="width: 33.3333%;">'.$row['caption'].'</td>';
                $rekap_hasil .= '<td style="width: 33.3333%;">'.$row['value'].'</td>';
                $rekap_hasil .= '<td style="width: 33.3333%;">'.$row['label'].'</td>';
                $rekap_hasil .= '</tr>';
            }
            $rekap_hasil .= '</tbody></table>';

            //replace kode template
            $content = str_replace(
                ['[logo_elsimil_'.$logo_elsimil_height_str.']', '[logo_bkkbn_'.$logo_bkkbn_height_str.']', '[provinsi_member]','[nama_member]','[hasil_kuesioner]', '[id_kuesioner]', 
                    '[deskripsi_hasil_kuesioner]', '[tanggal_widget_kesehatan]', '[nama_fasilitas_kesehatan]', '[rekapan_hasil_kuesioner]'], 
                [$logo_elsimil, $logo_bkkbn,$select->nama_provinsi, $select->name, $select->label, $select->kuis_code, 
                    $select->deskripsi, $mapping_widget_question['tanggal']['value'] ?? '', $mapping_widget_question['autocomplete']['value'] ?? '',
                    $rekap_hasil
                ], 
                $template);         
                
            //ammad-2021-06-21 0635

            $data = [
                'title' => $title,
                'content' => $content
            ];
        
            $filename = date('YmdHis') . ' - ' . $select->kuis_code . ' - ' . $title . ' - ' . $select->name . '.pdf';
            $oriPath = public_path('uploads/pdf');
            $pdf = PDF::loadView('pdf.generate', $data)->save(''.$oriPath.'/'.$filename);
        }

        $update = KuisResult::where('id', $simpan->id)->update([
            'filename' => $filename
        ]);
        $kuis['url'] = $base_url.$filename;

        //print_r ($detail);
        $data = [
            'header' => $kuis,
            'detail' => $result
        ];

        // START: add to logbook
        $kuis_result_header = array(
            'label' => $kuis['label'],
            'rating_color' => $kuis['rating_color']
        );
        $logbook_history = new LogbookHistory();
        $logbook_history->addToLogbook(0, $request->user_id, 2, json_encode($kuis_result_header));
        // END: add to logbook

        return response()->json([
            'error' => false,
            'code' => 200,
            'message' => 'Success',
            'data' => $data
        ], 200);

    }

    public function generate(Request $request) {
        $base_url = env('BASE_URL_PDF');
        $kuis = KuisResult::where('id', $request->id)
            ->select([
                'kuis_code', 
                'created_at', 
                'kuis_max_nilai', 
                'member_kuis_nilai', 
                DB::raw("concat('{$base_url}', filename) AS url"),
                'filename'
            ])
            ->first()
            ->toArray();

        $tanggal = explode(' ', $kuis['created_at']);
        $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
        $kuis['created_at'] = $tanggal; 

        $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
            $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
        })
        ->where('kuisioner_result_detail.result_id', $request->id)
        ->select([
            'kuisioner_result_header.pertanyaan_header_caption',
            'kuisioner_result_header.pertanyaan_header_jenis',
            'kuisioner_result_detail.pertanyaan_bobot_label',
            'kuisioner_result_detail.value',
            'kuisioner_result_detail.formula_value',
            'kuisioner_result_detail.pertanyaan_bobot_id',
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
            } else {
                $result[$key]['value'] = $row['value'];
            }

            $file = KuisBobotFile::where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select(['name', 'file'])->get()->toArray();
            $result[$key]['file'] = $file;
        }

        $data = [
            'header' => $kuis,
            'detail' => $result
        ];

        return response()->json([
            'error' => false,
            'code' => 200,
            'message' => 'Success',
            'data' => $data
        ], 200);

    }


}
