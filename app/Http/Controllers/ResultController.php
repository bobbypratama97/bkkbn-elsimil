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

use OneSignal;

use Helper;

use App\Member;
use App\Kuis;
use App\KuisResult;
use App\KuisResultHeader;
use App\KuisResultDetail;
use App\KuisBobotFile;
use App\KuisResultComment;

use App\NotificationLog;

//use App\

class ResultController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $result = KuisResult::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'kuisioner_result.member_id');
        })
        ->leftJoin('kuisioner_summary', function($join) {
            $join->on('kuisioner_summary.id', '=', 'kuisioner_result.summary_id');
        })
        ->leftJoin('adms_provinsi', function($join) {
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
        ->leftJoin('kuisioner_result_comment', function($join) {
            $join->on('kuisioner_result_comment.result_id', '=', 'kuisioner_result.id');
        })
        ->orderBy('kuisioner_result.id', 'DESC')
        ->select([
            'kuisioner_result.*',
            'members.name as nama',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'kuisioner_summary.label as kuis_label',
            'kuisioner_result_comment.komentar'
        ])
        ->get();

        return view('result.index', ['result' => $result]);
    }

    public function create() {
    }

    public function store(Request $request) {
    }

    public function edit($id) {
        $base_url = env('BASE_URL_PDF');
        $kuis = KuisResult::where('id', $id)
            ->select([
                'id',
                'kuis_id',
                'kuis_code', 
                'kuis_title',
                'created_at', 
                'kuis_max_nilai', 
                'member_kuis_nilai', 
                DB::raw("concat('{$base_url}', filename) AS url")
            ])
            ->first();

        $deskripsi = Kuis::where('id', $kuis->kuis_id)->first();

        $member = Member::leftJoin('adms_provinsi', function($join) {
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
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan'
        ])
        ->first();

        $tanggal = explode(' ', $kuis->created_at);
        $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
        $kuis->tanggal = $tanggal; 

        $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
            $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
        })
        ->where('kuisioner_result_detail.result_id', $id)
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

        $komentar = KuisResultComment::where('result_id', $id)->first();

        return view('result.edit', compact('kuis', 'member', 'deskripsi', 'result', 'out', 'komentar'));
    }

    public function update(Request $request, $id) {
        $check = KuisResultComment::where('result_id', $id)->first();

        if ($check) {
            $update = KuisResultComment::where('result_id', $request->id)->update([
                'komentar' => $request->komentar,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);
        } else {
            $save = new KuisResultComment;
            $save->result_id = $id;
            $save->komentar = $request->komentar;
            $save->created_at = date('Y-m-d H:i:s');
            $save->created_by = Auth::id();

            $save->save();

            $kuis = KuisResult::where('id', $id)->select(['member_id'])->first();

            $insert = new NotificationLog;
            $insert->member_id = $kuis->member_id;
            $insert->jenis = 'Ulasan';
            $insert->content = 'Hasil kuesioner kamu ditanggapi oleh petugas KB';
            $insert->created_at = date('Y-m-d H:i:s');
            $insert->created_by = Auth::id();

            $insert->save();
        }

        $get = KuisResult::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'kuisioner_result.member_id');
        })
        ->leftJoin('member_onesignal', function($join) {
            $join->on('member_onesignal.member_id', '=', 'members.id');
        })
        ->where('kuisioner_result.id', $id)->select('member_onesignal.player_id')->first();

        if (!empty($get)) {
            $parameters = [
                'include_player_ids' => [$get->player_id],
                'headings' => [
                    'en' => 'Hasil kuesioner'
                ],
                'contents' => [
                    'en' => 'Hai, kuesioner kamu sudah dapat tanggapan lho. Yuk lihat yuk'
                ],
                //'big_picture' => 'https://madiunkota.go.id/wp-content/uploads/2018/07/stunting-1080x675.jpg',
                //'ios_attachments' => [
                //    "id" => "https://madiunkota.go.id/wp-content/uploads/2018/07/stunting-1080x675.jpg"
                //],
                'ios_badgeType'  => 'Increase',
                'ios_badgeCount' => 1,
                //'included_segments' => array('All')
            ];

            $send = OneSignal::sendNotificationCustom($parameters);
        }



        $msg = 'Ulasan penilaian kuesioner berhasil.';
        return redirect()->route('admin.result.index')->with('success', $msg);
    }

    public function show($id) {
        $base_url = env('BASE_URL_PDF');
        $kuis = KuisResult::where('id', $id)
            ->select([
                'kuis_id',
                'kuis_code', 
                'kuis_title',
                'created_at', 
                'kuis_max_nilai', 
                'member_kuis_nilai', 
                DB::raw("concat('{$base_url}', filename) AS url")
            ])
            ->first();

        $deskripsi = Kuis::where('id', $kuis->kuis_id)->first();

        $member = Member::leftJoin('adms_provinsi', function($join) {
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
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan'
        ])
        ->first();

        $tanggal = explode(' ', $kuis->created_at);
        $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
        $kuis->tanggal = $tanggal; 

        $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
            $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
        })
        ->where('kuisioner_result_detail.result_id', $id)
        ->select([
            'kuisioner_result_header.pertanyaan_header_caption',
            'kuisioner_result_header.pertanyaan_header_jenis',
            'kuisioner_result_detail.pertanyaan_bobot_label',
            'kuisioner_result_detail.value',
            'kuisioner_result_detail.formula_value',
            'kuisioner_result_detail.pertanyaan_bobot_id',
            'kuisioner_result_detail.pertanyaan_bobot'
        ])
        ->groupBy('kuisioner_result_detail.header_id')
        ->get()
        ->toArray();

        $result = [];
        foreach ($detail as $key => $row) {
            $result[$key]['caption'] = $row['pertanyaan_header_caption'];
            $result[$key]['label'] = $row['pertanyaan_bobot_label'];
            $result[$key]['bobot'] = $row['pertanyaan_bobot'];

            if ($row['pertanyaan_header_jenis'] == 'combine') {
                $result[$key]['value'] = $row['formula_value'];
            } else {
                $result[$key]['value'] = $row['value'];
            }

            $file = KuisBobotFile::where('pertanyaan_bobot_id', $row['pertanyaan_bobot_id'])->select(['name', 'file'])->get()->toArray();
            $result[$key]['file'] = $file;
        }

        //print_r ($kuis); die;
        $data = [
            'header' => $kuis,
            'detail' => $result
        ];

        return view('result.show', compact('kuis', 'member', 'deskripsi', 'result'));

    }

    public function delete(Request $request) {
    }
}
