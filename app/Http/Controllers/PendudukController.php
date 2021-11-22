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

use App\Penduduk;

class PendudukController extends Controller
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
        $this->authorize('access', [\App\Penduduk::class, Auth::user()->role, 'index']);

        $paginate = Penduduk::leftJoin('users', function($join) {
            $join->on('users.id', '=', 'adms_penduduk.created_by');
        })
        ->leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_penduduk.provinsi_kode');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_kabupaten.kabupaten_kode', '=', 'adms_penduduk.kabupaten_kode');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_kecamatan.kecamatan_kode', '=', 'adms_penduduk.kecamatan_kode');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_kelurahan.kelurahan_kode', '=', 'adms_penduduk.kelurahan_kode');
        })
        ->select([
            'adms_penduduk.*', 
            'users.name',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.nama as kelurahan'
        ])
        ->whereNull('adms_penduduk.deleted_by');

        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $user = $paginate->where('adms_penduduk.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_penduduk.nik', 'like', '%' . $request->name . '%')
                ->orWhere('adms_provinsi.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kabupaten.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kecamatan.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kelurahan.nama', 'like', '%' . $request->name . '%');
        } 

        $paginate = $paginate->inRandomOrder()
            ->paginate(10);

        $penduduk = $paginate->items();
        // return $paginate;
        return view('penduduk.index', compact('penduduk', 'paginate'));
    }

    public function upload()
    {
        $this->authorize('access', [\App\Penduduk::class, Auth::user()->role, 'upload']);

        return view('penduduk.upload');
    }

    public function create() {
    }

    public function store(Request $request) {
        if ($request->hasFile('file')) {
            $size = $request->file('file')->getSize();

            $finSize = Helper::humanFilesize($size);

            //echo $finSize;

            if ($size > 96602462) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'error' => 'Perhatian', 
                        'keterangan' => 'Ukuran file yang Anda upload : '.$finSize.'<br />Ukuran file tidak boleh melebihi dari 90MB'
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
                        "B" => "kode_kabupaten",
                        "C" => "kode_kecamatan",
                        "D" => "kode_kelurahan",
                        "E" => "kode_rw",
                        "F" => "kode_rt",
                        "G" => "nik",
                        "H" => "nama",
                        "I" => "tanggal",
                        "J" => "bulan",
                        "K" => "tahun",
                        "L" => "hubungan_kki",
                        "M" => "kki"
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

                    $sukses = $gagal = 0;
                    foreach ($input as $key => $row) {
                        $kabupaten_kode = $row['kode_provinsi'] . $row['kode_kabupaten'];
                        $kecamatan_kode = $row['kode_provinsi'] . $row['kode_kabupaten'] . $row['kode_kecamatan'];
                        $kelurahan_kode = $row['kode_provinsi'] . $row['kode_kabupaten'] . $row['kode_kecamatan'] . $row['kode_kelurahan'];

                        $tgl_lahir = $row['tahun'] . '-' . sprintf("%02d", $row['bulan']) . '-' . sprintf("%02d", $row['tanggal']);

                        $check = Penduduk::whereNull('deleted_by')
                            ->where('nik', $row['nik'])
                            ->where('nama', $row['nama'])
                            ->where('tgl_lahir', $tgl_lahir)
                            ->where('provinsi_kode', $row['kode_provinsi'])
                            ->where('kabupaten_kode', $kabupaten_kode)
                            ->where('kecamatan_kode', $kecamatan_kode)
                            ->where('kelurahan_kode', $kelurahan_kode)
                            ->where('rw_kode', $row['kode_rw'])
                            ->where('rt_kode', $row['kode_rt'])
                            ->where('kki', $row['kki'])
                            ->where('hubungan_kki', $row['hubungan_kki'])
                            ->first();

                        if ($check) {
                            $gagal = $gagal + 1;
                        } else {
                            $penduduk = new Penduduk;
                            $penduduk->nik = Helper::encryptNik($row['nik']);
                            $penduduk->nama = $row['nama'];
                            $penduduk->tgl_lahir = $tgl_lahir;
                            $penduduk->provinsi_kode = $row['kode_provinsi'];
                            $penduduk->kabupaten_kode = $kabupaten_kode;
                            $penduduk->kecamatan_kode = $kecamatan_kode;
                            $penduduk->kelurahan_kode = $kelurahan_kode;
                            $penduduk->rw_kode = $row['kode_rw'];
                            $penduduk->rt_kode = $row['kode_rt'];
                            $penduduk->kki = $row['kki'];
                            $penduduk->hubungan_kki = $row['hubungan_kki'];
                            $penduduk->created_at = date('Y-m-d H:i:s');
                            $penduduk->created_by = Auth::id();

                            if ($penduduk->save()) {
                                $sukses = $sukses + 1;
                            } else {
                                $gagal = $gagal + 1;
                            }
                        }
                    }

                    $msg = 'Data master penduduk telah diproses. <br /><br />';
                    $msg .= 'Data sukses diproses : ' . $sukses . '<br />';
                    $msg .= 'Data gagal diproses : ' . $gagal . '<br />';
                    $msg .= '<span class="text-dark font-weight-bolder">Untuk data yang gagal diproses, kemungkinan data tidak lengkap atau data sudah ada sebelumnya.</span>';

                    return redirect()->route('admin.penduduk.index')->with('success', $msg);
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
        $this->authorize('access', [\App\Penduduk::class, Auth::user()->role, 'edit']);

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
