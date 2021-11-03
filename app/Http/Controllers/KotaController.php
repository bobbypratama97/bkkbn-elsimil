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

use App\Kabupaten;

class KotaController extends Controller
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
        $this->authorize('access', [\App\Kota::class, Auth::user()->role, 'index']);

        $kota = Kabupaten::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'adms_kabupaten.created_by');
        })
        ->select(['adms_provinsi.nama as provinsi', 'adms_kabupaten.*', 'users.name'])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->get();

        return view('kota.index', ['kota' => $kota]);
    }

    public function create() {
    }

    public function store(Request $request) {
    }

    public function edit($id) {
        $this->authorize('access', [\App\Kota::class, Auth::user()->role, 'edit']);

        $status = Helper::status();

        $kota = Kabupaten::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->select(['adms_provinsi.nama as provinsi', 'adms_kabupaten.*'])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->where('adms_kabupaten.id', $id)
        ->first();

        return view('kota.edit', compact('kota', 'status'));
    }

    public function update(Request $request, $id) {
        $update = Kabupaten::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kabupaten berhasil diubah';
            return redirect()->route('admin.kota.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kabupaten gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Kabupaten::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kabupaten berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kabupaten gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }
}
