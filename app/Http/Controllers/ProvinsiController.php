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

use App\Provinsi;

class ProvinsiController extends Controller
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
        $this->authorize('access', [\App\Provinsi::class, Auth::user()->role, 'index']);

        $provinsi = Provinsi::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'adms_provinsi.created_by');
        })
        ->select(['adms_provinsi.*', 'users.name'])
        ->whereNull('adms_provinsi.deleted_by')->get();

        return view('provinsi.index', ['provinsi' => $provinsi]);
    }

    public function upload()
    {
        $this->authorize('access', [\App\Provinsi::class, Auth::user()->role, 'upload']);

        return view('provinsi.upload');
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
                        "A" => "kode_provinsi",
                        "B" => "provinsi",
                        "C" => "kode_kabupaten",
                        "D" => "kabupaten",
                        "E" => "kode_kecamatan",
                        "F" => "kecamatan",
                        "G" => "kode_kelurahan",
                        "H" => "kelurahan",
                        "I" => "kode_rw",
                        "J" => "rw",
                        "K" => "kode_rt",
                        "L" => "rt"
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
                        $provinsi[] = [
                            'provinsi_kode' => $row['kode_provinsi'],
                            'provinsi' => $row['provinsi']
                        ];

                        $kota[] = [
                            'provinsi_kode' => $row['kode_provinsi'],
                            'kabupaten_kode' => $row['kode_kabupaten'],
                            'kabupaten' => $row['kabupaten']
                        ];

                        $kecamatan[] = [
                            'kabupaten_kode' => $row['kode_kabupaten'],
                            'kecamatan_kode' => $row['kode_kecamatan'],
                            'kecamatan' => $row['kecamatan']
                        ];

                        $kelurahan[] = [
                            'kecamatan_kode' => $row['kode_kecamatan'],
                            'kelurahan_kode' => $row['kode_kelurahan'],
                            'kelurahan' => $row['kelurahan']
                        ];

                        $rwrt[] = [
                            'kelurahan_kode' => $row['kode_kelurahan'],
                            'kode_rw' => $row['kode_rw'],
                            'rw' => $row['rw'],
                            'kode_rt' => $row['kode_rt'],
                            'rt' => $row['rt']
                        ];
                    }

                    $provinsi = array_map("unserialize", array_unique(array_map("serialize", $provinsi)));
                    $kota = array_map("unserialize", array_unique(array_map("serialize", $kota)));
                    $kecamatan = array_map("unserialize", array_unique(array_map("serialize", $kecamatan)));
                    $kelurahan = array_map("unserialize", array_unique(array_map("serialize", $kelurahan)));
                    $rwrt = array_map("unserialize", array_unique(array_map("serialize", $rwrt)));

                    $provsukses = $provgagal = 0;
                    foreach ($provinsi as $key => $row) {
                        $check = Provinsi::whereNull('deleted_by')->where('provinsi_kode', $row['provinsi_kode'])->where('nama', $row['provinsi'])->first();

                        if ($check) {
                            $provgagal = $provgagal + 1;
                        } else {
                            $prov = new Provinsi;
                            $prov->provinsi_kode = $row['provinsi_kode'];
                            $prov->nama = $row['provinsi'];
                            $prov->status = 2;
                            $prov->created_at = date('Y-m-d H:i:s');
                            $prov->created_by = Auth::id();

                            if ($prov->save()) {
                                $provsukses = $provsukses + 1;
                            } else {
                                $provgagal = $provgagal + 1;
                            }
                        }
                    }

                    $kotasukses = $kotagagal = 0;
                    foreach ($kota as $key => $row) {
                        $check = Kabupaten::whereNull('deleted_by')->where('provinsi_kode', $row['provinsi_kode'])->where('kabupaten_kode', $row['kabupaten_kode'])->where('nama', $row['kabupaten'])->first();

                        if ($check) {
                            $kotagagal = $kotagagal + 1;
                        } else {
                            $kota = new Kabupaten;
                            $kota->provinsi_kode = $row['provinsi_kode'];
                            $kota->kabupaten_kode = $row['kabupaten_kode'];
                            $kota->nama = $row['kabupaten'];
                            $kota->status = 2;
                            $kota->created_at = date('Y-m-d H:i:s');
                            $kota->created_by = Auth::id();

                            if ($kota->save()) {
                                $kotasukses = $kotasukses + 1;
                            } else {
                                $kotagagal = $kotagagal + 1;
                            }
                        }
                    }

                    $camatsukses = $camatgagal = 0;
                    foreach ($kecamatan as $key => $row) {
                        $check = Kecamatan::whereNull('deleted_by')->where('kabupaten_kode', $row['kabupaten_kode'])->where('kecamatan_kode', $row['kecamatan_kode'])->where('nama', $row['kecamatan'])->first();

                        if ($check) {
                            $camatgagal = $camatgagal + 1;
                        } else {
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

                    $lurahsukses = $lurahgagal = 0;
                    foreach ($kelurahan as $key => $row) {
                        $check = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $row['kecamatan_kode'])->where('kelurahan_kode', $row['kelurahan_kode'])->where('nama', $row['kelurahan'])->first();

                        if ($check) {
                            $lurahgagal = $lurahgagal + 1;
                        } else {
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

                    $rwrtsukses = $rwrtgagal = 0;
                    foreach ($rwrt as $key => $row) {
                        $check = Rwrt::whereNull('deleted_by')->where('kelurahan_kode', $row['kelurahan_kode'])->where('kode_rw', $row['kode_rw'])->where('rw', $row['rw'])->where('kode_rt', $row['kode_rt'])->where('rt', $row['rt'])->first();

                        if ($check) {
                            $rwrtgagal = $rwrtgagal + 1;
                        } else {
                            $rw = new Rwrt;
                            $rw->kelurahan_kode = $row['kelurahan_kode'];
                            $rw->kode_rw = $row['kode_rw'];
                            $rw->rw = $row['rw'];
                            $rw->kode_rt = $row['kode_rt'];
                            $rw->rt = $row['rt'];
                            $rw->status = 2;
                            $rw->created_at = date('Y-m-d H:i:s');
                            $rw->created_by = Auth::id();

                            if ($rw->save()) {
                                $rwrtsukses = $rwrtsukses + 1;
                            } else {
                                $rwrtgagal = $rwrtgagal + 1;
                            }
                        }
                    }

                    $msg = 'Data master wilayah telah diproses. <br /><br />';
                    $msg .= '<table class="table table-bordered">';
                    $msg .= '
                        <thead>
                            <tr>
                                <th>Provinsi</th>
                                <th>Kabupaten</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>RT RW</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $provsukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $provgagal . '</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $kotasukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $kotagagal . '</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $camatsukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $camatgagal . '</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $lurahsukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $lurahgagal . '</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $rwrtsukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $rwrtgagal . '</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    ';
                    $msg .= '</table>';
                    $msg .= '<span class="text-dark font-weight-bolder">Untuk data yang gagal diproses, kemungkinan data tidak lengkap atau data sudah ada sebelumnya.</span>';

                    return redirect()->route('admin.provinsi.index')->with('success', $msg);
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
        $this->authorize('access', [\App\Provinsi::class, Auth::user()->role, 'edit']);

        $status = Helper::status();

        $provinsi = Provinsi::whereNull('deleted_by')->where('id', $id)->first();

        return view('provinsi.edit', compact('provinsi', 'status'));
    }

    public function update(Request $request, $id) {
        $update = Provinsi::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Provinsi berhasil diubah';
            return redirect()->route('admin.provinsi.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Provinsi gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Provinsi::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Provinsi berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Provinsi gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }
}
