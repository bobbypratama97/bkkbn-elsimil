<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Hash;
use OneSignal;

use DB;
use Image;
use Helper;

use App\Member;
use App\MemberCouple;
use App\MemberCoupleApproval;
use App\MemberOnesignal;

use App\ChatHeader;
use App\ChatMessage;

use App\KuisResult;

use App\NotificationLog;

use App\MemberDelegate;
use App\MemberDelegateLog;

class AkunController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function index(Request $request) {
        try {

            $pic_url = env('BASE_URL') . env('BASE_URL_PROFILE');

            $data = [];

            $res = Member::leftJoin('adms_provinsi', function($join) {
                $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->leftJoin('adms_kecamatan', function($join) {
                $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
            })
            ->leftJoin('adms_kelurahan', function($join) {
                $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
            })
            ->select([
                'members.id',
                'members.name',
                'members.no_telp',
                'members.email',
                'members.no_ktp',
                DB::raw("concat('{$pic_url}', members.foto_pic) AS pic"),
                'members.tempat_lahir',
                'members.tgl_lahir',
                'members.gender',
                'members.alamat',
                'adms_provinsi.provinsi_kode as kode_provinsi',
                'adms_provinsi.nama as nama_provinsi',
                'adms_kabupaten.kabupaten_kode as kode_kabupaten',
                'adms_kabupaten.nama as nama_kabupaten',
                'adms_kecamatan.kecamatan_kode as kode_kecamatan',
                'adms_kecamatan.nama as nama_kecamatan',
                'adms_kelurahan.kelurahan_kode as kode_kelurahan',
                'adms_kelurahan.nama as nama_kelurahan',
                'members.rw',
                'members.rt',
                'members.kodepos',
                'members.rencana_pernikahan',
                'members.profile_code as profile_id'
            ])
            ->where('members.id', $request->id)
            ->first();

            if ($res) {
                $now = date('Y-m-d');
                $tanggal = ucwords(Helper::diffDate($now, $res->tgl_lahir, 'y'));
                $res->usia = $tanggal;
                $res->no_ktp = Helper::decryptNik($res->no_ktp);

                $data = $res;
            }

            return response()->json([
                'code' => 200,
                'error'   => false,
                'data' => $data
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function updateprofile(Request $request) {
        try {

            $existPhone = Member::where('no_telp', substr($request->phone, 1))->where('id', '!=', $request->id)->first();

            if ($existPhone) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Nomor telepon sudah terdaftar. Silahkan gunakan nomor telepon yang lain.'
                ], 401);
            }

            $existEmail = Member::where('email', $request->email)->where('id', '!=', $request->id)->first();

            if ($existEmail) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Email sudah terdaftar. Silahkan gunakan email yang lain.'
                ], 401);
            }

            // Upload Foto
            $originalPic = '';
            if (!empty($request->foto_pic)) {
                $oriPathPic = public_path('uploads/member');
    
                $originalPic = '';
                $output = [];
    
                $filenamePic = $request->pic_name;
                $imagePic = base64_decode($request->foto_pic);
    
                $originalPic = $filenamePic;
    
                $imgOriPic = Image::make($imagePic);
                $imgOriPic->save($oriPathPic . '/' . $originalPic);
            }

            $check = Member::where('id', $request->id)->first();

            if ($check->foto_pic == 'noimage.png') {
                if ($originalPic == '') {
                    $imgLatest = 'noimage.png';
                } else {
                    $imgLatest = $originalPic;
                }
            } else {
                if (!empty($originalPic)) {
                    $imgLatest = $originalPic;
                } else {
                    $imgLatest = $check->foto_pic;
                }
            }

            $update = Member::where('id', $request->id)->update([
                'name' => ucwords($request->name),
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                //'no_ktp' => $request->no_ktp,
                'foto_pic' => $imgLatest,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
                'provinsi_id' => $request->provinsi_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kecamatan_id' => $request->kecamatan_id,
                'kelurahan_id' => $request->kelurahan_id,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'rencana_pernikahan' => $request->rencana_pernikahan,
                'kodepos' => $request->kodepos,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $request->id
            ]);

            if ($update) {
                return response()->json([
                    'code' => 200,
                    'error'   => true,
                    'message' => 'Data diri Anda telah diperbaharui'
                ], 200);
            } else {
                return response()->json([
                    'code' => 200,
                    'error'   => true,
                    'message' => 'Pengubahan data diri gagal. Silahkan coba beberapa saat lagi'
                ], 200);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function changepassword(Request $request) {
        try {

            $user = Member::where('id', $request->id)->first();

            if(!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Password lama salah. Mohon pastikan kembali'
                ], 401);
            }

            $password = Hash::make($request->new_password);

            $update = Member::where('id', $request->id)->update([
                'password' => $password,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $request->id
            ]);

            if ($update) {
                return response()->json([
                    'code' => 200,
                    'error'   => true,
                    'message' => 'Password Anda telah diperbaharui'
                ], 200);
            } else {
                return response()->json([
                    'code' => 200,
                    'error'   => true,
                    'message' => 'Pengubahan password gagal. Silahkan coba beberapa saat lagi'
                ], 200);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function couplelist(Request $request) {
        try {

            $pic_url = env('BASE_URL') . env('BASE_URL_PROFILE');
            $now = date('Y-m-d');

            $pasangan = MemberCouple::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'member_couple.couple_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->where('member_id', $request->id)
            ->where('member_couple.status', '=', 'APM200')
            ->select([
                'members.id',
                'members.name',
                'members.tgl_lahir',
                DB::raw("concat('{$pic_url}', members.foto_pic) AS pic"),
                'adms_kabupaten.nama as kota'
            ])
            ->orderBy('member_couple.id', 'DESC')
            ->get();

            $data = [];
            if ($pasangan->isNotEmpty()) {
                foreach ($pasangan as $key => $val) {
                    $data[] = [
                        'id' => $val->id,
                        'name' => $val->name,
                        'tgl_lahir' => ucwords(Helper::diffDate($now, $val->tgl_lahir, 'y')),
                        'pic' => $val->pic,
                        'kota' => $val->kota
                    ];
                }
            }

            return response()->json([
                'code' => 200,
                'error'   => false,
                'data' => $data
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }

    }

    public function addcouple(Request $request) {
        try {

            $cekKtp = Helper::dcNik($request->no_ktp);
            $check = Member::where('no_ktp', 'LIKE', '%' . $cekKtp . '%')->first();

            if (empty($check)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Nomor KTP tidak ditemukan. Periksa kembali nomor KTP pasangan Anda'
                ], 401);
            }

            $checkid = Member::where('profile_code', strtoupper($request->profile_id))->first();

            if (empty($checkid)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Nomor ID pasangan tidak terdaftar. Periksa kembali nomor ID pasangan Anda'
                ], 401);
            }

            $checkall = Member::where('no_ktp', 'LIKE', '%' . $cekKtp . '%')->where('profile_code', strtoupper($request->profile_id))->first();

            if (empty($checkall)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Nomor KTP dan Nomor ID pasangan tidak match. Silahkan masukkan data yang valid'
                ], 401);
            }

            $memberGender = Member::where('id', $request->id)->first();
            $coupleGender = Member::where('id', $checkall->id)->first();

            if (empty($memberGender->gender)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Jenis kelamin kamu tidak diketahui. Silahkan lengkapi data diri terlebih dahulu'
                ], 401);
            } else if (empty($coupleGender->gender)) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Jenis kelamin pasangan kamu tidak diketahui. Silahkan informasikan kepada calon pasangan kamu untuk melengkapi data diri terlebih dahulu'
                ], 401);
            } else if ($memberGender->gender == $coupleGender->gender) {
                return response()->json([
                    'code' => 401,
                    'error' => true,
                    'title' => 'Perhatian',
                    'message' => 'Jenis kelamin pasangan tidak boleh sama dengan jenis kelamin kamu'
                ], 401);
            }

            $checkexist = MemberCouple::where('member_id', $request->id)->where('couple_id', $checkall->id)->first();
            if ($checkexist) {
                if ($checkexist->status == 'APM100') {
                    return response()->json([
                        'code' => 401,
                        'error' => true,
                        'title' => 'Perhatian',
                        'message' => 'Kamu sudah pernah mengirimkan permintaan pasangan sebelumnya. Silahkan menunggu konfirmasi permintaan sebelumnya'
                    ], 401);
                }
                if ($checkexist->status == 'APM200') {
                    return response()->json([
                        'code' => 401,
                        'error' => true,
                        'title' => 'Perhatian',
                        'message' => 'Pengajuan kamu tidak diproses. Kamu sudah menjadi pasangan dengan member ini'
                    ], 401);
                }
            }

            if ($memberGender->gender == '2') {
                $lihatstatus = MemberCouple::where('member_id', $memberGender->id)->first();
                if ($lihatstatus) {
                    if ($lihatstatus->status == 'APM200') {
                        return response()->json([
                            'code' => 401,
                            'error' => true,
                            'title' => 'Perhatian',
                            'message' => 'Kamu sudah punya pasangan. Kamu tidak bisa menambahkan pasangan baru lagi'
                        ], 401);
                    }
                }
            }

            if ($coupleGender->gender == '2') {
                $lihatstatus = MemberCouple::where('member_id', $coupleGender->id)->first();
                if ($lihatstatus) {
                    if ($lihatstatus->status == 'APM200') {
                        return response()->json([
                            'code' => 401,
                            'error' => true,
                            'title' => 'Perhatian',
                            'message' => 'Permintaan pasangan kamu tidak dapat diproses. Orang yang kamu tuju sudah memiliki pasangan'
                        ], 401);
                    }
                }
            }


            $couple = new MemberCouple;
            $couple->member_id = $request->id;
            $couple->couple_id = $checkall->id;
            $couple->status = 'APM100';
            $couple->created_at = date('Y-m-d H:i:s');
            $couple->created_by = $request->id;

            if ($couple->save()) {
                $insertself = new NotificationLog;
                $insertself->member_id = $request->id;
                $insertself->jenis = 'Pasangan';
                $insertself->content = 'Kamu mengirimkan permintaan pasangan ke ' . $checkall->name . '.';
                $insertself->created_at = date('Y-m-d H:i:s');
                $insertself->created_by = $request->id;

                $insertself->save();

                $insertc = new NotificationLog;
                $insertc->member_id = $checkall->id;
                $insertc->jenis = 'Pasangan';
                $insertc->content = 'Seseorang mengirimkan kamu permintaan sebagai pasangan';
                $insertc->created_at = date('Y-m-d H:i:s');
                $insertc->created_by = $request->id;

                $insertc->save();

                $log = new MemberCoupleApproval;
                $log->member_couple_id = $couple->id;
                $log->status = 'APM100';
                $log->step = 0;
                $log->created_at = date('Y-m-d H:i:s');
                $log->created_by = $request->id;

                $log->save();

                $onesignal = MemberOnesignal::where('member_id', $checkall->id)->where('status', 1)->first();
                
                if (!empty($onesignal)) {
                    $send = OneSignal::sendNotificationToUser(
                        "Hii...Seseorang telah mengirimkan pengajuan pasangan. Klik disini untuk melihat.",
                        $onesignal->player_id,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null,
                        $headings = "Pengajuan pasangan"
                    );
                }

                return response()->json([
                    'code' => 200,
                    'error'   => false,
                    'message' => 'Permintaan pasangan sedang diproses. Silahkan menunggu konfirmasi dari pasangan Anda'
                ], 200);
            } else {
                return response()->json([
                    'code' => 401,
                    'error'   => true,
                    'message' => 'Saat ini permintaan pasangan gagal diproses. Silahkan coba beberapa saat lagi'
                ], 401);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function pendingcouple(Request $request) {
        try {

            $pic_url = env('BASE_URL') . env('BASE_URL_PROFILE');
            $now = date('Y-m-d');

            // permintaan pasangan
            $pendinglist = MemberCouple::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'member_couple.member_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->where('member_couple.couple_id', $request->id)
            ->where('member_couple.status', '=', 'APM100')
            ->select([
                'members.id',
                DB::raw("concat('{$pic_url}', members.foto_pic) AS pic"),
                'members.name',
                'members.tgl_lahir',
                'adms_kabupaten.nama as kota'
            ])
            ->get();

            $pending = [];
            foreach ($pendinglist as $key => $val) {
                $pending[] = [
                    'request_id' => $val->id,
                    'pic' => $val->pic,
                    'name' => $val->name,
                    'tgl_lahir' => ucwords(Helper::diffDate($now, $val->tgl_lahir, 'y')),
                    'kota' => $val->kota
                ];
            }

            // untuk menunggu konfirmasi
            $waitinglist = MemberCouple::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'member_couple.couple_id');
            })
            ->leftJoin('adms_kabupaten', function($join) {
                $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
            })
            ->where('member_couple.member_id', $request->id)
            ->where('member_couple.status', '=', 'APM100')
            ->select([
                'members.id',
                DB::raw("concat('{$pic_url}', members.foto_pic) AS pic"),
                'members.name',
                'members.tgl_lahir',
                'adms_kabupaten.nama as kota'
            ])
            ->get();

            $waiting = [];
            foreach ($waitinglist as $key => $val) {
                $waiting[] = [
                    'sender_id' => $val->id,
                    'pic' => $val->pic,
                    'name' => $val->name,
                    'tgl_lahir' => ucwords(Helper::diffDate($now, $val->tgl_lahir, 'y')),
                    'kota' => $val->kota
                ];
            }

            $data = [
                'pending' => $pending,
                'waiting' => $waiting
            ];

            return response()->json([
                'code' => 200,
                'error'   => false,
                'data' => $data
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function confirmcouple(Request $request) {

        try {

            $status = ($request->action == 'terima') ? 'APM200' : 'APM300';

            $submit = MemberCouple::where('member_id', $request->request_id)->where('couple_id', $request->id)->update([
                'status' => ($request->action == 'terima') ? 'APM200' :  'APM300',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $request->id
            ]);

            $onesignal = MemberOnesignal::where('member_id', $request->request_id)->where('status', 1)->first();
            
            if (!empty($onesignal)) {
                if ($request->action == 'terima') {
                    $memberOne = Member::where('id', $request->request_id)->first();

                    $insertOne = new NotificationLog;
                    $insertOne->member_id = $request->id;
                    $insertOne->jenis = 'Pasangan';
                    $insertOne->content = 'Kamu menyetujui permintaan pasangan dari ' . $memberOne->name .'.';
                    $insertOne->created_at = date('Y-m-d H:i:s');
                    $insertOne->created_by = $request->id;
                    $insertOne->save();

                    $memberTwo = Member::where('id', $request->id)->first();

                    $insertTwo = new NotificationLog;
                    $insertTwo->member_id = $request->request_id;
                    $insertTwo->jenis = 'Pasangan';
                    $insertTwo->content = 'Permintaan pasangan kamu diterima oleh ' . $memberTwo->name . '.';
                    $insertTwo->created_at = date('Y-m-d H:i:s');
                    $insertTwo->created_by = $request->id;
                    $insertTwo->save();

                //$kuis = KuisResult::whereNull('responder_id')->where('member_id', $request->id)->first();
                /*if ($kuis) {
                    $updatekuis = KuisResult::whereNull('responder_id')->where('member_id', $request->id)->update([
                        'responder_id' => Auth::id(),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);
                }*/


                    /*$send = OneSignal::sendNotificationToUser(
                        "Yeay, pengajuan pasangan kamu telah diterima. Klik disini untuk melihat.",
                        $onesignal->player_id,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null,
                        $headings = "Pengajuan pasangan"
                    );*/

                    if (!empty($onesignal->player_id)) {
                        $parameters = [
                            'include_player_ids' => [$onesignal->player_id],
                            'headings' => [
                                'en' => "Pengajuan pasangan"
                            ],
                            'contents' => [
                                'en' => "Yeay, pengajuan pasangan kamu telah diterima. Klik disini untuk melihat."
                            ],
                            'ios_badgeType'  => 'Increase',
                            'ios_badgeCount' => 1,
                            //'included_segments' => array('All')
                        ];
                        $send = OneSignal::sendNotificationCustom($parameters);
                    }

                } else {
                    $memberOne = Member::where('id', $request->request_id)->first();

                    $insertOne = new NotificationLog;
                    $insertOne->member_id = $request->id;
                    $insertOne->jenis = 'Pasangan';
                    $insertOne->content = 'Kamu menolak permintaan pasangan dari ' . $memberOne->name .'.';
                    $insertOne->created_at = date('Y-m-d H:i:s');
                    $insertOne->created_by = $request->id;
                    $insertOne->save();

                    $memberTwo = Member::where('id', $request->id)->first();

                    $insertTwo = new NotificationLog;
                    $insertTwo->member_id = $request->request_id;
                    $insertTwo->jenis = 'Pasangan';
                    $insertTwo->content = 'Permintaan pasangan kamu ditolak oleh ' . $memberTwo->name . '.';
                    $insertTwo->created_at = date('Y-m-d H:i:s');
                    $insertTwo->created_by = $request->id;
                    $insertTwo->save();

                    /*$send = OneSignal::sendNotificationToUser(
                        "Hiks, sayang sekali pengajuan pasangan kamu ditolak.",
                        $onesignal->player_id,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null,
                        $headings = "Pengajuan pasangan"
                    );*/

                    if (!empty($onesignal->player_id)) {
                        $parameters = [
                            'include_player_ids' => [$onesignal->player_id],
                            'headings' => [
                                'en' => "Pengajuan pasangan"
                            ],
                            'contents' => [
                                'en' => "Hiks, sayang sekali pengajuan pasangan kamu ditolak."
                            ],
                            'ios_badgeType'  => 'Increase',
                            'ios_badgeCount' => 1,
                            //'included_segments' => array('All')
                        ];
                        $send = OneSignal::sendNotificationCustom($parameters);
                    }

                }
            }

            if ($submit) {
                if ($request->action == 'terima') {
                    $insert = new MemberCouple;
                    $insert->member_id = $request->id;
                    $insert->couple_id = $request->request_id;
                    $insert->status = 'APM200';
                    $insert->created_at = date('Y-m-d H:i:s');
                    $insert->created_by = $request->id;

                    $insert->save();

					// 
					// 
					// $insertTwo = new MemberCouple;
     //                $insertTwo->member_id = $request->request_id;
     //                $insertTwo->couple_id =$request->id;
     //                $insertTwo->status = 'APM100';
     //                $insertTwo->created_at = date('Y-m-d H:i:s');
     //                $insertTwo->created_by = $request->request_id;
					// 
     //                $insertTwo->save();
					
					

                    $self = Member::leftJoin('member_delegate', function($join) {
                        $join->on('member_delegate.member_id', '=', 'members.id');
                    })
                    ->where('members.id', $request->id)
                    ->select([
                        'members.*',
                        'member_delegate.user_id',
                        'member_delegate.created_at as tanggal'
                    ])
                    ->first();

                    $requester = Member::leftJoin('member_delegate', function($join) {
                        $join->on('member_delegate.member_id', '=', 'members.id');
                    })
                    ->where('members.id', $request->request_id)
                    ->select([
                        'members.*',
                        'member_delegate.user_id',
                        'member_delegate.created_at as tanggal'
                    ])
                    ->first();

                    $selfDataRegister = $self->tanggal;
                    $selfDataHandle = $self->user_id;

                    $requesterDataRegister = $requester->tanggal;
                    $requesterDataHandle = $requester->user_id;

                    $actived = 0;
                    if (!empty($selfDataRegister) && !empty($requesterDataRegister)) {
                        $selfTgl = strtotime($selfDataRegister);
                        $requesterTgl = strtotime($requesterDataRegister);

                        if ($selfTgl < $requesterTgl) {
                            $usedId = $requesterDataHandle;
                        } else if ($selfTgl > $requesterTgl) {
                            $usedId = $selfDataHandle;
                        } else {
                            $usedId = $selfDataHandle;
                        }

                        $actived = 1;
                    } else if (!empty($selfDataRegister) && empty($requesterDataRegister)) {
                        $usedId = $selfDataHandle;
                        $actived = 1;
                    } else if (empty($selfDataRegister) && !empty($requesterDataRegister)) {
                        $usedId = $requesterDataHandle;
                        $actived = 1;
                    } else {
                        $actived = 0;
                    }

                    if ($actived == 1) {

                        // itself
                        $cekSelf = MemberDelegate::where('member_id', $request->id)->first();

                        if ($cekSelf) {
                            $updateSelf = MemberDelegate::where('member_id', $request->id)->update([
                                'user_id' => $usedId,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $request->id
                            ]);
                        } else {
                            $insertSelf = new MemberDelegate;
                            $insertSelf->member_id = $request->id;
                            $insertSelf->user_id = $usedId;
                            $insertSelf->created_at = date('Y-m-d H:i:s');
                            $insertSelf->created_by = $request->id;

                            $insertSelf->save();

                        }

                        $logSelf = new MemberDelegateLog;
                        $logSelf->member_id = $request->id;
                        $logSelf->user_id = $usedId;
                        $logSelf->created_at = date('Y-m-d H:i:s');
                        $logSelf->created_by = $request->id;

                        $logSelf->save();


                        $chat = ChatHeader::where('member_id', $request->id)->first();

                        if ($chat) {
                            $updated = ChatHeader::where('member_id', $request->id)->update([
                                'responder_id' => $usedId,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $request->id
                            ]);
                        }

                        $get = KuisResult::where('member_id', $request->id)->where('status', 1)->first();
                        if ($get) {
                            $updated = KuisResult::where('member_id', $request->id)->where('status', 1)->update([
                                'responder_id' => $usedId,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $request->id
                            ]);
                        }


                        // requester
                        $cekRequester = MemberDelegate::where('member_id', $request->request_id)->first();

                        if ($cekRequester) {
                            $updateRequester = MemberDelegate::where('member_id', $request->request_id)->update([
                                'user_id' => $usedId,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $request->id
                            ]);
                        } else {
                            $insertRequester = new MemberDelegate;
                            $insertRequester->member_id = $request->request_id;
                            $insertRequester->user_id = $usedId;
                            $insertRequester->created_at = date('Y-m-d H:i:s');
                            $insertRequester->created_by = $request->id;

                            $insertRequester->save();
                        }

                        $logRequester = new MemberDelegateLog;
                        $logRequester->member_id = $request->request_id;
                        $logRequester->user_id = $usedId;
                        $logRequester->created_at = date('Y-m-d H:i:s');
                        $logRequester->created_by = $request->id;

                        $logRequester->save();


                        $chat = ChatHeader::where('member_id', $request->request_id)->first();

                        if ($chat) {
                            $updated = ChatHeader::where('member_id', $request->request_id)->update([
                                'responder_id' => $usedId,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $request->id
                            ]);
                        }

                        $get = KuisResult::where('member_id', $request->request_id)->where('status', 1)->first();
                        if ($get) {
                            $updated = KuisResult::where('member_id', $request->request_id)->where('status', 1)->update([
                                'responder_id' => $usedId,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => $request->id
                            ]);
                        }


                    }

                }

                return response()->json([
                    'code' => 200,
                    'error'   => false,
                    'message' => 'Permintaan pasangan telah selesai diproses'
                ], 200);
            } else {
                return response()->json([
                    'code' => 401,
                    'error'   => true,
                    'message' => 'Permintaan pasangan gagal diproses. Silahkan coba beberapa saat lagi'
                ], 401);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

    public function infonotif(Request $request) {
        try {
            $check = ChatMessage::select('status')->where('member_id', $request->id)->orderBy('id', 'DESC')->first();

            if ($check) {
                $point = ($check->status == 'reply') ? 1 : 0;
            } else {
                $point = 0;
            }

            return response()->json([
                'code' => 200,
                'error'   => false,
                'data' => ['status' => $point]
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(Helper::apiexpiredResponse($data), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(Helper::apiinvalidResponse($data), 401);
        } catch (JWTException $e) {
            return response()->json(Helper::apiexceptionResponse($data), 500);
        }
    }

}
