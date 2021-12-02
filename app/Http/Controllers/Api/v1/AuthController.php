<?php

namespace App\Http\Controllers\Api\v1;

// use App\Helpers\Helper as HelpersHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Illuminate\Support\Facades\Validator;

use Image;
use Helper;

use App\Member;
use App\MemberOnesignal;
use App\Helpers;

class AuthController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function register(Request $request) {
        try {
            $messages = array(
                'email.email' => 'Email tidak sesuai format.'
            );

            if (empty($request->no_telp)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Nomor telepon kosong.'
                ], 401);
            }

            if (empty($request->email)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Email kosong.'
                ], 401);
            }

            $validators = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255']
            ], $messages);

            if ($validators->fails()) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => $validators->errors()->first(),
                ], 401);
            }

            $email = $request->email;

            list($username, $domain) = explode('@', $email);
            if(!in_array($domain, $this->accept_email)){
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Mohon dipastikan kembali email yang anda masukan tidak ada kesalahan penulisan.'
                ], 401);
            }

            $existPhone = Member::where('no_telp', substr($request->no_telp, 1))->first();

            if ($existPhone) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Nomor telepon sudah terdaftar. Silahkan gunakan nomor telepon yang lain.'
                ], 401);
            }

            $existEmail = Member::where('email', $request->email)->first();

            if ($existEmail) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Email sudah terdaftar. Silahkan gunakan email yang lain.'
                ], 401);
            }

            $cekKtp = Helper::dcNik($request->no_ktp);
            $existKtp = Member::where('no_ktp', 'LIKE', '%' . $cekKtp . '%')->first();

            if ($existKtp) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'No KTP yang anda berikan sudah pernah terdaftar, Silahkan Login atau Klik Lupa Password jika anda tidak mengingat nya.'
                ], 401);
            }

            if (strlen($request->no_ktp) > 16) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'No KTP lebih dari 16 digit. Silahkan perbaiki terlebih dahulu nomor KTP Anda.'
                ], 401);
            }

            // Upload Gambar
            if (!empty($request->foto_ktp)) {
                $smPath = public_path('uploads/ktp/thumbnail/sm');
                $mdPath = public_path('uploads/ktp/thumbnail/md');
                $oriPath = public_path('uploads/ktp/ori');
    
                $original = '';
                $output = [];
    
                $filename = $request->foto_name;
                $image = base64_decode($request->foto_ktp);
    
                $smThumb = 'sm_' . $filename;
                $mdThumb = 'md_' . $filename;
                $original = $filename;
    
                $imgThumb = Image::make($image);
                $imgThumb->resize(150, 150, function($constraint) {
                    $constraint->aspectRatio();
                })->orientate();
                $imgThumb->stream('jpg', 100);
                $imgThumb->save($smPath . '/' . $smThumb);
    
                $imgMid = Image::make($image);
                $imgMid->resize(300, 300, function($constraint) {
                    $constraint->aspectRatio();
                })->orientate();
                //$imgMid->stream('jpg', 100);
                $imgMid->save($mdPath . '/' . $mdThumb);
    
                $imgOri = Image::make($image);
                //$imgOri->stream('jpg', 100);
                $imgOri->save($oriPath . '/' . $original);
            }


            // Insert data
            $password = Hash::make($request->password);

            $profile_code = Helper::randomString(8);

            $user = new Member;
            $user->name = ucwords($request->name);
            $user->no_telp = $request->no_telp;
            $user->email = $request->email;
            $user->password = $password;
            $user->no_ktp = Helper::encryptNik($request->no_ktp);
            $user->foto_ktp = $request->foto_name;
            $user->foto_pic = 'noimage.png';
            //$user->foto_ktp = '';
            $user->tempat_lahir = $request->tempat_lahir;
            $user->tgl_lahir = $request->tgl_lahir;
            $user->gender = $request->gender;
            $user->alamat = $request->alamat;
            $user->provinsi_id = $request->provinsi_id;
            $user->kabupaten_id = $request->kabupaten_id;
            $user->kecamatan_id = $request->kecamatan_id;
            $user->kelurahan_id = $request->kelurahan_id;
            $user->rt = $request->rt;
            $user->rw = $request->rw;
            $user->kodepos = $request->kodepos;
            $user->is_active = 1; //urgent auto active // $user->is_active = 4 waiting verify;
            $user->profile_code = $profile_code;
            $user->rencana_pernikahan;
            $user->created_at = date('Y-m-d H:i:s');

            if ($user->save()) {

                $onesignal = new MemberOnesignal;
                $onesignal->member_id = $user->id;
                $onesignal->player_id = $request->player_id;
                $onesignal->imei = $request->imei;
                $onesignal->status = 1;
                $onesignal->created_at = date('Y-m-d H:i:s');
                $onesignal->created_by = $user->id;

                $onesignal->save();

                // Helper::sendMail([
                //     'id' => $user->id, 
                //     'tipe' => 2, 
                //     'name' => $user->name, 
                //     'email' => $user->email, 
                //     'url' => 'vrf'
                // ]);
            }

            return response()->json([
                'code' => 200,
                'error'   => false,
                // 'message' => 'Registrasi berhasil. Kami telah mengirimkan link verifikasi ke email Anda. Silahkan login untuk memulai penggunaan aplikasi.'
                'message' => 'Selamat akun anda sudah aktif silahkan login dengan email/no tlp dan pasword anda.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 409,
                'error' => true,
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function login(Request $request) {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'no_telp';
        $request->merge([$field => $request->input('username')]);

        $credentials = $request->only([$field, 'password']);

        $data = [];

        if($field == 'no_telp') {
            $no_telp = Helper::phoneNumber($request->input('username'));
            $credentials[$field] = (int)$no_telp;
        }
        // return $credentials;
        try {
            if (!$token = auth($this->guard)->attempt($credentials)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Username atau password salah'
                ], 401);
            } else {
                $data = auth($this->guard)->user();
                if ($data->is_active == '3') {
                    return response()->json([
                        'code' => 401,
                        'error' => true,
                        'title' => 'Akun Anda telah disuspend',
                        'message' => 'Silahkan menghubungi Customer Service untuk aktivasi kembali ya'
                    ], 401);
                } else {
                    $create_token = Member::where('id', $data->id)->update(['remember_token' => $token]);

                    $check = MemberOnesignal::where('member_id', $data->id)->where('player_id', $request->player_id)->select(['member_id', 'player_id'])->first();

                    if (empty($check)) {
                        $insert = new MemberOnesignal;
                        $insert->member_id = $data->id;
                        $insert->player_id = $request->player_id;
                        $insert->imei = $request->imei;
                        $insert->status = 1;
                        $insert->created_at = date('Y-m-d H:i:s');
                        $insert->created_by = $data->id;

                        $insert->save();

                        $update = MemberOnesignal::where('member_id', $data->id)->where('player_id', '!=', $request->player_id)->update([
                            'status' => '0',
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $data->id
                        ]);
                    } else {
                        $update = MemberOnesignal::where('member_id', $data->id)->where('player_id', $request->player_id)->update([
                            'status' => '1',
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $data->id
                        ]);
                    }
                }
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'code' => 401,
                'error' => true,
                'message' => 'token_expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'code' => 401,
                'error' => true, 
                'message' => 'token_invalid'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'code' => 500,
                'error' => true,
                'message' => 'token_missing'
            ], 500);
        }

        return $this->respondWithToken($data, $token);
    }

    public function logout(Request $request) {
        $token = $request->header('Authorization');

        try {
            JWTAuth::parseToken()->invalidate($token);

            return response()->json([
                'code' => 200,
                'error'   => false,
                'message' => trans('auth.logged_out')
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => trans('auth.token.expired')
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => trans('auth.token.invalid')
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'code' => 500,
                'error'   => true,
                'message' => trans('auth.token.missing')
            ], 500);
        }
    }

    public function forgot(Request $request) {
        $check = Member::where('email', $request->email)->first();

        if ($check) {
            Helper::sendMail([
                'id' => $check->id, 
                'tipe' => 2, 
                'name' => $check->name, 
                'email' => $check->email, 
                'url' => 'cpw'
            ]);

            return response()->json([
                'code' => 200,
                'error'   => true,
                'message' => 'Email pengubahan password telah kami kirimkan. Silahkan periksa email Anda untuk proses selanjutnya'
            ], 200);

        } else {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => 'Email belum terdaftar. Silahkan registrasi terlebih dahulu'
            ], 401);
        }
    }

    public function resend(Request $request) {
        $check = Member::where('email', $request->email)->first();

        if ($check) {
            Helper::sendMail([
                'id' => $check->id, 
                'tipe' => $request->tipe, 
                'name' => $check->name, 
                'email' => $check->email, 
                'url' => 'vrf'
            ]);

            return response()->json([
                'code' => 200,
                'error'   => true,
                'message' => 'Email verifikasi telah kami kirimkan. Silahkan periksa email Anda untuk proses selanjutnya'
            ], 200);

        } else {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => 'Email belum terdaftar. Silahkan registrasi terlebih dahulu'
            ], 401);
        }

    }

    public function emailcheck(Request $request) {
        $messages = array(
            'email.email' => 'Email tidak sesuai format.'
        );
        $validators = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255']
        ], $messages);

        if ($validators->fails()) {
            return response()->json([
                'code' => 401,
                'error' => true,
                'message' => $validators->errors()->first(),
            ], 401);
        }

        $email = $request->email;
        list($username, $domain) = explode('@', $email);
        if(!in_array($domain, $this->accept_email)){
            return response()->json([
                'code' => 200,
                'error'   => true,
                'message' => 'Mohon dipastikan email anda sudah benar!. Klik Lanjut jika yakin.'
            ], 200);
        }

        $check = Member::where('email', $request->email)->first();

        if ($check) {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => 'Email sudah terdaftar. Silahkan login untuk menikmati layanan kami'
            ], 401);
        } else {
            return response()->json([
                'code' => 200,
                'error'   => false,
                'message' => 'Email tersedia'
            ], 200);
        }
    }

    public function checkverify(Request $request) {
        $check = Member::where('id', $request->id)->first();

        if (empty($check->email_verified_at) || $check->is_active == '4') {
            return response()->json([
                'code' => 401,
                'error'   => true,
                'message' => 'Akun Anda belum diverifikasi. Silahkan verifikasi terlebih dahulu melalui link yang telah kami kirimkan ke email Anda.'
            ], 401);
        } else {
            return response()->json([
                'code' => 200,
                'error'   => false,
                'message' => 'Verified'
            ], 200);
        }
    }

    protected function respondWithToken($data, $token)
    {
        return response()->json([
            'code' => 200,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($this->guard)->factory()->getTTL() * 60,
            'message' => 'Anda berhasil login',
            'data' => $data
        ], 200);
    }
}