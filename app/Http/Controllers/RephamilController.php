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

use App\Kuis;

use App\NotificationLog;

use App\UserRole;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Kelurahan;
use App\KuesionerHamil;

class RephamilController extends Controller
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
            $provinsi = Provinsi::whereNull('deleted_by')->orderBy('nama')->get();
        } else if($roles->role_id == '2') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->orderBy('nama')->get();
            $kabupaten = Kabupaten::where('provinsi_kode', $user->provinsi_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = [];//Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
            $kelurahan = [];//Kelurahan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
        } else if($roles->role_id == '3') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->orderBy('nama')->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = [];//Kelurahan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
        } else if($roles->role_id == '4') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->orderBy('nama')->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kecamatan_kode', $user->kecamatan_id)->orderBy('nama')->get();
        } else if($roles->role_id == '5') {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->orderBy('nama')->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kelurahan_kode', $user->kelurahan_id)->orderBy('nama')->get();
        } else {
            $provinsi = Provinsi::where('provinsi_kode', $user->provinsi_id)->orderBy('nama')->get();
            $kabupaten = Kabupaten::where('kabupaten_kode', $user->kabupaten_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kecamatan = Kecamatan::where('kecamatan_kode', $user->kecamatan_id)->whereNull('deleted_by')->orderBy('nama')->get();
            $kelurahan = Kelurahan::where('kelurahan_kode', $user->kelurahan_id)->orderBy('nama')->get();
        }

        $gender = Helper::statusGender();

        return view('rephamil.index', compact('gender', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'roles'));
    }

    public function search(Request $request){
        $kuishamils = KuesionerHamil::selectRaw('
                kuesioner_hamil.periode,
                sum(if(kuesioner_hamil.usia = "0", 1, kuesioner_hamil.usia >= 20 AND kuesioner_hamil.usia <= 35) && 
                    if(kuesioner_hamil.komplikasi = "", 1, kuesioner_hamil.komplikasi = "Ya") && 
                    if(kuesioner_hamil.asi = "", 1, kuesioner_hamil.asi = "Ya")) as sum_ideal,
                sum(if(kuesioner_hamil.usia = "0", 0, kuesioner_hamil.usia < 20 OR kuesioner_hamil.usia > 35) || 
                    if(kuesioner_hamil.komplikasi = "0", 0, kuesioner_hamil.komplikasi = "Tidak") || 
                    if(kuesioner_hamil.asi = "0", 0, kuesioner_hamil.asi = "Tidak")) as sum_resiko
            ')
            ->groupBy('kuesioner_hamil.periode')
            ->get();

        $res['data'] = [];
        foreach ($kuishamils as $kuis) {
            $total = $kuis['sum_ideal'] + $kuis['sum_resiko'];
            $val_ideal = round($kuis['sum_ideal'] / $total, 1) * 100;
            $val_resiko = round($kuis['sum_resiko'] / $total, 1) * 100;

            $res['data'][] = [
                'border_color' => ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
                'background_color' => ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                'total' => $total,
                'value' => [$kuis['sum_resiko'], $kuis['sum_ideal']],
                'label' => ['Ideal','Bersiko'],
                'legend' => 'Ideal: '.$val_ideal.'% |Beresiko: '.$val_resiko.'%',
                'text' => 'Periode '.$kuis['periode']
            ];
        }

        $res['count'] = count($res['data']);
        return $res;
    }
}
