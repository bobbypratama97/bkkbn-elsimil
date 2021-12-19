<?php

namespace App\Http\Controllers;

use App\Exports\KecamatanTemplate;
use App\Http\Controllers\Controller;
use App\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Kecamatan;
use Maatwebsite\Excel\Facades\Excel;

class KecamatanController extends Controller
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
        $this->authorize('access', [\App\Kecamatan::class, Auth::user()->role, 'index']);

        $paginate = Kecamatan::leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'adms_kecamatan.kabupaten_kode');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'adms_kabupaten.created_by');
        })
        ->select([
            'adms_provinsi.nama as provinsi', 'adms_provinsi.provinsi_kode', 
            'adms_kabupaten.nama as kabupaten', 
            'adms_kecamatan.*', 
            'users.name'
        ])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->whereNull('adms_kecamatan.deleted_by');

        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $paginate = $paginate->where('adms_provinsi.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kabupaten.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kecamatan.nama', 'like', '%' . $request->name . '%');
        }

        $paginate = $paginate->paginate(10);
        $camat = $paginate->items();

        return view('kecamatan.index', ['camat' => $camat, 'paginate' => $paginate, 'name' => $name]);
    }

    public function create() {
    }

    public function store(Request $request) {
        if ($request->hasFile('file')) {
            $size = $request->file('file')->getSize();

            $finSize = Helper::humanFilesize($size);

            //echo $finSize;

            if ($size > 36602462) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Perhatian', 
                        'keterangan' => 'Ukuran file yang Anda upload : '.$finSize.'<br />Ukuran file tidak boleh melebihi dari 30MB'
                    ]);
            } else {
                ini_set('memory_limit', '-1');
                set_time_limit(0);

                //$data = Excel::import(new WilayahImport, $request->file('file')->store('temp'));
                //echo '<pre>'; print_r ($data);

                $file = $request->file('file');
                $name = time().'.xlsx';
                $path = public_path('documents'.DIRECTORY_SEPARATOR);

                if ( $file->move($path, $name) ) {
                    $inputFileName = $path.$name;

                    /*if('csv' == $extension) {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                    } else {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    }*/

                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $reader->setReadDataOnly(true);
                    //$reader->setLoadSheetsOnly(["USER DATA"]);
                    $spreadSheet = $reader->load($inputFileName);
                    $workSheet = $spreadSheet->getActiveSheet();
                    $startRow = 2;
                    $max = 650000;
                    $columns = [
                        "A" => "kode_kabupaten",
                        "B" => "kabupaten",
                        "C" => "kode_kecamatan",
                        "D" => "kecamatan",
                        // "G" => "kode_kelurahan",
                        // "H" => "kelurahan",
                        // "I" => "kode_rw",
                        // "J" => "rw",
                        // "K" => "kode_rt",
                        // "L" => "rt"
                    ];

                    $data_insert = [];
                    for ($i = $startRow; $i < $max; $i++) {
                        $id = $workSheet->getCell("A$i")->getValue();
                        if (empty( $id) || !is_numeric( $id )) continue;

                        $data_row = [];
                        foreach ($columns as $col => $field) {
                            $val = $workSheet->getCell("$col$i")->getValue();
                            $data_row[$field] = $val;
                        }
                        $data_insert[] = $data_row;
                    }
                    $input = array_map("unserialize", array_unique(array_map("serialize", $data_insert)));

                    $provinsi = $kota = $kecamatan = $kelurahan = $rwrt = [];
                    foreach ($input as $key => $row) {

                        $kecamatan[] = [
                            'kabupaten_kode' => $row['kode_kabupaten'],
                            'kecamatan_kode' => $row['kode_kecamatan'],
                            'kecamatan' => $row['kecamatan']
                        ];

                        // $kelurahan[] = [
                        //     'kecamatan_kode' => $row['kode_kecamatan'],
                        //     'kelurahan_kode' => $row['kode_kelurahan'],
                        //     'kelurahan' => $row['kelurahan']
                        // ];

                        // $rwrt[] = [
                        //     'kelurahan_kode' => $row['kode_kelurahan'],
                        //     'kode_rw' => $row['kode_rw'],
                        //     'rw' => $row['rw'],
                        //     'kode_rt' => $row['kode_rt'],
                        //     'rt' => $row['rt']
                        // ];
                    }

                    $kecamatan = array_map("unserialize", array_unique(array_map("serialize", $kecamatan)));
                    // $kelurahan = array_map("unserialize", array_unique(array_map("serialize", $kelurahan)));
                    // $rwrt = array_map("unserialize", array_unique(array_map("serialize", $rwrt)));

                    $camatsukses = $camatgagal = 0;
                    foreach ($kecamatan as $key => $row) {
                        $check = Kecamatan::whereNull('deleted_by')->where('kabupaten_kode', $row['kabupaten_kode'])->where('kecamatan_kode', $row['kecamatan_kode'])->where('nama', $row['kecamatan'])->first();

                        if ($check) {
                            $camatgagal = $camatgagal + 1;
                        } else {
                            //cek curretn prov
                            $current_kab = Kabupaten::where('kabupaten_kode', $row['kabupaten_kode'])->first();
                            if(!$current_kab) {
                                $camatgagal = $camatgagal + 1;
                            }else{
                                $camat = new Kecamatan;
                                $camat->kabupaten_kode = $row['kabupaten_kode'];
                                $camat->kecamatan_kode = $row['kecamatan_kode'];
                                $camat->nama = $row['kecamatan'];
                                $camat->status = 2;
                                $camat->created_at = date('Y-m-d H:i:s');
                                $camat->created_by = Auth::id();

                                if ($camat->save()) {
                                    $camatsukses = $camatsukses + 1;
                                } else {
                                    $camatgagal = $camatgagal + 1;
                                }
                            }
                        }
                    }

                    // $lurahsukses = $lurahgagal = 0;
                    // foreach ($kelurahan as $key => $row) {
                    //     $check = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $row['kecamatan_kode'])->where('kelurahan_kode', $row['kelurahan_kode'])->where('nama', $row['kelurahan'])->first();

                    //     if ($check) {
                    //         $lurahgagal = $lurahgagal + 1;
                    //     } else {
                    //         $lurah = new Kelurahan;
                    //         $lurah->kecamatan_kode = $row['kecamatan_kode'];
                    //         $lurah->kelurahan_kode = $row['kelurahan_kode'];
                    //         $lurah->nama = $row['kelurahan'];
                    //         $lurah->status = 2;
                    //         $lurah->created_at = date('Y-m-d H:i:s');
                    //         $lurah->created_by = Auth::id();

                    //         if ($lurah->save()) {
                    //             $lurahsukses = $lurahsukses + 1;
                    //         } else {
                    //             $lurahgagal = $lurahgagal + 1;
                    //         }
                    //     }
                    // }

                    // $rwrtsukses = $rwrtgagal = 0;
                    // foreach ($rwrt as $key => $row) {
                    //     $check = Rwrt::whereNull('deleted_by')->where('kelurahan_kode', $row['kelurahan_kode'])->where('kode_rw', $row['kode_rw'])->where('rw', $row['rw'])->where('kode_rt', $row['kode_rt'])->where('rt', $row['rt'])->first();

                    //     if ($check) {
                    //         $rwrtgagal = $rwrtgagal + 1;
                    //     } else {
                    //         $rw = new Rwrt;
                    //         $rw->kelurahan_kode = $row['kelurahan_kode'];
                    //         $rw->kode_rw = $row['kode_rw'];
                    //         $rw->rw = $row['rw'];
                    //         $rw->kode_rt = $row['kode_rt'];
                    //         $rw->rt = $row['rt'];
                    //         $rw->status = 2;
                    //         $rw->created_at = date('Y-m-d H:i:s');
                    //         $rw->created_by = Auth::id();

                    //         if ($rw->save()) {
                    //             $rwrtsukses = $rwrtsukses + 1;
                    //         } else {
                    //             $rwrtgagal = $rwrtgagal + 1;
                    //         }
                    //     }
                    // }

                    $msg = 'Data master wilayah telah diproses. <br /><br />';
                    $msg .= '<table class="table table-bordered">';
                    $msg .= '
                        <thead>
                            <tr>
                                <th>Kecamatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $camatsukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $camatgagal . '</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    ';
                    $msg .= '</table>';
                    $msg .= '<span class="text-dark font-weight-bolder">Untuk data yang gagal diproses, kemungkinan data tidak lengkap atau data sudah ada sebelumnya.</span>';

                    return redirect()->route('admin.kecamatan.index')->with('success', $msg);
                }
            }
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Proses upload file master wilayah gagal. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function edit($id) {
        $this->authorize('access', [\App\Kecamatan::class, Auth::user()->role, 'edit']);

        $status = Helper::status();

        $camat = Kecamatan::leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'adms_kecamatan.kabupaten_kode');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->select([
            'adms_provinsi.nama as provinsi', 'adms_provinsi.provinsi_kode', 
            'adms_kabupaten.nama as kabupaten', 
            'adms_kecamatan.*'
        ])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->whereNull('adms_kecamatan.deleted_by')
        ->where('adms_kecamatan.id', $id)
        ->first();

        return view('kecamatan.edit', ['camat' => $camat, 'status' => $status]);
    }

    public function update(Request $request, $id) {
        $update = Kecamatan::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kecamatan berhasil diubah';
            return redirect()->route('admin.kecamatan.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kecamatan gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Kecamatan::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kecamatan berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kecamatan gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function upload()
    {
        $this->authorize('access', [\App\Kecamatan::class, Auth::user()->role, 'upload']);

        return view('kecamatan.upload');
    }

    public function downloadExcel(){
        $kota[] = [
            'Kode Kabupaten'        => '0000',
            'Kabupaten'             => '',
            'Kode Kecamatan'        => '000000',
            'Kecamatan'             => '',
            // 'Kode Kelurahan'        => '00000000',
            // 'Kelurahan'             => '',
            // 'Kode Rw'               => '01',
            // 'Rw'                    => '1',
            // 'Kode Rt'               => '004',
            // 'Rt'                    => '4'
        ];

        $title = 'Template_Upload_Kecamatan.xlsx';
        return Excel::download(new KecamatanTemplate($kota), $title);
    }
}
