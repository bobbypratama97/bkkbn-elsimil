<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Helper;
use Session;
use Redirect;
use Hash;

use App\User;
use App\UserRole;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;
    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected function redirectTo() {
        //$role = UserRole::getACL(Auth::id());
        //Session::put('role', 'ABC');
        //Session::save();

        return '/';
    }

    public function login(Request $request) {
        $email = $request->get($this->username());
        
        if($this->username() == 'no_telp' && $email != 0) {
            $no_telp = Helper::phoneNumber($email);
            $email = (int)$no_telp;
        }

        $user = User::where($this->username(), $email)->first();
        $password = $request->password;
        // return $user;
        if (empty($user)) {
            return redirect()->back()
                ->withInput($request->only($this->username()))
                ->withErrors([
                    'error' => 'Login gagal',
                    'keterangan' => 'Data yang Anda masukkan salah. Silahkan ulangi kembali.'
                ]);
        } else if (!Hash::check($password, $user->password, [])) {
            return redirect()->back()
                ->withInput($request->only($this->username()))
                ->withErrors([
                    'error' => 'Login gagal',
                    'keterangan' => 'Data yang Anda masukkan salah. Silahkan ulangi kembali.'
                ]);
        } else {
            if ($user->is_active == '0') {
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        'error' => 'Akun Anda belum aktif.',
                        'keterangan' => 'Silahkan verifikasi email yang kami kirimkan terlebih dahulu.'
                    ]);
            } else if ($user->is_active == '2') {
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        'suspend' => 'Akun Anda telah disuspend.',
                        'keterangan' => 'Silahkan menghubungi Customer Service untuk aktivasi kembali.'
                    ]);
            } else if ($user->is_active == '3') {
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        'error' => 'Akun dalam proses menunggu approval.',
                        'keterangan' => 'Mohon bersabar menunggu sampai proses approval selesai.'
                    ]);
            } else {
                if (Auth::attempt([$this->username() => $email, 'password' => $password])) {
                    $user = Auth::user();
                    Session::forget('role');

                    $role = UserRole::getACL(Auth::id());

                    Session::put('role', $role);
                    Session::save();

                    return Redirect::intended('/');
                }
            }
        }

        /*if (Auth::attempt([$this->username() => $email, 'password' => $password])) {
            $user = Auth::user();
            echo '<pre>'; print_r ($user);
            echo $user->is_active;
            die;
            if ($user->is_active == '0') {
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        'error' => 'Akun Anda belum aktif.',
                        'keterangan' => 'Silahkan verifikasi email yang kami kirimkan terlebih dahulu.'
                    ]);
            } else if ($user->is_active == '2') {
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        'suspend' => 'Akun Anda telah disuspend.',
                        'keterangan' => 'Silahkan menghubungi Customer Service untuk aktivasi kembali.'
                    ]);
            } else if ($user->is_active == '3') {
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        'error' => 'Akun Anda dalam proses menunggu approval.',
                        'keterangan' => 'Silahkan menghubungi Customer Service untuk aktivasi kembali.'
                    ]);
            } else {
                return Redirect::intended('/');
            }
        } else {
            return redirect()->back()
                ->withInput($request->only($this->username()))
                ->withErrors([
                    'error' => 'Login gagal',
                    'keterangan' => 'Data yang Anda masukkan salah. Silahkan ulangi kembali.'
                ]);
        }*/

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function getCredentials(Request $request)
    {
        return [
            'username' => $request->username,
            'password' => $request->input('password')
        ];
    }

    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'no_telp';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

}
