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

use App\Kecamatan;

class KecamatanController extends Controller
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
        $this->authorize('access', [\App\Kecamatan::class, Auth::user()->role, 'index']);

        $camat = Kecamatan::leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'adms_kecamatan.kabupaten_kode');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'adms_kabupaten.created_by');
        })
        ->select([
            'adms_provinsi.nama as provinsi', 'adms_provinsi.provinsi_kode', 
            'adms_kabupaten.nama as kabupaten', 
            'adms_kecamatan.*', 
            'users.name'
        ])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->whereNull('adms_kecamatan.deleted_by')
        ->get();

        return view('kecamatan.index', ['camat' => $camat]);
    }

    public function create() {
    }

    public function store(Request $request) {
    }

    public function edit($id) {
        $this->authorize('access', [\App\Kecamatan::class, Auth::user()->role, 'edit']);

        $status = Helper::status();

        $camat = Kecamatan::leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'adms_kecamatan.kabupaten_kode');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->select([
            'adms_provinsi.nama as provinsi', 'adms_provinsi.provinsi_kode', 
            'adms_kabupaten.nama as kabupaten', 
            'adms_kecamatan.*'
        ])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->whereNull('adms_kecamatan.deleted_by')
        ->where('adms_kecamatan.id', $id)
        ->first();

        return view('kecamatan.edit', ['camat' => $camat, 'status' => $status]);
    }

    public function update(Request $request, $id) {
        $update = Kecamatan::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kecamatan berhasil diubah';
            return redirect()->route('admin.kecamatan.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kecamatan gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Kecamatan::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kecamatan berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kecamatan gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }
}
