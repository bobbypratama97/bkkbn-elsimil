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
use Illuminate\Support\Facades\Log;

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

        $self = Member::join('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
        })
        ->join('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
        })
        ->join('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
        })
        ->join('adms_kelurahan', function($join) {
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

        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $self = $self->where('members.name', 'like', '%' . $request->name . '%');
        }

        $paginate = $self->paginate(10);
        $self = $paginate->items();
        foreach ($self as $key => $val) {
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

        $cop = [];
        foreach ($self as $keyz => $valz) {
            $cop[] = $valz->id;
        }

        $couples = Member::join('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'members.provinsi_id');
        })
        ->join('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'members.kabupaten_id');
        })
        ->join('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'members.kecamatan_id');
        })
        ->join('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'members.kelurahan_id');
        })
        ->leftJoin('member_delegate', function($join) {
            $join->on('members.id', '=', 'member_delegate.member_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'member_delegate.user_id');
        })
        ->join('member_couple', function($join) {
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

        // $self = $self->toArray();
        $couples = $couples->toArray();

        $member = array_merge($self, $couples);

        return view('member.index', compact('member', 'search', 'paginate', 'name'));
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

        // get result before intervensi
        $kuis_first = KuisResult::where('member_id', $id)
            ->select([
                'id',
                'member_id',
                'kuis_id',
                'kuis_code', 
                'kuis_title',
                'created_at', 
                'kuis_max_nilai', 
                'member_kuis_nilai', 
                'label',
                'rating_color'
            ])
            ->first();
        $details_first = [];
        $details_last = [];


        if($kuis_first){
            $deskripsi = Kuis::where('id', $kuis_first->kuis_id)->first();

            $details_first = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
            })
            ->where('kuisioner_result_detail.result_id', $kuis_first->id)
            ->select([
                'kuisioner_result_header.pertanyaan_header_caption',
                'kuisioner_result_header.pertanyaan_header_jenis',
                'kuisioner_result_detail.pertanyaan_detail_title',
                'kuisioner_result_detail.pertanyaan_detail_pilihan',
                'kuisioner_result_detail.pertanyaan_bobot_label',
                'kuisioner_result_detail.pertanyaan_rating',
                'kuisioner_result_detail.pertanyaan_rating_color',
                'kuisioner_result_detail.value',
                'kuisioner_result_detail.formula_value',
                'kuisioner_result_detail.pertanyaan_bobot_id',
                'kuisioner_result_detail.pertanyaan_bobot'
            ])
            ->get()
            ->toArray();
        }

        // get result after intervensi
        $kuis = KuisResult::where('member_id', $id)
            ->select([
                'id',
                'member_id',
                'kuis_id',
                'kuis_code', 
                'kuis_title',
                'created_at', 
                'kuis_max_nilai', 
                'member_kuis_nilai', 
                'label',
                'rating_color'
            ])
            ->orderBy('id', 'desc');
        $kuis_last = [];

        if($kuis->count() > 1 && $logbook){
            $kuis_last = $kuis->first();
            $deskripsi = Kuis::where('id', $kuis_last->kuis_id)->first();

            $details_last = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
            })
            ->where('kuisioner_result_detail.result_id', $kuis_last->id)
            ->select([
                'kuisioner_result_header.pertanyaan_header_caption',
                'kuisioner_result_header.pertanyaan_header_jenis',
                'kuisioner_result_detail.pertanyaan_detail_title',
                'kuisioner_result_detail.pertanyaan_detail_pilihan',
                'kuisioner_result_detail.pertanyaan_bobot_label',
                'kuisioner_result_detail.pertanyaan_rating',
                'kuisioner_result_detail.pertanyaan_rating_color',
                'kuisioner_result_detail.value',
                'kuisioner_result_detail.formula_value',
                'kuisioner_result_detail.pertanyaan_bobot_id',
                'kuisioner_result_detail.pertanyaan_bobot'
            ])
            ->get()
            ->toArray();
        }

        $couple = MemberCouple::where('member_id', $id)->first();
        $details_couple_first = [];
        $details_couple_last = [];

        if($couple){
                // get result before intervensi
            $kuis_couple_first = KuisResult::where('member_id', $couple->couple_id)
            ->select([
                'id',
                'member_id',
                'kuis_id',
                'kuis_code', 
                'kuis_title',
                'created_at', 
                'kuis_max_nilai', 
                'member_kuis_nilai', 
                'label',
                'rating_color'
            ])
            ->first();

            if($kuis_couple_first){
                $deskripsi = Kuis::where('id', $kuis_couple_first->kuis_id)->first();

                $tanggal = explode(' ', $kuis_couple_first->created_at);
                $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2];
                $kuis_couple_first->tanggal = $tanggal; 

                $details_couple_first = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                    $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                    $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
                })
                ->where('kuisioner_result_detail.result_id', $kuis_couple_first->id)
                ->select([
                    'kuisioner_result_header.pertanyaan_header_caption',
                    'kuisioner_result_header.pertanyaan_header_jenis',
                    'kuisioner_result_detail.pertanyaan_detail_title',
                    'kuisioner_result_detail.pertanyaan_detail_pilihan',
                    'kuisioner_result_detail.pertanyaan_bobot_label',
                    'kuisioner_result_detail.pertanyaan_rating',
                    'kuisioner_result_detail.pertanyaan_rating_color',
                    'kuisioner_result_detail.value',
                    'kuisioner_result_detail.formula_value',
                    'kuisioner_result_detail.pertanyaan_bobot_id',
                    'kuisioner_result_detail.pertanyaan_bobot'
                ])
                ->get()
                ->toArray();
            }

            // get result after intervensi
            $kuis_couple = KuisResult::where('member_id', $couple->couple_id)
                ->select([
                    'id',
                    'member_id',
                    'kuis_id',
                    'kuis_code', 
                    'kuis_title',
                    'created_at', 
                    'kuis_max_nilai', 
                    'member_kuis_nilai', 
                    'label',
                    'rating_color'
                ])
                ->orderBy('id', 'desc');

            if($kuis_couple->count() > 1){
                $kuis_couple_last = $kuis_couple->first();
                $deskripsi = Kuis::where('id', $kuis_couple_last->kuis_id)->first();

                $tanggal = explode(' ', $kuis_couple_last->created_at);
                $tanggal = $tanggal[0] . ' ' . $tanggal[1] . ' ' . $tanggal[2];
                $kuis_couple_last->tanggal = $tanggal; 

                $details_couple_last = KuisResultDetail::leftJoin('kuisioner_result_header', function($join) {
                    $join->on('kuisioner_result_header.header_id', '=', 'kuisioner_result_detail.header_id');
                    $join->on('kuisioner_result_header.result_id', '=', 'kuisioner_result_detail.result_id');
                })
                ->where('kuisioner_result_detail.result_id', $kuis_couple_last->id)
                ->select([
                    'kuisioner_result_header.pertanyaan_header_caption',
                    'kuisioner_result_header.pertanyaan_header_jenis',
                    'kuisioner_result_detail.pertanyaan_detail_title',
                    'kuisioner_result_detail.pertanyaan_detail_pilihan',
                    'kuisioner_result_detail.pertanyaan_bobot_label',
                    'kuisioner_result_detail.pertanyaan_rating',
                    'kuisioner_result_detail.pertanyaan_rating_color',
                    'kuisioner_result_detail.value',
                    'kuisioner_result_detail.formula_value',
                    'kuisioner_result_detail.pertanyaan_bobot_id',
                    'kuisioner_result_detail.pertanyaan_bobot'
                ])
                ->get()
                ->toArray();
            }
        }

        if(!$logbook){
            $logbook = new Logbook();
        }

        return view('member.logbook', compact(
            'member','logbook', 'details_first',
            'details_last', 'kuis_first', 'kuis_last', 
            'details_couple_last','details_couple_first','couple'));
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
            * Kontak awal = 1
            * 12 Minggu = 2
            * 16 Minggu = 3
            * dan seterusnya

            * nanti ngeGetnya pake query di bawah ini aja
            * KuesionerHamil::where('id_member',$id)->select('created_at')->groupBy('periode')
            * jadi bisa dapet per periode kuis

            */

            for($i=0 ; $i<10 ; $i++)
            {
                switch ($i){
                    case '0' :
                        $hamilkontakAwal = KuesionerHamil::where([['id_member','=',$id],['periode','=',1]])->select('created_at')->first();
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
                    case '1' :
                        $hamil12minggu = KuisHamil12Minggu::where('id_member',$id)->first();
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
                    case '2' :
                        $hamil16minggu = KuisHamil16Minggu::where('id_member',$id)->first();
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
                    case '3' :
                            $hamil20minggu = KuisHamilIbuJanin::where('id_member',$id)->where('periode',20)->first();
                            if($hamil20minggu != null){
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
                    case '4' :
                            $hamil24minggu = KuisHamilIbuJanin::where('id_member',$id)->where('periode',24)->first();
                            if($hamil24minggu != null){
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
                    case '5' :
                            $hamil28minggu = KuisHamilIbuJanin::where('id_member',$id)->where('periode',28)->first();
                            if($hamil28minggu != null){
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
                    case '6' :
                            $hamil32minggu = KuisHamilIbuJanin::where('id_member',$id)->where('periode',32)->first();
                            if($hamil32minggu != null){
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
                     case '7' :
                            $hamil36minggu = KuisHamilIbuJanin::where('id_member',$id)->where('periode',36)->first();
                            if($hamil36minggu != null){
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
                    case '8' :
                            $hamilPersalinan = KuisHamilPersalinan::where('id_member',$id)->first();
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
                     case '9' :
                            $hamilNifas = KuisHamilNifas::where('id_member',$id)->first();
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
            // dd($kuesionerData);
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

}
