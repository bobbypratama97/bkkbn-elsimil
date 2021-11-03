<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Member;
use App\Kuis;
use App\KuisResult;
use App\KuisResultDetail;
use App\KuisBobotFile;
use App\KuisResultComment;
use App\MemberCouple;


class KuaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('kua.index');
    }

    public function result(Request $request) {

        $cekKuis = KuisResult::where('kuis_code', $request->kode)->get();
        //echo '<pre>';

        $result = [];
        $output = [];
        $output['count'] = 0;
        if ($cekKuis->isNotEmpty()) {
            //print_r ($cekKuis);
            $res = $cekKuis;

            $cekMember = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            }) 
            ->leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            }) 
            ->leftJoin('adms_kelurahan', function($join) {
                $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
            })
            ->leftJoin('member_delegate', function($join) {
                $join->on('member_delegate.member_id', '=', 'members.id');
            })
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'member_delegate.user_id');
            })
            ->where('members.id', $cekKuis->member_id)
            ->select([
                'members.*',
                'adms_provinsi.nama as provinsi',
                'adms_kabupaten.nama as kabupaten',
                'adms_kecamatan.nama as kecamatan',
                'adms_kelurahan.nama as kelurahan',
                'users.id as petugas_id'
            ])
            ->first();

            $output['member'] = $cekMember;

        } else {
            //$cekKtp = Helper::dcNik($request->kode);
            $profile_code = $request->kode;

            $cekMember = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            }) 
            ->leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            }) 
            ->leftJoin('adms_kelurahan', function($join) {
                $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
            })
            ->leftJoin('member_delegate', function($join) {
                $join->on('member_delegate.member_id', '=', 'members.id');
            })
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'member_delegate.user_id');
            })
            ->where('profile_code', $profile_code)
            ->select([
                'members.*',
                'adms_provinsi.nama as provinsi',
                'adms_kabupaten.nama as kabupaten',
                'adms_kecamatan.nama as kecamatan',
                'adms_kelurahan.nama as kelurahan',
                'users.id as petugas_id'
            ])
            ->first();

            if ($cekMember) {
                $output['member'] = $cekMember;
                $cekKuis = KuisResult::where('member_id', $cekMember->id)->where('status', 1)->orderBy('id', 'DESC')->get();
                //print_r ($cekKuis);
                $res = $cekKuis;

                $pasangan = MemberCouple::where('member_id', $cekMember->id)->where('status', 'APM200')->get();
                $output['couple'] = count($pasangan);

            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Perhatian', 
                        'keterangan' => 'Nomor KTP atau kode kuis catin tidak ditemukan'
                    ]);
            }
        }

        //echo '<pre>'; //print_r ($res);

        if ($res->isNotEmpty()) {
            $output['count'] = 1;
            $base_url = env('BASE_URL_PDF');

            foreach ($res as $kkuis => $vkuis) {
                $kuis = KuisResult::where('id', $vkuis->id)
                    ->select([
                        'id',
                        'member_id',
                        'kuis_id',
                        'kuis_code', 
                        'kuis_title',
                        'created_at', 
                        'kuis_max_nilai', 
                        'member_kuis_nilai', 
                        'label',
                        'rating_color',
                        DB::raw("concat('{$base_url}', filename) AS url")
                    ])
                    ->first();

                $deskripsi = Kuis::where('id', $kuis->kuis_id)->first();

                $tanggal = explode(' ', $kuis->created_at);
                $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
                $kuis->tanggal = $tanggal; 

                $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                    $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                    $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
                })
                ->where('kuisioner_result_detail.result_id', $vkuis->id)
                ->select([
                    'kuisioner_result_header.pertanyaan_header_caption',
                    'kuisioner_result_header.pertanyaan_header_jenis',
                    'kuisioner_result_detail.pertanyaan_detail_title',
                    'kuisioner_result_detail.pertanyaan_detail_pilihan',
                    'kuisioner_result_detail.pertanyaan_bobot_label',
                    'kuisioner_result_detail.pertanyaan_rating',
                    'kuisioner_result_detail.pertanyaan_rating_color',
                    'kuisioner_result_detail.value',
                    'kuisioner_result_detail.formula_value',
                    'kuisioner_result_detail.pertanyaan_bobot_id',
                    'kuisioner_result_detail.pertanyaan_bobot'
                ])
                //->groupBy('kuisioner_result_detail.header_id')
                ->get()
                ->toArray();

                $arr = [];
                foreach ($detail as $key => $row) {
                    $arr[$row['pertanyaan_header_caption']][] = $row;
                }
                //echo '<pre>'; 
                //print_r ($arr);
                
                $out = [];
                foreach ($arr as $keys => $rows) {
                    if (isset($rows) && !empty($rows)) {
                        $out[$keys]['header'] = [
                            'jenis' => $rows[0]['pertanyaan_header_jenis'],
                            'tipe' => $rows[0]['pertanyaan_detail_pilihan'],
                            'formula_value' => $rows[0]['formula_value'],
                            'rating' => $rows[0]['pertanyaan_rating'],
                            'label' => $rows[0]['pertanyaan_bobot_label'],
                            'bobot' => $rows[0]['pertanyaan_bobot'],
                            'color' => (!empty($rows[0]['pertanyaan_rating_color'])) ? $rows[0]['pertanyaan_rating_color'] : '#f3f6f9'
                        ];
                        $out[$keys]['child'] = $rows;
                    }
                }
                //print_r ($out);

                //die;

                $result = [];
                foreach ($detail as $key => $row) {
                    $result[$key]['caption'] = $row['pertanyaan_header_caption'];
                    $result[$key]['title'] = $row['pertanyaan_detail_title'];
                    $result[$key]['label'] = $row['pertanyaan_bobot_label'];
                    $result[$key]['bobot'] = $row['pertanyaan_bobot'];
                    $result[$key]['formula'] = $row['formula_value'];
                    $result[$key]['value'] = $row['value'];

                    $file = KuisBobotFile::where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select(['name', 'file'])->get()->toArray();
                    $result[$key]['file'] = $file;
                }

                //$mbr = Member::where('id', $vkuis->member_id)->first();
                $title = Kuis::where('id', $vkuis->kuis_id)->select(['title'])->first();
                $komentar = KuisResultComment::where('result_id', $vkuis->id)->first();

                $output['result'][$kkuis] = [
                    'kuis' => $title,
                    'header' => $kuis,
                    'result' => $result,
                    'out' => $out,
                    'komentar' => $komentar
                ];

            }
        } else {
            $output['count'] = 0;
        }

        //print_r ($output);

        return view('kua.result', compact('output'));

    }

    public function couples($id) {


        $result = [];
        $output = [];

        $kode = $id;
        $profile_code = $kode;
        //$output['mine'] = $id;

        //echo '<pre>';

        $couple = MemberCouple::where('member_id', $id)->where('status', 'APM200')->get();
        if ($couple->isNotEmpty()) {
            $output['mine'] = $couple[0]->couple_id;
        }

        //print_r ($couple); die;

        if ($couple->isNotEmpty()) {
            foreach ($couple as $kk => $vv) {
                $cekMember = Member::leftJoin('adms_provinsi', function($join) {
                    $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
                })
                ->leftJoin('adms_kabupaten', function($join) {
                    $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
                }) 
                ->leftJoin('adms_kecamatan', function($join) {
                    $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
                }) 
                ->leftJoin('adms_kelurahan', function($join) {
                    $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
                })
                ->leftJoin('member_delegate', function($join) {
                    $join->on('member_delegate.member_id', '=', 'members.id');
                })
                ->leftJoin('users', function($join) {
                    $join->on('users.id', '=', 'member_delegate.user_id');
                })
                ->where('members.id', $vv->couple_id)
                ->select([
                    'members.*',
                    'adms_provinsi.nama as provinsi',
                    'adms_kabupaten.nama as kabupaten',
                    'adms_kecamatan.nama as kecamatan',
                    'adms_kelurahan.nama as kelurahan',
                    'users.id as petugas_id'
                ])
                ->first();

                if ($cekMember) {
                    $output['res'][$kk]['member'] = $cekMember;
                    $cekKuis = KuisResult::where('member_id', $cekMember->id)->where('status', 1)->orderBy('id', 'DESC')->get();
                    //print_r ($cekKuis);
                    $res = $cekKuis;
                    $output['res'][$kk]['count'] = 1;
                } else {
                    $output['res'][$kk]['count'] = 0;
                }

                if (isset($res) && $res->isNotEmpty()) {
                    $output['res'][$kk]['count'] = 1;
                    $base_url = env('BASE_URL_PDF');

                    foreach ($res as $kkuis => $vkuis) {
                        $kuis = KuisResult::where('id', $vkuis->id)
                            ->select([
                                'id',
                                'member_id',
                                'kuis_id',
                                'kuis_code', 
                                'kuis_title',
                                'created_at', 
                                'kuis_max_nilai', 
                                'member_kuis_nilai', 
                                'label',
                                'rating_color',
                                DB::raw("concat('{$base_url}', filename) AS url")
                            ])
                            ->first();

                        $deskripsi = Kuis::where('id', $kuis->kuis_id)->first();

                        $tanggal = explode(' ', $kuis->created_at);
                        $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
                        $kuis->tanggal = $tanggal; 

                        $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                            $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
                        })
                        ->where('kuisioner_result_detail.result_id', $vkuis->id)
                        ->select([
                            'kuisioner_result_header.pertanyaan_header_caption',
                            'kuisioner_result_header.pertanyaan_header_jenis',
                            'kuisioner_result_detail.pertanyaan_detail_title',
                            'kuisioner_result_detail.pertanyaan_detail_pilihan',
                            'kuisioner_result_detail.pertanyaan_bobot_label',
                            'kuisioner_result_detail.pertanyaan_rating',
                            'kuisioner_result_detail.pertanyaan_rating_color',
                            'kuisioner_result_detail.value',
                            'kuisioner_result_detail.formula_value',
                            'kuisioner_result_detail.pertanyaan_bobot_id',
                            'kuisioner_result_detail.pertanyaan_bobot'
                        ])
                        //->groupBy('kuisioner_result_detail.header_id')
                        ->get()
                        ->toArray();

                        $arr = [];
                        foreach ($detail as $key => $row) {
                            $arr[$row['pertanyaan_header_caption']][] = $row;
                        }
                        //echo '<pre>'; 
                        //print_r ($arr);
                        
                        $out = [];
                        foreach ($arr as $keys => $rows) {
                            if (isset($rows) && !empty($rows)) {
                                $out[$keys]['header'] = [
                                    'jenis' => $rows[0]['pertanyaan_header_jenis'],
                                    'tipe' => $rows[0]['pertanyaan_detail_pilihan'],
                                    'formula_value' => $rows[0]['formula_value'],
                                    'rating' => $rows[0]['pertanyaan_rating'],
                                    'label' => $rows[0]['pertanyaan_bobot_label'],
                                    'bobot' => $rows[0]['pertanyaan_bobot'],
                                    'color' => (!empty($rows[0]['pertanyaan_rating_color'])) ? $rows[0]['pertanyaan_rating_color'] : '#f3f6f9'
                                ];
                                $out[$keys]['child'] = $rows;
                            }
                        }
                        //print_r ($out);

                        //die;

                        $result = [];
                        foreach ($detail as $key => $row) {
                            $result[$key]['caption'] = $row['pertanyaan_header_caption'];
                            $result[$key]['title'] = $row['pertanyaan_detail_title'];
                            $result[$key]['label'] = $row['pertanyaan_bobot_label'];
                            $result[$key]['bobot'] = $row['pertanyaan_bobot'];
                            $result[$key]['formula'] = $row['formula_value'];
                            $result[$key]['value'] = $row['value'];

                            $file = KuisBobotFile::where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select(['name', 'file'])->get()->toArray();
                            $result[$key]['file'] = $file;
                        }

                        //$mbr = Member::where('id', $vkuis->member_id)->first();
                        $title = Kuis::where('id', $vkuis->kuis_id)->select(['title'])->first();
                        $komentar = KuisResultComment::where('result_id', $vkuis->id)->first();

                        $output['res'][$kk]['result'][$kkuis] = [
                            'kuis' => $title,
                            'header' => $kuis,
                            'result' => $result,
                            'out' => $out,
                            'komentar' => $komentar
                        ];

                    }
                } else {
                    $output[$kk]['count'] = 0;
                }
            }
        }


        //print_r ($output);
        //die;

        return view('kua.couple', compact('output'));

    }

    public function edit($id) {
    }

    public function update(Request $request, $id) {
    }

}
