<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Redirect;
use Helper;


use App\Member;
use App\KuisHamilKontakAwal;

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
        return view('kuis_ibuhamil.kontakawal_create',[
            "id" => $id,
            "name" => $name,
            "no_ktp" => $no_ktp,
            "gender" => $gender,
            "umur" => $age,
            "tempat_lahir" => $tempat_lahir,
            "tanggal_lahir" => $tanggal_lahir,
            "alamat" => $alamat
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
        $kontakAwal = new KuisHamilKontakAwal;
        $kontakAwal->id_user = Auth::user()->id;
        $kontakAwal->id_member = $request->id;
        $kontakAwal->nama = $request->nama;
        $kontakAwal->nik = $request->nik;
        $kontakAwal->usia = $request->usia;
        $kontakAwal->alamat = $request->alamat;
        $kontakAwal->jumlah_anak = $request->jumlah_anak;
        $kontakAwal->usia_anak_terakhir = $request->usia_anak_terakhir;
        $kontakAwal->anak_stunting = $request->anak_stunting;
        $kontakAwal->hari_pertama_haid_terakhir = $request->hari_pertama_haid_terakhir;
        $kontakAwal->sumber_air_bersih = $request->sumber_air_bersih;
        $kontakAwal->rumah_layak_huni = $request->rumah_layak_huni;
        $kontakAwal->bansos = $request->bansos;
        // dd($kontakAwal);
        $kontakAwal->save();
        // dd("masuk2");
        $message = 'Kuesioner hamil kontak awal berhasil ditambahkan';
        return redirect()->route('admin.kontakawal-create',["id" => $request->id])->with('success', $message);

        // $insertKontakAwal = array(
        //     'id_user' => Auth::user()->id,
        //     'id_member' => $request->id,
        //     'nama' => $request->nama,
        //     'nik' => $request->nik,
        //     'usia' => $request->usia,
        //     'alamat' => $request->alamat,
        //     'jumlah_anak' => $request->jumlah_anak,
        //     'usia_anak_terakhir' => $request->usia_anak_terakhir,
        //     'anak_stunting' => $request->anak_stunting,
        //     'hari_pertama_haid_terakhir' => $request->hari_pertama_haid_terakhir,
        //     'sumber_air_bersih' => $request->sumber_air_bersih,
        //     'rumah_layak_huni' => $request->rumah_layak_huni,
        //     'bansos' => $request->bansos
        // );
        // KuisHamilKontakAwal::create($insertKontakAwal);

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
