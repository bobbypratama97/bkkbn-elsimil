<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Helper;

use App\Member;
use App\User;
use App\UserRole;

use App\Kuis;
use App\Apply;
use App\Sso;

class VerifController extends Controller
{
    public function changepswd(Request $request) {
		$data['status'] = 0;
		$data['id'] = 0;

		if (isset($request->pwd)) {
	    	$req = explode('_', base64_decode($request->pwd));
	    	$expired = str_replace('lm-', '', $req[4]);

	    	date_default_timezone_set('Asia/Jakarta');
	    	$expired = date('Y-m-d H:i:s', $expired);
	    	$expired = strtotime($expired);

	    	/*if ($expired < time()) {
	    		$data['status'] = 0;
	    		$data['id'] = 0;
	    		$data['tipe'] = 0;
	    	} else {
	    		$data['status'] = 1;
	    		$data['id'] = $req[1];
	    		$data['tipe'] = $req[2];
	    	}*/
	    	$data['status'] = 1;
	    	$data['id'] = $req[1];
	    	$data['tipe'] = $req[2];
		}
		return view('auth.pwd', compact('data'));
    }

    public function submitchange(Request $request) {
    	$password = Hash::make($request->password);

    	$data = ['tipe' => $request->tipe];

    	if ($request->tipe == 1) {
	    	$update = User::where('id', $request->id)->update(
	    		[
	    			'password' => $password, 
	    			'remember_token' => '', 
	    			'updated_at' => date('Y-m-d H:i:s'), 
	    			'updated_by' => $request->id
	    		]
	    	);
    	} else {
	    	$update = Member::where('id', $request->id)->update(
	    		[
	    			'password' => $password, 
	    			'remember_token' => '', 
	    			'updated_at' => date('Y-m-d H:i:s'), 
	    			'updated_by' => $request->id
	    		]
	    	);
    	}

    	return view('auth.suksespwd', compact('data'));
    }

    public function verify(Request $request) {
		$data['status'] = 0;
		$data['id'] = 0;

		if (isset($request->pwd)) {
	    	$req = explode('_', base64_decode($request->pwd));
	    	//print_r ($req); die;
	    	$expired = str_replace('lm-', '', $req[4]);

	    	if ($req[2] == '1') {
	    		$checkstatus = User::where('id', $req[1])->first();

	    		$is_active = $checkstatus->is_active;

	    		if ($checkstatus->is_active != '4' && $checkstatus->is_active != '5' && $checkstatus->is_active != '3' && $checkstatus->is_active != '1') {
		    		$update = User::where('id', $req[1])->update([
		    			'is_active' => 3,
		    			'updated_by' => $req[1],
		    			'updated_at' => date('Y-m-d H:i:s'),
		    			'email_verified_at' => date('Y-m-d H:i:s')
		    		]);

		    		$check = UserRole::where('user_id', $req[1])->first();

		    		if (empty($check)) {
			    		$insert = new UserRole;
			    		$insert->role_id = 2;
			    		$insert->user_id = $req[1];

			    		$insert->save();
		    		}

		    		$is_active = 3;
	    		}
	    	} else {
	    		$update = Member::where('id', $req[1])->update([
	    			'is_active' => 1,
	    			'updated_by' => $req[1],
	    			'updated_at' => date('Y-m-d H:i:s'),
	    			'email_verified_at' => date('Y-m-d H:i:s')
	    		]);

	    		$is_active = 1;
	    	}

	    	date_default_timezone_set('Asia/Jakarta');
	    	$expired = date('Y-m-d H:i:s', $expired);
	    	$expired = strtotime($expired);

	    	/*if ($expired < time()) {
	    		$data['status'] = 0;
	    		$data['id'] = 0;
	    		$data['tipe'] = 0;
	    	} else {
	    		$data['status'] = 1;
	    		$data['id'] = $req[1];
	    		$data['tipe'] = $req[2];
	    	}*/
	    	$data['status'] = 1;
	    	$data['id'] = $req[1];
	    	$data['tipe'] = $req[2];
	    	$data['is_active'] = $is_active;
		}
		return view('auth.suksesverify', compact('data'));
    }

    public function resend(Request $request) {
    	$check = User::where('email', $request->email)->first();

        Helper::sendMail([
            'id' => $check->id, 
            'tipe' => 1, 
            'name' => $check->name, 
            'email' => $check->email, 
            'url' => 'vrf'
        ]);

        $output['email'] = $check->email;

        return view('auth.suksesreg', compact('output'));
    }

    public function approve(Request $request) {
    	if (isset($request->id)) {
    		//echo '<pre>';
    		//echo $request->id;
    		$req = explode('_', base64_decode($request->id));
    		//print_r ($req);
    		$id = $req[1];

            $clientIP = request()->ip();
            //echo $clientIP;

    		$detect = new \Mobile_Detect;
            $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

            //echo $deviceType;

            $kuis = Kuis::where('id', $id)->first();

            $apply = Apply::where('kuis_id', $id)->orderBy('id', 'DESC')->first();

            if ($apply->proceed_by != '0') {
            	$user = User::where('id', $apply->proceed_by)->select('name')->first();

            	$msg = 'Anda tidak dapat memproses approval kuesioner ini karena proses approval sedang dikerjakan oleh <span class="text-dark font-weight-bolder">' . $user->name . '</span>';
            	return view('auth.onprocess', compact('msg'));
            } else {
	            $check = Sso::where('ip_address', $clientIP)->where('device', $deviceType)->first();

	            if ($check) {
	            	return redirect()->route('admin.kuis.review', $id);
	            } else {
	            	return redirect()->route('admin.kuis.review', $id);
	            }
            }

    	}
    }

    public function lgn(Request $request) {
    	return redirect()->route('login');
    }

}
