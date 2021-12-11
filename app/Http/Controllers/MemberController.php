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
use App\Logbook;
use App\MemberCouple;
use App\MemberDelegate;
use App\MemberDelegateLog;
use App\ChatHeader;
use App\KuisResult;
use App\Kuis;
use App\KuisResultDetail;

use App\KuisHamilKontakAwal;
use App\KuisHamil12Minggu;
use App\KuisHamil16Minggu;
use App\KuisHamilIbuJanin;
use App\KuisHamilPersalinan;
use App\KuisHamilNifas;
use App\KuesionerHamil;
use App\LogbookHistory;
use Illuminate\Support\Facades\Log;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Support\Facades\Hash;

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
        $role_child = Auth::user()->roleChild;
        $role_dampingi = $this->role_child_id; //hanya role bidan yg bisa mendapingi pertama kali

        if($role_child != $role_dampingi) $is_dampingi = false;
        else $is_dampingi = true;

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
        // ->leftJoin('member_delegate', function($join) {
        //     $join->on('members.id', '=', 'member_delegate.member_id');
        // })
        // ->leftJoin('users', function($join) {
        //     $join->on('users.id', '=', 'member_delegate.user_id');
        // })
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            // 'users.id as petugas_id',
            // 'users.name as petugas'
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

        if ($role == '5') {
            $self = $self->where('members.provinsi_id', $auth->provinsi_id)->where('members.kabupaten_id', $auth->kabupaten_id)->where('members.kecamatan_id', $auth->kecamatan_id)->where('members.kelurahan_id', $auth->kelurahan_id);
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

        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $self = $self->where('members.name', 'like', '%' . $request->name . '%');
        }

        $paginate = $self->orderBy('id', 'asc')->paginate(10);
        $self = $paginate->items();
        foreach ($self as $key => $val) {
            // if($role == 5){
            //cek member delegate
            $member_delegates = MemberDelegate::select('member_delegate.member_id', 'member_delegate.user_id', 'role_user.role_id', 'role_user.role_child_id', 'u.name as petugas_name')
                ->join('users as u', 'u.id', 'member_delegate.user_id')
                ->join('role_user', 'role_user.user_id', 'u.id')
                ->where('member_id', $val->id);

            $all_member_delegates = clone($member_delegates);
            $all_member_delegates = $all_member_delegates->get()->toArray();
            $self_member_delegates = $member_delegates->where('role_user.role_child_id', $role_child)->first();
            
            if($self_member_delegates) {
                $petugas = array_column($all_member_delegates, 'petugas_name');
                $self[$key]['petugas_id'] = $self_member_delegates->user_id;
                $self[$key]['petugas'] = implode(', ',$petugas);
            }else{
                $petugas = array_column($all_member_delegates, 'petugas_name');
                $self[$key]['petugas_id'] = null;
                $self[$key]['petugas'] = implode(',',$petugas);
            }
            // }else {
            //     $member_delegates = MemberDelegate::select('member_delegate.member_id', 'member_delegate.user_id', 'u.name as petugas_name')
            //         ->join('users as u', 'u.id', 'member_delegate.user_id')
            //         ->where('member_id', $val->id)->first();

            //     $self[$key]['petugas_id'] = $member_delegates->user_id ?? null;
            //     $self[$key]['petugas'] = $member_delegates->petugas_name ?? null;
            // }
            // echo('<pre>');
            // print_r( $self[0]);die;
            
            $couple = MemberCouple::join('members', function($join) {
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

        $member = $self;
        // echo('<pre>');
        // print_r($member);die;

        return view('member.index', compact('member', 'search', 'paginate', 'name', 'is_dampingi'));
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

        $cekdelegate = MemberDelegate::where('member_id', $id)
        ->where('user_id', Auth::user()->id)->first();

        return view('member.result', compact('member', 'res','cekdelegate'));
    }

    public function logbookUpdate (Request $request) {


        $logbook = Logbook::where([['id_user','=', $request->id_user],['id_member','=', $request->id_member]])->first();

        if(!$logbook){
            $logbook = new Logbook();
        }

        $logbook->id_user = $request->id_user;
        $logbook->id_member = $request->id_member;
        $logbook->suplemen_makanan = isset($request->suplemenMakanan) ? 1 : 0;
        $logbook->suplemen_darah = isset($request->suplemenDarah) ? 1 : 0;
        $logbook->kie = isset($request->kie) ? 1 : 0;
        $logbook->rujukan = isset($request->rujukan) ? 1 : 0;

        if($logbook->save()){
            $msg = 'Intervensi berhasil ditambahkan';

            $logbook_history = new LogbookHistory();
            $logbook_history->addToLogbook($logbook->id_user, $logbook->id_member, 1, $logbook->toJson());

            return redirect()->back()->with('success', $msg);
        }else {
            return redirect()->back()->withErrors([
                'error' => 'Perhatian',
                'keterangan' => 'Intervensi gagal. Silahkan coba beberapa saat lagi'
            ]);
        }
    }

    public function logbook($id) {

        $member = Member::where('id', $id)->first();

        if($member->rencana_pernikahan) {
            $member->rencana_pernikahan = \Carbon\Carbon::parse($member->rencana_pernikahan)->isoFormat('D MMMM Y');
        }

        if($member->tgl_lahir) {
            $member->tgl_lahir = \Carbon\Carbon::parse($member->tgl_lahir)->isoFormat('D MMMM Y');
        }


        $logbook = Logbook::where('id_member',$member->id)->first();

        if(!$logbook){
            $logbook = new Logbook();
        }

        $logbook_histories = LogbookHistory::leftJoin('users', function($join) {
                    $join->on('users.id', '=', 'logbook_history.user_id');
                })->select([
                    'users.name as name',
                    'logbook_history.created_at as created_at',
                    'logbook_history.log_type as log_type',
                    'logbook_history.meta_data as meta_data',
                ])->where('member_id',$member->id)->paginate(10);
                
            
        $histories = array(); 

        foreach ($logbook_histories as $logbook_history){ 
            $temp = array(
                "date" => \Carbon\Carbon::parse($logbook_history["created_at"])->setTimezone('Asia/Phnom_Penh')->isoFormat('D MMMM Y HH:mm:ss'),
                "name" => $logbook_history["name"],
                "log_type" => $logbook_history["log_type"],
                "meta_data" => json_decode($logbook_history["meta_data"],true)
              );
            array_push($histories,$temp);
        }

        $last_result = KuisResult::select([
            'label',
            'rating_color'
        ])
        ->where('kuisioner_result.status', 1)
        ->where('kuisioner_result.member_id', $id)
        ->first();

        return view('member.logbook', compact(
            'member','logbook', 'histories','last_result','logbook_histories'));
    }

    public function show($id, Request $request) {
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
        ->leftJoin('member_delegate as md', function($q) {
            $q->on('md.member_id', 'members.id')
                ->where('user_id', Auth::user()->id);
        })
        ->select([
            'members.*',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan',
            'md.user_id'
        ])
        ->where('members.id', $id)
        ->first();

        //generate token
        $token = encrypt($member->id.'+'.Auth::user()->id.'+generatetoken');
        if($member->user_id) $member->link_token = url()->current().'?token='.$token;
        else $member->link_token = null;

        //cek button dampingi
        $member_delegates = MemberDelegate::select('member_delegate.member_id', 'member_delegate.user_id', 'role_user.role_id', 'role_user.role_child_id', 'u.name as petugas_name')
                ->join('users as u', 'u.id', 'member_delegate.user_id')
                ->join('role_user', 'role_user.user_id', 'u.id')
                ->where('member_id', $member->id)
                ->where('member_delegate.user_id', Auth::user()->id)
                ->first();

        //decode token
        $decode = decrypt($token);
        $arr_decode = explode($decode, '+');
        Log::debug('arr_decode==========', $arr_decode);
        Log::debug('member==========',array($member_delegates));
        if(!$member_delegates && ($arr_decode[0] == $id)) $is_dampingi = true;
        else $is_dampingi = false;
        
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

        return view('member.show', compact('member', 'couple', 'is_dampingi'));
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
        $role_child = Auth::user()->roleChild;
        // $check = MemberDelegate::where('member_id', $request->id)->first();
        $check = MemberDelegate::select('member_delegate.member_id', 'member_delegate.user_id', 'role_user.role_id', 'role_user.role_child_id', 'u.name as petugas_name')
            ->join('users as u', 'u.id', 'member_delegate.user_id')
            ->join('role_user', 'role_user.user_id', 'u.id')
            ->where('member_id', $request->id)
            ->where('role_user.role_child_id', $role_child)->first();

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

                $chat = ChatHeader::where('member_id', $request->id)->where('type', $role_child)->first();

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
                        // $cekdelegate = MemberDelegate::where('member_id', $rows->couple_id)->first();
                        $cekdelegate = MemberDelegate::select('member_delegate.member_id', 'member_delegate.user_id', 'role_user.role_id', 'role_user.role_child_id', 'u.name as petugas_name')
                            ->join('users as u', 'u.id', 'member_delegate.user_id')
                            ->join('role_user', 'role_user.user_id', 'u.id')
                            ->where('member_id', $rows->couple_id)
                            ->where('role_user.role_child_id', $role_child)->first();

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

    public function indexIbuHamil($id)
    {
        $member = Member::where('id', $id)->first();
        if($member != null){
            $name = $member->name;
            $no_ktp =  Helper::decryptNik($member->no_ktp);
            if($member -> gender == 1){
                $gender = "Pria";
            }else{
                $gender = "Wanita";
            }
            $today = date("Y-m-d");
            $ageCalculation = date_diff(date_create($member->tgl_lahir), date_create($today));
            $age = $ageCalculation->format('%y');
            $tempat_lahir = $member->tempat_lahir;
            $tanggal_lahir = $member->tgl_lahir;
            $alamat = $member->alamat;

            #retrieve kuesioner data
            $kuesionerData = array();

            /*
            * Nanti semuanya masuk ke tabel kuesioner hamil dan dibedakan berdasarkan kolom periode
            * Periode berdasarkan kuesionernya
                Kontak awal                     = 1
                12 Minggu                       = 2
                16 Minggu                       = 3
                20 Minggu                       = 4
                24 Minggu                       = 5
                28 Minggu                       = 6
                32 Minggu                       = 7
                36 Minggu                       = 8
                Setelah Persalinan              = 9
                Pasca Salin Akhir Masa Nifas    = 10
            * dan seterusnya

            */


            // mengambil kuesioner yang sudah diisi sesuai dengan periodenya
            $kuesionerChecked = KuesionerHamil::where('id_member',$id)->select('periode','created_at')->get()->groupBy('periode');
            for($i=1 ; $i<=10 ; $i++)
            {
                switch ($i){
                    case '1' :
                        $hamilkontakAwal = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                        if($hamilkontakAwal != null){
                            $arrayKontakAwal = array(
                                'id' => 'kontak-awal',
                                'created_at' => $hamilkontakAwal -> created_at
                            );
                        }else{
                            $arrayKontakAwal = array(
                                'id' => 'kontak-awal',
                                'created_at' => null
                            );
                         }
                         array_push($kuesionerData,$arrayKontakAwal);
                         break;
                    case '2' :
                        $hamil12minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                        if($hamil12minggu != null){
                            $array12Minggu = array(
                                'id' => '12-minggu',
                                'created_at' => $hamil12minggu->created_at
                            );
                        }else{
                            $array12Minggu = array(
                                'id' => '12-minggu',
                                'created_at' => null
                            );
                         }
                         array_push($kuesionerData,$array12Minggu);
                         break;
                    case '3' :
                        $hamil16minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                        if($hamil16minggu != null){
                            $array16Minggu = array(
                                'id' => '16-minggu',
                                'created_at' => $hamil16minggu->created_at
                            );
                        }else{
                            $array16Minggu = array(
                                'id' => '16-minggu',
                                'created_at' => null
                            );
                            }
                        array_push($kuesionerData,$array16Minggu);
                        break;
                    case '4' :
                            $hamil20Minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamil20Minggu != null){
                                $array20Minggu = array(
                                    'id' => '20-minggu',
                                    'created_at' => $hamil20Minggu->created_at
                                );
                            }else{
                                $array20Minggu = array(
                                    'id' => '20-minggu',
                                    'created_at' => null
                                );
                            }
                            array_push($kuesionerData,$array20Minggu);

                            break;
                    case '5' :
                            $hamil24Minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamil24Minggu != null){
                                $array24Minggu = array(
                                    'id' => '24-minggu',
                                    'created_at' => $hamil24Minggu->created_at
                                );
                            }else{
                                $array24Minggu = array(
                                    'id' => '24-minggu',
                                    'created_at' => null
                                );
                            }
                            array_push($kuesionerData,$array24Minggu);
                            break;
                    case '6' :
                            $hamil28Minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamil28Minggu != null){
                                $array28Minggu = array(
                                    'id' => '28-minggu',
                                    'created_at' => $hamil28Minggu->created_at
                                );
                            }else{
                                $array28Minggu = array(
                                    'id' => '28-minggu',
                                    'created_at' => null
                                );
                                }
                            array_push($kuesionerData,$array28Minggu);
                            break;
                    case '7' :
                            $hamil32Minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamil32Minggu != null){
                                $array32Minggu = array(
                                    'id' => '32-minggu',
                                    'created_at' => $hamil32Minggu->created_at
                                );
                            }else{
                                $array32Minggu = array(
                                    'id' => '32-minggu',
                                    'created_at' => null
                                );
                                }
                            array_push($kuesionerData,$array32Minggu);
                            break;
                     case '8' :
                            $hamil36Minggu = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamil36Minggu != null){
                                $array36Minggu = array(
                                    'id' => '36-minggu',
                                    'created_at' => $hamil36Minggu->created_at
                                );
                            }else{
                                $array36Minggu = array(
                                    'id' => '36-minggu',
                                    'created_at' => null
                                );
                                }
                            array_push($kuesionerData,$array36Minggu);
                            break;
                    case '9' :
                            $hamilPersalinan = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamilPersalinan != null){
                                $arrayPersalinan = array(
                                    'id' => 'persalinan',
                                    'created_at' => $hamilPersalinan->created_at
                                );
                            }else{
                                $arrayPersalinan = array(
                                    'id' => 'persalinan',
                                    'created_at' => null
                                );
                                }
                            array_push($kuesionerData,$arrayPersalinan);
                            break;
                     case '10' :
                            $hamilNifas = isset($kuesionerChecked[$i]) ? $kuesionerChecked[$i]->first() : null;
                            if($hamilNifas != null){
                                $arrayNifas = array(
                                    'id' => 'nifas',
                                    'created_at' => $hamilNifas->created_at
                                );
                            }else{
                                $arrayNifas = array(
                                    'id' => 'nifas',
                                    'created_at' => null
                                );
                                }
                            array_push($kuesionerData,$arrayNifas);
                            break;
                }
            }
            return view('kuis_ibuhamil.index',[
                "id" => $id,
                "name" => $name,
                "no_ktp" => $no_ktp,
                "gender" => $gender,
                "umur" => $age,
                "tempat_lahir" => $tempat_lahir,
                "tanggal_lahir" => $tanggal_lahir,
                "alamat" => $alamat,
                "kuesionerData" => $kuesionerData
            ]);

        }

    }

    public function edit($id)
    {
        $this->authorize('access', [\App\Member::class, Auth::user()->role, 'edit']);

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

        return view('member.edit', compact('member'));   
    }

    public function update(Request $request)
    {
        $member_upd = Member::where('id', $request->cid)->first();

        $member_upd->name = $request->name;
        $member_upd->alamat = $request->alamat;
        $member_upd->rt = $request->rt;
        $member_upd->rw = $request->rw;
        $member_upd->kodepos = $request->kodepos;
        $member_upd->gender = $request->gender;
        $member_upd->tempat_lahir = $request->tempat_lahir;
        $member_upd->tgl_lahir = $request->tgl_lahir;

        $member_upd->update();
        return redirect()->route('admin.member.index')->with('success', 'Data Catin berhasil diupdate.');
    }
}
