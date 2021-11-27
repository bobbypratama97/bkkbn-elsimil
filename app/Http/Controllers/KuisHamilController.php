<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Redirect;
use Helper;


use App\Member;
use App\KuesionerHamil;
use App\KuisHamilKontakAwal;
use App\KuisHamil12Minggu;
use App\KuisHamil16Minggu;

class KuisHamilController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexKontakAwal($id)
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

        }
        #data kuesioner
        $data = KuesionerHamil::where([['id_member','=',$id],['periode','=',1]])
                ->select(['nama', 'nik', 'usia', 
                        'alamat', 'jumlah_anak','usia_anak_terakhir', 
                        'anak_stunting', 'hari_pertama_haid_terakhir','sumber_air_bersih', 
                        'rumah_layak_huni', 'bansos','created_at','updated_at'])->first();
        return view('kuis_ibuhamil.kontakawal_create',[
            "id" => $id,
            "name" => $name,
            "no_ktp" => $no_ktp,
            "gender" => $gender,
            "umur" => $age,
            "tempat_lahir" => $tempat_lahir,
            "tanggal_lahir" => $tanggal_lahir,
            "alamat" => $alamat,
            "data_kuesioner" => $data
        ]);

    }

    public function indexPeriode12Minggu($id)
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

        }
        #data kuesioner
        $data = KuesionerHamil::where([['id_member','=',$id],['periode','=',2]])->first();
        return view('kuis_ibuhamil.periode12_create',[
            "id" => $id,
            "name" => $name,
            "no_ktp" => $no_ktp,
            "gender" => $gender,
            "umur" => $age,
            "tempat_lahir" => $tempat_lahir,
            "tanggal_lahir" => $tanggal_lahir,
            "alamat" => $alamat,
            "data_kuesioner" => $data
        ]);

    }

    public function indexPeriode16Minggu($id)
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

        }
        #data kuesioner
        $data = KuesionerHamil::where([['id_member','=',$id],['periode','=',3]])->first();
        return view('kuis_ibuhamil.periode16_create',[
            "id" => $id,
            "name" => $name,
            "no_ktp" => $no_ktp,
            "gender" => $gender,
            "umur" => $age,
            "tempat_lahir" => $tempat_lahir,
            "tanggal_lahir" => $tanggal_lahir,
            "alamat" => $alamat,
            "data_kuesioner" => $data
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeKontakAwal(Request $request)
    {
        $checkExisting = KuesionerHamil::where([['id_member','=',$request->id],['periode','=',1]])->select('created_at')->first();
        if($checkExisting != null){
            KuesionerHamil::where([['id_member','=',$request->id],['periode','=',1]])
            ->update([
                'nama' => $request->nama,
                'nik' => Helper::encryptNik($request->nik),
                'usia' => $request->usia,
                'alamat' => $request->alamat,
                'jumlah_anak' => $request->jumlah_anak,
                'usia_anak_terakhir' => $request->usia_anak_terakhir,
                'anak_stunting' => $request->anak_stunting,
                'hari_pertama_haid_terakhir' => $request->hari_pertama_haid_terakhir,
                'sumber_air_bersih' => $request->sumber_air_bersih,
                'rumah_layak_huni' => $request->rumah_layak_huni,
                'bansos' => $request->bansos,
                'periode' => 1
            ]);
            $message = 'Kuesioner hamil kontak awal berhasil diperbaharui';
            return redirect()->route('admin.kontakawal-create',["id" => $request->id])->with('success', $message);
        }else{
            $this->validate($request,[
                'nama' => 'required',
                'nik' => 'required',
                'usia' => 'required',
                'alamat' => 'required',
                'jumlah_anak' => 'required',
                'usia_anak_terakhir' => 'required',
                'anak_stunting' => 'required',
                'hari_pertama_haid_terakhir' => 'required',
                'sumber_air_bersih' => 'required',
                'rumah_layak_huni' => 'required',
                'bansos' => 'required'
            ],
            [
                'nama.required' => 'Nama harus diisi.',
                'nik.required' => 'NIK harus diisi.',
                'usia.required' => 'Usia harus diisi.',
                'alamat.required' => 'Alamat harus diisi.',
                'jumlah_anak.required' => 'Jumlah anak harus diisi.',
                'usia_anak_terakhir.required' => 'Usia anak terakhir harus diisi.',
                'anak_stunting.required' => 'Anak stunting harus diisi.',
                'hari_pertama_haid_terakhir.required' => 'Tanggal hari pertama haid harus diisi.',
                'sumber_air_bersih.required' => 'Sumber air bersih harus diisi.',
                'rumah_layak_huni.required' => 'Rumah layak huni bersih harus diisi.',
                'bansos.required' => 'Bansos bersih harus diisi.',
            ]);
            $kontakAwal = new KuesionerHamil;
            $kontakAwal->id_user = Auth::user()->id;
            $kontakAwal->id_member = $request->id;
            $kontakAwal->nama = $request->nama;
            $kontakAwal->nik = Helper::encryptNik($request->nik);
            $kontakAwal->usia = $request->usia;
            $kontakAwal->alamat = $request->alamat;
            $kontakAwal->jumlah_anak = $request->jumlah_anak;
            $kontakAwal->usia_anak_terakhir = $request->usia_anak_terakhir;
            $kontakAwal->anak_stunting = $request->anak_stunting;
            $kontakAwal->hari_pertama_haid_terakhir = $request->hari_pertama_haid_terakhir;
            $kontakAwal->sumber_air_bersih = $request->sumber_air_bersih;
            $kontakAwal->rumah_layak_huni = $request->rumah_layak_huni;
            $kontakAwal->bansos = $request->bansos;
            $kontakAwal->periode = 1;
            $kontakAwal->save();
            $message = 'Kuesioner hamil kontak awal berhasil ditambahkan';
            return redirect()->route('admin.kontakawal-create',["id" => $request->id])->with('success', $message);
        }
    }

    public function storePeriode12Minggu(Request $request)
    {
        $checkExisting = KuesionerHamil::where([['id_member','=',$request->id],['periode','=',2]])->select('created_at')->first();
        if($checkExisting != null){
            KuesionerHamil::where([['id_member','=',$request->id],['periode','=',2]])
            ->update([
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'lingkar_lengan_atas' => $request->lingkar_lengan_atas,
                'hemoglobin' => $request->hemoglobin,
                'tensi_darah' => $request->tensi_darah,
                'gula_darah' => $request->gula_darah,
                'riwayat_sakit_kronik' => $request->riwayat_sakit_kronik
            ]);
            $message = 'Kuesioner hamil periode 12 minggu berhasil diperbaharui';
            return redirect()->route('admin.periode12minggu-create',["id" => $request->id])->with('success', $message);
        }else{
            $this->validate($request,[
                'berat_badan' => 'required',
                'tinggi_badan' => 'required',
                'lingkar_lengan_atas' => 'required',
                'hemoglobin' => 'required',
                'tensi_darah' => 'required',
                'gula_darah' => 'required',
                'riwayat_sakit_kronik' => 'required'
            ],
            [
                'berat_badan.required' => 'Berat Badan harus diisi.',
                'tinggi_badan.required' => 'Tinggi Badan harus diisi.',
                'lingkar_lengan_atas.required' => 'Lingkar Lengan Atas harus diisi.',
                'hemoglobin.required' => 'Hemoglobin harus diisi.',
                'tensi_darah.required' => 'Tensi Darah harus diisi.',
                'gula_darah.required' => 'Gula Darah harus diisi.',
            ]);
            $periode12Minggu = new KuesionerHamil;
            $periode12Minggu->periode = 2;
            $periode12Minggu->id_user = Auth::user()->id;
            $periode12Minggu->id_member = $request->id;
            $periode12Minggu->berat_badan = $request->berat_badan;
            $periode12Minggu->tinggi_badan = $request->tinggi_badan;
            $periode12Minggu->lingkar_lengan_atas = $request->lingkar_lengan_atas;
            $periode12Minggu->hemoglobin = $request->hemoglobin;
            $periode12Minggu->tensi_darah = $request->tensi_darah;
            $periode12Minggu->gula_darah = $request->gula_darah;
            $periode12Minggu->riwayat_sakit_kronik = $request->riwayat_sakit_kronik;
            $periode12Minggu->save();
            $message = 'Kuesioner hamil periode 12 minggu berhasil ditambahkan';
            return redirect()->route('admin.periode12minggu-create',["id" => $request->id])->with('success', $message);
        }
    }

    public function storePeriode16Minggu(Request $request)
    {
        $checkExisting = KuesionerHamil::where([['id_member','=',$request->id],['periode','=',3]])->select('created_at')->first();
        if($checkExisting != null){
            KuesionerHamil::where([['id_member','=',$request->id],['periode','=',3]])
            ->update([
                'hemoglobin' => $request->hemoglobin,
                'tensi_darah' => $request->tensi_darah,
                'gula_darah_sewaktu' => $request->gula_darah_sewaktu,
            ]);
            $message = 'Kuesioner hamil periode 16 minggu berhasil diperbaharui';
            return redirect()->route('admin.periode16minggu-create',["id" => $request->id])->with('success', $message);
        }else{
            $this->validate($request,[
                'hemoglobin' => 'required',
                'tensi_darah' => 'required',
                'gula_darah_sewaktu' => 'required',
            ],
            [
                'hemoglobin.required' => 'Hemoglobin harus diisi.',
                'tensi_darah.required' => 'Tinggi Badan harus diisi.',
                'gula_darah_sewaktu.required' => 'Gula Darah Sewaktu harus diisi.',
            ]);
            $periode16Minggu = new KuesionerHamil;
            $periode16Minggu->periode = 3;
            $periode16Minggu->id_user = Auth::user()->id;
            $periode16Minggu->id_member = $request->id;
            $periode16Minggu->hemoglobin = $request->hemoglobin;
            $periode16Minggu->tensi_darah = $request->tensi_darah;
            $periode16Minggu->gula_darah_sewaktu = $request->gula_darah_sewaktu;
            $periode16Minggu->save();
            $message = 'Kuesioner hamil periode 16 minggu berhasil ditambahkan';
            return redirect()->route('admin.periode16minggu-create',["id" => $request->id])->with('success', $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
