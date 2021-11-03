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

class NewsController extends Controller
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
        $news = News::leftJoin('news_kategori as nk', function($join) {
            $join->on('nk.id', '=', 'news.kategori_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'news.created_by');
        })
        ->whereNull('news.deleted_by')
        ->whereNull('nk.deleted_by')
        ->orderBy('news.id', 'DESC')
        ->select([
            'news.id', 'news.title', 'news.thumbnail', 'news.status', 'news.created_at',
            'nk.name as parent',
            'users.name as nama'
        ])->get();

        return view('news.index', ['news' => $news]);
    }

    public function create() {
        $kategori = NewsKategori::whereNull('deleted_by')->where('status', 2)->get();
        $status = Helper::status();
        return view('news.create', ['kategori' => $kategori, 'status' => $status]);
    }

    public function store(Request $request) {
        $original = '';
        $output = [];
        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                $time = time();
                $filenamewithextension = $request->file('file')->getClientOriginalName();
                $extension = $request->file('file')->getClientOriginalExtension();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension; 

                $foto = $request->file('file');

                $smThumb = 'sm_' . $filename;
                $mdThumb = 'md_' . $filename;
                $original = $filename;

                $smPath = public_path('uploads/artikel/thumbnail/sm');
                $mdPath = public_path('uploads/artikel/thumbnail/md');
                $oriPath = public_path('uploads/artikel/ori');

                /*$imgThumb = Image::make($image)->resize(150, 150, function($constraint) {
                    $constraint->aspectRatio();
                })->orientate();
                $imgThumb->stream('jpg', 100);
                $imgThumb->save($smPath . '/' . $smThumb);*/

                Image::make($foto->getRealPath())->resize(150, 150)->save($smPath . '/' . $smThumb);
                Image::make($foto->getRealPath())->resize(300, 300)->save($mdPath . '/' . $mdThumb);
                Image::make($foto->getRealPath())->save($oriPath . '/' . $original);
            }

            $news = new News();
            $news->kategori_id = $request->kategori_id;
            $news->title = $request->title;
            $news->thumbnail = $original;
            $news->deskripsi = $request->deskripsi;
            $news->content = $request->content;
            $news->status = $request->publikasi;
            $news->created_at = date('Y-m-d H:i:s');
            $news->created_by = Auth::id();

            if ($news->save()) {
                $msg = 'Artikel berhasil ditambahkan';
                $output = Helper::successResponse($msg);
            } else {
                $msg = 'Artikel gagal ditambahkan';
                $output = Helper::successResponse($msg);
            }
        }

        return json_encode($output);

        die();
    }

    public function edit($id) {
        $data = News::findOrFail($id);
        $kategori = NewsKategori::where('status', 2)->whereNull('deleted_by')->get();
        $status = Helper::status();

        return view('news.edit', compact('kategori', 'data', 'status'));
    }

    public function update(Request $request, $id) {
        $original = '';
        $output = [];

        if ($request->hasFile('file')) {
            //if ($request->file('file')->getClientOriginalName() != 'md_' . $request->image) {
            if ($request->file('file')->isValid()) {
                $time = time();
                $filenamewithextension = $request->file('file')->getClientOriginalName();
                $extension = $request->file('file')->getClientOriginalExtension();
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension; 

                $foto = $request->file('file');

                $smThumb = 'sm_' . $filename;
                $mdThumb = 'md_' . $filename;
                $original = $filename;

                $smPath = public_path('uploads/artikel/thumbnail/sm');
                $mdPath = public_path('uploads/artikel/thumbnail/md');
                $oriPath = public_path('uploads/artikel/ori');

                Image::make($foto->getRealPath())->resize(150, 150)->save($smPath . '/' . $smThumb);
                Image::make($foto->getRealPath())->resize(300, 300)->save($mdPath . '/' . $mdThumb);
                Image::make($foto->getRealPath())->save($oriPath . '/' . $original);
            }

            //} else {
            $original = $original;
            //}
        } else {
            $original = $request->image;
        }

        $update = News::where('id', $id)
            ->update([
                'kategori_id' => $request->kategori_id,
                'title' => $request->title,
                'thumbnail' => $original,
                'deskripsi' => $request->deskripsi,
                'content' => $request->content,
                'status' => $request->publikasi,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

        if ($update) {
            $msg = 'Artikel berhasil diubah';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Artikel gagal diubah. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }
        return json_encode($output);
        
        die();
    }

    public function show($id) {
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
            'news.id', 'news.title', 'news.thumbnail', 'news.deskripsi', 'news.content', 'news.status', 'news.created_at',
            'nk.name as parent',
            'users.name as nama'
        ])->first();

        return view('news.show', ['news' => $news]);
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
}
