<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

use OneSignal;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Notifikasi;
use App\NotificationLog;
use App\TempImage;

class NotifikasiController extends Controller
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
        $this->authorize('access', [\App\Notifikasi::class, Auth::user()->role, 'index']);

        $notifikasi = Notifikasi::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'notifikasi.created_by');
        })
        ->whereNull('notifikasi.deleted_by')
        ->select([
            'notifikasi.*',
            'users.name as nama'
        ])
        ->get();

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function create() {
        $this->authorize('access', [\App\Notifikasi::class, Auth::user()->role, 'create']);

        return view('notifikasi.create');
    }

    public function store(Request $request) {
        $data = new Notifikasi();
        $data->title = $request->title;
        $data->content = $request->content;
        $data->image = (!empty($request->thumbnail)) ? $request->thumbnail : 'no_image.png';
        $data->created_by = Auth::id();
        $data->created_at = date('Y-m-d H:i:s');

        if ($data->save()) {
            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'notifikasi')->delete();

            $msg = 'Notifikasi ' . $data->title . ' berhasil dibuat';
            return redirect()->route('admin.notifikasi.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Notifikasi gagal dibuat. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function edit($id) {
        $this->authorize('access', [\App\Notifikasi::class, Auth::user()->role, 'edit']);

        $data = Notifikasi::findOrFail($id);

        return view('notifikasi.edit', compact('data'));
    }

    public function update(Request $request, $id) {
        $update = Notifikasi::where('id', $id)->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => (!empty($request->thumbnail)) ? $request->thumbnail : 'no_image.png',
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'notifikasi')->delete();

            $msg = 'Notifikasi berhasil diubah';
            return redirect()->route('admin.notifikasi.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Notifikasi gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Notifikasi::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Notifikasi berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Notifikasi gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function send(Request $request) {
        $pic_url = env('BASE_URL') . env('BASE_URL_NOTIF');

        $data = Notifikasi::where('id', $request->id)->first();

        $insert = new NotificationLog;
        $insert->jenis = $data->title;
        $insert->content = $data->content;
        $insert->created_at = date('Y-m-d H:i:s');
        $insert->created_by = Auth::id();
        $insert->save();

        if ($data->image != 'no_image.png') {
            $parameters = [
                //'include_player_ids' => ['65727e7c-3ccc-40b1-8d50-592deac3f3f2'],
                'headings' => [
                    'en' => $data->title
                ],
                'contents' => [
                    'en' => $data->content
                ],
                'big_picture' => $pic_url . $data->image,
                'ios_attachments' => [
                    "id" => $pic_url . $data->image
                ],
                'ios_badgeType'  => 'Increase',
                'ios_badgeCount' => 1,
                'included_segments' => array('All')
            ];
        } else {
            $parameters = [
                //'include_player_ids' => ['65727e7c-3ccc-40b1-8d50-592deac3f3f2'],
                'headings' => [
                    'en' => $data->title
                ],
                'contents' => [
                    'en' => $data->content
                ],
                'ios_badgeType'  => 'Increase',
                'ios_badgeCount' => 1,
                'included_segments' => array('All')
            ];
        }

        $send = OneSignal::sendNotificationCustom($parameters);

        if ($send) {
            $msg = 'Notifikasi telah dikirimkan';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Notifikasi gagal dikirimkan. Silahkan coba beberapa saat lagi';
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
                $path = public_path() . '/uploads/notif/' . $check->filename;
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

            $oriPath = public_path('uploads/notif');

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
                $path = public_path() . '/uploads/notif/' . $check->filename;
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
