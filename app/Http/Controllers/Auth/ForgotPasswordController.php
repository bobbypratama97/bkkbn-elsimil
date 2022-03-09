<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Redirect;
use Helper;

use App\User;
use App\Member;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    function index(Request $request) {
        if ($request->tipe == 1) {
            if(!Helper::phoneNumber($request->email)){
                //cek by email
                $check = User::where('email', $request->email)->whereRaw('deleted_at is null')->first();
            }else{
                //cek by no hp
                $check = User::where('no_telp', Helper::phoneNumber($request->email))->whereRaw('deleted_at is null')->first();
            }
            // $check = User::where('email', $request->email)->orWhere('no_telp', Helper::phoneNumber($request->email))->first();
        } else {
            $check = Member::where('email', $request->email)->whereRaw('deleted_at is null')->first();
        }

        if (empty($check)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Pengiriman link gagal', 
                    'keterangan' => 'Email atau Nomor Telepon yang Anda masukkan salah. Silahkan ulangi kembali.'
                ]);
        } else {
            if($check->email == null){
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Pengiriman link gagal', 
                        'keterangan' => 'Kami tidak dapat mengirimkan akses link perubahan password dikarenakan Email Anda belum didaftarkan. Silakan hubungi Admin Anda untuk meminta password baru.'
                    ]);
            }

            try {
                Helper::sendMail([
                    'id' => $check->id, 
                    'tipe' => $request->tipe, 
                    'name' => $check->name, 
                    'email' => $check->email, 
                    'url' => 'cpw'
                ]);
            } catch (\Throwable $th) {
                //throw $th;
            }

            return view('auth.suksesfg', ['email' => $check->email]);
        }
    }
}
