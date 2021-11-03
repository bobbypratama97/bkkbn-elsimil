<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Member;
use App\MemberCouple;
use App\MemberDelegate;
use App\MemberDelegateLog;
use App\ChatHeader;
use App\KuisResult;

class MemberController extends Controller
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
    public function index(Request $request)
    {
        $this->authorize('access', [\App\Member::class, Auth::user()->role, 'index']);

        $auth = Auth::user();
        $role = Auth::user()->role;

        $self = Member::leftJoin('adms_provinsi', function($join) {
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
        ->leftJoin('member_delegate', function($join) {
            $join->on('members.id', '=', 'member_delegate.member_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'member_delegate.user_id');
        })
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'users.id as petugas_id',
            'users.name as petugas'
        ]);

        if ($role == '2') {
            $self = $self->where('members.provinsi_id', $auth->provinsi_id);
        }

        if ($role == '3') {
            $self = $self->where('members.provinsi_id', $auth->provinsi_id)->where('members.kabupaten_id', $auth->kabupaten_id);
        }

        if ($role == '4') {
            $self = $self->where('members.provinsi_id', $auth->provinsi_id)->where('members.kabupaten_id', $auth->kabupaten_id)->where('members.kecamatan_id', $auth->kecamatan_id);
        }

        $search = '';
        if (isset($request->s)) {
            if ($request->s == 'all') {
                $search = 'all';
            } else if ($request->s == 'h') {
                $self = $self->whereNotNull('member_delegate.user_id');
                $search = 'h';
            } else if ($request->s == 'nh') {
                $self = $self->whereNull('member_delegate.user_id');
                $search = 'nh';
            } else if ($request->s == 'm') {
                $self = $self->where('member_delegate.user_id', Auth::id());
                $search = 'm';
            }
        }


        $self = $self->get();

        foreach ($self as $key => $val) {
            $couple = MemberCouple::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'member_couple.couple_id');
            })
            ->where('member_couple.member_id', $val->id)
            ->where('member_couple.status', '=', 'APM200')
            ->select(['members.name as namapasangan'])
            ->get();

            $pasangan = '';
            if ($couple->isNotEmpty()) {
                foreach ($couple as $keys => $vals) {
                    $pasangan .= $vals->namapasangan . ', ';
                }
                $pasangan = substr($pasangan, 0, -2);
                $self[$key]->pasangan = $pasangan;
            }
        }

        $cop = [];
        foreach ($self as $keyz => $valz) {
            $cop[] = $valz->id;
        }

        $couples = Member::leftJoin('adms_provinsi', function($join) {
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
        ->leftJoin('member_delegate', function($join) {
            $join->on('members.id', '=', 'member_delegate.member_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'member_delegate.user_id');
        })
        ->leftJoin('member_couple', function($join) {
            $join->on('member_couple.couple_id', '=', 'members.id');
        })
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'users.id as petugas_id',
            'users.name as petugas'
        ])
        ->where('member_couple.status', '=', 'APM200')
        ->where('member_delegate.user_id', Auth::id())
        ->whereNotIn('member_delegate.member_id', $cop);


        $search = '';
        if (isset($request->s)) {
            if ($request->s == 'all') {
                $search = 'all';
            } else if ($request->s == 'h') {
                $couples = $couples->whereNotNull('member_delegate.user_id');
                $search = 'h';
            } else if ($request->s == 'nh') {
                $couples = $couples->whereNull('member_delegate.user_id');
                $search = 'nh';
            } else if ($request->s == 'm') {
                $couples = $couples->where('member_delegate.user_id', Auth::id());
                $search = 'm';
            }
        }

        $couples = $couples->get();

        foreach ($couples as $keyzz => $valzz) {
            $coups = MemberCouple::leftJoin('members', function($join) {
                $join->on('members.id', '=', 'member_couple.couple_id');
            })
            ->where('member_couple.member_id', $valzz->id)
            ->where('member_couple.status', '=', 'APM200')
            ->select(['members.name as namapasangancoup'])
            ->get();

            $pasangancoup = '';
            if ($coups->isNotEmpty()) {
                foreach ($coups as $keyss => $valss) {
                    $pasangancoup .= $valss->namapasangancoup . ', ';
                }
                $pasangancoup = substr($pasangancoup, 0, -2);
                $couples[$keyzz]->pasangan = $pasangancoup;
            }
        }

        $self = $self->toArray();
        $couples = $couples->toArray();

        $member = array_merge($self, $couples);

        return view('member.index', compact('member', 'search'));
    }

    public function result($id) {

        $member = Member::where('id', $id)->first();

        $res = KuisResult::select([
            'kuisioner_result.*',
            'kuisioner.title',
            'users.name as petugas',
            'kuisioner_result_comment.komentar'
        ])
        ->leftJoin('kuisioner', function($join) {
            $join->on('kuisioner.id', '=', 'kuisioner_result.kuis_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'kuisioner_result.responder_id');
        })
        ->leftJoin('kuisioner_result_comment', function($join) {
            $join->on('kuisioner_result_comment.result_id', '=', 'kuisioner_result.id');
        })
        ->where('kuisioner_result.status', 1)
        ->where('kuisioner_result.member_id', $id)
        ->get();

        return view('member.result', compact('member', 'res'));
    }

    public function show($id) {
        $this->authorize('access', [\App\Member::class, Auth::user()->role, 'show']);

        $baseurlmember = env('BASE_URL') . env('BASE_URL_PROFILE');
        $baseurlavatar = env('BASE_URL') . env('BASE_URL_CHAT');

        $member = Member::leftJoin('adms_provinsi', function($join) {
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
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan'
        ])
        ->where('members.id', $id)
        ->first();


        if (!empty($member->foto_pic)) {
            if ($member->foto_pic == 'noimage.png') {
                if ($member->gender == '2') {
                    $member->gambar = $baseurlavatar . '018-girl-9.svg';
                } else if ($member->gender == '1') {
                    $member->gambar = $baseurlavatar . '009-boy-4.svg';
                } else {
                    $member->gambar = $baseurlavatar . '024-boy-9.svg';
                }
            } else {
                $member->gambar = $baseurlmember . $member->foto_pic;
            }
        } else {
            if ($member->gender == '2') {
                $member->gambar = $baseurlavatar . '018-girl-9.svg';
            } else if ($member->gender == '1') {
                $member->gambar = $baseurlavatar . '009-boy-4.svg';
            } else {
                $member->gambar = $baseurlavatar . '024-boy-9.svg';
            }
        }



        $couple = MemberCouple::leftJoin('members', function($join) {
            $join->on('members.id', '=', 'member_couple.couple_id');
        })
        ->leftJoin('adms_provinsi', function($join) {
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
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan'
        ])
        ->where('member_couple.member_id', $id)
        ->orderBy('member_couple.id', 'DESC')
        ->first();

        if (!empty($couple)) {
            if (!empty($couple->foto_pic)) {
                if ($couple->foto_pic == 'noimage.png') {
                    if ($couple->gender == '2') {
                        $couple->gambar = $baseurlavatar . '018-girl-9.svg';
                    } else if ($couple->gender == '1') {
                        $couple->gambar = $baseurlavatar . '009-boy-4.svg';
                    } else {
                        $couple->gambar = $baseurlavatar . '024-boy-9.svg';
                    }
                } else {
                    $couple->gambar = $baseurlmember . $couple->foto_pic;
                }
            } else {
                if ($couple->gender == '2') {
                    $couple->gambar = $baseurlavatar . '018-girl-9.svg';
                } else if ($couple->gender == '1') {
                    $couple->gambar = $baseurlavatar . '009-boy-4.svg';
                } else {
                    $couple->gambar = $baseurlavatar . '024-boy-9.svg';
                }
            }
        }

        return view('member.show', compact('member', 'couple'));
    }

    public function blokir(Request $request) {
        $member = Member::where('id', $request->cid)->first();

        if ($member->is_active == '3') {
            $update = Member::where('id', $request->cid)->update([
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $blokir = 0;
        } else {
            $update = Member::where('id', $request->cid)->update([
                'is_active' => 3,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $blokir = 1;
        }

        $msg = ($blokir == 0) ? 'Pembukaan blokir catin berhasil.' : 'Pemblokiran catin berhasil';
        return redirect()->route('admin.member.index')->with('success', $msg);
    }

    public function kelola(Request $request) {
        $check = MemberDelegate::where('member_id', $request->id)->first();

        $output = [];
        if ($check) {
            $output = [
                'count' => 0,
                'message' => 'Proses setting pendampingan catin gagal.<br />Catin telah ditangani oleh petugas KB yang lain'
            ];
        } else {
            $insert = new MemberDelegate;
            $insert->member_id = $request->id;
            $insert->user_id = Auth::id();
            $insert->status = 1;
            $insert->created_at = date('Y-m-d H:i:s');
            $insert->created_by = Auth::id();

            if ($insert->save()) {

                $tambah = new MemberDelegateLog;
                $tambah->member_id = $request->id;
                $tambah->user_id = Auth::id();
                $tambah->created_at = date('Y-m-d H:i:s');
                $tambah->created_by = Auth::id();

                $tambah->save();

                $chat = ChatHeader::where('member_id', $request->id)->first();

                if ($chat) {
                    $updated = ChatHeader::where('member_id', $request->id)->update([
                        'responder_id' => Auth::id(),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);
                }

                $get = KuisResult::where('member_id', $request->id)->where('status', 1)->first();
                $updated = KuisResult::where('member_id', $request->id)->where('status', 1)->update([
                    'responder_id' => Auth::id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::id()
                ]);



                $check = MemberCouple::select(['couple_id'])->where('member_id', $request->id)->where('status', 'APM200')->get();

                if ($check->isNotEmpty()) {
                    foreach ($check as $keys => $rows) {
                        $cekdelegate = MemberDelegate::where('member_id', $rows->couple_id)->first();

                        if ($cekdelegate) {
                            $updateCouple = MemberDelegate::where('member_id', $rows->couple_id)->update([
                                'user_id' => Auth::id(),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::id()
                            ]);
                        } else {
                            $simpanCouple = new MemberDelegate;
                            $simpanCouple->member_id = $rows->couple_id;
                            $simpanCouple->user_id = Auth::id();
                            $simpanCouple->created_at = date('Y-m-d H:i:s');
                            $simpanCouple->created_by = Auth::id();

                            $simpanCouple->save();
                        }

                        $logCouple = new MemberDelegateLog;
                        $logCouple->member_id = $rows->couple_id;
                        $logCouple->user_id = Auth::id();
                        $logCouple->created_at = date('Y-m-d H:i:s');
                        $logCouple->created_by = Auth::id();

                        $logCouple->save();

                        $updateChatCouple = ChatHeader::where('member_id', $rows->couple_id)->update([
                            'responder_id' => Auth::id(),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::id()
                        ]);

                        $updateKuisCouple = KuisResult::where('member_id', $rows->couple_id)->where('status', 1)->update([
                            'responder_id' => Auth::id(),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::id()
                        ]);
                    }
                }


                //$kuis = KuisResult::whereNull('responder_id')->where('member_id', $request->id)->first();
                /*if ($kuis) {
                    $updatekuis = KuisResult::whereNull('responder_id')->where('member_id', $request->id)->update([
                        'responder_id' => Auth::id(),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);
                }*/

                if ($request->action == 'chat') {
                    $output = [
                        'count' => 1,
                        'message' => 'Catin telah disetting menjadi tanggung jawab Anda',
                        'url' => route('admin.chat.show', $request->chatid)
                    ];
                } else if ($request->action == 'kuis') {
                    $output = [
                        'count' => 1,
                        'message' => 'Catin telah disetting menjadi tanggung jawab Anda'
                    ];
                } else {
                    $output = [
                        'count' => 1,
                        'message' => 'Catin telah disetting menjadi tanggung jawab Anda'
                    ];
                }
            } else {
                $output = [
                    'count' => 0,
                    'message' => 'Proses setting pendampingan catin gagal. Silahkan coba beberapa saat lagi'
                ];
            }
        }

        return json_encode($output);

        die();
    }

}
