<?php

namespace App\Http\Controllers;

use App\Exports\KelurahanTemplate;
use App\Http\Controllers\Controller;
use App\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Kelurahan;
use Maatwebsite\Excel\Facades\Excel;

class KelurahanController extends Controller
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
        $this->authorize('access', [\App\Kelurahan::class, Auth::user()->role, 'index']);

        $paginate = Kelurahan::leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'adms_kelurahan.kecamatan_kode');
        })
        ->leftJoin('adms_kabupaten', function($join) {
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
            'adms_kabupaten.nama as kabupaten', 'adms_kabupaten.kabupaten_kode',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.*', 
            'users.name'
        ])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->whereNull('adms_kecamatan.deleted_by')
        ->whereNull('adms_kelurahan.deleted_by');
        
        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $paginate = $paginate->where('adms_provinsi.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kabupaten.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kecamatan.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kelurahan.nama', 'like', '%' . $request->name . '%');
        }

        $paginate = $paginate->paginate(10);
        $lurah = $paginate->items();

        return view('kelurahan.index', ['lurah' => $lurah, 'paginate' => $paginate, 'name' => $name]);
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
                        "A" => "kode_kecamatan",
                        "B" => "kecamatan",
                        "C" => "kode_kelurahan",
                        "D" => "kelurahan",
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

                        $kelurahan[] = [
                            'kecamatan_kode' => $row['kode_kecamatan'],
                            'kelurahan_kode' => $row['kode_kelurahan'],
                            'kelurahan' => $row['kelurahan']
                        ];

                        // $rwrt[] = [
                        //     'kelurahan_kode' => $row['kode_kelurahan'],
                        //     'kode_rw' => $row['kode_rw'],
                        //     'rw' => $row['rw'],
                        //     'kode_rt' => $row['kode_rt'],
                        //     'rt' => $row['rt']
                        // ];
                    }

                    $kelurahan = array_map("unserialize", array_unique(array_map("serialize", $kelurahan)));
                    // $rwrt = array_map("unserialize", array_unique(array_map("serialize", $rwrt)));

                    $lurahsukses = $lurahgagal = 0;
                    foreach ($kelurahan as $key => $row) {
                        $check = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $row['kecamatan_kode'])->where('kelurahan_kode', $row['kelurahan_kode'])->where('nama', $row['kelurahan'])->first();

                        if ($check) {
                            $lurahgagal = $lurahgagal + 1;
                        } else {
                            //cek curretn prov
                            $current_kab = Kecamatan::where('kecamatan_kode', $row['kecamatan_kode'])->first();
                            if(!$current_kab) {
                                $lurahgagal = $lurahgagal + 1;
                            }else{
                                $lurah = new Kelurahan;
                                $lurah->kecamatan_kode = $row['kecamatan_kode'];
                                $lurah->kelurahan_kode = $row['kelurahan_kode'];
                                $lurah->nama = $row['kelurahan'];
                                $lurah->status = 2;
                                $lurah->created_at = date('Y-m-d H:i:s');
                                $lurah->created_by = Auth::id();

                                if ($lurah->save()) {
                                    $lurahsukses = $lurahsukses + 1;
                                } else {
                                    $lurahgagal = $lurahgagal + 1;
                                }
                            }
                        }
                    }

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
                                <th>Kelurahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $lurahsukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $lurahgagal . '</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    ';
                    $msg .= '</table>';
                    $msg .= '<span class="text-dark font-weight-bolder">Untuk data yang gagal diproses, kemungkinan data tidak lengkap atau data sudah ada sebelumnya.</span>';

                    return redirect()->route('admin.kelurahan.index')->with('success', $msg);
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
        $this->authorize('access', [\App\Kelurahan::class, Auth::user()->role, 'edit']);

        $status = Helper::status();

        $lurah = Kelurahan::leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'adms_kelurahan.kecamatan_kode');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'adms_kecamatan.kabupaten_kode');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->select([
            'adms_provinsi.nama as provinsi', 'adms_provinsi.provinsi_kode', 
            'adms_kabupaten.nama as kabupaten', 'adms_kabupaten.kabupaten_kode',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.*'
        ])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->whereNull('adms_kecamatan.deleted_by')
        ->whereNull('adms_kelurahan.deleted_by')
        ->where('adms_kelurahan.id', $id)
        ->first();

        return view('kelurahan.edit', ['lurah' => $lurah, 'status' => $status]);
    }

    public function update(Request $request, $id) {
        $update = Kelurahan::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kelurahan berhasil diubah';
            return redirect()->route('admin.kelurahan.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kelurahan gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Kelurahan::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kelurahan berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kelurahan gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function upload()
    {
        $this->authorize('access', [\App\Kelurahan::class, Auth::user()->role, 'upload']);

        return view('kelurahan.upload');
    }

    public function downloadExcel(){
        $kota[] = [
            'Kode Kecamatan'        => '000000',
            'Kecamatan'             => '',
            'Kode Kelurahan'        => '00000000',
            'Kelurahan'             => '',
            // 'Kode Rw'               => '01',
            // 'Rw'                    => '1',
            // 'Kode Rt'               => '004',
            // 'Rt'                    => '4'
        ];

        $title = 'Template_Upload_Kelurahan.xlsx';
        return Excel::download(new KelurahanTemplate($kota), $title);
    }
}
