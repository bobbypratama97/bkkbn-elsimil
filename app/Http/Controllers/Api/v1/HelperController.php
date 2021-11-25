<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Helper;

use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Kelurahan;
use App\Penduduk;
use App\Member;

class HelperController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function provinsi(Request $request) {
        $data = Provinsi::whereNull('deleted_by')->orderBy('nama', 'asc')->get();

        return response()->json([
            'code' => 200,
            'error'   => true,
            'data' => $data
        ], 200);
    }

    public function kabupaten(Request $request) {
        if (!empty($request->code)) {
            $data = Kabupaten::whereNull('deleted_by')->where('provinsi_kode', $request->code)->orderBy('nama', 'asc')->get()->toArray();
        } else if (!empty($request->provinsi_code)) {
            $data = Kabupaten::whereNull('deleted_by')->where('provinsi_kode', $request->provinsi_code)->orderBy('nama', 'asc')->get()->toArray();
        } else {
            $data = Kabupaten::whereNull('deleted_by')->get();
        }

        return response()->json([
            'code' => 200,
            'error'   => true,
            'data' => $data
        ], 200);
    }

    public function kecamatan(Request $request) {
        if (!empty($request->code)) {
            $data = Kecamatan::whereNull('deleted_by')->where('kabupaten_kode', $request->code)->orderBy('nama', 'asc')->get()->toArray();
        } else if (!empty($request->kabupaten_code)) {
            $data = Kecamatan::whereNull('deleted_by')->where('kabupaten_kode', $request->kabupaten_code)->orderBy('nama', 'asc')->get()->toArray();
        } else {
            $data = Kecamatan::whereNull('deleted_by')->get();
        }

        return response()->json([
            'code' => 200,
            'error'   => true,
            'data' => $data
        ], 200);
    }

    public function kelurahan(Request $request) {
        if (!empty($request->code)) {
            $data = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $request->code)->orderBy('nama', 'asc')->get()->toArray();
        } else if (!empty($request->kecamatan_code)) {
            $data = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $request->kecamatan_code)->orderBy('nama', 'asc')->get()->toArray();
        } else {
            $data = Kelurahan::whereNull('deleted_by')->get();
        }

        return response()->json([
            'code' => 200,
            'error'   => true,
            'data' => $data
        ], 200);
    }

    public function checknik(Request $request) {

        $nik = Helper::dcNik($request->nik);

        $member = Member::where('no_ktp', 'LIKE', '%' . $nik . '%')->first();

        if ($member) {
            return response()->json([
                'code' => 401,
                'error' => true,
                'title' => 'Perhatian',
                'message' => 'No KTP sudah terdaftar. Silahkan login.'
            ], 401);
        }

        $data = Penduduk::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_penduduk.provinsi_kode', '=', 'adms_provinsi.provinsi_kode');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_penduduk.kabupaten_kode', '=', 'adms_kabupaten.kabupaten_kode');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_penduduk.kecamatan_kode', '=', 'adms_kecamatan.kecamatan_kode');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_penduduk.kelurahan_kode', '=', 'adms_kelurahan.kelurahan_kode');
        })
        ->where('nik', 'LIKE', '%' . $nik . '%')
        ->whereNull('adms_penduduk.deleted_by')
        ->select([
            'adms_penduduk.nama',
            'adms_penduduk.tgl_lahir',
            'adms_provinsi.provinsi_kode',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.kabupaten_kode',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.kecamatan_kode',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.kelurahan_kode',
            'adms_kelurahan.nama as kelurahan',
            'adms_penduduk.rw_kode',
            'adms_penduduk.rt_kode'
        ])
        ->first();

        $output = [];
        if ($data) {
            return response()->json([
                'code' => 200,
                'error'   => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'code' => 401,
                'error' => true,
                'title' => 'Perhatian',
                'message' => 'No KTP tidak ditemukan. Silahkan melanjutkan proses registrasi dengan mengisi data diri Anda'
            ], 401);
        }
    }
}
