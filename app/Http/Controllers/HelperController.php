<?php

namespace App\Http\Controllers;

use App\Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions;

use Helper;

use App\Penduduk;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Kelurahan;

use App\Member;

use App\NewsKategori;

use App\Widget;
use App\WidgetComponent;
use App\WidgetComponentDetail;

class HelperController extends Controller
{
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register', 'socialiteLogin']]);
        $this->guard = "member";
    }

    public function carinik(Request $request) {
        $nik = Helper::dcNik($request->nik);

        $data = Penduduk::leftJoin('adms_provinsi', function($join) {
            $join->on('adms_penduduk.provinsi_kode', '=', 'adms_provinsi.provinsi_kode');
        })
        ->leftJoin('adms_kabupaten', function($join) {
            $join->on('adms_penduduk.kabupaten_kode', '=', 'adms_kabupaten.kabupaten_kode');
        })
        ->leftJoin('adms_kecamatan', function($join) {
            $join->on('adms_penduduk.kecamatan_kode', '=', 'adms_kecamatan.kecamatan_kode');
        })
        ->leftJoin('adms_kelurahan', function($join) {
            $join->on('adms_penduduk.kelurahan_kode', '=', 'adms_kelurahan.kelurahan_kode');
        })
        ->where('nik', 'LIKE', '%' . $nik . '%')
        ->select([
            'adms_penduduk.nik',
            'adms_penduduk.nama',
            'adms_provinsi.provinsi_kode',
            'adms_provinsi.nama as provinsi',
            'adms_kabupaten.kabupaten_kode',
            'adms_kabupaten.nama as kabupaten',
            'adms_kecamatan.kecamatan_kode',
            'adms_kecamatan.nama as kecamatan',
            'adms_kelurahan.kelurahan_kode',
            'adms_kelurahan.nama as kelurahan'
        ])
        ->first();

        //print_r ($data);

        $output = [];
        if ($data) {
            $prov = Provinsi::whereNull('deleted_by')->get();

            $sprov = '<option value="">Pilih Provinsi</option>';
            foreach ($prov as $row) {
                $selected = ($row->provinsi_kode == $data->provinsi_kode) ? 'selected' : '';
                $sprov .= '<option value="'.$row->provinsi_kode.'" '.$selected.'>'.$row->nama.'</option>';
            }

            $kab = Kabupaten::whereNull('deleted_by')->where('provinsi_kode', $data->provinsi_kode)->get();

            $skab = '<option value="">Pilih Kabupaten</option>';
            foreach ($kab as $row) {
                $selected = ($row->kabupaten_kode == $data->kabupaten_kode) ? 'selected' : '';
                $skab .= '<option value="'.$row->kabupaten_kode.'" '.$selected.'>'.$row->nama.'</option>';
            }

            $kec = Kecamatan::whereNull('deleted_by')->where('kabupaten_kode', $data->kabupaten_kode)->get();
            
            $skec = '<option value="">Pilih Kecamatan</option>';
            foreach ($kec as $row) {
                $selected = ($row->kecamatan_kode == $data->kecamatan_kode) ? 'selected' : '';
                $skec .= '<option value="'.$row->kecamatan_kode.'" '.$selected.'>'.$row->nama.'</option>';
            }

            $lur = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $data->kecamatan_kode)->get();

            $slur = '<option value="">Pilih Kelurahan</option>';
            foreach ($lur as $row) {
                $selected = ($row->kelurahan_kode == $data->kelurahan_kode) ? 'selected' : '';
                $slur .= '<option value="'.$row->kelurahan_kode.'" '.$selected.'>'.$row->nama.'</option>';
            }

            $output = [
                'count' => 1,
                'nik' => Helper::decryptNik($data->nik),
                'nama' => $data->nama,
                'provinsi' => $sprov,
                'kabupaten' => $skab,
                'kecamatan' => $skec,
                'kelurahan' => $slur
            ];

        } else {
            $prov = Provinsi::whereNull('deleted_by')->get();

            $sprov = '<option value="">Pilih Provinsi</option>';
            foreach ($prov as $row) {
                $sprov .= '<option value="'.$row->provinsi_kode.'">'.$row->nama.'</option>';
            }

            $skab = '<option value="">Pilih</option>';
            $skec = '<option value="">Pilih</option>';
            $slur = '<option value="">Pilih</option>';

            $output = [
                'count' => 0,
                'nik' => '',
                'nama' => '',
                'provinsi' => $sprov,
                'kabupaten' => $skab,
                'kecamatan' => $skec,
                'kelurahan' => $slur
            ];

        }

        echo json_encode($output);

        die();
    }

    public function provinsi() {
        $data = Provinsi::whereNull('deleted_by')->orderBy('nama', 'asc')->get();

        $count = 0;
        $select = '';
        if (!empty($data)) {
            $count = 1;
            $select .= '<option value="">Pilih Provinsi</option>';
            foreach ($data as $row) {
                $select .= '<option value="'.$row->provinsi_kode.'">' . $row->nama . '</option>';
            }
        }

        $output = [
            'count' => $count,
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function kabupaten(Request $request) {
        if (!empty($request->provinsi_id)) {
            $data = Kabupaten::whereNull('deleted_by')->where('provinsi_kode', $request->provinsi_id)->orderBy('nama', 'ASC')->get();
        } else {
            $data = Kabupaten::whereNull('deleted_by')->orderBy('nama', 'asc')->get();
        }

        $count = 0;
        $select = '';
        if (!empty($data)) {
            $count = 1;
            $select .= '<option value="">Pilih Kabupaten</option>';
            foreach ($data as $row) {
                $select .= '<option value="'.$row->kabupaten_kode.'">' . $row->nama . '</option>';
            }
        }

        $output = [
            'count' => $count,
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function kecamatan(Request $request) {
        if (!empty($request->kabupaten_id)) {
            $data = Kecamatan::whereNull('deleted_by')->where('kabupaten_kode', $request->kabupaten_id)->orderBy('nama', 'ASC')->get();
        } else {
            $data = Kecamatan::whereNull('deleted_by')->orderBy('nama', 'asc')->get();
        }

        $count = 0;
        $select = '';
        if (!empty($data)) {
            $count = 1;
            $select .= '<option value="">Pilih Kecamatan</option>';
            foreach ($data as $row) {
                $select .= '<option value="'.$row->kecamatan_kode.'">' . $row->nama . '</option>';
            }
        }

        $output = [
            'count' => $count,
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function kelurahan(Request $request) {
        if (!empty($request->kecamatan_id)) {
            $data = Kelurahan::whereNull('deleted_by')->where('kecamatan_kode', $request->kecamatan_id)->orderBy('nama', 'ASC')->get();
        } else {
            $data = Kelurahan::whereNull('deleted_by')->orderBy('nama', 'asc')->get();
        }

        $count = 0;
        $select = '';
        if (!empty($data)) {
            $count = 1;
            $select .= '<option value="">Pilih Kelurahan</option>';
            foreach ($data as $row) {
                $select .= '<option value="'.$row->kelurahan_kode.'">' . $row->nama . '</option>';
            }
        }

        $output = [
            'count' => $count,
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function member(Request $request) {
        $data = [];

        //print_r ($request->all());

        $count = 0;
        $select = '';
        if (!empty($request->kecamatan)) {
            $data = Member::leftJoin('member_delegate', function($join) {
                $join->on('member_delegate.member_id', '=', 'members.id');
            })
            ->whereNull('member_delegate.member_id')
            ->whereIn('members.is_active', [1, 2, 4])
            ->where('members.provinsi_id', $request->provinsi)
            ->where('members.kabupaten_id', $request->kabupaten)
            ->where('members.kecamatan_id', $request->kecamatan)
            ->select(['members.*'])
            ->orderBy('members.name')
            ->get();

            //echo $data;

            if ($data->isNotEmpty()) {
                $count = 1;
                $select .= '<option value="">Pilih</option>';
                foreach ($data as $row) {
                    $select .= '<option value="'.$row->id.'">'.$row->name.'</option>';
                }
            }

        }

        $output = [
            'count' => $count,
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function subNewsKategori(Request $request) {
        if (empty($request->parent_id)) {
            $count = 0;
            $select = '';
        } else {
            $data = NewsKategori::whereNull('deleted_by')->where('parent_id', $request->parent_id)->where('status', 2)->get();

            $count = 0;
            $select = '';
            if (!empty($data)) {
                $count = 1;
                $select .= '<option value="">Pilih Sub Kategori</option>';
                foreach ($data as $row) {
                    if (isset($request->sub_kat_id)) {
                        $selected = ($row->id == $request->sub_kat_id) ? 'selected' : '';
                        $select .= '<option value="'.$row->id.'" '.$selected.'>' . $row->name . '</option>';
                    } else {
                        $select .= '<option value="'.$row->id.'">' . $row->name . '</option>';
                    }
                }
            }
        }

        $output = [
            'count' => $count,
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function jenis(Request $request) {
        $output = [];

        if ($request->jenis == 'single') {
            $header = 0;
            $caption = $title = $pilihan = $bobot = '';
            if (isset($request->header)) {
                $header = unserialize(html_entity_decode($request->header, ENT_QUOTES));
                $caption = $header['caption'];
            }

            if (isset($request->detail)) {
                $detail = unserialize(html_entity_decode($request->detail, ENT_QUOTES));
                $title = $detail['title'];
                $pilihan = $detail['pilihan'];
                $bobot = $detail['bobot'];
            }

            $choicePilihan = ['' => 'Pilih', 'text' => 'Text', 'angka' => 'Angka', 'dropdown' => 'Dropdown', 'radio' => 'Radio'];

            $optionPilihan = '';
            foreach ($choicePilihan as $key => $val) {
                $selectedPilihan = ($pilihan == $key) ? 'selected' : '';
                $optionPilihan .= '<option value="'.$key.'" '.$selectedPilihan.'>'.$val.'</option>';
            }

            $choiceBobot = ['' => 'Pilih', '1' => 'Ya', '2' => 'Tidak'];

            $optionBobot = '';
            foreach ($choiceBobot as $key => $val) {
                $selectedBobot = ($bobot == $key) ? 'selected' : '';
                $optionBobot .= '<option value="'.$key.'" '.$selectedBobot.'>'.$val.'</option>';
            }

            $select = '';
            $result = '
                <div class="form-group">
                    <label>Caption Pertanyaan</label>
                    <input type="text" class="form-control" id="caption" name="caption" required value="'.$caption.'">
                </div>

                <div class="form-group">
                    <label>Pertanyaan</label>
                    <input type="text" class="form-control" id="title" name="title" required value="'.$title.'">
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan">
                </div>

                <div id="group0">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Tampilkan jawaban / pilihan dalam bentuk </label>
                            <select class="form-control" name="pilihan" id="pilihan" data-id="0">
                                '.$optionPilihan.'
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Punya Bobot Penilaian ?</label>
                            <select class="form-control" name="have_bobot" id="bobot" data-id="0">
                                '.$optionBobot.'
                            </select>
                        </div>
                    </div>

                </div>
            ';
        }

        if ($request->jenis == 'combine') {
            $header = 1;
            $result = '
                <div class="form-group">
                    <label>Caption Pertanyaan</label>
                    <input type="text" class="form-control" id="caption" name="caption" required value="">
                </div>

                <div class="form-group">
                    <label>Pertanyaan 1</label>
                    <input type="text" class="form-control" id="title" name="title[1]" required value="">
                    <input type="hidden" name="pilihan[1]" value="angka">
                    <input type="hidden" name="have_bobot[1]" value="1">
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan[1]" value="">
                </div>

                <br /><br />

                <div class="form-group">
                    <label>Pertanyaan 2</label>
                    <input type="text" class="form-control" id="title" name="title[2]" required value="">
                    <input type="hidden" name="pilihan[2]" value="angka">
                    <input type="hidden" name="have_bobot[2]" value="1">
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan[2]" value="">
                </div>

                <br /><br />

                <div class="form-group">
                    <label>Formula perhitungan bobot <span style="font-size: 8px">(contoh: hasil_pertanyaan_1 + hasil_pertanyaan_2 )</span></label>
                    <textarea name="formula" rows="4" class="form-control" required></textarea>
                </div>
            ';


            $select = '';

            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Kondisi</label>
                                        <select name="kondisi['.$i.']" class="form-control">
                                            <option value="">Pilih</option>
                                            <option value="1">Kurang dari</option>
                                            <option value="2">Sama dengan</option>
                                            <option value="3">Diantara</option>
                                            <option value="4">Lebih dari</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Nilai</label>
                                        <input name="nilai['.$i.']" type="text" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Bobot</label>
                                        <input type="number" name="bobot['.$i.']" class="form-control">
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" name="rating['.$i.']">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="label['.$i.']">
                                    </div>
                                    <div class="col-lg-8">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

        }

        if ($request->jenis == 'widget') {
            $widget = Widget::whereNull('deleted_by')->where('status', 2)->get();

            $res = '<select name="widget_id" class="form-control" id="widget">';
            $res .= '<option value="">Pilih</option>';
            foreach ($widget as $key => $row) {
                $res .= '<option value="'.$row->id.'">'.$row->name.'</option>';
            }
            $res .= '</select>';

            $header = 0;
            $select = '';
            $result = '
                <div class="form-group">
                    <label>Widget</label>
                    '.$res.'
                </div>
            ';
        }

        $output = [
            'content' => $result,
            'bobot' => $select,
            'header' => $header
        ];

        return json_encode($output);

        die();
    }

    public function pilihan(Request $request) {
        $output = [];

        // Text dengan bobot
        if ($request->pilihan == 'text' && $request->bobot == '1') {
            $select = '';

            for ($i = 0; $i < 2; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-3">
                                        <label>Kondisi</label>
                                        <select name="kondisi['.$i.']" class="form-control">
                                            <option value="">Pilih</option>
                                            <option value="1">Diisi</option>
                                            <option value="2">Tidak Diisi</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <label>Bobot</label>
                                        <input type="number" name="bobot['.$i.']" class="form-control">
                                    </div>
                                    <div class="col-lg-3">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="label['.$i.']">
                                    </div>
                                    <div class="col-lg-3">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" name="rating['.$i.']">
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];

        }

        // Text tanpa bobot
        if ($request->pilihan == 'text' && $request->bobot == '2') {
            $select = '';

            for ($i = 0; $i < 1; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-12">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="label['.$i.']">
                                        <input type="hidden" class="form-control" name="rating['.$i.']">
                                    </div>
                                    <div class="col-lg-8">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];
        }

        // Angka dengan bobot
        if ($request->pilihan == 'angka' && $request->bobot == '1') {
            $select = '';

            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Kondisi</label>
                                        <select name="kondisi['.$i.']" class="form-control">
                                            <option value="">Pilih</option>
                                            <option value="1">Kurang dari</option>
                                            <option value="2">Sama dengan</option>
                                            <option value="3">Diantara</option>
                                            <option value="4">Lebih dari</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Nilai</label>
                                        <input name="nilai['.$i.']" type="text" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Bobot</label>
                                        <input type="number" name="bobot['.$i.']" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="label['.$i.']">
                                        <input type="hidden" class="form-control" name="rating['.$i.']">
                                    </div>
                                    <div class="col-lg-8">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];

        }

        // Angka tanpa bobot
        if ($request->pilihan == 'angka' && $request->bobot == '2') {
            $select = '';

            for ($i = 0; $i < 1; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-12">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Label</label>
                                        <input type="text" class="form-control" name="label['.$i.']">
                                        <input type="hidden" class="form-control" name="rating['.$i.']">
                                    </div>
                                    <div class="col-lg-8">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];

        }

        // Dropdown dengan bobot
        if ($request->pilihan == 'dropdown' && $request->bobot == '1') {
            $select = '';

            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Jawaban / Pilihan</label>
                                        <input type="text" name="label['.$i.']" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Bobot</label>
                                        <input type="number" name="bobot['.$i.']" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" name="rating['.$i.']">
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];
        }

        // Dropdown tanpa bobot
        if ($request->pilihan == 'dropdown' && $request->bobot == '2') {
            $select = '';

            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label>Jawaban / Pilihan</label>
                                        <input type="text" name="label['.$i.']" class="form-control">
                                        <input type="hidden" class="form-control" name="rating['.$i.']">
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];
        }

        // Radio dengan bobot
        if ($request->pilihan == 'radio' && $request->bobot == '1') {
            $select = '';

            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-4">
                                        <label>Jawaban / Pilihan</label>
                                        <input type="text" name="label['.$i.']" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Bobot</label>
                                        <input type="number" name="bobot['.$i.']" class="form-control">
                                    </div>
                                    <div class="col-lg-4">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" name="rating['.$i.']">
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];
        }

        // Radio tanpa bobot
        if ($request->pilihan == 'radio' && $request->bobot == '2') {
            $select = '';

            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $file = '';
                for ($j = 0; $j < 3; $j++) {
                    $file .= '
                        <tr>
                            <td><input type="text" name="name['.$i.'][]" class="form-control"></td>
                            <td><input type="file" class="form-control" name="file['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                        </tr>
                    ';

                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'">'.$v['title'].'</option>';
                }

                $select .= '
                    <div class="col-lg-6">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label>Jawaban / Pilihan</label>
                                        <input type="text" name="label['.$i.']" class="form-control">
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Warna Background</label>
                                        <br />
                                        <select class="form-control background-dropdown" name="background['.$i.']">
                                            '.$colors.'
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tbl_posts">
                                        <thead>
                                            <tr>
                                                <th width="55%">Nama File</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = [
                'content' => $select,
                'header' => '1'
            ];
        }

        return json_encode($output);

        die();
    }

    public function jenisedit(Request $request) {
        $output = '';
        $base_url = env('BASE_URL_KUIS');

        $disable = ($request->action == 'show') ? 'disabled' : '';

        if ($request->jenis == 'single') {
            $bigLabel = 1;
            $select = '';
            $caption = $title = $pilihan = $bobot = '';
            if (isset($request->header)) {
                $header = unserialize(html_entity_decode($request->header, ENT_QUOTES));
                $caption = $header['caption'];
            }

            if (isset($request->detail)) {
                $detail = unserialize(html_entity_decode($request->detail, ENT_QUOTES));
                $detail_id = $detail['id'];
                $title = $detail['title'];
                $satuan = $detail['satuan'];
                $pilihan = $detail['pilihan'];
                $bobot = $detail['bobot'];
                //$bigLabel = ($bobot == '2') ? 0 : 1;
            }

            $choicePilihan = ['' => 'Pilih', 'text' => 'Text', 'angka' => 'Angka', 'dropdown' => 'Dropdown', 'radio' => 'Radio'];

            $optionPilihan = '';
            foreach ($choicePilihan as $key => $val) {
                $selectedPilihan = ($pilihan == $key) ? 'selected' : '';
                $optionPilihan .= '<option value="'.$key.'" '.$selectedPilihan.'>'.$val.'</option>';
            }

            $choiceBobot = ['' => 'Pilih', '1' => 'Ya', '2' => 'Tidak'];

            $optionBobot = '';
            foreach ($choiceBobot as $key => $val) {
                $selectedBobot = ($bobot == $key) ? 'selected' : '';
                $optionBobot .= '<option value="'.$key.'" '.$selectedBobot.'>'.$val.'</option>';
            }

            $output = '
                <div class="form-group">
                    <label>Caption Pertanyaan</label>
                    <input type="text" class="form-control" id="caption" name="caption" required value="'.$caption.'" '.$disable.'>
                </div>

                <div class="form-group">
                    <label>Pertanyaan</label>
                    <input type="hidden" name="detail_id" value="'.$detail_id.'">
                    <input type="text" class="form-control" id="title" name="title" required value="'.$title.'" '.$disable.'>
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan" value="'.$satuan.'" '.$disable.'>
                </div>

                <div id="group0">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Tampilkan jawaban / pilihan dalam bentuk </label>
                            <select class="form-control" name="pilihan" id="pilihan" data-id="0" '.$disable.'>
                                '.$optionPilihan.'
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Punya Bobot Penilaian ?</label>
                            <select class="form-control" name="have_bobot" id="bobot" data-id="0" '.$disable.'>
                                '.$optionBobot.'
                            </select>
                        </div>
                    </div>
                </div>
            ';
        }

        if ($request->jenis == 'combine') {
            $bigLabel = 1;
            $caption = $title = $pilihan = $bobot = '';
            if (isset($request->header)) {
                $header = unserialize(html_entity_decode($request->header, ENT_QUOTES));
                $caption = $header['caption'];
            }

            if (isset($request->detail)) {
                $detail = unserialize(html_entity_decode($request->detail, ENT_QUOTES));
            }

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobot) && !empty($request->bobot)) {
                $unserialized = unserialize(html_entity_decode($request->bobot, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $select = '';
            $pilihan = ['' => 'Pilih', '1' => 'Kurang dari', '2' => 'Sama dengan', '3' => 'Diantara', '4' => 'Lebih dari'];

            $output = '
                <div class="form-group">
                    <label>Caption Pertanyaan</label>
                    <input type="text" class="form-control" id="caption" name="caption" required value="'.$caption.'" '.$disable.'>
                </div>

                <div class="form-group">
                    <label>Pertanyaan 1</label>
                    <input type="hidden" name="detail_id[1]" value="'.$detail[0]['id'].'">
                    <input type="text" class="form-control" id="title" name="title[1]" required value="'.$detail[0]['title'].'" '.$disable.'>
                    <input type="hidden" name="pilihan[1]" value="angka">
                    <input type="hidden" name="have_bobot[1]" value="1">
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan[1]" required value="'.$detail[0]['satuan'].'" '.$disable.'>
                </div>

                <br /><br />

                <div class="form-group">
                    <label>Pertanyaan 2</label>
                    <input type="hidden" name="detail_id[2]" value="'.$detail[1]['id'].'">
                    <input type="text" class="form-control" id="title" name="title[2]" required value="'.$detail[1]['title'].'" '.$disable.'>
                    <input type="hidden" name="pilihan[2]" value="angka">
                    <input type="hidden" name="have_bobot[2]" value="1">
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan[2]" required value="'.$detail[1]['satuan'].'" '.$disable.'>
                </div>

                <br /><br />

                <div class="form-group">
                    <label>Formula perhitungan bobot <span style="font-size: 8px">(contoh: hasil_pertanyaan_1 + hasil_pertanyaan_2 )</span></label>
                    <textarea name="formula" rows="4" class="form-control" '.$disable.' required>'.$header['formula'].'</textarea>
                </div>
            ';


            $select = $column = '';

            $max = ($request->action == 'edit') ? '4' : count($unserialized);

            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }


                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $kondisi = (isset($unserialized[$i]['kondisi'])) ? $unserialized[$i]['kondisi'] : '';
                $nilai = (isset($unserialized[$i]['nilai'])) ? $unserialized[$i]['nilai'] : '';
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $options = '';
                foreach ($pilihan as $key => $row) {
                    $selected = ($kondisi == $key) ? 'selected' : '';
                    $options .= '<option value="'.$key.'" '.$selected.'>'.$row.'</option>';
                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Kondisi</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <select name="kondisi['.$i.']" id="kondisi_'.$i.'" class="form-control" '.$disable.'>
                                '.$options.'
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Nilai</label>
                            <input type="text" name="nilai['.$i.']" id="nilai_'.$i.'" class="form-control" value="'.$nilai.'" '.$disable.'>
                        </div>
                        <div class="col-lg-4">
                            <label>Bobot</label>
                            <input type="number" name="bobot['.$i.']" id="bobot_'.$i.'" class="form-control" value="'.$bobot.'" '.$disable.'>
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Label</label>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                            <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                        </div>
                        <div class="col-lg-8">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

        }

        if ($request->jenis == 'widget') {
            $bigLabel = 2;
            $select = '';
            $id = $caption = $title = $pilihan = $bobot = '';
            if (isset($request->header)) {
                $get = unserialize(html_entity_decode($request->header, ENT_QUOTES));
                $id = $get['widget_id'];
            }

            if (!empty($id)) {
                $header = Widget::where('id', $id)->first();

                if ($request->action == 'edit') {
                    $list = Widget::where('status', 2)->get();

                    $option = '<select name="widget_id" class="form-control" id="widget">';
                    foreach ($list as $key => $row) {
                        $selected = ($row->id == $id) ? 'selected' : '';
                        $option .= '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                    }
                    $option .= '</select>';

                    $resheader = '
                        <div class="form-group">
                            <label>Widget</label><br />
                            '.$option.'
                        </div>

                        <div class="form-group" id="widget-header">
                            <label>Caption</label>
                            <input type="text" value="'.$header->caption.'" class="form-control" disabled>
                            <input type="hidden" name="caption" value="'.$header->caption.'" class="form-control">
                        </div>
                    ';

                } else {
                    $resheader = '                
                        <div class="form-group">
                            <label>Widget</label><br />
                            <input type="text" name="caption" value="'.$header->name.'" class="form-control" '.$disable.'>
                        </div>

                        <div class="form-group">
                            <label>Caption</label>
                            <input type="text" value="'.$header->caption.'" '.$disable.' class="form-control">
                        </div>
                    ';
                }

                $detail = WidgetComponent::where('widget_id', $id)->get();

                $resdetail = '
                    <div class="form-group" id="widget-komponen">
                        <label>Komponen</label>
                        <table class="table table-bordered" id="tblfile-0">
                            <thead>
                                <tr>
                                    <th width="65%">Pertanyaan</th>
                                    <th>Bentuk Jawaban</th>
                                </tr>
                            </thead>
                            <tbody>
                ';

                foreach ($detail as $key => $row) {
                    $resdetail .= '
                        <tr>
                            <td>
                                <input type="hidden" class="form-control" id="komponen_id" name="komponen_id['.$key.']" required value="'.$row['id'].'">
                                <input type="hidden" class="form-control" id="title" name="title['.$key.']" required value="'.$row['title'].'">
                                <input type="hidden" class="form-control" name="pilihan['.$key.']" value="'.$row['tipe'].'">
                                <input type="hidden" class="form-control" name="have_bobot['.$key.']" value="0">
                                '.$row['title'].'
                            </td>
                            <td>'.$row['naming'].'</td>
                        </tr>
                    ';
                }

                $resdetail .= '
                            </tbody>
                        </table>
                    </div>
                ';

                $output = $resheader . $resdetail;
            }

        }

        $output = [
            'header' => $bigLabel,
            'content' => $output,
            'bobot' => $select
        ];


        return json_encode($output);

        die();
    }

    public function pilihanedit(Request $request) {
        $output = '';
        $base_url = env('BASE_URL_KUIS');
        $disable = ($request->action == 'show') ? 'disabled' : '';

        // Text dengan bobot
        if ($request->pilihan == 'text' && $request->bobot == '1') {
            $pilihan = ['' => 'Pilih', '1' => 'Diisi', '2' => 'Tidak Diisi'];


            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '2' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $kondisi = (isset($unserialized[$i]['kondisi'])) ? $unserialized[$i]['kondisi'] : '';
                $nilai = (isset($unserialized[$i]['nilai'])) ? $unserialized[$i]['nilai'] : '';
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $options = '';
                foreach ($pilihan as $key => $row) {
                    $selected = ($kondisi == $key) ? 'selected' : '';
                    $options .= '<option value="'.$key.'" '.$selected.'>'.$row.'</option>';
                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Kondisi</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <select name="kondisi['.$i.']" id="kondisi_'.$i.'" class="form-control" '.$disable.'>
                                '.$options.'
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Bobot</label>
                            <input type="number" name="bobot['.$i.']" id="bobot_'.$i.'" class="form-control" value="'.$bobot.'" '.$disable.'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Label</label>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                        </div>
                        <div class="col-lg-6">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                        <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Text tanpa bobot
        if ($request->pilihan == 'text' && $request->bobot == '2') {
            $pilihan = ['' => 'Pilih', '1' => 'Diisi', '2' => 'Tidak Diisi'];


            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '1' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $kondisi = (isset($unserialized[$i]['kondisi'])) ? $unserialized[$i]['kondisi'] : '';
                $nilai = (isset($unserialized[$i]['nilai'])) ? $unserialized[$i]['nilai'] : '';
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $options = '';
                foreach ($pilihan as $key => $row) {
                    $selected = ($kondisi == $key) ? 'selected' : '';
                    $options .= '<option value="'.$key.'" '.$selected.'>'.$row.'</option>';
                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Label</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                            <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                        </div>
                        <div class="col-lg-6">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-12 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Angka dengan bobot
        if ($request->pilihan == 'angka' && $request->bobot == '1') {
            $pilihan = ['' => 'Pilih', '1' => 'Kurang dari', '2' => 'Sama dengan', '3' => 'Diantara', '4' => 'Lebih dari'];


            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '4' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $kondisi = (isset($unserialized[$i]['kondisi'])) ? $unserialized[$i]['kondisi'] : '';
                $nilai = (isset($unserialized[$i]['nilai'])) ? $unserialized[$i]['nilai'] : '';
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $options = '';
                foreach ($pilihan as $key => $row) {
                    $selected = ($kondisi == $key) ? 'selected' : '';
                    $options .= '<option value="'.$key.'" '.$selected.'>'.$row.'</option>';
                }

                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Kondisi</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <select name="kondisi['.$i.']" id="kondisi_'.$i.'" class="form-control" '.$disable.'>
                                '.$options.'
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>Nilai</label>
                            <input type="text" name="nilai['.$i.']" id="nilai_'.$i.'"class="form-control" value="'.$nilai.'" '.$disable.'>
                        </div>
                        <div class="col-lg-4">
                            <label>Bobot</label>
                            <input type="number" name="bobot['.$i.']" id="bobot_'.$i.'" class="form-control" value="'.$bobot.'" '.$disable.'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Label</label>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                            <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                        </div>
                        <div class="col-lg-8">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Angka tanpa bobot
        if ($request->pilihan == 'angka' && $request->bobot == '2') {

            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '1' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $kondisi = (isset($unserialized[$i]['kondisi'])) ? $unserialized[$i]['kondisi'] : '';
                $nilai = (isset($unserialized[$i]['nilai'])) ? $unserialized[$i]['nilai'] : '';
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Label</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                            <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                        </div>
                        <div class="col-lg-6">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-12 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Dropdown dengan bobot
        if ($request->pilihan == 'dropdown' && $request->bobot == '1') {

            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '4' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Jawaban / Pilihan</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                        </div>
                        <div class="col-lg-4">
                            <label>Bobot</label>
                            <input type="number" name="bobot['.$i.']" id="bobot_'.$i.'" class="form-control" value="'.$bobot.'" '.$disable.'>
                        </div>
                        <div class="col-lg-4">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                        <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Dropdown tanpa bobot
        if ($request->pilihan == 'dropdown' && $request->bobot == '2') {

            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '4' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Jawaban / Pilihan</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                            <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                        </div>
                        <div class="col-lg-6">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");
                
                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Radio dengan bobot
        if ($request->pilihan == 'radio' && $request->bobot == '1') {
            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '4' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $warna = Helper::colorPicker();
                $colors = '';
                foreach ($warna as $k => $v) {
                    $selected = ($rating_color == $k) ? 'selected' : '';
                    $colors .= '<option value="'.$k.'" data-class="'.$v['class'].'" '.$selected.'>'.$v['title'].'</option>';
                }

                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Jawaban / Pilihan</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                        </div>
                        <div class="col-lg-4">
                            <label>Bobot</label>
                            <input type="number" name="bobot['.$i.']" id="bobot_'.$i.'" class="form-control" value="'.$bobot.'" '.$disable.'>
                        </div>
                        <div class="col-lg-4">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                        <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        // Radio tanpa bobot
        if ($request->pilihan == 'radio' && $request->bobot == '2') {
            $select = $column = '';

            $label = $bobot = $rating = $rating_color = '';
            $unserialized = [];
            if (isset($request->bobotvalue) && !empty($request->bobotvalue)) {
                $unserialized = unserialize(html_entity_decode($request->bobotvalue, ENT_QUOTES));
            }
            //print_r ($unserialized);

            $max = ($request->action == 'edit') ? '4' : count($unserialized);

            $select = '';
            for ($i = 0; $i < $max; $i++) {
                if ($i == 0) {
                    $select .= '<div class="row">';
                }
                if ($i != 0 && $i % 2 == 0) {
                    $select .= '</div><div class="row">';
                }

                $choice_id = (isset($unserialized[$i]['id'])) ? $unserialized[$i]['id'] : 1000000 + $i;
                $label = (isset($unserialized[$i]['label'])) ? $unserialized[$i]['label'] : '';
                $bobot = (isset($unserialized[$i]['bobot'])) ? $unserialized[$i]['bobot'] : '';
                $rating = (isset($unserialized[$i]['rating'])) ? $unserialized[$i]['rating'] : '';
                $rating_color = (isset($unserialized[$i]['rating_color'])) ? $unserialized[$i]['rating_color'] : '';
                $files = (isset($unserialized[$i]['file']) && !empty($unserialized[$i]['file'])) ? $unserialized[$i]['file'] : [];


                $tombol = '';
                if ($request->action == 'edit') {
                    $tombol = '
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-light-danger float-right kosong" data-id="'.$i.'"><i class="flaticon-delete"></i> Hapus bobot penilaian</button>
                            </div>
                        </div>
                    ';
                }

                $column = $tombol . '
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Jawaban / Pilihan</label>
                            <input type="hidden" name="choice_id['.$i.']" value="'.$choice_id.'" '.$disable.'>
                            <input type="text" name="label['.$i.']" id="label_'.$i.'" class="form-control" value="'.$label.'" '.$disable.'>
                            <input type="hidden" class="form-control" name="rating['.$i.']" value="" '.$disable.'>
                        </div>
                        <div class="col-lg-6">
                            <label>Warna Background</label>
                            <br />
                            <select class="form-control background-dropdown" name="background['.$i.']" id="background_'.$i.'" '.$disable.'>
                                '.$colors.'
                            </select>
                        </div>
                    </div>
                ';


                $file = $fileid = '';
                for ($j = 0; $j < 3; $j++) {
                    if ($request->action == 'show') {
                        if (isset($files[$j]['name'])) {
                            $file .= '
                                <tr>
                                    <td>'.$files[$j]['name'].'</td>
                                    <td class="text-center"><a href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a></td>
                                </tr>
                            ';
                        }
                    }
                    if ($request->action == 'edit') {
                        if (isset($files[$j]['name'])) {
                            $fileid .= $files[$j]['id'] . ',';
                            $file .= '
                                <tr>
                                    <td>
                                        <input type="hidden" name="choice['.$choice_id.'][fileid]['.$i.'][]" class="form-control" value="'.$files[$j]['id'].'">
                                        <input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control" value="'.$files[$j]['name'].'">
                                        <input type="hidden" name="choice['.$choice_id.'][files]['.$i.'][]" class="form-control" value="'.$files[$j]['file'].'">
                                    </td>
                                    <td class="text-center"><a class="text-success font-weight-bolder" href="'.$base_url.$files[$j]['file'].'" target="_blank">Lihat File</a> | <a class="text-danger font-weight-bolder hapusfile" data-id="'.$files[$j]['id'].'" style="cursor: pointer;">Hapus</a></td>
                                </tr>
                            ';
                        } else {
                            $file .= '
                                <tr>
                                    <td><input type="text" name="choice['.$choice_id.'][name]['.$i.'][]" class="form-control"></td>
                                    <td><input type="file" class="form-control" name="choice['.$choice_id.'][newfile]['.$i.'][]" accept=".gif, .jpg, .jpeg, .png, .doc, .docx, .pdf"></td>
                                </tr>
                            ';
                        }
                    }
                }
                $fileid = rtrim($fileid, ",");

                $select .= '
                    <div class="col-lg-6 exfile" id="exfile-'.$i.'">
                        <input type="hidden" class="choiceparent" id="choiceparent_'.$i.'" value="'.$choice_id.'">
                        <input type="hidden" class="existfile" name="existfile['.$i.']" value="'.$fileid.'">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                '.$column.'
                                <div class="form-group">
                                    <label>Dokumen Pendukung Penilaian</label>
                                    <table class="table table-bordered" id="tblfile-'.$i.'">
                                        <thead>
                                            <tr>
                                                <th width="65%">Nama File</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$file.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }
            $select .= '</div>';

            $output = $select;
        }

        return json_encode($output);

        die();
    }

    public function widgetcomponent(Request $request) {
        //print_r ($request->all());

        $header = Widget::where('id', $request->widget)->first();

        $resheader = '
            <div class="form-group">
                <label>Caption</label>
                <input type="text" value="'.$header->caption.'" class="form-control" disabled>
                <input type="hidden" name="caption" value="'.$header->caption.'" class="form-control" readonly>
            </div>

        ';

        $detail = WidgetComponent::where('widget_id', $request->widget)->get();

        $resdetail = '
            <div class="form-group">
                <label>Komponen</label>
                <table class="table table-bordered" id="tblfile-0">
                    <thead>
                        <tr>
                            <th width="65%">Pertanyaan</th>
                            <th>Bentuk Jawaban</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

        foreach ($detail as $key => $row) {
            $resdetail .= '
                <tr>
                    <td>
                        <input type="hidden" class="form-control" id="komponen_id" name="komponen_id['.$key.']" required value="'.$row['id'].'">
                        <input type="hidden" class="form-control" id="title" name="title['.$key.']" required value="'.$row['title'].'">
                        <input type="hidden" class="form-control" name="pilihan['.$key.']" value="'.$row['tipe'].'">
                        <input type="hidden" class="form-control" name="have_bobot['.$key.']" value="0">
                        '.$row['title'].'
                    </td>
                    <td>'.$row['naming'].'</td>
                </tr>
            ';
        }

        $resdetail .= '
                    </tbody>
                </table>
            </div>
        ';

        $output = $resheader . $resdetail;

        $output = [
            'header' => 0,
            'content' => $output
        ];

        echo json_encode($output);

        die();
    }

    public function widgetedit(Request $request) {
        //print_r ($request->all()); die;

        $header = Widget::where('id', $request->widget)->first();

        $resheader = '
            <div class="form-group" id="widget-header">
                <label>Caption</label>
                <input type="hidden" value="'.$header->caption.'" class="form-control" disabled>
                <input type="text" name="caption" value="'.$header->caption.'" class="form-control" disabled>
            </div>

        ';

        $detail = WidgetComponent::where('widget_id', $request->widget)->get();

        $resdetail = '
            <div class="form-group" id="widget-komponen">
                <label>Komponen</label>
                <table class="table table-bordered" id="tblfile-0">
                    <thead>
                        <tr>
                            <th width="65%">Pertanyaan</th>
                            <th>Bentuk Jawaban</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

        foreach ($detail as $key => $row) {
            $resdetail .= '
                <tr>
                    <td>
                        <input type="hidden" class="form-control" id="komponen_id" name="komponen_id['.$key.']" required value="'.$row['id'].'">
                        <input type="hidden" class="form-control" id="title" name="title['.$key.']" required value="'.$row['title'].'">
                        <input type="hidden" class="form-control" name="pilihan['.$key.']" value="'.$row['tipe'].'">
                        <input type="hidden" class="form-control" name="have_bobot['.$key.']" value="0">
                        '.$row['title'].'
                    </td>
                    <td>'.$row['naming'].'</td>
                </tr>
            ';
        }

        $resdetail .= '
                    </tbody>
                </table>
            </div>
        ';

        //$output = $resheader . $resdetail;

        $output = [
            'header' => $resheader,
            'komponen' => $resdetail
        ];

        echo json_encode($output);

        die();
    }

    public function getRole(){
        $select = '<option value="">Pilih Role</option>';
        $roles = [
            ['id' => 4, 'nama' => 'Admin', 'is_child' => 0],
            ['id' => 5, 'nama' => 'Petugas Pendamping', 'is_child' => 1]
        ];

        foreach ($roles as $role) {
            $select .= '<option value="'.$role['id'].'">' . $role['nama'] . '</option>';
        }

        $output = [
            'count' => count($roles),
            'content' => $select
        ];

        return json_encode($output);

        die();
    }

    public function getRoleChild($roleid){
		$role_child = Config::select('configs.value as id', 'configs.name')
			->where('code', 'role_child_'.$roleid)
			->get();

		return $role_child;
	}
}
