<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KuisExport;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use OneSignal;

use Helper;

use App\Member;
use App\MemberCouple;
use App\Kuis;
use App\KuisResult;
use App\KuisResultHeader;
use App\KuisResultDetail;
use App\KuisBobotFile;
use App\KuisResultComment;
use App\KuisResultCommentLog;

use App\NotificationLog;

use App\UserRole;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Kelurahan;

class RepkuisController extends Controller
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
        $this->authorize('access', [\App\Repkuis::class, Auth::user()->role, 'index']);

        $user = Auth::user();
        $roles = UserRole::where('user_id', $user->id)->first();

        if ($roles->role_id == '1') {
            $kelurahan = [];
            $kecamatan = [];
            $kabupaten = [];
            $provinsi = Provinsi::whereNull('deleted_by')->get();
        } else if($roles->role_id == '2') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->get();
            $kabupaten = Kabupaten::where('provinsi_kode', $user->provinsi_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = [];//Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
            $kelurahan = [];//Kelurahan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
        } else if($roles->role_id == '3') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = [];//Kelurahan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
        } else if($roles->role_id == '4') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
        } else if($roles->role_id == '5') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kelurahan_kode', $user->kelurahan_id)->orderBy('nama')->get();
        }

        $kuis = Kuis::whereNull('deleted_by')->orderBy('id', 'DESC')->get();
        $gender = Helper::statusGender();

        return view('repkuis.index', compact('kuis', 'gender', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'roles'));
    }

    public function search(Request $request) {
        $res = Kuis::select([
            'pertanyaan_header.id as header_id', 
            'pertanyaan_header.caption', 
            'pertanyaan_bobot.id as bobot_id',
            'pertanyaan_bobot.label', 
            'pertanyaan_bobot.rating', 
            'pertanyaan_bobot.rating_color' 
        ])
        ->leftJoin('pertanyaan_header', function($join) {
            $join->on('pertanyaan_header.kuis_id', '=', 'kuisioner.id');
        })
        ->leftJoin('pertanyaan_detail', function($join) {
            $join->on('pertanyaan_detail.header_id', '=', 'pertanyaan_header.id');
        })
        ->leftJoin('pertanyaan_bobot', function($join) {
            $join->on('pertanyaan_bobot.header_id', '=', 'pertanyaan_header.id');
        })
        ->where('kuisioner.id', $request->kuesioner)
        ->where('pertanyaan_detail.bobot', 1)
        ->whereNull('pertanyaan_header.deleted_by')
        ->whereNull('pertanyaan_detail.deleted_by')
        ->whereNull('pertanyaan_bobot.deleted_by')
        ->groupBy(['pertanyaan_header.id', 'pertanyaan_bobot.id'])
        ->orderBy('pertanyaan_header.position')
        ->get();

        //print_r ($res);

        $total = [0];
        $final = [];
        $whereSummary = '';
        if ($res->isNotEmpty()) {
            $res = $res->toArray();

            foreach ($res as $key => $row) {
                $result = KuisResultDetail::leftJoin('kuisioner_result', function($join) {
                    $join->on('kuisioner_result.id', '=', 'kuisioner_result_detail.result_id');
                })
                ->leftJoin('members', function($join) {
                    $join->on('members.id', '=', 'kuisioner_result.member_id');
                })
                ->where('kuisioner_result.status', 1);

                if (!empty($request->tanggal)) {
                    $exp = explode(' - ', $request->tanggal);

                    $start = explode('/', $exp[0]);
                    $start = $start[2] . '-' . $start[1] . '-' . $start[0];
                    $end = explode('/', $exp[1]);
                    $end = $end[2] . '-' . $end[1] . '-' . $end[0];

                    // $result->whereBetween('kuisioner_result.created_at', [$start, $end]);
                    $result->whereBetween(DB::raw('date(kuisioner_result.created_at)'), [$start, $end]);
                    $whereSummary .= " AND (date(kuisioner_result.created_at) BETWEEN '".$start."' AND '".$end."')";
                }

                if (!empty($request->provinsi)) {
                    $result->where('members.provinsi_id', $request->provinsi);
                    $whereSummary .= " AND members.provinsi_id = ".$request->provinsi;
                }

                if (!empty($request->kabupaten)) {
                    $result->where('members.kabupaten_id', $request->kabupaten);
                }

                if (!empty($request->kecamatan)) {
                    $result->where('members.kecamatan_id', $request->kecamatan);
                }

                if (!empty($request->kelurahan)) {
                    $result->where('members.kelurahan_id', $request->kelurahan);
                }

                if (!empty($request->nik)) {
                    $cekKtp = Helper::dcNik($request->nik);
                    $result->where('members.nik', 'LIKE', '%' . $cekKtp . '%');
                }

                if (!empty($request->nama)) {
                    $result->where('members.name', $request->nama);
                }

                if (!empty($request->gender)) {
                    $result->where('kuisioner_result.kuis_gender', $request->gender);
                }

                $result = $result->where('kuisioner_result_detail.pertanyaan_bobot_id', $row['bobot_id'])
                    ->groupBy('kuisioner_result.member_id')
                    ->get();

                if ($result->isNotEmpty()) {
                    $count = count($result);
                    $total[] = 1;
                } else {
                    $count = 0;
                    $total[] = 0;
                }

                $res[$key]['hitung'] = $count;
            }
            // return $res;

            $fin = [];
            foreach ($res as $keys => $vals) {
                $fin[$vals['caption']][] = $vals;
            }

            $i = 1;
            foreach ($fin as $k => $v) {
                $jumlah = 0;
                foreach ($v as $kz => $vz) {
                    $final[$i]['label'] = $k;
                    $final[$i]['legend'][] = $vz['label'] . ' (' .$vz['hitung']. ')';
                    $final[$i]['color'][] = $vz['rating_color'];
                    $final[$i]['value'][] = $vz['hitung'];

                    $jumlah = $jumlah + $vz['hitung'];
                }
                $final[$i]['jumlah'] = $jumlah;

                $i++;
            }

            foreach ($final as $key => $row) {
                foreach ($row['value'] as $keys => $rows) {
                    $final[$key]['legend'][$keys] = ($final[$key]['jumlah'] != '0') ? $final[$key]['legend'][$keys] . ' - (' . round($rows / $final[$key]['jumlah'], 1) * 100 . '%)' : '0%';
                }
            }
        }

        $total = array_unique($total);
        if (($key = array_search('0', $total)) !== false) {
            unset($total[$key]);
        }

        $total = (!empty($total)) ? 1 : 0;

        $output = [
            'count' => $total,
            'data' => $final
        ];

        //get summary report
        $summary = "SELECT kuisioner_result.kuis_id, max(kuisioner_summary.`label`) as label, max(kuisioner_summary.`rating_color`) as rating_color, COUNT(kuisioner_result.id) AS total
                    FROM
                        kuisioner_result  
                        JOIN members ON members.id =kuisioner_result.`member_id`
                        JOIN kuisioner_summary ON kuisioner_summary.id = kuisioner_result.`summary_id`
                    WHERE kuisioner_result.label IS NOT NULL
                        AND kuisioner_result.kuis_id = ".$request->kuesioner."
                        AND kuisioner_result.status = 1 {$whereSummary}
                        GROUP BY kuisioner_summary.`kondisi`;
                        ";
        $summ = DB::select($summary);
        $total = array_sum(array_column($summ, 'total'));
        foreach ($summ as $keys => $rows) {
            $rows->count = $rows->total ?? 0;
            $rows->persen = ($total == '0') ? 0 : round($rows->count / $total, 1) * 100;
        }
 
        if($summ) {
            $finKuis['label'] = 'Summary Kuisioner';
            $finKuis['total'] = $total ?? 0;

            foreach ($summ as $keys => $rows) {
                $finKuis['legend'][$keys] = $rows->label . ' ('.$rows->count.') ' . $rows->persen . ' %';
                $finKuis['color'][$keys] = $rows->rating_color;
                $finKuis['value'][$keys] = $rows->persen;
                $finKuis['jumlah'][$keys] = $rows->count;
                $finKuis['link'][$keys] = route('admin.repkuis.detail', ['keyword' => $rows->label, 'kuesioner' => $rows->kuis_id, 'search'=> 'all']);
            }

            $output['data'][] = $finKuis;
        }


        return response()->json($output);

        die();
    }

    public function download(Request $request) {
        //print_r ($request->all());

        $result = KuisResult::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'kuisioner_result.member_id');
        })
        ->leftJoin('kuisioner_result_header', function($join) {
            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result.id');
        })
        ->leftJoin('kuisioner_result_detail', function($join) {
            $join->on('kuisioner_result_detail.result_id', '=', 'kuisioner_result.id');
            $join->on('kuisioner_result_detail.header_id', '=', 'kuisioner_result_header.header_id');
        })
        ->leftJoin('kuisioner_result_comment', function($join) {
            $join->on('kuisioner_result_comment.result_id', '=', 'kuisioner_result.id');
        })
        //->leftJoin('kuisioner_summary', function($join) {
        //    $join->on('kuisioner_summary.id', '=', 'kuisioner_result.summary_id');
        //})
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
        ->where('kuisioner_result.status', 1)
        ->orderBy('kuisioner_result.id', 'DESC')
        ->select([
            //'kuisioner_result.*',
            'kuisioner_result.kuis_title', 
            'kuisioner_result.kuis_code', 
            'members.name as nama',
            'members.gender',
            'members.no_telp',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'kuisioner_result.kuis_max_nilai', 
            'kuisioner_result.member_kuis_nilai', 
            'kuisioner_result.label',
            'kuisioner_result_header.pertanyaan_header_jenis', 
            'kuisioner_result_header.pertanyaan_header_caption',
            'kuisioner_result_header.pertanyaan_header_formula',
            'kuisioner_result_detail.pertanyaan_detail_title', 
            'kuisioner_result_detail.pertanyaan_detail_pilihan', 
            'kuisioner_result_detail.pertanyaan_bobot_label', 
            'kuisioner_result_detail.pertanyaan_bobot_kondisi',
            'kuisioner_result_detail.pertanyaan_bobot_nilai',
            'kuisioner_result_detail.value as member_jawaban', 
            'kuisioner_result_detail.formula_value',
            'kuisioner_result_detail.pertanyaan_bobot',
            //'kuisioner_summary.label as kuis_label',
            'kuisioner_result_comment.komentar',
            'kuisioner_result.created_at'
        ]);

        if (!empty($request->tanggal)) {
            $exp = explode(' - ', $request->tanggal);

            $start = explode('/', $exp[0]);
            $start = $start[2] . '-' . $start[1] . '-' . $start[0];
            $end = explode('/', $exp[1]);
            $end = $end[2] . '-' . $end[1] . '-' . $end[0];

            $result->whereBetween('kuisioner_result.created_at', [$start, $end]);
        }

        if (!empty($request->kuesioner)) {
            $result->where('kuisioner_result.kuis_id', $request->kuesioner);
        }

        if (!empty($request->provinsi)) {
            $result->where('members.provinsi_id', $request->provinsi);
        }

        if (!empty($request->kabupaten)) {
            $result->where('members.kabupaten_id', $request->kabupaten);
        }

        if (!empty($request->kecamatan)) {
            $result->where('members.kecamatan_id', $request->kecamatan);
        }

        if (!empty($request->kelurahan)) {
            $result->where('members.kelurahan_id', $request->kelurahan);
        }

        if (!empty($request->nik)) {
            $cekKtp = Helper::dcNik($request->nik);
            $result->where('members.nik', 'LIKE', '%' . $cekKtp . '%');
        }

        if (!empty($request->nama)) {
            $result->where('members.name', $request->nama);
        }

        if (!empty($request->gender)) {
            $result->where('kuisioner_result.gender', $request->gender);
        }

        $result = $result->get();

        if ($result->isNotEmpty()) {
            $result = $result->toArray();

            $fin = [];
            foreach ($result as $key => $row) {
                if ($row['pertanyaan_header_jenis'] == 'single') {
                    if ($row['pertanyaan_detail_pilihan'] == 'radio' || $row['pertanyaan_detail_pilihan'] == 'dropdown') {
                        $result[$key]['pertanyaan_bobot_label'] = '';
                        $result[$key]['member_jawaban'] = $row['pertanyaan_bobot_label'];
                    } else {
                        $result[$key]['member_jawaban'] = $row['member_jawaban'];
                    }
                }

                if ($row['pertanyaan_bobot_kondisi'] != '0' || !empty($row['pertanyaan_bobot_kondisi'])) {
                    $result[$key]['pertanyaan_bobot_kondisi'] = Helper::kondisiKuis($row['pertanyaan_bobot_kondisi']);
                } else {
                    $result[$key]['pertanyaan_bobot_kondisi'] = '';
                }

                if (!empty($row['gender']) || $row['gender'] != '0') {
                    $result[$key]['gender'] = Helper::jenisKelamin($row['gender']);
                } else {
                    $result[$key]['gender'] = '';
                }
            }

            $filename = 'KuesionerResult ' . date('Y-m-d-H-i-s') . '.csv';

            return Excel::download(new KuisExport($result), $filename, \Maatwebsite\Excel\Excel::CSV, [
                'Content-Type' => 'text/csv'
            ]);
        }
    }

    public function detail(Request $request) {

        $self = KuisResult::leftJoin('members', function($join) {
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
        ->leftJoin('member_delegate', function($join) {
            $join->on('member_delegate.member_id', '=', 'members.id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'member_delegate.user_id');
        })
        ->where('kuisioner_result.status', 1)
        //->where('kuisioner_result.responder_id', Auth::id())
        ->orderBy('kuisioner_result.id', 'DESC')
        ->select([
            'kuisioner_result.*',
            'members.id as memberid',
            'members.name as nama',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'kuisioner_summary.label as kuis_label',
            'kuisioner_result_comment.komentar',
            'users.id as petugas_id',
            'users.name as petugas'
        ]);

        if (!empty($request->tanggal)) {
            $exp = explode(' - ', $request->tanggal);

            $start = explode('/', $exp[0]);
            $start = $start[2] . '-' . $start[1] . '-' . $start[0];
            $end = explode('/', $exp[1]);
            $end = $end[2] . '-' . $end[1] . '-' . $end[0];

            // $self->whereBetween('kuisioner_result.created_at', [$start, $end]);
            $self->whereBetween(DB::raw('date(kuisioner_result.created_at)'), [$start, $end]);
        }

        if (!empty($request->kuesioner)) {
            $self->where('kuisioner_result.kuis_id', $request->kuesioner);
        }

        if (!empty($request->provinsi)) {
            $self->where('members.provinsi_id', $request->provinsi);
        }

        if (!empty($request->kabupaten)) {
            $self->where('members.kabupaten_id', $request->kabupaten);
        }

        if (!empty($request->kecamatan)) {
            $self->where('members.kecamatan_id', $request->kecamatan);
        }

        if (!empty($request->kelurahan)) {
            $self->where('members.kelurahan_id', $request->kelurahan);
        }

        if (!empty($request->nik)) {
            $cekKtp = Helper::dcNik($request->nik);
            $self->where('members.nik', 'LIKE', '%' . $cekKtp . '%');
        }

        if (!empty($request->nama)) {
            $self->where('members.name', $request->nama);
        }

        if (!empty($request->gender)) {
            $self->where('kuisioner_result.gender', $request->gender);
        }

        if (empty($request->kuesioner)) {
            //$result->limit(2);
            $self->whereNull('kuisioner_result_comment.komentar');
        }

        if (isset($request->search)) {
            if ($request->search == 'mine') {
                $self->where('kuisioner_result.responder_id', Auth::id());
            } else if ($request->search == 'other') {
                $self->where('kuisioner_result.responder_id', '!=', Auth::id());
            } else if ($request->search == 'all') {

            } else {
                $self->where('kuisioner_result.responder_id', Auth::id());
            }
            $selected = $request->search;
        } else {
            $self->where('kuisioner_result.responder_id', Auth::id());
            $selected = 'mine';
        }

        if($request->keyword != ''){
            // return $request->keyword;
            $key = $request->keyword;
            $self->where(function($q) use($key){
                $q->where('kuisioner_result.kuis_title', 'LIKE', '%'.$key.'%');
                $q->OrWhere('members.name', 'LIKE', '%'.$key.'%');
                $q->OrWhere('kuisioner_result.kuis_code', 'LIKE', '%'.$key.'%');
                $q->OrWhere('kuisioner_summary.label', 'LIKE', '%'.$key.'%');
                $q->OrWhere('users.name', 'LIKE', '%'.$key.'%');
            });
        }

        // $st = $self->toSql();
        // echo('<pre>');
        // print_r($self);die;
        $paginate = $self->groupBy('kuisioner_result.member_id')->paginate(10);
        $self = $paginate->items();

        $cop = [];
        if ($self) {
            // $self = $self->toArray();

            foreach ($self as $key => $val) {
                $couple = MemberCouple::leftJoin('members', function($join) {
                    $join->on('members.id', '=', 'member_couple.couple_id');
                })
                ->where('member_couple.member_id', $val['memberid'])
                ->where('member_couple.status', '=', 'APM200')
                ->select(['members.name as namapasangan'])
                ->get();

                $pasangan = '';
                if ($couple->isNotEmpty()) {
                    foreach ($couple as $keys => $vals) {
                        $pasangan .= $vals->namapasangan . ', ';
                    }
                    $pasangan = substr($pasangan, 0, -2);
                    $self[$key]['pasangan'] = $pasangan;
                }
            }

            foreach ($self as $keyz => $valz) {
                $cop[] = $valz['memberid'];
            }
        } else {
            $self = [];
        }

        // $couple = KuisResult::leftJoin('members', function($join) {
        //     $join->on('members.id', '=', 'kuisioner_result.member_id');
        // })
        // ->leftJoin('kuisioner_summary', function($join) {
        //     $join->on('kuisioner_summary.id', '=', 'kuisioner_result.summary_id');
        // })
        // ->leftJoin('adms_provinsi', function($join) {
        //     $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
        // })
        // ->leftJoin('adms_kabupaten', function($join) {
        //     $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
        // })
        // ->leftJoin('adms_kecamatan', function($join) {
        //     $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
        // })
        // ->leftJoin('adms_kelurahan', function($join) {
        //     $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
        // })
        // ->leftJoin('kuisioner_result_comment', function($join) {
        //     $join->on('kuisioner_result_comment.result_id', '=', 'kuisioner_result.id');
        // })
        // ->leftJoin('member_delegate', function($join) {
        //     $join->on('member_delegate.member_id', '=', 'members.id');
        // })
        // ->leftJoin('users', function($join) {
        //     $join->on('users.id', '=', 'member_delegate.user_id');
        // })
        // ->leftJoin('member_couple', function($join) {
        //     $join->on('member_couple.couple_id', '=', 'members.id');
        // })
        // ->where('kuisioner_result.status', 1)
        // ->where('member_couple.status', '=', 'APM200')
        // ->whereNotIn('member_delegate.member_id', $cop)
        // ->orderBy('kuisioner_result.id', 'DESC')
        // ->select([
        //     'kuisioner_result.*',
        //     'members.id as memberid',
        //     'members.name as nama',
        //     'adms_provinsi.nama as provinsi',
        //     'adms_kabupaten.nama as kabupaten',
        //     'adms_kecamatan.nama as kecamatan',
        //     'adms_kelurahan.nama as kelurahan',
        //     'kuisioner_summary.label as kuis_label',
        //     'kuisioner_result_comment.komentar',
        //     'users.id as petugas_id',
        //     'users.name as petugas'
        // ]);

        // if (!empty($request->tanggal)) {
        //     $exp = explode(' - ', $request->tanggal);

        //     $start = explode('/', $exp[0]);
        //     $start = $start[2] . '-' . $start[1] . '-' . $start[0];
        //     $end = explode('/', $exp[1]);
        //     $end = $end[2] . '-' . $end[1] . '-' . $end[0];

        //     $couple->whereBetween('kuisioner_result.created_at', [$start, $end]);
        // }

        // if (!empty($request->nik)) {
        //     $cekKtp = Helper::dcNik($request->nik);
        //     $couple->where('members.nik', 'LIKE', '%' . $cekKtp . '%');
        // }

        // if (!empty($request->nama)) {
        //     $couple->where('members.name', $request->nama);
        // }

        // if (!empty($request->gender)) {
        //     $couple->where('kuisioner_result.gender', $request->gender);
        // }

        // if (empty($request->kuesioner)) {
        //     $couple->whereNull('kuisioner_result_comment.komentar');
        // }

        // if (isset($request->search)) {
        //     if ($request->search == 'mine') {
        //         $couple->where('kuisioner_result.responder_id', Auth::id());
        //     } else if ($request->search == 'other') {
        //         $couple->where('kuisioner_result.responder_id', '!=', Auth::id());
        //     } else if ($request->search == 'all') {

        //     } else {
        //         $couple->where('kuisioner_result.responder_id', Auth::id());
        //     }
        //     $selected = $request->search;
        // } else {
        //     $couple->where('kuisioner_result.responder_id', Auth::id());
        //     $selected = 'mine';
        // }

        // if($request->keyword != ''){
        //     $key = $request->keyword;
        //     $couple->where(function($q) use($key){
        //         $q->where('kuisioner_result.kuis_title', 'LIKE', '%'.$key.'%');
        //         $q->OrWhere('members.name', 'LIKE', '%'.$key.'%');
        //     });
        // }

        // $couple = $couple->get();
        // // $st = $couple;
        // // echo $st;die;

        // if ($couple->isNotEmpty()) {
        //     $couple = $couple->toArray();

        //     $pasangan = '';
        //     foreach ($couple as $key => $val) {
        //         $coups = MemberCouple::leftJoin('members', function($join) {
        //             $join->on('members.id', '=', 'member_couple.couple_id');
        //         })
        //         ->where('member_couple.member_id', $val['memberid'])
        //         ->where('member_couple.status', '=', 'APM200')
        //         ->select(['members.name as namapasangan'])
        //         ->get();

        //         $pasangan = '';
        //         if ($coups->isNotEmpty()) {
        //             foreach ($coups as $keys => $vals) {
        //                 $pasangan .= $vals->namapasangan . ', ';
        //             }
        //             $pasangan = substr($pasangan, 0, -2);
        //             $couple[$key]['pasangan'] = $pasangan;
        //         }
        //     }

        // } else {
        //     $couple = [];
        // }


        // $result = array_merge($self, $couple);
        $result = $self;

        if (isset($request->curl) && !empty($request->curl)) {
            $fullurl = $request->curl . '&search=' . $request->search . '&keyword=' . $request->keyword;
            return redirect()->to($fullurl)->with(['result' => $result])->with(['selected' => $request->search, 'paginate' => $paginate]);
        } else {
            return view('repkuis.detail', compact('result', 'selected', 'paginate'));
        }

    }

    public function details(Request $request) {

        $base_url = env('BASE_URL_PDF');
        $kuis = KuisResult::where('id', $request->cid)
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
        ->leftJoin('member_delegate', function($join) {
            $join->on('member_delegate.member_id', '=', 'members.id')
                ->where('member_delegate.user_id', Auth::user()->id);
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'member_delegate.user_id');
        })
        ->where('members.id', $kuis->member_id)
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'users.id as petugas_id'
        ])
        ->first();

        $tanggal = explode(' ', $kuis->created_at);
        $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2] . ' Pukul ' . str_replace(':', '.', $tanggal[3]);
        $kuis->tanggal = $tanggal; 

        $detail = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
            $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
            $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
        })
        ->where('kuisioner_result_detail.result_id', $request->cid)
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

        $komentar = KuisResultComment::where('result_id', $request->cid)->first();

        $fullurl = $request->cu;

        // echo('<pre>');
        // print_r($member);die;
        if(Auth::user()->roleChild == $this->role_child_id) $is_comment = 1;
        else $is_comment = 0;

        //get member couple 
        $couple = MemberCouple::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'member_couple.couple_id');
        })
        ->join('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
        })
        ->join('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
        })
        ->join('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
        })
        ->join('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
        })
        ->join('kuisioner_result', function($q){
            $q->on('kuisioner_result.member_id', 'members.id')
                ->where('kuisioner_result.status', 1);
        })
        ->select([
            'member_couple.*',
            'members.id', 'members.name',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'kuisioner_result.id as kuis_result_id'
        ])
        ->where('member_couple.member_id', $kuis->member_id)
        ->orderBy('member_couple.id', 'DESC')
        ->first();
        // return $couple;

        return view('repkuis.edit', compact('kuis', 'member', 'deskripsi', 'result', 'out', 'komentar', 'fullurl', 'is_comment', 'couple'));
    }

    public function update(Request $request, $id) {
        $check = KuisResultComment::where('result_id', $id)->first();

        if ($check) {
            $update = KuisResultComment::where('result_id', $id)->update([
                'user_id' => Auth::id(),
                'komentar' => $request->komentar,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);
        } else {
            $save = new KuisResultComment;
            $save->user_id = Auth::id();
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

        $tambah = new KuisResultCommentLog;
        $tambah->user_id = Auth::id();
        $tambah->result_id = $id;
        $tambah->komentar = $request->komentar;
        $tambah->created_at = date('Y-m-d H:i:s');
        $tambah->created_by = Auth::id();

        $tambah->save();

        $get = KuisResult::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'kuisioner_result.member_id');
        })
        ->leftJoin('member_onesignal', function($join) {
            $join->on('member_onesignal.member_id', '=', 'members.id');
        })
        ->where('kuisioner_result.id', $id)->select('member_onesignal.player_id')->first();

        if (!empty($get)) {
            if (!empty($get->player_id)) {
                $parameters = [
                    'include_player_ids' => [$get->player_id],
                    'headings' => [
                        'en' => 'Hasil kuesioner'
                    ],
                    'contents' => [
                        'en' => 'Hai, kuesioner kamu sudah dapat tanggapan lho. Yuk lihat yuk'
                    ],
                    'ios_badgeType'  => 'Increase',
                    'ios_badgeCount' => 1,
                ];

                $send = OneSignal::sendNotificationCustom($parameters);
            }
        }

        $fullurl = $request->fullurl;

        $msg = 'Ulasan penilaian kuesioner berhasil.';
        return redirect()->to($fullurl)->with('success', $msg);
    }

    public function history($id, $cid) {
        $result = [];
        $output = [];

        $cekKuis = KuisResult::where('kuis_id', $id)->where('member_id', $cid)->orderBy('id', 'DESC')->limit(30)->get();

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
        ->leftJoin('member_delegate', function($join) {
            $join->on('member_delegate.member_id', '=', 'members.id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'member_delegate.user_id');
        })
        ->where('members.id', $cid)
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'users.id as petugas_id'
        ])
        ->first();

        $output['member'] = $member;

        if ($cekKuis->isNotEmpty()) {
            $base_url = env('BASE_URL_PDF');

            foreach ($cekKuis as $kkuis => $vkuis) {
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
        }


        return view('repkuis.history', compact('output'));
    }

}
