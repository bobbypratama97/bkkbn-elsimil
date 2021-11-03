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

use App\Page;

class PageController extends Controller
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
        $this->authorize('access', [\App\Page::class, Auth::user()->role, 'index']);

        $page = Page::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'page.created_by');
        })
        ->whereNull('page.deleted_by')
        ->orderBy('page.id', 'DESC')
        ->select([
            'page.id', 'page.title', 'page.slug', 'page.content', 'page.status', 'page.created_at',
            'users.name as nama'
        ])->get();

        return view('page.index', compact('page'));
    }

    public function create() {
        $this->authorize('access', [\App\Page::class, Auth::user()->role, 'create']);

        $status = Helper::status();
        return view('page.create', compact('status'));
    }

    public function store(Request $request) {
        $page = new Page;
        $page->title = $request->title;
        $page->content = $request->deskripsi;
        $page->status = $request->publikasi;
        $page->created_by = Auth::id();
        $page->created_at = date('Y-m-d H:i:s');

        if ($page->save()) {
            $slug = Helper::createSlug('page', $request->title, $page->id);
            $updateSlug = Page::where('id', $page->id)->update(['slug' => $slug]);

            return redirect()->route('admin.page.index')->with('success', 'Page telah ditambahkan');
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Page gagal ditambahkan. Silahkan coba beberapa saat lagi'
                ]);
        }

    }

    public function edit($id)
    {
        $this->authorize('access', [\App\Page::class, Auth::user()->role, 'edit']);

        $page = Page::findOrFail($id);
        $status = Helper::status();

        return view('page.edit', ['page' => $page, 'status' => $status]);
    }

    public function update(Request $request, $id)
    {
        $update = Page::where('id', $id)
            ->update([
                'title' => $request->title,
                'content' => $request->deskripsi,
                'status' => $request->publikasi,
                'updated_by' => Auth::id(),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        if ($update) {
            $slug = Helper::createSlug('page', $request->title, $id);
            $updateSlug = Page::where('id', $id)->update(['slug' => $slug]);

            return redirect()->route('admin.page.index')->with('success', 'Page telah diubah');
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Page gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function show($id)
    {
        $this->authorize('access', [\App\Page::class, Auth::user()->role, 'show']);

        $page = Page::findOrFail($id);
        $status = Helper::status();

        return view('page.show', ['page' => $page, 'status' => $status]);
    }

    public function delete(Request $request) {
        $update = Page::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Page berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Page gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

}
