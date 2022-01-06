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
            $check = User::where('email', $request->email)->first();
        } else {
            $check = Member::where('email', $request->email)->first();
        }

        if (empty($check)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Pengiriman link gagal', 
                    'keterangan' => 'Email yang Anda masukkan salah. Silahkan ulangi kembali.'
                ]);
        } else {
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

            return view('auth.suksesfg');
        }
    }
}
