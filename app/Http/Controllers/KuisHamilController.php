<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Redirect;


use App\KuisHamilKontakAwal;

class KuisHamilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        // $this->validate($request,[
        //     'id_member' => 'required',
        //     'nama' => 'required|string',
        //     'nik' => 'required|string',
        //     'usia' => 'required|integer',
        //     'alamat' => 'required|string',
        //     'jumlah_anak' => 'required|integer',
        //     'usia_anak_terakhir' => 'required|integer',
        //     'anak_stunting' => 'required|boolean',
        //     'hari_pertama_haid_terakhir' => 'required|date',
        //     'sumber_air_bersih' => 'required|boolean',
        //     'rumah_layak_huni' => 'required|boolean',
        //     'bansos' => 'required|boolean'
        // ]);


        // $kuisHamilKontakAwal = new KuisHamilKontakAwal;
        // $kuisHamilKontakAwal->id_user = Auth::user()->id;
        // $kuisHamilKontakAwal->id_member = $request->id_member;
        // $kuisHamilKontakAwal->nama = $request->nama;
        // $kuisHamilKontakAwal->nik = $request->nik;
        // $kuisHamilKontakAwal->usia = $request->usia;
        // $kuisHamilKontakAwal->alamat = $request->alamat;
        // $kuisHamilKontakAwal->jumlah_anak = $request->jumlah_anak;
        // $kuisHamilKontakAwal->usia_anak_terakhir = $request->usia_anak_terakhir;
        // $kuisHamilKontakAwal->anak_stunting = $request->anak_stunting;
        // $kuisHamilKontakAwal->hari_pertama_haid_terakhir = $request->hari_pertama_haid_terakhir;
        // $kuisHamilKontakAwal->sumber_air_bersih = $request->sumber_air_bersih;
        // $kuisHamilKontakAwal->rumah_layak_huni = $request->rumah_layak_huni;
        // $kuisHamilKontakAwal->bansos = $request->bansos;
        // $kuisHamilKontakAwal->save();


        dd("Success!");
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
