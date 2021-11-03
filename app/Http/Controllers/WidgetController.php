<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Auth;
use DB;
use Redirect;

use Helper;

use App\Widget;
use App\Faskes;

class WidgetController extends Controller
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
        $this->authorize('access', [\App\Widget::class, Auth::user()->role, 'index']);

        $widget = Widget::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'widgets.created_by');
        })
        ->whereNull('widgets.deleted_by')
        ->select(['widgets.*', 'users.name as nama'])
        ->get();

        return view('widget.index', compact('widget'));
    }

    public function edit($id) {
        $this->authorize('access', [\App\Widget::class, Auth::user()->role, 'edit']);

        $status = Helper::status();
        $widget = Widget::where('id', $id)->first();

        return view('widget.edit', compact('widget', 'status'));
    }

    public function update(Request $request, $id) {
        $update = Widget::where('id', $id)->update([
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Pengubahan status publikasi widget berhasil.';
            return redirect()->route('admin.widget.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Pengubahan status publikasi widget gagal. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function faskeswidget(Request $request) {
        $faskes = FasKes::whereNull('deleted_by')->select(['nama'])->get();
        print_r ($faskes);
    }

}
