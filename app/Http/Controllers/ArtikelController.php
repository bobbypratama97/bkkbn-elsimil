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
use Helper;

use App\News;
use App\NewsKategori;
use App\NewsKategoriMapping;
use App\FotoArtikelTemp;

use App\TempImage;

class ArtikelController extends Controller
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
        $this->authorize('access', [\App\Artikel::class, Auth::user()->role, 'index']);

        $paginate = News::leftJoin('news_kategori as nk', function($join) {
            $join->on('nk.id', '=', 'news.kategori_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'news.created_by');
        })
        ->whereNull('news.deleted_by')
        ->whereNull('nk.deleted_by');

        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $paginate = $paginate->where('nk.name', 'like', '%' . $request->name . '%')
                ->orWhere('news.title', 'like', '%' . $request->name . '%')
                ->orWhere('users.name', 'like', '%' . $request->name . '%');
        }

        $paginate = $paginate
        ->orderBy('news.id', 'DESC')
        ->select([
            'news.id', 'news.title', 'news.thumbnail', 'news.status', 'news.created_at',
            'nk.name as parent',
            'users.name as nama'
        ])->paginate(10);

        $news = $paginate->items();

        return view('artikel.index', ['news' => $news, 'paginate'=> $paginate]);
    }

    public function create() {
        $this->authorize('access', [\App\Artikel::class, Auth::user()->role, 'create']);

        $kategori = NewsKategori::whereNull('deleted_by')->where('status', 2)->get();
        $status = Helper::status();
        return view('artikel.create', ['kategori' => $kategori, 'status' => $status]);
    }

    public function store(Request $request) {
        $original = '';
        $output = [];

        //$getThumb = FotoArtikelTemp::where('user_id', Auth::id())->where('tipe', 'thumbnail')->select('filename')->first();
        //$getReal = FotoArtikelTemp::where('user_id', Auth::id())->where('tipe', 'full')->select('filename')->first();

        $news = new News();
        $news->kategori_id = $request['kategori_id'];
        $news->title = $request['title'];
        $news->thumbnail = (!empty($request->thumbnail)) ? $request->thumbnail : 'no_thumb.png';
        $news->gambar = (!empty($request->original)) ? $request->original : 'no_original.png';
        $news->deskripsi = $request['deskripsi'];
        $news->content = $request['content'];
        $news->status = $request['publikasi'];
        $news->created_at = date('Y-m-d H:i:s');
        $news->created_by = Auth::id();

        if ($news->save()) {
            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'artikel')->delete();

            $msg = 'Artikel berhasil ditambahkan';
            return redirect()->route('admin.artikel.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Penambahan artikel gagal. Silahkan coba beberapa saat lagi'
                ]);
        }

        return json_encode($output);

        die();
    }

    public function edit($id) {
        $this->authorize('access', [\App\Artikel::class, Auth::user()->role, 'edit']);

        $data = News::findOrFail($id);
        $kategori = NewsKategori::where('status', 2)->whereNull('deleted_by')->get();
        $status = Helper::status();

        return view('artikel.edit', compact('kategori', 'data', 'status'));
    }

    public function update(Request $request, $id) {
        $original = '';
        $output = [];

        $update = News::where('id', $id)
            ->update([
                'kategori_id' => $request->kategori_id,
                'title' => $request->title,
                'thumbnail' => (!empty($request->thumbnail)) ? $request->thumbnail : 'no_thumb.png',
                'gambar' => (!empty($request->original)) ? $request->original : 'no_original.png',
                'deskripsi' => $request->deskripsi,
                'content' => $request->content,
                'status' => $request->publikasi,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

        if ($update) {
            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'artikel')->delete();

            $msg = 'Pengubahan artikel berhasil dilakukan.';
            return redirect()->route('admin.artikel.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Pengubahan artikel gagal dibuat. Silahkan coba beberapa saat lagi'
                ]);
        }

    }

    public function show($id) {
        $this->authorize('access', [\App\Artikel::class, Auth::user()->role, 'show']);

        $news = News::leftJoin('news_kategori as nk', function($join) {
            $join->on('nk.id', '=', 'news.kategori_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'news.created_by');
        })
        ->whereNull('news.deleted_by')
        ->whereNull('nk.deleted_by')
        ->where('news.id', $id)
        ->orderBy('news.id', 'DESC')
        ->select([
            'news.id', 'news.title', 'news.thumbnail', 'news.gambar', 'news.deskripsi', 'news.content', 'news.status', 'news.created_at',
            'nk.name as parent',
            'users.name as nama'
        ])->first();

        return view('artikel.show', ['news' => $news]);
    }

    public function delete(Request $request) {
        $update = News::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Artikel berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Artikel gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function upload(Request $request) {

        $smPath = public_path('uploads/artikel/thumbnail/sm');
        $mdPath = public_path('uploads/artikel/thumbnail/md');
        $oriPath = public_path('uploads/artikel/ori');

        $original = '';
        $output = [];

        if ($request->action == 'upload') {
            $check = TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->first();

            if (!empty($check)) {
                $pathSm = public_path() . '/uploads/artikel/thumbnail/sm/sm_' . $check->filename;
                $pathMd = public_path() . '/uploads/artikel/thumbnail/md/md_' . $check->filename;
                $pathOri = public_path() . '/uploads/artikel/ori/' . $check->filename;
                if (file_exists($pathSm)) {
                    unlink($pathSm);
                }
                if (file_exists($pathMd)) {
                    unlink($pathMd);
                }
                if (file_exists($pathOri)) {
                    unlink($pathOri);
                }

                TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->delete();
            }

            $time = time();
            $filenamewithextension = $request->file('file')->getClientOriginalName();
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

            $foto = $request->file('file');

            $smThumb = 'sm_' . $filename;
            $mdThumb = 'md_' . $filename;
            $original = $filename;

            Image::make($foto->getRealPath())->resize(150, 150)->save($smPath . '/' . $smThumb);
            Image::make($foto->getRealPath())->resize(300, 300)->save($mdPath . '/' . $mdThumb);
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
                $pathSm = public_path() . '/uploads/artikel/thumbnail/sm/sm_' . $check->filename;
                $pathMd = public_path() . '/uploads/artikel/thumbnail/md/md_' . $check->filename;
                $pathOri = public_path() . '/uploads/artikel/ori/' . $check->filename;
                if (file_exists($pathSm)) {
                    unlink($pathSm);
                }
                if (file_exists($pathMd)) {
                    unlink($pathMd);
                }
                if (file_exists($pathOri)) {
                    unlink($pathOri);
                }

                TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->delete();

                $output['image'] = '';
            }

        }

        return json_encode($output);

        die();
    }

}
