<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Kuis;
use App\KuisSummary;
use App\KuisFile;
use App\KuisHeader;
use App\KuisDetail;
use App\KuisBobot;
use App\KuisBobotFile;
use App\TempImage;

use App\Apply;
use App\Role;
use App\UserRole;

use App\Sso;

class KuisController extends Controller
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
    public function index()
    {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'index']);

        $kuis = Kuis::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'kuisioner.created_by');
        })
        ->leftJoin('kuisioner_approval', function($join) {
            $join->on('kuisioner_approval.kuis_id', '=', 'kuisioner.id');
            $join->on('kuisioner_approval.status', '=', 'kuisioner.apv')
            ->whereRaw('kuisioner_approval.id IN (select MAX(a2.id) from kuisioner_approval as a2 join kuisioner as u2 on u2.id = a2.kuis_id group by u2.id)');
        })
        ->whereNull('kuisioner.deleted_by')
        ->select([
            'kuisioner.id',
            'kuisioner.title',
            'kuisioner.gender',
            'kuisioner.apv',
            'kuisioner.created_at',
            'users.name',
            'kuisioner_approval.catatan'
        ])
        ->orderBy('kuisioner.position')
        ->get();

        return view('kuis.index', ['kuis' => $kuis]);
    }

    public function create() {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'create']);

        $status = Helper::status();
        $color = Helper::colorPicker();
        return view('kuis.create', compact('status', 'color'));
    }

    public function store(Request $request) {
        //echo '<pre>'; print_r ($request->all()); die;

        $check = Kuis::whereNull('deleted_by')->where('title', $request->title)->first();

        if ($check) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kuesioner dengan judul ini sudah pernah dibuat. Silahkan gunakan judul yang lain'
                ]);
        }

        if (empty($request->kondisi)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Summary penilaian harus diisi'
                ]);
        }

        $kuis = new Kuis;
        $kuis->title = $request->title;
        $kuis->gender = $request->gender;
        $kuis->deskripsi = $request->deskrip;
        $kuis->thumbnail = (!empty($request->thumbnail)) ? $request->thumbnail : 'kuesioner_thumbnail.png';
        $kuis->image = (!empty($request->original)) ? $request->original : 'kuesioner.png';
        $kuis->apv = 'APV100';
        $kuis->max_point = $request->max_point;
        $kuis->created_at = date('Y-m-d H:i:s');
        $kuis->created_by = Auth::id();

        if ($kuis->save()) {

            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'kuesioner')->delete();

            $kondisi = $request->kondisi;
            $kondisi = array_filter($kondisi);
            foreach ($kondisi as $key => $value) {
                $summary = new KuisSummary;
                $summary->kuis_id = $kuis->id;
                $summary->kondisi = $value;
                $summary->label = $request->label[$key];
                $summary->nilai = $request->nilai[$key];
                $summary->rating = $request->rating[$key];
                $summary->rating_color = $request->background[$key];
                $summary->deskripsi = $request->deskripsi[$key];
                $summary->template = $request->template[$key];
                $summary->created_at = date('Y-m-d H:i:s');
                $summary->created_by = Auth::id();

                $summary->save();
            }

            $msg = 'Kuesioner berhasil dibuat. Tambahkan pertanyaan-pertanyaan yang berhubungan dengan kuesioner ini';
            return redirect()->route('admin.pertanyaan.create', $kuis->id)->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kuesioner gagal dibuat. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function show($id) {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'show']);

        $kuis = Kuis::where('id', $id)->first();
        $summary = KuisSummary::whereNull('deleted_by')->where('kuis_id', $id)->orderBy('kondisi')->get()->toArray();
        $gender = Helper::statusGender();
        $kondisi = Helper::kondisiKuis();
        $color = Helper::colorPicker();

        return view('kuis.show', compact('kuis', 'summary', 'gender', 'kondisi', 'color'));
    }

    public function edit($id) {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'edit']);

        $kuis = Kuis::where('id', $id)->first();
        $summary = KuisSummary::whereNull('deleted_by')->where('kuis_id', $id)->orderBy('kondisi')->get()->toArray();
        $gender = Helper::statusGender();
        $kondisi = Helper::kondisiKuis();
        $color = Helper::colorPicker();
        $point = ['A', 'B', 'C', 'D'];

        return view('kuis.edit', compact('kuis', 'summary', 'gender', 'kondisi', 'color', 'point'));
    }

    public function update(Request $request, $id) {
        $update = Kuis::where('id', $id)->update([
            'title' => $request->title,
            'gender' => $request->gender,
            'deskripsi' => $request->deskrip,
            'thumbnail' => (!empty($request->thumbnail)) ? $request->thumbnail : 'kuesioner_thumbnail.png',
            'image' => (!empty($request->original)) ? $request->original : 'kuesioner.png',
            'max_point' => $request->max_point,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id()
        ]);

        if ($update) {
            $hapus = TempImage::where('user_id', Auth::id())->where('module', 'kuesioner')->delete();

            $select = KuisSummary::whereNull('deleted_by')->where('kuis_id', $id)->select(['id', 'kondisi', 'label', 'nilai', 'rating', 'rating_color', 'deskripsi', 'template'])->get()->toArray();

            $res = [];
            $detail = array_filter($request->nilai);
            foreach ($detail as $key => $row) {
                $res[] = [
                    'id' => $request->detail_id[$key],
                    'kondisi' => $request->kondisi[$key],
                    'label' => $request->label[$key],
                    'nilai' => $request->nilai[$key],
                    'rating' => $request->rating[$key],
                    'rating_color' => $request->background[$key],
                    'deskripsi' => $request->deskripsi[$key],
                    'template' => $request->template[$key]
                ];
            }
            
            //ammad-2021-07-22 0704
            //update kuisoner bobot
            $updates = KuisSummary::where('kuis_id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::id()
            ]);
            foreach ($res as $key => $val) {
                //craete kuisoner bobot
                $insert = new KuisSummary;
                $insert->kuis_id = $id;
                $insert->kondisi = $val['kondisi'];
                $insert->label = $val['label'];
                $insert->nilai = $val['nilai'];
                $insert->rating = $val['rating'];
                $insert->rating_color = $val['rating_color'];
                $insert->deskripsi = $val['deskripsi'];
                $insert->template = $val['template'];
                $insert->created_at = date('Y-m-d H:i:s');
                $insert->created_by = Auth::id();

                $insert->save();
            }
            
            // $result = Helper::compare($res, $select, false);

            // foreach ($result as $key => $val) {
            //     $updates = KuisSummary::where('id', $val['id'])->update([
            //         'deleted_at' => date('Y-m-d H:i:s'),
            //         'deleted_by' => Auth::id()
            //     ]);

            //     if (!empty($val['kondisi'])) {
            //         $insert = new KuisSummary;
            //         $insert->kuis_id = $id;
            //         $insert->kondisi = $val['kondisi'];
            //         $insert->label = $val['label'];
            //         $insert->nilai = $val['nilai'];
            //         $insert->rating = $val['rating'];
            //         $insert->rating_color = $val['rating_color'];
            //         $insert->deskripsi = $val['deskripsi'];
            //         $insert->template = $val['template'];
            //         $insert->created_at = date('Y-m-d H:i:s');
            //         $insert->created_by = Auth::id();

            //         $insert->save();
            //     }
            // }

            $msg = 'Pengubahan kuesioner berhasil dilakukan.';
            return redirect()->route('admin.kuis.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Pengubahan kuesioner gagal dibuat. Silahkan coba beberapa saat lagi'
                ]);
        }

    }

    public function delete(Request $request) {
        $update = Kuis::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kuisioner berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kuisioner gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function sort() {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'sort']);

        $kuis = Kuis::whereNull('deleted_by')->where('apv', 'APV300')->select(['id', 'title', 'gender'])->orderBy('position')->get();

        return view('kuis.sort', compact('kuis'));
    }

    public function submit(Request $request) {
        $position = str_replace(['"', '[', ']'], ['', '', ''], $request->position);

        $explode = explode(',', $position);
        $i = 1;
        foreach ($explode as $row) {
            //echo $row; echo '<br />';
            $update = Kuis::where('id', $row)->update([
                'position' => $i,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $i++;
        }

        $msg = 'Sorting pertanyaan berhasil dilakukan.';
        return redirect()->route('admin.pertanyaan.index', $id)->with('success', $msg);
    }

    public function preview($id) {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'preview']);

        $kuis = Kuis::where('id', $id)->first();
        $summary = KuisSummary::whereNull('deleted_by')->where('kuis_id', $id)->orderBy('kondisi')->get()->toArray();

        $pertanyaan = KuisHeader::whereNull('deleted_by')->where('kuis_id', $id)
            ->select(['id', 'kuis_id', 'jenis', 'deskripsi', 'caption', 'formula', 'widget_id'])
            ->orderBy('position')
            ->get()
            ->toArray();

        foreach ($pertanyaan as $key => $row) {
            //echo '<pre>'; print_r ($row);
            if ($row['jenis'] == 'widget') {
                $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'header_id', 'title', 'pilihan', 'bobot', 'komponen_id'])
                    ->get()
                    ->toArray();
                $bobot = [];
            } else if ($row['jenis'] == 'combine') {
                $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'header_id', 'title', 'pilihan', 'bobot', 'komponen_id'])
                    ->get()
                    ->toArray();

                $bobot = KuisBobot::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])
                    ->orderBy('kondisi')
                    ->get()
                    ->toArray();


                foreach ($bobot as $keys => $rows) {
                    $file = KuisBobotFile::whereNull('deleted_by')
                        ->where('pertanyaan_bobot_id', $rows['id'])
                        ->select(['id', 'name', 'file'])
                        ->get()
                        ->toArray();

                    $bobot[$keys]['file'] = $file;
                }
                //$detail['bobot'] = $bobot;

            } else {
                $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'header_id', 'title', 'pilihan', 'bobot', 'komponen_id'])
                    ->first()
                    ->toArray();

                $bobot = KuisBobot::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])
                    ->orderBy('kondisi')
                    ->get()
                    ->toArray();


                foreach ($bobot as $keys => $rows) {
                    $file = KuisBobotFile::whereNull('deleted_by')
                        ->where('pertanyaan_bobot_id', $rows['id'])
                        ->select(['id', 'name', 'file'])
                        ->get()
                        ->toArray();

                    $bobot[$keys]['file'] = $file;
                }
                $detail['bobot'] = $bobot;

                $bobot = [];
            }
            $pertanyaan[$key]['detail'] = $detail;
            $pertanyaan[$key]['bobot'] = $bobot;
        }

        //echo '<pre>';
        //print_r ($pertanyaan);
        //die;

        return view('kuis.preview', compact('kuis', 'summary', 'pertanyaan'));
    }

    public function apply(Request $request) {
        $kuis = Kuis::where('id', $request->cid)->first();

        $insert = new Apply;
        $insert->kuis_id = $request->cid;
        $insert->filer_by = Auth::id();
        $insert->description = 'Pengajuan kuesioner ' . $kuis->title . ' untuk diapprove';
        $insert->status = 'APV200';
        $insert->step = 0;
        $insert->created_at = date('Y-m-d H:i:s');
        $insert->created_by = Auth::id();

        if ($insert->save()) {
            $update = Kuis::where('id', $request->cid)->update([
                //'status' => 2,
                'apv' => 'APV200',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $user = UserRole::leftJoin('users', function($join) {
                $join->on('users.id', '=', 'role_user.user_id');
            })
            ->leftJoin('role', function($join) {
                $join->on('role.id', '=', 'role_user.role_id');
            })
            ->where('role_id', 1)
            ->where('users.is_active', 1)
            ->select(['users.*', 'role.name as role'])->get()->toArray();

            if (!empty($user)) {
                $name = $user[0]['role'];
                $cc = array_column($user, 'email');

                $date = explode(' ', date('Y-m-d H.i'));
                $tanggal = Helper::customDateMember($date[0]);
                $tanggal = $tanggal . ' pukul ' . $date[1];

                Helper::sendMail([
                    'id' => $kuis->id,
                    'tipe' => 1,
                    'name' => $name,
                    'email' => $user[0]['email'],
                    'cc' => $cc,
                    'content' => $kuis->title,
                    'date' => $tanggal,
                    'url' => 'apv'
                ]);

                return redirect()->route('admin.kuis.index')->with('success', 'Approval untuk kuesioner ' . $kuis->title . ' telah diajukan');
            }
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Pengajuan approval kuesioner ' .$kuis->title. ' gagal. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function review($id) {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'review']);

        $kuis = Kuis::where('id', $id)->first();
        $log = Apply::where('kuis_id', $id)->orderBy('id', 'DESC')->first();
        $summary = KuisSummary::whereNull('deleted_by')->where('kuis_id', $id)->orderBy('kondisi')->get()->toArray();
        $status = Helper::approval();

        $pertanyaan = KuisHeader::whereNull('deleted_by')->where('kuis_id', $id)
            ->select(['id', 'kuis_id', 'jenis', 'deskripsi', 'caption', 'formula', 'widget_id'])
            ->orderBy('position')
            ->get()
            ->toArray();

        foreach ($pertanyaan as $key => $row) {
            //echo '<pre>'; print_r ($row);
            if ($row['jenis'] == 'widget') {
                $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'header_id', 'title', 'pilihan', 'bobot', 'komponen_id'])
                    ->get()
                    ->toArray();
                $bobot = [];
            } else if ($row['jenis'] == 'combine') {
                $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'header_id', 'title', 'pilihan', 'bobot', 'komponen_id'])
                    ->get()
                    ->toArray();

                $bobot = KuisBobot::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])
                    ->orderBy('kondisi')
                    ->get()
                    ->toArray();


                foreach ($bobot as $keys => $rows) {
                    $file = KuisBobotFile::whereNull('deleted_by')
                        ->where('pertanyaan_bobot_id', $rows['id'])
                        ->select(['id', 'name', 'file'])
                        ->get()
                        ->toArray();

                    $bobot[$keys]['file'] = $file;
                }
                //$detail['bobot'] = $bobot;

            } else {
                $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'header_id', 'title', 'pilihan', 'bobot', 'komponen_id'])
                    ->first()
                    ->toArray();

                $bobot = KuisBobot::whereNull('deleted_by')->where('header_id', $row['id'])
                    ->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])
                    ->orderBy('kondisi')
                    ->get()
                    ->toArray();


                foreach ($bobot as $keys => $rows) {
                    $file = KuisBobotFile::whereNull('deleted_by')
                        ->where('pertanyaan_bobot_id', $rows['id'])
                        ->select(['id', 'name', 'file'])
                        ->get()
                        ->toArray();

                    $bobot[$keys]['file'] = $file;
                }
                $detail['bobot'] = $bobot;

                $bobot = [];
            }
            $pertanyaan[$key]['detail'] = $detail;
            $pertanyaan[$key]['bobot'] = $bobot;
        }

        return view('kuis.review', compact('kuis', 'pertanyaan', 'log', 'summary', 'status'));
    }

    public function submitreview(Request $request) {
        $kuis = Kuis::where('id', $request->cid)->first();

        $aprv = Apply::where('kuis_id', $request->cid)->orderBy('id', 'DESC')->first();

        $status = Helper::approval($request->apv);

        if ($aprv->status == $request->apv) {
            $update = Apply::where('id', $aprv->id)->update([
                'catatan' => $request->catatan,
                'status' => $request->apv,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $fix = Apply::where('kuis_id', $request->cid)->update([
                'step' => 1
            ]);

            if ($update) {
                return redirect()->route('admin.kuis.approve')->with('success', 'Approval untuk kuesioner ' . $kuis->title . ' berhasil. Status kuesioner adalah ' . $status);
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Perhatian', 
                        'keterangan' => 'Proses approval kuesioner ' .$kuis->title. ' gagal. Silahkan coba beberapa saat lagi'
                    ]);
            }

        } else {
            $insert = new Apply;
            $insert->kuis_id = $request->cid;
            $insert->filer_by = $aprv->filer_by;
            $insert->proceed_by = Auth::id();
            $insert->catatan = $request->catatan;
            $insert->description = 'Pengubahan status kuesioner ' . $kuis->title . ' menjadi ' . $status;
            $insert->status = $request->apv;
            $insert->created_at = date('Y-m-d H:i:s');
            $insert->created_by = Auth::id();

            if ($insert->save()) {
                $update = Kuis::where('id', $request->cid)->update([
                    //'status' => $request->apv,
                    'apv' => $request->apv
                ]);

                $fix = Apply::where('kuis_id', $request->cid)->update([
                    'step' => 1
                ]);

                $detect = new \Mobile_Detect;
                $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

                $simpan = new Sso;
                $simpan->ip_address = request()->ip();
                $simpan->device = $deviceType;
                $simpan->status = 1;
                $simpan->created_at = date('Y-m-d H:i:s');
                $simpan->created_by = Auth::id();

                $simpan->save();

                return redirect()->route('admin.kuis.approve')->with('success', 'Approval untuk kuesioner ' . $kuis->title . ' berhasil. Status kuesioner adalah ' . $status);
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Perhatian', 
                        'keterangan' => 'Proses approval kuesioner ' .$kuis->title. ' gagal. Silahkan coba beberapa saat lagi'
                    ]);
            }
        }

    }

    public function approve() {
        $this->authorize('access', [\App\Kuis::class, Auth::user()->role, 'approve']);

        $kuis = Kuis::whereNull('kuisioner.deleted_by')
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'kuisioner.created_by');
            })
            ->leftJoin('kuisioner_approval', function($join) {
                $join->on('kuisioner_approval.kuis_id', '=', 'kuisioner.id');
                $join->on('kuisioner_approval.status', '=', 'kuisioner.apv')
                ->whereRaw('kuisioner_approval.id IN (select MAX(a2.id) from kuisioner_approval as a2 join kuisioner as u2 on u2.id = a2.kuis_id group by u2.id)');
            })
            ->leftJoin('users as uka', function($join) {
                $join->on('uka.id', '=', 'kuisioner_approval.proceed_by');
            })
            ->whereIn('kuisioner.apv', ['APV200'])
            ->select([
                'kuisioner.*',
                'users.name as nama',
                'uka.name as proceed_name'
            ])
            ->get();

        return view('kuis.approve', compact('kuis'));
    }

    public function upload(Request $request) {

        $output = [];

        if ($request->action == 'upload') {

            $check = TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->first();

            if (!empty($check)) {
                $path = public_path() . '/uploads/kuesioner/' . $check->filename;
                if (file_exists($path)) {
                    unlink($path);
                }

                TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->delete();
            }

            $time = time();
            $filenamewithextension = $request->file('file')->getClientOriginalName();
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension; 

            $foto = $request->file('file');

            $original = $filename;

            $oriPath = public_path('uploads/kuesioner');

            Image::make($foto->getRealPath())->save($oriPath . '/' . $original);

            $image = new TempImage;
            $image->jenis = $request->jenis;
            $image->user_id = Auth::id();
            $image->module = $request->module;
            $image->filename = $original;
            $image->created_at = date('Y-m-d H:i:s');
            $image->created_by = Auth::id();

            if ($image->save()) {
                $output['msg'] = 'Gambar berhasil diupload';
                $output['image'] = $original;
            } else {
                $output['msg'] = 'Gambar gagal diupload. Silahkan coba beberapa saat lagi';
                $output['image'] = '';
            }

        }

        if ($request->action == 'delete') {
            $check = TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->first();

            if (!empty($check)) {
                $path = public_path() . '/uploads/kuesioner/' . $check->filename;
                if (file_exists($path)) {
                    unlink($path);
                }

                TempImage::where('jenis', $request->jenis)->where('module', $request->module)->where('user_id', Auth::id())->delete();

                $output['image'] = '';
            }
        }

        return json_encode($output);

        die();
    }

}
