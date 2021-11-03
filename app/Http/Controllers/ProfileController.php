<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Hash;
use Session;

use Helper;

use App\User;

class ProfileController extends Controller
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
        return view('profile.index');
    }

    public function store(Request $request) {

        $user = User::where('id', Auth::id())->first();

        if ($request->password != $request->confirm) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Password baru dan konfirmasi password tidak sama. Silahkan diulangi kembali'
                ]);
        } else if (!Hash::check($request->old, $user->password)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Password lama salah'
                ]);
        } else {
            $password = Hash::make($request->password);

            $update = User::where('id', Auth::id())->update([
                'password' => $password,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            if ($update) {
                return redirect()->back()->with('success', 'Pengubahan password berhasil');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Perhatian', 
                        'keterangan' => 'Update password gagal. Silahkan coba kembali beberapa saat lagi'
                    ]);
            }
        }

    }

}
