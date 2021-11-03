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
use App\KuisHeader;
use App\KuisDetail;
use App\KuisBobot;
use App\KuisBobotFile;

use App\KuisHeaderLog;
use App\KuisDetailLog;

use App\Widget;

class PertanyaanController extends Controller
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
    public function index($id)
    {
        $this->authorize('access', [\App\Pertanyaan::class, Auth::user()->role, 'index']);

        $header = Kuis::where('id', $id)->first();

        $data = KuisHeader::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'pertanyaan_header.created_by');
        })
        ->leftJoin('pertanyaan_detail', function($join) {
            $join->on('pertanyaan_detail.header_id', '=', 'pertanyaan_header.id');
        })
        ->leftJoin('widgets', function($join) {
            $join->on('widgets.id', '=', 'pertanyaan_header.widget_id');
        })
        ->whereNull('pertanyaan_header.deleted_by')
        ->where('pertanyaan_header.kuis_id', $id)
        ->select([
            'pertanyaan_header.kuis_id',
            'pertanyaan_header.id',
            'pertanyaan_header.caption',
            'pertanyaan_header.jenis',
            'pertanyaan_header.created_at',
            'pertanyaan_detail.pilihan',
            'pertanyaan_detail.bobot',
            'widgets.name as widget_name',
            'users.name'
        ])
        ->groupBy('pertanyaan_header.id')
        ->orderBy('pertanyaan_header.position')
        ->get();

        return view('pertanyaan.index', compact('header', 'data'));
    }

    public function create(Request $request) {
        $this->authorize('access', [\App\Pertanyaan::class, Auth::user()->role, 'create']);

        $kuis = Kuis::where('id', $request->id)->first();
        
        return view('pertanyaan.create', ['kuis' => $kuis]);
    }

    public function store(Request $request, $id) {
        //echo '<pre>'; print_r ($request->all()); die;

        $oriPath = public_path('uploads/kuis');

        /*if ($request->jenis == 'widget') {
            $widget = Widget::where('id', $request->widget_id)->first();
            $caption = $widget->name;
        }*/

        $get = KuisHeader::where('kuis_id', $id)->orderBy('position', 'desc')->select('position')->first();

        $urutan = ($get) ? $get->position + 1 : 1;

        $header = new KuisHeader;
        $header->kuis_id = $id;
        $header->jenis = $request->jenis;
        $header->deskripsi = $request->deskripsi;
        $header->widget_id = ($request->jenis == 'widget') ? $request->widget_id : null;
        $header->caption = $request->caption;
        $header->formula = $request->formula;
        $header->position = $urutan;
        $header->created_at = date('Y-m-d H:i:s');
        $header->created_by = Auth::id();

        if ($header->save()) {
            $header_id = $header->id;

            if ($request->jenis == 'widget') {
                foreach ($request->title as $key => $row) {
                    $detail = new KuisDetail;
                    $detail->header_id = $header_id;
                    $detail->title = $request->title[$key];
                    $detail->pilihan = $request->pilihan[$key];
                    $detail->bobot = $request->have_bobot[$key];
                    $detail->komponen_id = $request->komponen_id[$key];
                    $detail->created_at = date('Y-m-d H:i:s');
                    $detail->created_by = Auth::id();

                    $detail->save();
                }
            }

            if ($request->jenis == 'combine') {
                $i = 1;
                foreach ($request->title as $key => $row) {
                    $detail = new KuisDetail;
                    $detail->header_id = $header_id;
                    $detail->title = $request->title[$i];
                    $detail->satuan = $request->satuan[$i];
                    $detail->pilihan = $request->pilihan[$i];
                    $detail->bobot = $request->have_bobot[$i];
                    $detail->created_at = date('Y-m-d H:i:s');
                    $detail->created_by = Auth::id();

                    $detail->save();

                    $i++;
                }

                $kondisi = array_filter($request->kondisi);
                foreach ($kondisi as $key => $val) {
                    $bobot = new KuisBobot;
                    $bobot->header_id = $header_id;
                    $bobot->kondisi = $kondisi[$key];
                    $bobot->label = (isset($request->label[$key])) ? $request->label[$key] : null;
                    $bobot->nilai = $request->nilai[$key];
                    $bobot->bobot = $request->bobot[$key];
                    $bobot->rating = (isset($request->rating[$key])) ? $request->rating[$key] : null;
                    $bobot->rating_color = $request->background[$key];
                    $bobot->created_at = date('Y-m-d H:i:s');
                    $bobot->created_by = Auth::id();

                    $bobot->save();

                    if (isset($request->file[$key])) {
                        foreach ($request->file[$key] as $keys => $rows) {
                            $time = time();
                            $filenamewithextension = $rows->getClientOriginalName();
                            $extension = $rows->getClientOriginalExtension();

                            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                            $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                            $rows->move($oriPath, $filename);

                            $savefile = new KuisBobotFile;
                            $savefile->pertanyaan_bobot_id = $bobot->id;
                            $savefile->name = (isset($request->name[$key][$keys]) && !empty($request->name[$key][$keys])) ? $request->name[$key][$keys] : '';
                            $savefile->file = $filename;
                            $savefile->created_at = date('Y-m-d H:i:s');
                            $savefile->created_by = Auth::id();

                            $savefile->save();
                        }
                    }
                }
            }

            if ($request->jenis == 'single') {
                $detail = new KuisDetail;
                $detail->header_id = $header_id;
                $detail->title = $request->title;
                $detail->satuan = $request->satuan;
                $detail->pilihan = $request->pilihan;
                $detail->bobot = $request->have_bobot;
                $detail->created_at = date('Y-m-d H:i:s');
                $detail->created_by = Auth::id();

                $detail->save();

                if ($request->have_bobot == '1') {
                    if ($request->pilihan == 'radio' || $request->pilihan == 'dropdown') {
                        $label = array_filter($request->label);
                        foreach ($label as $key => $val) {
                            $bobot = new KuisBobot;
                            $bobot->header_id = $header_id;
                            $bobot->kondisi = (isset($request->kondisi[$key])) ? $request->kondisi[$key] : null;
                            $bobot->label = (isset($request->label[$key])) ? $request->label[$key] : null;
                            $bobot->nilai = (isset($request->nilai[$key])) ? $request->nilai[$key] : null;
                            $bobot->bobot = (isset($request->bobot[$key])) ? $request->bobot[$key] : null;
                            $bobot->rating = (isset($request->rating[$key])) ? $request->rating[$key] : null;
                            $bobot->rating_color = (isset($request->background[$key])) ? $request->background[$key] : null;
                            $bobot->created_at = date('Y-m-d H:i:s');
                            $bobot->created_by = Auth::id();

                            $bobot->save();

                            if (isset($request->file[$key])) {
                                foreach ($request->file[$key] as $keys => $rows) {
                                    $time = time();
                                    $filenamewithextension = $rows->getClientOriginalName();
                                    $extension = $rows->getClientOriginalExtension();

                                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                    $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                    $rows->move($oriPath, $filename);

                                    $savefile = new KuisBobotFile;
                                    $savefile->pertanyaan_bobot_id = $bobot->id;
                                    $savefile->name = (isset($request->name[$key][$keys]) && !empty($request->name[$key][$keys])) ? $request->name[$key][$keys] : '';
                                    $savefile->file = $filename;
                                    $savefile->created_at = date('Y-m-d H:i:s');
                                    $savefile->created_by = Auth::id();

                                    $savefile->save();
                                }
                            }
                        }
                    } else {
                        $kondisi = array_filter($request->kondisi);
                        foreach ($kondisi as $key => $val) {
                            $bobot = new KuisBobot;
                            $bobot->header_id = $header_id;
                            $bobot->kondisi = $kondisi[$key];
                            $bobot->label = (isset($request->label[$key])) ? $request->label[$key] : null;
                            $bobot->nilai = (isset($request->nilai[$key])) ? $request->nilai[$key] : null;
                            $bobot->bobot = (isset($request->bobot[$key])) ? $request->bobot[$key] : null;
                            $bobot->rating = (isset($request->rating[$key])) ? $request->rating[$key] : null;
                            $bobot->rating_color = (isset($request->background[$key])) ? $request->background[$key] : null;
                            $bobot->created_at = date('Y-m-d H:i:s');
                            $bobot->created_by = Auth::id();

                            $bobot->save();

                            if (isset($request->file[$key])) {
                                foreach ($request->file[$key] as $keys => $rows) {
                                    $time = time();
                                    $filenamewithextension = $rows->getClientOriginalName();
                                    $extension = $rows->getClientOriginalExtension();

                                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                    $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                    $rows->move($oriPath, $filename);

                                    $savefile = new KuisBobotFile;
                                    $savefile->pertanyaan_bobot_id = $bobot->id;
                                    $savefile->name = (isset($request->name[$key][$keys]) && !empty($request->name[$key][$keys])) ? $request->name[$key][$keys] : '';
                                    $savefile->file = $filename;
                                    $savefile->created_at = date('Y-m-d H:i:s');
                                    $savefile->created_by = Auth::id();

                                    $savefile->save();
                                }
                            }
                        }
                    }
                }
            }

            //die;

            $msg = 'Pertanyaan kuesioner berhasil dibuat.';
            return redirect()->route('admin.pertanyaan.index', $id)->with('success', $msg);

        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kuisioner gagal dibuat. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function edit($id) {
        $this->authorize('access', [\App\Pertanyaan::class, Auth::user()->role, 'edit']);

        $pertanyaan = KuisHeader::whereNull('deleted_by')->where('id', $id)
            ->select(['id', 'kuis_id', 'jenis', 'deskripsi', 'caption', 'formula', 'widget_id'])
            ->first()
            ->toArray();

        if ($pertanyaan['jenis'] == 'combine') {
            $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $id)
                ->select(['id', 'header_id', 'title', 'satuan', 'pilihan', 'bobot'])
                ->get()
                ->toArray();
        } else {
            $detail = KuisDetail::whereNull('deleted_by')->where('header_id', $id)
                ->first()
                ->toArray();
        }

        $bobot = KuisBobot::whereNull('deleted_by')->where('header_id', $id)
            ->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])
            ->orderBy('kondisi')
            ->get()
            ->toArray();

        foreach ($bobot as $key => $row) {
            $file = KuisBobotFile::whereNull('deleted_by')
                ->where('pertanyaan_bobot_id', $row['id'])
                ->select(['id', 'name', 'file'])
                ->get()
                ->toArray();

            $bobot[$key]['file'] = $file;
        }

        $kuis = Kuis::where('id', $pertanyaan['kuis_id'])->select(['id', 'title'])->first();

        return view('pertanyaan.edit', compact('id', 'kuis', 'pertanyaan', 'detail', 'bobot'));
    }

    public function update(Request $request, $id) {
        //echo '<pre>';
        //echo $id;
        //print_r ($request->all()); //die;

        $oriPath = public_path('uploads/kuis');

        $kuisid = KuisHeader::where('id', $id)->first();

        if ($request->jenis == 'widget') {
            $widget = Widget::where('id', $request->widget_id)->first();
            $caption = $widget->name;
        }

        $update = KuisHeader::where('id', $id)->update([
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            //'widget_id' => ($request->jenis == 'widget') ? $request->widget_id : null,
            'caption' => ($request->jenis == 'widget') ? $caption : $request->caption,
            'formula' => $request->formula,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id()
        ]);

        $headerLog = new KuisHeaderLog;
        $headerLog->pertanyaan_header_id = $id;
        $headerLog->jenis = $request->jenis;
        $headerLog->deskripsi = $request->deskripsi;
        $headerLog->widget_id = ($request->jenis == 'widget') ? $request->widget_id : null;
        $headerLog->caption = ($request->jenis == 'widget') ? $caption : $request->caption;
        $headerLog->formula = $request->formula;
        $headerLog->created_at = date('Y-m-d H:i:s');
        $headerLog->created_by = Auth::id();
        $headerLog->save();

        $checkcurrent = KuisDetail::where('id', $request->detail_id)->select(['pilihan', 'bobot'])->first();
//        print_r ($checkcurrent); //die;

        if ($update) {
            if ($request->jenis == 'widget') {
                //$current = KuisHeader::where('id', $id)->
                if ($request->current_widget_id != $request->widget_id) {
                    $get = KuisDetail::where('header_id', $id)->select('id')->get()->toArray();
                    $array = array_column($get, 'id');

                    $update = KuisDetail::whereIn('id', $array)->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'deleted_by' => Auth::id()
                    ]);

                    foreach ($request->title as $key => $row) {
                        $insert = new KuisDetail;
                        $insert->header_id = $id;
                        $insert->title = $request->title[$key];
                        $insert->pilihan = $request->pilihan[$key];
                        $insert->bobot = $request->have_bobot[$key];
                        $insert->komponen_id = $request->komponen_id[$key];
                        $insert->created_at = date('Y-m-d H:i:s');
                        $insert->created_by = Auth::id();

                        $insert->save();
                    }

                    $updateheader = KuisHeader::where('id', $id)->update([
                        'widget_id' => $request->widget_id
                    ]);
                } else {
                    $update = KuisDetail::where('header_id', $id)->update([
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);
                }
            }

            if ($request->jenis == 'combine') {
                $idexist = [];

                $current = KuisBobot::whereNull('deleted_by')->where('header_id', $id)->select(['id', 'header_id', 'kondisi', 'nilai', 'label', 'bobot', 'rating', 'rating_color'])->get()->toArray();

                $currentid = [];
                foreach ($current as $row) {
                    $currentid[] = $row['id'];
                }

                $updated = [];
                foreach ($request->choice_id as $key => $row) {
                    $updated[] = [
                        'id' => $row,
                        'header_id' => $id,
                        'kondisi' => (isset($request->kondisi[$key])) ? $request->kondisi[$key] : '',
                        'nilai' => (isset($request->nilai[$key])) ? $request->nilai[$key] : '',
                        'label' => (isset($request->label[$key])) ? $request->label[$key] : '',
                        'bobot' => (isset($request->bobot[$key])) ? $request->bobot[$key] : '',
                        'rating' => (isset($request->rating[$key])) ? $request->rating[$key] : '',
                        'rating_color' => $request->background[$key]
                    ];
                }

                $result = Helper::compare($updated, $current, false);

                $setinsert = $setdelete = $setupdate = [];
                foreach ($result as $key => $row) {
                    if (empty($row['label'])) {
                        $setdelete[] = $row;
                    } else if (!in_array($row['id'], $currentid)) {
                        $setinsert[] = $row;
                    } else {
                        $setupdate[] = $row;
                    }
                }


                //die;

                //echo 'update';
                //print_r ($setupdate);
                if (!empty($setupdate)) {
                    $files = [];
                    foreach ($setupdate as $key => $row) {
                        $headerupdate = KuisBobot::where('id', $row['id'])->update([
                            'header_id' => $row['header_id'],
                            'kondisi' => $row['kondisi'],
                            'nilai' => $row['nilai'],
                            'label' => $row['label'],
                            'bobot' => $row['bobot'],
                            'rating' => $row['rating'],
                            'rating_color' => $row['rating_color'],
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::id()
                        ]);
                    }
                }

                if (!empty($setinsert)) {
                    $files = [];
                    foreach ($setinsert as $key => $row) {
                        $existfileid = (isset($request->choice[$row['id']]['fileid'])) ? $request->choice[$row['id']]['fileid'] : [];
                        $existfilename = (isset($request->choice[$row['id']]['name'])) ? $request->choice[$row['id']]['name'] : [];
                        $existfile = (isset($request->choice[$row['id']]['files'])) ? $request->choice[$row['id']]['files'] : [];
                        $addfile = (isset($request->choice[$row['id']]['newfile'])) ? $request->choice[$row['id']]['newfile'] : [];

                        $buildfile = $this->restructure($existfileid, $existfilename, $existfile, $addfile);

                        $final = [
                            'header' => $row,
                            'child' => $buildfile['lastfile']
                        ];

                        $headerinsert = new KuisBobot;
                        $headerinsert->header_id = $final['header']['header_id'];
                        $headerinsert->kondisi = $final['header']['kondisi'];
                        $headerinsert->nilai = $final['header']['nilai'];
                        $headerinsert->label = $final['header']['label'];
                        $headerinsert->bobot = $final['header']['bobot'];
                        $headerinsert->rating = $final['header']['rating'];
                        $headerinsert->rating_color = $final['header']['rating_color'];
                        $headerinsert->created_at = date('Y-m-d H:i:s');
                        $headerinsert->created_by = Auth::id();

                        $headerinsert->save();

                        if (isset($final['child']) && !empty($final['child'])) {
                            foreach ($final['child'] as $key => $val) {
                                $time = time();
                                $filenamewithextension = $val['file']->getClientOriginalName();
                                $extension = $val['file']->getClientOriginalExtension();

                                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                $val['file']->move($oriPath, $filename);

                                $savefile = new KuisBobotFile;
                                $savefile->pertanyaan_bobot_id = $headerinsert->id;
                                $savefile->name = (isset($val['name']) && !empty($val['name'])) ? $val['name'] : '';
                                $savefile->file = $filename;
                                $savefile->created_at = date('Y-m-d H:i:s');
                                $savefile->created_by = Auth::id();

                                $savefile->save();

                                $idexist[] = $savefile->id;
                            }
                        }
                    }
                }

                if (!empty($setdelete)) {
                    $files = [];
                    foreach ($setdelete as $key => $row) {
                        $check = KuisBobot::where('id', $row['id'])->first();
                        if ($check) {
                            $headerdelete = KuisBobot::where('id', $row['id'])->update([
                                'deleted_at' => date('Y-m-d H:i:s'),
                                'deleted_by' => Auth::id()
                            ]);

                            $detaildelete = KuisBobotFile::where('pertanyaan_bobot_id', $row['id'])->update([
                                'deleted_at' => date('Y-m-d H:i:s'),
                                'deleted_by' => Auth::id()
                            ]);
                        }
                    }
                }

                foreach ($current as $key => $val) {
                    $existfileid = (isset($request->choice[$val['id']]['fileid'])) ? $request->choice[$val['id']]['fileid'] : [];
                    $existfilename = (isset($request->choice[$val['id']]['name'])) ? $request->choice[$val['id']]['name'] : [];
                    $existfile = (isset($request->choice[$val['id']]['files'])) ? $request->choice[$val['id']]['files'] : [];
                    $addfile = (isset($request->choice[$val['id']]['newfile'])) ? $request->choice[$val['id']]['newfile'] : [];

                    $buildfile = $this->restructure($existfileid, $existfilename, $existfile, $addfile);
                    $final = [
                        'header' => $val,
                        'child' => $buildfile['lastfile']
                    ];

                    if (isset($final['child']) && !empty($final['child'])) {                                
                        foreach ($final['child'] as $keys => $rows) {
                            $idexist[] = $rows['id'];

                            if (empty($rows['id'])) {
                                $time = time();
                                $filenamewithextension = $rows['file']->getClientOriginalName();
                                $extension = $rows['file']->getClientOriginalExtension();

                                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                $rows['file']->move($oriPath, $filename);

                                $savefile = new KuisBobotFile;
                                $savefile->pertanyaan_bobot_id = $final['header']['id'];
                                $savefile->name = (isset($rows['name']) && !empty($rows['name'])) ? $rows['name'] : '';
                                $savefile->file = $filename;
                                $savefile->created_at = date('Y-m-d H:i:s');
                                $savefile->created_by = Auth::id();

                                $savefile->save();

                                $idexist[] = $savefile->id;
                            }
                        }
                    }

                    $updateimage = KuisBobotFile::whereNotIn('id', $idexist)
                        ->where('pertanyaan_bobot_id', $final['header']['id'])->update([
                            'deleted_at' => date('Y-m-d H:i:s'),
                            'deleted_by' => Auth::id()
                        ]);
                }



                foreach ($request->title as $key => $row) {
                    $updatedetail = KuisDetail::where('id', $request->detail_id[$key])->update([
                        'header_id' => $id,
                        'title' => $request->title[$key],
                        'satuan' => $request->satuan[$key],
                        'pilihan' => $request->pilihan[$key],
                        'bobot' => $request->have_bobot[$key],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::id()
                    ]);


                    $detailLog = new KuisDetailLog;
                    $detailLog->header_id = $id;
                    $detailLog->detail_id = $request->detail_id[$key];
                    $detailLog->title = $request->title[$key];
                    $detailLog->pilihan = $request->pilihan[$key];
                    $detailLog->bobot = $request->have_bobot[$key];
                    $detailLog->created_at = date('Y-m-d H:i:s');
                    $detailLog->created_by = Auth::id();
                    $detailLog->save();
                }


            } 

            if ($request->jenis == 'single') {

                $checkcurrent = KuisDetail::where('id', $request->detail_id)->select(['pilihan', 'bobot'])->first();

                if ($checkcurrent->pilihan != $request->pilihan || $checkcurrent->bobot != $request->have_bobot) {
                    $getparent = KuisBobot::where('header_id', $id)->select('id')->get()->toArray();
                    $array = array_column($getparent, 'id');

                    $updatechild = KuisBobotFile::whereIn('pertanyaan_bobot_id', $array)->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'deleted_by' => Auth::id()
                    ]);

                    $updateparent = KuisBobot::where('header_id', $id)->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'deleted_by' => Auth::id()
                    ]);

                    $label = array_filter($request->label);
                    foreach ($label as $key => $val) {
                        $bobot = new KuisBobot;
                        $bobot->header_id = $id;
                        $bobot->kondisi = (isset($request->kondisi[$key])) ? $request->kondisi[$key] : null;
                        $bobot->label = (isset($request->label[$key])) ? $request->label[$key] : null;
                        $bobot->nilai = (isset($request->nilai[$key])) ? $request->nilai[$key] : null;
                        $bobot->bobot = (isset($request->bobot[$key])) ? $request->bobot[$key] : null;
                        $bobot->rating = (isset($request->rating[$key])) ? $request->rating[$key] : null;
                        $bobot->rating_color = (isset($request->background[$key])) ? $request->background[$key] : null;
                        $bobot->created_at = date('Y-m-d H:i:s');
                        $bobot->created_by = Auth::id();

                        $bobot->save();

                        if (isset($request->file[$key])) {
                            foreach ($request->file[$key] as $keys => $rows) {
                                $time = time();
                                $filenamewithextension = $rows->getClientOriginalName();
                                $extension = $rows->getClientOriginalExtension();

                                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                $rows->move($oriPath, $filename);

                                $savefile = new KuisBobotFile;
                                $savefile->pertanyaan_bobot_id = $bobot->id;
                                $savefile->name = (isset($request->name[$key][$keys]) && !empty($request->name[$key][$keys])) ? $request->name[$key][$keys] : '';
                                $savefile->file = $filename;
                                $savefile->created_at = date('Y-m-d H:i:s');
                                $savefile->created_by = Auth::id();

                                $savefile->save();
                            }
                        }
                    }

                } else {
                    $idexist = [];

                    $current = KuisBobot::whereNull('deleted_by')->where('header_id', $id)->select(['id', 'header_id', 'kondisi', 'nilai', 'label', 'bobot', 'rating', 'rating_color'])->get()->toArray();

                    $currentid = [];
                    foreach ($current as $row) {
                        $currentid[] = $row['id'];
                    }

                    $updated = [];
                    foreach ($request->choice_id as $key => $row) {
                        $updated[] = [
                            'id' => $row,
                            'header_id' => $id,
                            'kondisi' => (isset($request->kondisi[$key])) ? $request->kondisi[$key] : '',
                            'nilai' => (isset($request->nilai[$key])) ? $request->nilai[$key] : '',
                            'label' => (isset($request->label[$key])) ? $request->label[$key] : '',
                            'bobot' => (isset($request->bobot[$key])) ? $request->bobot[$key] : '',
                            'rating' => (isset($request->rating[$key])) ? $request->rating[$key] : '',
                            'rating_color' => $request->background[$key]
                        ];
                    }

                    $result = Helper::compare($updated, $current, false);

                    $setinsert = $setdelete = $setupdate = [];
                    foreach ($result as $key => $row) {
                        if (empty($row['label'])) {
                            $setdelete[] = $row;
                        } else if (!in_array($row['id'], $currentid)) {
                            $setinsert[] = $row;
                        } else {
                            $setupdate[] = $row;
                        }
                    }

                    //echo 'update';
                    //print_r ($setupdate);
                    if (!empty($setupdate)) {
                        $files = [];
                        foreach ($setupdate as $key => $row) {
                            $headerupdate = KuisBobot::where('id', $row['id'])->update([
                                'header_id' => $row['header_id'],
                                'kondisi' => $row['kondisi'],
                                'nilai' => $row['nilai'],
                                'label' => $row['label'],
                                'bobot' => $row['bobot'],
                                'rating' => $row['rating'],
                                'rating_color' => $row['rating_color'],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => Auth::id()
                            ]);
                        }
                    }

                    if (!empty($setinsert)) {
                        $files = [];
                        foreach ($setinsert as $key => $row) {
                            $existfileid = (isset($request->choice[$row['id']]['fileid'])) ? $request->choice[$row['id']]['fileid'] : [];
                            $existfilename = (isset($request->choice[$row['id']]['name'])) ? $request->choice[$row['id']]['name'] : [];
                            $existfile = (isset($request->choice[$row['id']]['files'])) ? $request->choice[$row['id']]['files'] : [];
                            $addfile = (isset($request->choice[$row['id']]['newfile'])) ? $request->choice[$row['id']]['newfile'] : [];

                            $buildfile = $this->restructure($existfileid, $existfilename, $existfile, $addfile);

                            $final = [
                                'header' => $row,
                                'child' => $buildfile['lastfile']
                            ];

                            $headerinsert = new KuisBobot;
                            $headerinsert->header_id = $final['header']['header_id'];
                            $headerinsert->kondisi = $final['header']['kondisi'];
                            $headerinsert->nilai = $final['header']['nilai'];
                            $headerinsert->label = $final['header']['label'];
                            $headerinsert->bobot = $final['header']['bobot'];
                            $headerinsert->rating = $final['header']['rating'];
                            $headerinsert->rating_color = $final['header']['rating_color'];
                            $headerinsert->created_at = date('Y-m-d H:i:s');
                            $headerinsert->created_by = Auth::id();

                            $headerinsert->save();

                            if (isset($final['child']) && !empty($final['child'])) {
                                foreach ($final['child'] as $key => $val) {
                                    $time = time();
                                    $filenamewithextension = $val['file']->getClientOriginalName();
                                    $extension = $val['file']->getClientOriginalExtension();

                                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                    $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                    $val['file']->move($oriPath, $filename);

                                    $savefile = new KuisBobotFile;
                                    $savefile->pertanyaan_bobot_id = $headerinsert->id;
                                    $savefile->name = (isset($val['name']) && !empty($val['name'])) ? $val['name'] : '';
                                    $savefile->file = $filename;
                                    $savefile->created_at = date('Y-m-d H:i:s');
                                    $savefile->created_by = Auth::id();

                                    $savefile->save();

                                    $idexist[] = $savefile->id;
                                }
                            }
                        }
                    }

                    if (!empty($setdelete)) {
                        $files = [];
                        foreach ($setdelete as $key => $row) {
                            $check = KuisBobot::where('id', $row['id'])->first();
                            if ($check) {
                                $headerdelete = KuisBobot::where('id', $row['id'])->update([
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                    'deleted_by' => Auth::id()
                                ]);

                                $detaildelete = KuisBobotFile::where('pertanyaan_bobot_id', $row['id'])->update([
                                    'deleted_at' => date('Y-m-d H:i:s'),
                                    'deleted_by' => Auth::id()
                                ]);
                            }
                        }
                    }

                    foreach ($current as $key => $val) {
                        $existfileid = (isset($request->choice[$val['id']]['fileid'])) ? $request->choice[$val['id']]['fileid'] : [];
                        $existfilename = (isset($request->choice[$val['id']]['name'])) ? $request->choice[$val['id']]['name'] : [];
                        $existfile = (isset($request->choice[$val['id']]['files'])) ? $request->choice[$val['id']]['files'] : [];
                        $addfile = (isset($request->choice[$val['id']]['newfile'])) ? $request->choice[$val['id']]['newfile'] : [];

                        $buildfile = $this->restructure($existfileid, $existfilename, $existfile, $addfile);
                        $final = [
                            'header' => $val,
                            'child' => $buildfile['lastfile']
                        ];

                        if (isset($final['child']) && !empty($final['child'])) {                                
                            foreach ($final['child'] as $keys => $rows) {
                                $idexist[] = $rows['id'];

                                if (empty($rows['id'])) {
                                    $time = time();
                                    $filenamewithextension = $rows['file']->getClientOriginalName();
                                    $extension = $rows['file']->getClientOriginalExtension();

                                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                                    $filename = hash('sha256', $filename . $time . rand(1, 100), false) . $time . '.' . $extension;

                                    $rows['file']->move($oriPath, $filename);

                                    $savefile = new KuisBobotFile;
                                    $savefile->pertanyaan_bobot_id = $final['header']['id'];
                                    $savefile->name = (isset($rows['name']) && !empty($rows['name'])) ? $rows['name'] : '';
                                    $savefile->file = $filename;
                                    $savefile->created_at = date('Y-m-d H:i:s');
                                    $savefile->created_by = Auth::id();

                                    $savefile->save();

                                    $idexist[] = $savefile->id;
                                }
                            }
                        }

                        $updateimage = KuisBobotFile::whereNotIn('id', $idexist)
                            ->where('pertanyaan_bobot_id', $final['header']['id'])->update([
                                'deleted_at' => date('Y-m-d H:i:s'),
                                'deleted_by' => Auth::id()
                            ]);
                    }
                }

                
                $updatedetail = KuisDetail::where('id', $request->detail_id)->update([
                    'header_id' => $id,
                    'title' => $request->title,
                    'satuan' => $request->satuan,
                    'pilihan' => $request->pilihan,
                    'bobot' => $request->have_bobot,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::id()
                ]);

                $detailLog = new KuisDetailLog;
                $detailLog->header_id = $id;
                $detailLog->detail_id = $request->detail_id;
                $detailLog->title = $request->title;
                $detailLog->pilihan = $request->pilihan;
                $detailLog->bobot = $request->have_bobot;
                $detailLog->created_at = date('Y-m-d H:i:s');
                $detailLog->created_by = Auth::id();
                $detailLog->save();


            }
        }

        //die;

        $msg = 'Pertanyaan kuesioner berhasil diubah.';
        return redirect()->route('admin.pertanyaan.index', $kuisid->kuis_id)->with('success', $msg);
    }

    public function show($id) {
        $this->authorize('access', [\App\Pertanyaan::class, Auth::user()->role, 'show']);

        $pertanyaan = KuisHeader::where('id', $id)
            ->select(['id', 'kuis_id', 'jenis', 'deskripsi', 'caption', 'formula', 'widget_id'])
            ->first()
            ->toArray();

        if ($pertanyaan['jenis'] == 'combine') {
            $detail = KuisDetail::where('header_id', $id)
                ->select(['id', 'header_id', 'title', 'satuan', 'pilihan', 'bobot'])
                ->get()
                ->toArray();
        } else {
            $detail = KuisDetail::where('header_id', $id)
                ->first()
                ->toArray();
        }

        $bobot = KuisBobot::whereNull('deleted_by')->where('header_id', $id)
            ->select(['id', 'kondisi', 'label', 'nilai', 'bobot', 'rating', 'rating_color'])
            ->orderBy('kondisi')
            ->get()
            ->toArray();

        foreach ($bobot as $key => $row) {
            $file = KuisBobotFile::whereNull('deleted_by')
                ->where('pertanyaan_bobot_id', $row['id'])
                ->select(['id', 'name', 'file'])
                ->get()
                ->toArray();

            $bobot[$key]['file'] = $file;
        }

        $kuis = Kuis::where('id', $pertanyaan['kuis_id'])->select(['id', 'title'])->first();

        return view('pertanyaan.show', compact('kuis', 'pertanyaan', 'detail', 'bobot'));
    }

    public function delete(Request $request) {
        $update = KuisHeader::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Pertanyaan berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Pertanyaan gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function sort($id) {
        $this->authorize('access', [\App\Pertanyaan::class, Auth::user()->role, 'sort']);

        $kuis = KuisHeader::whereNull('deleted_by')->where('kuis_id', $id)->select(['id', 'jenis', 'caption'])->orderBy('position')->get();

        return view('pertanyaan.sort', compact('kuis', 'id'));
    }

    public function submit(Request $request) {
        $position = str_replace(['"', '[', ']'], ['', '', ''], $request->position);

        $explode = explode(',', $position);
        $i = 1;
        foreach ($explode as $row) {
            //echo $row; echo '<br />';
            $update = KuisHeader::where('id', $row)->update([
                'position' => $i,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::id()
            ]);

            $i++;
        }

        /*$change = Kuis::where('status', 1)->orWhereNotNull('deleted_by')->update([
            'position' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id()
        ]);*/

        $msg = 'Sorting kuesioner berhasil dilakukan.';
        return redirect()->route('admin.pertanyaan.index', $request->cid)->with('success', $msg);
    }

    public function restructure($existfileid=array(), $existfilename=array(), $existfile=array(), $addfile=array()) {
        $lastid = [];
        if (!empty($existfileid)) {
            foreach ($existfileid as $key => $val) {
                foreach ($val as $keys => $vals) {
                    $lastid[] = $vals;
                }
            }
        }

        $lastname = [];
        if (!empty($existfilename)) {
            foreach ($existfilename as $key => $val) {
                foreach ($val as $keys => $vals) {
                    $lastname[] = $vals;
                }
            }
        }

        $lastfile = [];
        if (!empty($existfile)) {
            foreach ($existfile as $key => $val) {
                foreach ($val as $keys => $vals) {
                    $lastfile[] = $vals;
                }
            }
        }

        if (!empty($addfile)) {
            foreach ($addfile as $key => $val) {
                foreach ($val as $keys => $vals) {
                    $lastfile[] = $vals;
                }
            }
        }

        $finfile = [];
        foreach ($lastfile as $key => $val) {
            $finfile[] = [
                'id' => (isset($lastid[$key])) ? $lastid[$key] : '',
                'name' => $lastname[$key],
                'file' => $val
            ];
        }

        $data = [
            'deleted' => $lastid,
            'lastfile' => $finfile
        ];

        return $data;
    }


}
