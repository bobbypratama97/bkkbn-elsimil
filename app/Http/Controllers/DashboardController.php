<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Session;

use Helper;

use App\User;
use App\Member;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Kelurahan;
use App\UserRole;

class DashboardController extends Controller
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
    public function dashboard()
    {
        $auth = Auth::user();
        $role = UserRole::where('user_id', Auth::id())->first();

        $whereChat = $whereReview = $whereChats = $whereKuis = $whereAllChats = $whereAllKuis = $whereUnmap = '';
        $provcur = $kabcur = $keccur = '';

        if ($role->role_id == '1') {
            $whereKuis = " WHERE kr.status = 1 AND krc.id IS NULL";
            $whereAllKuis = " WHERE kr.status = 1 AND krc.id IS NULL";
            $whereUnmap = " WHERE md.member_id IS NULL";
        }

        if ($role->role_id == '2') {
            $whereChat = " WHERE members.provinsi_id = ". $auth->provinsi_id ?? null;
            $whereReview = " AND members.provinsi_id = ". $auth->provinsi_id ?? null;

            $whereChats = " WHERE mb.provinsi_id = ". $auth->provinsi_id ?? null;

            $whereAllChats = " WHERE (ch.responder_id is null OR ch.responder_id = {$auth->id}) AND mb.provinsi_id = ". $auth->provinsi_id ?? null;

            $whereKuis = " WHERE kr.status = 1 AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null;

            $whereAllKuis = " WHERE kr.status = 1 AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null;

            $whereUnmap = " WHERE md.member_id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null;

            $provcur = $auth->provinsi_id;
            $kabcur = '';
            $keccur = '';
        }

        if ($role->role_id == '3') {
            $whereChat = " WHERE members.provinsi_id = ". $auth->provinsi_id ?? null ." AND members.kabupaten_id = ". $auth->kabupaten_id ?? null;
            $whereReview = " AND members.provinsi_id = ". $auth->provinsi_id ?? null ." AND members.kabupaten_id = ". $auth->kabupaten_id ?? null;

            $whereChats = " WHERE mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null;

            $whereAllChats = " WHERE (ch.responder_id is null OR ch.responder_id = {$auth->id}) AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ". $auth->kecamatan_id ?? null;

            $whereKuis = " WHERE kr.status = 1 AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null;

            $whereAllKuis = " WHERE kr.status = 1 AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null;

            $whereUnmap = " WHERE md.member_id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null;

            $provcur = $auth->provinsi_id;
            $kabcur = $auth->kabupaten_id;
            $keccur = '';
        }

        if ($role->role_id == '4') {
            $whereChat = " WHERE members.provinsi_id = ".$auth->provinsi_id ?? null." AND members.kabupaten_id = ".$auth->kabupaten_id ?? null." AND members.kecamatan_id = ".$auth->kecamatan_id ?? null;
            $whereReview = " AND members.provinsi_id = ".$auth->provinsi_id ?? null." AND members.kabupaten_id = ".$auth->kabupaten_id ?? null." AND members.kecamatan_id = ".$auth->kecamatan_id ?? null;

            $whereChats = " WHERE ch.responder_id = ".$auth->id." AND mb.provinsi_id = ".$auth->provinsi_id ?? null." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null." AND mb.kecamatan_id = ".$auth->kecamatan_id ?? null;
            $whereAllChats = " WHERE (ch.responder_id is null OR ch.responder_id = {$auth->id}) AND mb.provinsi_id = ".$auth->provinsi_id ?? null ." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ".$auth->kecamatan_id ?? null;
            $whereKuis = " WHERE kr.status = 1 AND kr.responder_id = {$auth->id} AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ".$auth->kecamatan_id ?? null;
            $whereAllKuis = " WHERE kr.status = 1 AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ". $auth->kecamatan_id ?? null;
            $whereUnmap = " WHERE md.member_id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ". $auth->kecamatan_id ?? null;

            $provcur = $auth->provinsi_id;
            $kabcur = $auth->kabupaten_id;
            $keccur = $auth->kecamatan_id;
        }

        if ($role->role_id == '5') {
            $whereChat = " WHERE members.provinsi_id = ".$auth->provinsi_id ?? null." AND members.kabupaten_id = ".$auth->kabupaten_id ?? null." AND members.kecamatan_id = ".$auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;
            $whereReview = " AND members.provinsi_id = ".$auth->provinsi_id ?? null." AND members.kabupaten_id = ".$auth->kabupaten_id ?? null." AND members.kecamatan_id = ".$auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;

            $whereChats = " WHERE ch.responder_id = ".$auth->id." AND mb.provinsi_id = ".$auth->provinsi_id ?? null." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null." AND mb.kecamatan_id = ".$auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;
            $whereAllChats = " WHERE (ch.responder_id is null OR ch.responder_id = {$auth->id}) AND mb.provinsi_id = ".$auth->provinsi_id ?? null ." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ".$auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;
            $whereKuis = " WHERE kr.status = 1 AND kr.responder_id = {$auth->id} AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ".$auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ".$auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;
            $whereAllKuis = " WHERE kr.status = 1 AND krc.id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ". $auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;
            $whereUnmap = " WHERE md.member_id IS NULL AND mb.provinsi_id = ". $auth->provinsi_id ?? null ." AND mb.kabupaten_id = ". $auth->kabupaten_id ?? null ." AND mb.kecamatan_id = ". $auth->kecamatan_id ?? null." AND members.kelurahan_id = ".$auth->kelurahan_id;

            $provcur = $auth->provinsi_id;
            $kabcur = $auth->kabupaten_id;
            $keccur = $auth->kecamatan_id;
        }

        $infonorespondchat = "
            SELECT COUNT(*) AS total FROM chat_header ch
            LEFT JOIN chat_message cm ON cm.chat_id = ch.id AND cm.last = 1 AND cm.status = 'send'
            LEFT JOIN members mb ON mb.id = ch.member_id
            {$whereChats}
        ";
        $resnorespondchat = DB::select($infonorespondchat);

        $infonorespkuis = "
            SELECT COUNT(*) AS total FROM kuisioner_result kr 
            LEFT JOIN kuisioner_result_comment krc ON krc.result_id = kr.id
            LEFT JOIN members mb ON mb.id = kr.member_id
            {$whereKuis}
        ";
        //echo $infonorespkuis;
        $resnorespondkuis = DB::select($infonorespkuis);

        $infoallnoresponsechat = "
            SELECT COUNT(*) AS total FROM chat_header ch
            LEFT JOIN chat_message cm ON cm.chat_id = ch.id AND cm.last = 1 AND cm.status = 'send'
            LEFT JOIN members mb ON mb.id = ch.member_id
            {$whereAllChats}
        ";
        $resallnorespchat = DB::select($infoallnoresponsechat);

        $infoallnorespkuis = "
            SELECT COUNT(*) AS total FROM kuisioner_result kr 
            LEFT JOIN kuisioner_result_comment krc ON krc.result_id = kr.id
            LEFT JOIN members mb ON mb.id = kr.member_id
            {$whereAllKuis}
        ";
        $resallnorespondkuis = DB::select($infoallnorespkuis);

        $infounmapping = "
            SELECT COUNT(*) AS total FROM members mb 
            LEFT JOIN member_delegate md ON md.member_id = mb.id
            {$whereUnmap}
        ";
        $resallunmap = DB::select($infounmapping);

        $sqlchatalloc = "
            SELECT COUNT(*) AS total FROM chat_header LEFT JOIN members ON members.id = chat_header.member_id WHERE responder_id IS NULL {$whereReview}
        ";
        $chatalloc = DB::select($sqlchatalloc);

        $sql = "
            SELECT count(*) AS total FROM (
                SELECT member_id, MAX(chat_message.id) AS request_id
                FROM chat_message 
                LEFT JOIN members ON members.id = chat_message.member_id
                {$whereChat}
                GROUP BY member_id DESC
            ) a LEFT JOIN chat_message d ON d.id = a.request_id 
            WHERE d.status = 'send'
        ";
        //echo $sql;

        $chat = DB::select($sql);

        $sql1 = "
            SELECT count(*) AS total
            FROM kuisioner_result kr LEFT JOIN kuisioner_result_comment krc ON kr.id = krc.result_id
            LEFT JOIN members ON members.id = kr.member_id
            WHERE krc.result_id IS NULL AND kr.status = 1 {$whereReview}
        ";

        //echo $sql1;

        $review = DB::select($sql1);

        $sql2 = "
            SELECT count(*) AS total 
            FROM members 
            WHERE created_at = now()
            {$whereReview}
        ";
        $sql3 = "SELECT count(*) AS total FROM members WHERE deleted_by IS NULL {$whereReview}";

        $member = DB::select($sql2);
        $membertotal = DB::select($sql3);

        $sql4 = "SELECT title FROM kuisioner ORDER BY id DESC LIMIT 1";
        $kuis = DB::select($sql4);

        $totalprov = $totalkota = $totalcamat = 0;

        $totalmember = Member::select(DB::raw('count(*) AS total'))->first();
        $countmember = $totalmember->total;

        $members = [
            'total' => [
                'count' => $countmember,
                'label' => 'Total Catin',
                'text' => 'Total catin yang terdaftar di ELSIMIL'
            ]
        ];

        if ($role->role_id == '1') {
            $members['provinsi'] = [
                'count' => $countmember,
                'label' => 'Semua Provinsi',
                'text' => 'Semua Provinsi'
            ];

            $members['kabupaten'] = [
                'count' => $countmember,
                'label' => 'Semua Kabupaten / Kota',
                'text' => 'Semua Kabupaten / Kota'
            ];

            $members['kecamatan'] = [
                'count' => $countmember,
                'label' => 'Semua Kecamatan',
                'text' => 'Semua Kecamatan'
            ];
        }

        if ($role->role_id == '2') {

            $provinsi = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->select([
                'adms_provinsi.nama as provinsi',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->first();

            $members['provinsi'] = [
                'count' => $provinsi->total,
                'label' => $provinsi->provinsi,
                'text' => 'Provinsi'
            ];

            $kabupaten = Kabupaten::select('nama')->where('kabupaten_kode', $auth->kabupaten_id)->first();
            $members['kabupaten'] = [
                'count' => $provinsi->total,
                'label' => $kabupaten->nama,
                'text' => 'Kabupaten / Kota'
            ];

            $kecamatan = Kecamatan::select('nama')->where('kecamatan_kode', $auth->kecamatan_id)->first();
            $members['kecamatan'] = [
                'count' => $provinsi->total,
                'label' => $kecamatan->nama,
                'text' => 'Kecamatan'
            ];

        }

        if ($role->role_id == '3') {
            $provinsi = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->select([
                'adms_provinsi.nama as provinsi',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->first();

            $members['provinsi'] = [
                'count' => $provinsi->total,
                'label' => $provinsi->provinsi,
                'text' => 'Provinsi'
            ];


            $kabupaten = Member::leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->select([
                'adms_kabupaten.nama as kabupaten',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->where('members.kabupaten_id', $auth->kabupaten_id)
            ->first();

            $members['kabupaten'] = [
                'count' => $kabupaten->total,
                'label' => $kabupaten->kabupaten,
                'text' => 'Kabupaten / Kota'
            ];

            $kecamatan = Kecamatan::select('nama')->where('kecamatan_kode', $auth->kecamatan_id)->first();
            $members['kecamatan'] = [
                'count' => $kabupaten->total,
                'label' => $kecamatan->nama,
                'text' => 'Kecamatan'
            ];

        }

        if ($role->role_id == '4') {
            $provinsi = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->select([
                'adms_provinsi.nama as provinsi',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->first();

            $members['provinsi'] = [
                'count' => $provinsi->total,
                'label' => $provinsi->provinsi,
                'text' => 'Provinsi'
            ];


            $kabupaten = Member::leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->select([
                'adms_kabupaten.nama as kabupaten',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->where('members.kabupaten_id', $auth->kabupaten_id)
            ->first();

            $members['kabupaten'] = [
                'count' => $kabupaten->total,
                'label' => $kabupaten->kabupaten,
                'text' => 'Kabupaten / Kota'
            ];

            $kecamatan = Member::leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            })
            ->select([
                'adms_kecamatan.nama as kecamatan',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->where('members.kabupaten_id', $auth->kabupaten_id)
            ->where('members.kecamatan_id', $auth->kecamatan_id)
            ->first();

            $members['kecamatan'] = [
                'count' => $kecamatan->total,
                'label' => $kecamatan->kecamatan,
                'text' => 'Kecamatan'
            ];

        }

        if ($role->role_id == '5') {
            $provinsi = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->select([
                'adms_provinsi.nama as provinsi',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->first();

            $members['provinsi'] = [
                'count' => $provinsi->total,
                'label' => $provinsi->provinsi,
                'text' => 'Provinsi'
            ];


            $kabupaten = Member::leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->select([
                'adms_kabupaten.nama as kabupaten',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->where('members.kabupaten_id', $auth->kabupaten_id)
            ->first();

            $members['kabupaten'] = [
                'count' => $kabupaten->total,
                'label' => $kabupaten->kabupaten,
                'text' => 'Kabupaten / Kota'
            ];

            $kecamatan = Member::leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            })
            ->select([
                'adms_kecamatan.nama as kecamatan',
                DB::raw('count(*) AS total')
            ])
            ->where('members.provinsi_id', $auth->provinsi_id)
            ->where('members.kabupaten_id', $auth->kabupaten_id)
            ->where('members.kecamatan_id', $auth->kecamatan_id)
            ->first();

            $members['kecamatan'] = [
                'count' => $kecamatan->total,
                'label' => $kecamatan->kecamatan,
                'text' => 'Kecamatan'
            ];

        }

        $sql10 = "
            SELECT 
                kr.id, kr.kuis_code, kr.kuis_title, kr.label, kr.rating_color, 
                krc.komentar,
                members.id AS member_id, members.name, members.gender, 
                adms_provinsi.nama as provinsi, adms_kabupaten.nama as kabupaten, adms_kecamatan.nama as kecamatan, adms_kelurahan.nama as kelurahan
            FROM kuisioner_result kr LEFT JOIN kuisioner_result_comment krc ON kr.id = krc.result_id
            LEFT JOIN members ON members.id = kr.member_id
            LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = members.provinsi_id
            LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = members.kabupaten_id
            LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
            LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = members.kelurahan_id
            WHERE krc.result_id IS NULL AND kr.status = 1 {$whereReview} LIMIT 10
        ";

        //echo $sql10;

        $resp = DB::select($sql10);

        $cur = '';
        if (!empty($resp)) {
            foreach ($resp as $key => $row) {
                $cur .= $row->member_id . ', ';
            }

            $cur = substr($cur, 0, -2);

            $array = [1, 2, 3];

            if (!in_array($role->role_id, $array)) {
                $check = "AND member_delegate.user_id = {$auth->id}";
            } else {
                $check = "";
            }

            $sql20 = "
                SELECT 
                    kr.id, kr.kuis_code, kr.kuis_title, kr.label, kr.rating_color, 
                    krc.komentar,
                    members.id AS member_id, members.name, members.gender, 
                    adms_provinsi.nama as provinsi, adms_kabupaten.nama as kabupaten, adms_kecamatan.nama as kecamatan, adms_kelurahan.nama as kelurahan
                FROM kuisioner_result kr LEFT JOIN kuisioner_result_comment krc ON kr.id = krc.result_id
                LEFT JOIN members ON members.id = kr.member_id
                LEFT JOIN member_delegate ON member_delegate.member_id = members.id
                LEFT JOIN adms_provinsi ON adms_provinsi.provinsi_kode = members.provinsi_id
                LEFT JOIN adms_kabupaten ON adms_kabupaten.kabupaten_kode = members.kabupaten_id
                LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
                LEFT JOIN adms_kelurahan ON adms_kelurahan.kelurahan_kode = members.kelurahan_id
                WHERE krc.result_id IS NULL AND kr.status = 1 {$check} AND member_delegate.member_id NOT IN ({$cur}) LIMIT 10
            ";
            //echo $sql20;

            $rescur = DB::select($sql20);

            $resp = array_merge($resp, $rescur);
        }


        return view('dashboard', compact('resnorespondchat', 'resnorespondkuis', 'resallnorespchat', 'resallnorespondkuis', 'resallunmap', 'chatalloc', 'chat', 'review', 'members', 'member', 'membertotal', 'kuis', 'resp', 'provcur', 'kabcur', 'keccur'));
    }

    public function gender(Request $request) {
        $auth = Auth::user();
        $role = UserRole::where('user_id', Auth::id())->first();

        $jumlah = 0;
        // gender
        // $totalgender = Member::select(DB::raw('count(*) as jumlah'))->first();
        // $gender = Member::select([
        //     'gender', 
        //     DB::raw('count(*) AS total'),
        //     DB::raw("round(count(*) / {$totalgender->jumlah}, 1) * 100 as persen")
        // ]);
        $gender = Member::select([
            'gender', 
            DB::raw('count(*) AS total')
        ]);

        if ($role->role_id == '2') {
            $gender = $gender->where('provinsi_id', $auth->provinsi_id);
        }

        if ($role->role_id == '3') {
            $gender = $gender->where('provinsi_id', $auth->provinsi_id)->where('kabupaten_id', $auth->kabupaten_id);
        }

        if ($role->role_id == '4') {
            $gender = $gender->where('provinsi_id', $auth->provinsi_id)->where('kabupaten_id', $auth->kabupaten_id)->where('kecamatan_id', $auth->kecamatan_id);
        }

        $gender = $gender->groupBy('gender')->get();
        $total = 0;
        if ($gender->isNotEmpty()) {
            $gender = $gender->toArray();
            $total = array_sum(array_column($gender,'total'));
        }

        $i = 0;
        $finGender = [];
        foreach ($gender as $key => $row) {
            $persen = round($row['total'] / $total, 2) * 100;
            $finGender['label'][$i] = Helper::statusGender($row['gender']) . ' ' . $persen . ' %';
            $finGender['data'][$i] = $persen;
            $i++;
        }

        return response()->json([
            'count' => $total,
            'data' => $finGender
        ]);

        die();
    }

    public function umur(Request $request) {
        $auth = Auth::user();
        $role = UserRole::where('user_id', Auth::id())->first();

        $jumlah = 0;
        // $totalmember = Member::select(DB::raw('count(*) as jumlah'))->first();
        $usia = Member::select([
            DB::raw('count(*) AS total'),
            DB::raw("FLOOR(DATEDIFF(now(), tgl_lahir) / 365) as umur")
        ]);

        if ($role->role_id == '2') {
            $usia = $usia->where('provinsi_id', $auth->provinsi_id);
        }

        if ($role->role_id == '3') {
            $usia = $usia->where('provinsi_id', $auth->provinsi_id)->where('kabupaten_id', $auth->kabupaten_id);
        }

        if ($role->role_id == '4') {
            $usia = $usia->where('provinsi_id', $auth->provinsi_id)->where('kabupaten_id', $auth->kabupaten_id)->where('kecamatan_id', $auth->kecamatan_id);
        }

        $usia = $usia->groupBy('umur')->get();
        $totalmember = 0;
        if ($usia->isNotEmpty()) {
            $usia = $usia->toArray();
            $totalmember = array_sum(array_column($usia, 'total'));
        }

        $compare = [
            '0 - 20',
            '21 - 35',
            '36 - 1000'
        ];
        
        foreach ($usia as $key => $row) {
            $value = $row['umur'];
            $total = 0;
            foreach ($compare as $z => $y) {
                $exp = explode(' - ', $y);

                if (is_numeric($value) && $value >= $exp[0] && $value <= $exp[1]) {
                    $usia[$key]['range'] = $y;
                }
            }
        }

        $output = [];
        foreach ($usia as $key => $row) {
            $output[$row['range']][] = $row;
        }

        $fin = [];
        foreach ($compare as $key => $row) {
            $sum = 0;
            if (isset($output[$row])) {
                foreach ($output[$row] as $keys => $rows) {
                    $sum += $rows['total'];
                }
            }

            if ($row == '0 - 20') {
                $fin[$key]['label'] = '< 21';
            } else if ($row == '21 - 35') {
                $fin[$key]['label'] = '21 - 35';
            } else {
                $fin[$key]['label'] = '> 35';
            }
            $fin[$key]['data'] = round($sum / $totalmember, 1) * 100;
        }

        $i = 0;
        $finUsia = [];
        foreach ($fin as $key => $row) {
            $finUsia['label'][$i] = $row['label'] . ' => ' . round($row['data']) . ' %';
            $finUsia['data'][$i] = round($row['data']);
            $i++;
        }

        return response()->json([
            'count' => $totalmember,
            'data' => $finUsia
        ]);

        die();
    }

    public function top(Request $request) {
        $auth = Auth::user();
        $role = UserRole::where('user_id', Auth::id())->first();

        $jumlah = 0;
        // $totalmember = Member::select(DB::raw('count(*) as jumlah'))->first();

        // $sql = "
        //     SELECT a.*, ROUND(ROUND(a.kuis_total / a.member_total, 1) * 100) AS persen FROM (
        //         SELECT 
        //             l.kecamatan_id AS kecamatan_id, 
        //             l.nama AS nama, 
        //             l.total AS kuis_total, 
        //             m.total AS member_total
        //         FROM (
        //             SELECT count(*) AS total, kecamatan_id, adms_kecamatan.nama FROM kuisioner_result 
        //             LEFT JOIN members ON members.id = kuisioner_result.member_id 
        //             LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
        //             WHERE kuisioner_result.status = 1
        //             GROUP BY members.kecamatan_id
        //         ) AS l JOIN (
        //             SELECT count(*) AS total, kecamatan_id, adms_kecamatan.nama FROM members 
        //             LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
        //             GROUP BY members.kecamatan_id
        //         ) AS m
        //         ON l.kecamatan_id = m.kecamatan_id
        //     ) a ORDER BY persen DESC LIMIT 10
        // ";
        $sql = "SELECT adk.`kecamatan_kode`,adk.`nama`,COUNT(kr.id) AS kuis_total 
                FROM kuisioner_result kr 
                JOIN members mb ON kr.`member_id`=mb.`id` 
                JOIN adms_kecamatan adk ON adk.`kecamatan_kode`=mb.`kecamatan_id` 
                WHERE kr.status = 1 
                GROUP BY adk.`kecamatan_kode` 
                ORDER BY kuis_total DESC LIMIT 10;
            ";

        $res = DB::select($sql);
        $total = array_sum(array_column($res, 'kuis_total'));

        $i = 0;
        $finTop = [];
        foreach ($res as $key => $row) {
            $persen = ceil(round($row->kuis_total / $total, 2) * 100);
            $finTop['label'][$i] = $row->nama;
            $finTop['data'][$i] = $persen;
            $i++;
        }

        return response()->json([
            'count' => $total,//$totalmember->jumlah,
            'data' => $finTop
        ]);

        die();
    }

    public function bottom(Request $request) {
        // $auth = Auth::user();
        // $role = UserRole::where('user_id', Auth::id())->first();

        // $jumlah = 0;
        // $totalmember = Member::select(DB::raw('count(*) as jumlah'))->first();

        // $sql = "
        //     SELECT a.*, ABS(ROUND(100 - (ROUND(a.kuis_total / a.member_total, 1) * 100))) AS persen FROM (
        //         SELECT 
        //             l.kecamatan_id AS kecamatan_id, 
        //             l.nama AS nama, 
        //             l.total AS kuis_total, 
        //             m.total AS member_total
        //         FROM (
        //             SELECT count(*) AS total, kecamatan_id, adms_kecamatan.nama FROM kuisioner_result 
        //             LEFT JOIN members ON members.id = kuisioner_result.member_id 
        //             LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
        //             WHERE kuisioner_result.status = 1
        //             GROUP BY members.kecamatan_id
        //         ) AS l JOIN (
        //             SELECT count(*) AS total, kecamatan_id, adms_kecamatan.nama FROM members 
        //             LEFT JOIN adms_kecamatan ON adms_kecamatan.kecamatan_kode = members.kecamatan_id
        //             GROUP BY members.kecamatan_id
        //         ) AS m
        //         ON l.kecamatan_id = m.kecamatan_id
        //     ) a ORDER BY persen ASC LIMIT 10
        // ";

        //echo $sql;
        $sql = "SELECT adk.`kecamatan_kode`,adk.`nama`,COUNT(kr.id) AS kuis_total 
                FROM kuisioner_result kr 
                JOIN members mb ON kr.`member_id`=mb.`id` 
                JOIN adms_kecamatan adk ON adk.`kecamatan_kode`=mb.`kecamatan_id` 
                WHERE kr.status = 1 
                GROUP BY adk.`kecamatan_kode` 
                ORDER BY kuis_total ASC LIMIT 10;
            ";

        $res = DB::select($sql);
        $total = array_sum(array_column($res, 'kuis_total'));

        $i = 0;
        $finBottom = [];
        foreach ($res as $key => $row) {
            // $persen = round(($row->kuis_total / $total) * 100, 2);
            $persen = ceil(round($row->kuis_total / $total, 2) * 100);
            $finBottom['label'][$i] = $row->nama;
            $finBottom['data'][$i] = $persen;
            $i++;
        }

        return response()->json([
            'count' => $total,
            'data' => $finBottom
        ]);

        die();
    }

    public function kuis(Request $request) {
        $auth = Auth::user();
        $role = UserRole::where('user_id', Auth::id())->first();

        $whereChat = $whereReview = '';

        if ($role->role_id == '2') {
            $whereChat = " WHERE members.provinsi_id = {$auth->provinsi_id}";
            $whereReview = " AND members.provinsi_id = {$auth->provinsi_id}";
        }

        if ($role->role_id == '3') {
            $whereChat = " WHERE members.provinsi_id = {$auth->provinsi_id} AND members.kabupaten_id = {$auth->kabupaten_id}";
            $whereReview = " AND members.provinsi_id = {$auth->provinsi_id} AND members.kabupaten_id = {$auth->kabupaten_id}";
        }

        if ($role->role_id == '4') {
            $whereChat = " WHERE members.provinsi_id = {$auth->provinsi_id} AND members.kabupaten_id = {$auth->kabupaten_id} AND members.kecamatan_id = {$auth->kecamatan_id}";
            $whereReview = " AND members.provinsi_id = {$auth->provinsi_id} AND members.kabupaten_id = {$auth->kabupaten_id} AND members.kecamatan_id = {$auth->kecamatan_id}";
        }

        $sql4 = "SELECT id, title FROM kuisioner WHERE id in (20, 21) AND deleted_by IS NULL ORDER BY id DESC LIMIT 2";
        $list = DB::select($sql4);

        foreach ($list as $key => $row) {
            // $sql5 = "
            //     SELECT COUNT(*) AS total FROM kuisioner_result  
            //     LEFT JOIN members ON kuisioner_result.member_id = members.id
            //     WHERE kuisioner_result.label IN (
            //         SELECT kuisioner_summary.label FROM kuisioner 
            //         LEFT JOIN kuisioner_summary ON kuisioner_summary.kuis_id = kuisioner.id
            //         WHERE kuisioner.id = {$row->id} AND kuisioner_summary.deleted_by IS NULL
            //     ) AND kuisioner_result.kuis_id = {$row->id} AND kuisioner_result.status = 1 {$whereReview}
            // ";
            $sql5 = "SELECT max(kuisioner_summary.`label`) as label, max(kuisioner_summary.`rating_color`) as rating_color, COUNT(kuisioner_result.id) AS total
                    FROM
                        kuisioner_result  
                        JOIN members ON members.id =kuisioner_result.`member_id`
                        JOIN kuisioner_summary ON kuisioner_summary.id = kuisioner_result.`summary_id`
                    WHERE kuisioner_result.label IS NOT NULL
                        AND kuisioner_result.kuis_id = ".$row->id."
                        AND kuisioner_result.status = 1 {$whereReview}
                        GROUP BY kuisioner_summary.`kondisi`;
                        ";
            $summ = DB::select($sql5);

            $total = array_sum(array_column($summ, 'total'));

            // $list[$key]->total = $total[0]->total;

            // $sql5 = "SELECT label, rating_color FROM kuisioner_summary WHERE kuis_id = '{$row->id}' AND deleted_by IS NULL";
            // $summ = DB::select($sql5);

            foreach ($summ as $keys => $rows) {
                // $sql6 = "
                //     SELECT count(*) AS jumlah FROM kuisioner_result 
                //     LEFT JOIN members ON members.id = kuisioner_result.member_id
                //     WHERE kuisioner_result.kuis_id = '{$row->id}' AND kuisioner_result.status = 1 AND kuisioner_result.label = '{$rows->label}' {$whereReview}
                // ";
                // $count = DB::select($sql6);

                $rows->count = $rows->total ?? 0;
                $rows->persen = ($total == '0') ? 0 : round($rows->count / $total, 1) * 100;
            }

            $list[$key]->label = $summ;
            $list[$key]->total = $total;
        }

        $finKuis = [];
        foreach ($list as $key => $row) {
            $finKuis[$key]['legend'] = $row->title;
            $finKuis[$key]['total'] = $row->total;

            foreach ($row->label as $keys => $rows) {
                $finKuis[$key]['label'][$keys] = $rows->label . ' ' . $rows->persen . ' %';
                $finKuis[$key]['color'][$keys] = $rows->rating_color;
                $finKuis[$key]['value'][$keys] = $rows->persen;
                $finKuis[$key]['raw'][$keys] = $rows->count;
            }
        }

        return response()->json([
            'data' => $finKuis
        ]);

        die();
    }

}
