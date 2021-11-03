<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\News;
use App\NewsKategori;
use App\NewsKategoriMapping;

class NewsKategoriController extends Controller
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
        $kategori = NewsKategori::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'news_kategori.created_by');
        })
        ->whereNull('news_kategori.deleted_by')
        ->select([
            'news_kategori.id',
            'news_kategori.name',
            'news_kategori.deskripsi',
            'news_kategori.status',
            'news_kategori.created_at',
            'users.name as nama'
        ])
        ->get();

        return view('newskategori.index', ['kategori' => $kategori]);
    }

    public function create() {
        $status = Helper::status();
        return view('newskategori.create', ['status' => $status]);
    }

    public function store(Request $request) {
        $check = NewsKategori::whereNull('deleted_by')->where('name', $request->name)->first();
        if ($check) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Nama kategori sudah digunakan. Silahkan gunakan nama kategori yang lain'
                ]);
        }

        $data = new NewsKategori();
        $data->name = $request->name;
        $data->deskripsi = $request->deskripsi;
        $data->status = $request->publikasi;
        $data->created_by = Auth::id();
        $data->created_at = date('Y-m-d H:i:s');

        if ($data->save()) {
            $msg = 'Kategori ' . $data->name . ' berhasil dibuat';
            return redirect()->route('newskategori.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kategori gagal dibuat. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function edit($id) {
        $data = NewsKategori::findOrFail($id);
        $status = Helper::status();

        return view('newskategori.edit', compact('data', 'status'));
    }

    public function update(Request $request, $id) {
        $check = NewsKategori::whereNull('deleted_by')->where('name', $request->name)->where('id', '!=', $id)->first();

        if ($check) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Nama kategori sudah digunakan. Silahkan gunakan nama kategori yang lain'
                ]);
        }

        $update = NewsKategori::where('id', $id)->update([
            'name' => $request->name,
            'deskripsi' => $request->deskripsi,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kategori berhasil diubah';
            return redirect()->route('newskategori.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kategori gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = NewsKategori::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kategori berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kategori gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }
}
