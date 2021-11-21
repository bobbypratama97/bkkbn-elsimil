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

use App\TempImage;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
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
    public function index(Request $request)
    {
        $this->authorize('access', [\App\Kategori::class, Auth::user()->role, 'index']);

        $kategori = NewsKategori::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'news_kategori.created_by');
        })
        ->whereNull('news_kategori.deleted_by');

        $kategori = $kategori
        ->select([
            'news_kategori.id',
            'news_kategori.name',
            'news_kategori.deskripsi',
            'news_kategori.thumbnail',
            'news_kategori.color', 
            'news_kategori.status',
            'news_kategori.created_at',
            'users.name as nama'
        ]);

        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $kategori = $kategori->where('news_kategori.name', 'like', '%' . $request->name . '%')
                ->orWhere('news_kategori.deskripsi', 'like', '%' . $request->name . '%')
                ->orWhere('users.name', 'like', '%' . $request->name . '%');
        }

        $paginate = $kategori->orderBy('position')
        ->paginate(10);

        $kategori = $paginate->items();

        return view('kategori.index',compact('kategori', 'name', 'paginate'));
    }

    public function create() {
        $this->authorize('access', [\App\Kategori::class, Auth::user()->role, 'create']);

        $status = Helper::status();
        return view('kategori.create', ['status' => $status]);
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

        $get = NewsKategori::select('position')->orderBy('position', 'desc')->first();

        $urutan = ($get) ? $get->position + 1 : 1;

        $data = new NewsKategori();
        $data->name = $request->name;
        $data->thumbnail = (!empty($request->thumbnail)) ? $request->thumbnail : 'no_thumb.png';
        $data->color = $request->warna;
        $data->deskripsi = $request->deskripsi;
        $data->status = $request->publikasi;
        $data->position = $urutan;
        $data->created_by = Auth::id();
        $data->created_at = date('Y-m-d H:i:s');

        if ($data->save()) {

            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'kategori')->delete();

            $msg = 'Kategori ' . $data->name . ' berhasil dibuat';
            return redirect()->route('admin.kategori.index')->with('success', $msg);
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
        $this->authorize('access', [\App\Kategori::class, Auth::user()->role, 'edit']);

        $data = NewsKategori::findOrFail($id);
        $status = Helper::status();

        return view('kategori.edit', compact('data', 'status'));
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
            'color' => $request->warna,
            'thumbnail' => (!empty($request->thumbnail)) ? $request->thumbnail : 'no_thumb.png',
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'kategori')->delete();

            $msg = 'Kategori berhasil diubah';
            return redirect()->route('admin.kategori.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kategori gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function sort() {
        $this->authorize('access', [\App\Kategori::class, Auth::user()->role, 'sort']);

        $kategori = NewsKategori::whereNull('deleted_by')->where('status', 2)->select(['id', 'name'])->orderBy('position')->get();

        return view('kategori.sort', compact('kategori'));
    }

    public function submit(Request $request) {
        $position = str_replace(['"', '[', ']'], ['', '', ''], $request->position);

        $explode = explode(',', $position);
        $i = 1;
        foreach ($explode as $row) {
            $update = NewsKategori::where('id', $row)->update([
                'position' => $i,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $i++;
        }

        $msg = 'Sorting kategori berhasil dilakukan.';
        return redirect()->route('admin.kategori.index')->with('success', $msg);
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

    public function upload(Request $request) {

        $output = [];

        if ($request->action == 'upload') {

            $check = TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->first();

            if (!empty($check)) {
                $path = public_path() . '/uploads/artikel_kategori/' . $check->filename;
                if (file_exists($path)) {
                    unlink($path);
                }

                TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->delete();
            }

            $time = time();
            $filenamewithextension = $request->file('file')->getClientOriginalName();
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension; 

            $foto = $request->file('file');

            $original = $filename;

            $oriPath = public_path('uploads/artikel_kategori');

            Image::make($foto->getRealPath())->save($oriPath . '/' . $original);

            $image = new TempImage;
            $image->jenis = $request->jenis;
            $image->user_id = Auth::id();
            $image->module = $request->module;
            $image->filename = $original;
            $image->created_at = date('Y-m-d H:i:s');
            $image->created_by = Auth::id();

            if ($image->save()) {
                $output['msg'] = 'Gambar berhasil diupload';
                $output['image'] = $original;
            } else {
                $output['msg'] = 'Gambar gagal diupload. Silahkan coba beberapa saat lagi';
                $output['image'] = '';
            }

        }

        if ($request->action == 'delete') {
            $check = TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->first();

            if (!empty($check)) {
                $path = public_path() . '/uploads/artikel_kategori/' . $check->filename;
                if (file_exists($path)) {
                    unlink($path);
                }

                TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->delete();

                $output['image'] = '';
            }
        }

        return json_encode($output);

        die();
    }

}
