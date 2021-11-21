<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Helper;

use App\User;
use App\UserRole;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/email/verify';
    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = array(
            //'nik.unique' => 'NIK sudah terdaftar. Silahkan Login jika Anda sudah terdaftar atau klik Lupa Password',
            'email.unique' => 'Email sudah terdaftar. Silahkan Login atau klik Lupa Password',
            'email.email' => 'Format email salah',
            'password.min' => 'Password minimal :min karakter',
        );

        return Validator::make($data, [
            //'nik' => ['required', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'provinsi_id' => ['required'],
            'kabupaten_id' => ['required'],
            'kecamatan_id' => ['required'],
            'kelurahan_id' => ['required']
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Registrasi gagal', 
                    'keterangan' => 'Data yang Anda masukkan salah. Silahkan ulangi kembali.'
                ]);
        } else {
            return [];
        }
    }

    public function register(\Illuminate\Http\Request $request) {
        $this->validator($request->all())->validate();

        //print_r ($request->all()); die;

        $user = new User;
        $user->nik = Helper::encryptNik($request->nik);
        // $user->nik = $request->nik;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_active = 3;
        $user->provinsi_id = $request->provinsi_id;
        $user->kabupaten_id = $request->kabupaten_id;
        $user->kecamatan_id = $request->kecamatan_id;
        $user->kelurahan_id = $request->kelurahan_id;

        $user->save();


        $insert = new UserRole;
        $insert->role_id = 4;
        $insert->user_id = $user->id;

        $insert->save();

        $output = [];
        /*if ($user->save()) {
            Helper::sendMail([
                'id' => $user->id, 
                'tipe' => 1, 
                'name' => $user->name, 
                'email' => $user->email, 
                'url' => 'vrf'
            ]);

            $output['email'] = $user->email;
        }*/

        return view('auth.suksesreg', compact('output'));

        //return redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    /*protected function create(array $data)
    {
        return User::create([
            'nik' => $data['nik'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => 0,
            'provinsi_id' => $data['provinsi_id'],
            'kabupaten_id' => $data['kabupaten_id'],
            'kecamatan_id' => $data['kecamatan_id'],
            'kelurahan_id' => $data['kelurahan_id']
        ]);
    }*/
}
