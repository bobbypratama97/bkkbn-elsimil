<?php

namespace App\Http\Controllers;

use App\Exports\KotaTemplate;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Auth;
use Str;
use File;
use DB;
use Redirect;

use Helper;

use App\Kabupaten;
use App\Provinsi;
use Maatwebsite\Excel\Facades\Excel;

class KotaController extends Controller
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
        $this->authorize('access', [\App\Kota::class, Auth::user()->role, 'index']);

        $paginate = Kabupaten::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'adms_kabupaten.created_by');
        })
        ->select(['adms_provinsi.nama as provinsi', 'adms_kabupaten.*', 'users.name'])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by');
        
        $name = '';
        if (isset($request->name)) {
            $name = $request->name;
            $paginate = $paginate->where('adms_provinsi.nama', 'like', '%' . $request->name . '%')
                ->orWhere('adms_kabupaten.nama', 'like', '%' . $request->name . '%');
        }

        $paginate = $paginate->paginate(10);
        $kota = $paginate->items();

        return view('kota.index', ['kota' => $kota, 'paginate' => $paginate, 'name' => $name]);
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
                        "D" => "kabupaten"
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

                        $kota[] = [
                            'provinsi_kode' => $row['kode_provinsi'],
                            'kabupaten_kode' => $row['kode_kabupaten'],
                            'kabupaten' => $row['kabupaten']
                        ];
                    }

                    $kota = array_map("unserialize", array_unique(array_map("serialize", $kota)));

                    $kotasukses = $kotagagal = 0;
                    foreach ($kota as $key => $row) {
                        $check = Kabupaten::whereNull('deleted_by')->where('provinsi_kode', $row['provinsi_kode'])->where('kabupaten_kode', $row['kabupaten_kode'])->where('nama', $row['kabupaten'])->first();

                        if ($check) {
                            $kotagagal = $kotagagal + 1;
                        } else {
                            //cek curretn prov
                            $current_prov = Provinsi::where('provinsi_kode', $row['provinsi_kode'])->first();
                            if(!$current_prov) {
                                $kotagagal = $kotagagal + 1;
                            }else{
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
                    }

                    $msg = 'Data master wilayah telah diproses. <br /><br />';
                    $msg .= '<table class="table table-bordered">';
                    $msg .= '
                        <thead>
                            <tr>
                                <th>Kabupaten/Kota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-6">Data sukses diproses : ' . $kotasukses . '</div>
                                        <div class="col-lg-6">Data gagal diproses : ' . $kotagagal . '</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    ';
                    $msg .= '</table>';
                    $msg .= '<span class="text-dark font-weight-bolder">Untuk data yang gagal diproses, kemungkinan data tidak lengkap atau data sudah ada sebelumnya.</span>';

                    return redirect()->route('admin.kota.index')->with('success', $msg);
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
        $this->authorize('access', [\App\Kota::class, Auth::user()->role, 'edit']);

        $status = Helper::status();

        $kota = Kabupaten::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_provinsi.provinsi_kode', '=', 'adms_kabupaten.provinsi_kode');
        })
        ->select(['adms_provinsi.nama as provinsi', 'adms_kabupaten.*'])
        ->whereNull('adms_provinsi.deleted_by')
        ->whereNull('adms_kabupaten.deleted_by')
        ->where('adms_kabupaten.id', $id)
        ->first();

        return view('kota.edit', compact('kota', 'status'));
    }

    public function update(Request $request, $id) {
        $update = Kabupaten::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->publikasi,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kabupaten berhasil diubah';
            return redirect()->route('admin.kota.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Perhatian', 
                    'keterangan' => 'Kabupaten gagal diubah. Silahkan coba beberapa saat lagi'
                ]);
        }
    }

    public function delete(Request $request) {
        $update = Kabupaten::where('id', $request->id)->update([
            'deleted_by' => Auth::id(),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $msg = 'Kabupaten berhasil dihapus';
            $output = Helper::successResponse($msg);
        } else {
            $msg = 'Kabupaten gagal dihapus. Silahkan coba beberapa saat lagi';
            $output = Helper::successResponse($msg);
        }

        return json_encode($output);

        die();
    }

    public function upload()
    {
        $this->authorize('access', [\App\Kota::class, Auth::user()->role, 'upload']);

        return view('kota.upload');
    }

    public function downloadExcel(){
        $kota[] = [
            'Kode Provinsi'         => '00',
            'Provinsi'              => '',
            'Kode Kabupaten'        => '0000',
            'Kabupaten'             => '',
            // 'Kode Kecamatan'        => '000000',
            // 'Kecamatan'             => '',
            // 'Kode Kelurahan'        => '00000000',
            // 'Kelurahan'             => '',
            // 'Kode Rw'               => '01',
            // 'Rw'                    => '1',
            // 'Kode Rt'               => '004',
            // 'Rt'                    => '4'
        ];

        $title = 'Template_Upload_KotaKabupaten.xlsx';
        return Excel::download(new KotaTemplate($kota), $title);
    }
}
