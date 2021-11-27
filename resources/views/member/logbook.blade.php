@extends('layouts.master')
@push('css')
@endpush

@section('content')
  <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12">
              @if ( Session::has( 'success' ))
                <div class="alert alert-custom alert-success" role="alert">
                  <div class="alert-icon">
                      <i class="flaticon2-telegram-logo"></i>
                  </div>
                  <div class="alert-text"><strong>Perhatian</strong><br />{{ Session::get( 'success' ) }}</div>
                </div>
              @endif
              <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap py-3">
                  <div class="card-title">
                      <h3 class="card-label">Data Catin : {{ $member->name }}
                  </div>

                  <div class="card-toolbar">
                    <a href="{{ route('admin.member.result',$member->id) }}" class="btn btn-danger font-weight-bolder btn-md">Kembali</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row px-5">
                    <div class="col-md-6">
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon2-user"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">Nama: {!! Helper::customUser($member->name) !!}</span>
                        </div>
                      </div>
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon2-website"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">No KTP: {!! Helper::decryptNik($member->no_ktp) !!}</span>
                        </div>
                      </div>
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon-rotate"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">Gender: {{ $member->gender == 1 ? 'Pria' : 'Wanita' }}</span>
                        </div>
                      </div>
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon2-calendar-8"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">Rencana Menikah: {{ $member->rencana_pernikahan }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      {{-- <div>Usia: {{ $member->usia }}</div> --}}
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon-calendar-with-a-clock-time-tools"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">Tempat/ Tanggal Lahir: {{ $member->tempat_lahir }} / {{ $member->tgl_lahir }}</span>
                        </div>
                      </div>
                      {{-- <div>Lokasi: {{ $member->gender }}</div> --}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-5">
              <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap py-3">
                  <h5 class="card-title">Intervensi Pendampingan Calon Pengantin</h5>
                  <h6 class="card-subtitle my-1 text-muted">Pengisian Checklist ini oleh pendamping</h6>
                </div>
                <form id="logbook-update" class="form-horizontal m-t-30" action="{{ route('admin.member.logbook_update') }}" method="POST" accept-charset="UTF-8">
                  @csrf
                  <input type="hidden" name="id_user" value="{{ Auth::user()->id }}">
                  <input type="hidden" name="id_member" value="{{ $member->id }}">
                  <div class="card-body">
                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="suplemenDarah" id="suplemenDarah" {{ $logbook->suplemen_darah ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="suplemenDarah">
                        Suplemen Penambah Darah
                      </label>
                      <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div>
                    </div>

                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="suplemenMakanan" id="suplemenMakanan" {{ $logbook->suplemen_makanan ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="suplemenMakanan">
                        Suplemen Makanan
                      </label>
                      <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div>
                    </div>

                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="kie" id="kie" {{ $logbook->kie ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="kie">
                        KIE
                      </label>
                      <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div>
                    </div>

                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="rujukan" id="rujukan" {{ $logbook->rujukan ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="rujukan">
                        Rujukan
                      </label>
                      <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg btn-block mt-6"><span class="font-weight-boldest">Simpan</span></button>
                  </div>
                </form>
              </div>
            </div>

            <div class="col-sm-7">
              <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap py-3">
                  <h3 class="card-title">Perbandingan Hasil Sebelum dan Sesudah Intervensi</h3>
                  <h6 class="card-subtitle my-1 text-muted">Tabel ini untuk membandingkan sebelum dan sesudah pendamping melakukan intercensi pada catin</h6>
                </div>
                <div class="card-body">
                  <h5 class="card-text">Calon Pengantin {{ $member->gender == 1 ? 'Pria' : 'Wanita' }}</h5>

                  <div class="my-10">
                    <button type="button" class="btn btn-success btn-lg btn-block mb-5"><span class="font-weight-boldest">Tgl Pengisian : {{ $kuis_first ? $kuis_first->created_at : '-' }}</span></button>
                    <button type="button" class="btn btn-info btn-lg btn-block mb-5"><span class="font-weight-boldest">Tgl Update : {{ $kuis_last ? $kuis_last->created_at : '-' }}</span></button>
                  </div>

                  <table class="table table-bordered text-left mt-10">
                    <thead>
                      <tr>
                        <th scope="col" class="h6 p-6">Variabel</th>
                        <th scope="col" class="h6 p-6">Sebelum Intervensi</th>
                        <th scope="col" class="h6 p-6">Setelah Intervensi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($details_first)
                        @foreach ($details_first as $key => $detail)

                          @if($key-1 > -1)
                            @if($details_first[$key-1]["pertanyaan_header_caption"] == $detail["pertanyaan_header_caption"])
                              @continue
                            @endif
                          @endif

                          @if ($detail["pertanyaan_bobot_id"] != "0")
                            <tr>
                              <td scope="row" class="h6 p-6">{{ $detail["pertanyaan_header_caption"] }}</td>
                              {{-- sebelum intervensi --}}
                              <td class="h6 p-6" >
                                <span class="badge p-2 mr-2" style="background-color:{{ $detail["pertanyaan_rating_color"] }}"> </span>
                                
                                @if($detail["pertanyaan_header_caption"] == "Perilaku Merokok")
                                  {{ $detail["pertanyaan_bobot_label"] }}
                                @elseif($detail["pertanyaan_header_caption"] == "Indeks Massa Tubuh")
                                  {{ $detail["formula_value"] }} - {{ $detail["pertanyaan_bobot_label"] }}
                                @else
                                  {{ $detail["value"] }} - {{ $detail["pertanyaan_bobot_label"] }}
                                @endif
                                
                              </td>
                              {{-- setelah intervensi --}}
                              <td class="h6 p-6">
                                @if($details_last)
                                  <span class="badge p-2 mr-2" style="background-color:{{ $details_last[$loop->index]["pertanyaan_rating_color"] }}"> </span>
                                  
                                  @if($details_last[$loop->index]["pertanyaan_header_caption"] == "Perilaku Merokok")
                                    {{ $details_last[$loop->index]["pertanyaan_bobot_label"] }}
                                  @elseif($details_last[$loop->index]["pertanyaan_header_caption"] == "Indeks Massa Tubuh")
                                    {{ $details_last[$loop->index]["formula_value"] }} - {{ $details_last[$loop->index]["pertanyaan_bobot_label"] }}
                                  @else
                                    {{ $details_last[$loop->index]["value"] }} - {{ $details_last[$loop->index]["pertanyaan_bobot_label"] }}
                                  @endif
                                @else
                                  -
                                @endif
                                
                                
                              </td>
                            </tr>
                          @endif
                        @endforeach
                      @endif
                    </tbody>
                  </table>

                  @if($couple)
                    <div class="mt-10">
                      <h5 class="card-text">Calon Pengantin {{ $member->gender == 1 ? 'Wanita' : 'Pria' }} / Pasangan</h5>
                      <h5 class="card-text">{{ $couple->namapasangan }} - {!! Helper::decryptNik($couple->no_ktp) !!}</h5>

                      <table class="table table-bordered text-left mt-10">
                        <thead>
                          <tr>
                            <th scope="col" class="h6 p-6">Variabel</th>
                            <th scope="col" class="h6 p-6">Sebelum Intervensi</th>
                            <th scope="col" class="h6 p-6">Setelah Intervensi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if($details_couple_first)

                            @foreach ($details_couple_first as $key => $detail_couple)

                            @if($key-1 > -1)
                              @if($details_couple_first[$key-1]["pertanyaan_header_caption"] == $detail_couple["pertanyaan_header_caption"])
                                @continue
                              @endif
                            @endif

                              @if ($detail_couple["pertanyaan_bobot_id"] != "0")
                                <tr>
                                  <td scope="row" class="h6 p-6">{{ $detail_couple["pertanyaan_header_caption"] }}</td>
                                  {{-- sebelum intervensi --}}
                                  <td class="h6 p-6" >
                                    <span class="badge p-2 mr-2" style="background-color:{{ $detail_couple["pertanyaan_rating_color"] }}"> </span>
                                    
                                    @if($detail_couple["pertanyaan_header_caption"] == "Perilaku Merokok")
                                      {{ $detail_couple["pertanyaan_bobot_label"] }}
                                    @elseif($detail_couple["pertanyaan_header_caption"] == "Indeks Massa Tubuh")
                                      {{ $detail_couple["formula_value"] }} - {{ $detail_couple["pertanyaan_bobot_label"] }}
                                    @else
                                      {{ $detail_couple["value"] }} - {{ $detail_couple["pertanyaan_bobot_label"] }}
                                    @endif
                                    
                                  </td>
                                  {{-- setelah intervensi --}}
                                  <td class="h6 p-6">
                                    @if($details_couple_last)
                                      <span class="badge p-2 mr-2" style="background-color:{{ $details_couple_last[$loop->index]["pertanyaan_rating_color"] }}"> </span>
                                      
                                      @if($details_couple_last[$loop->index]["pertanyaan_header_caption"] == "Perilaku Merokok")
                                        {{ $details_couple_last[$loop->index]["pertanyaan_bobot_label"] }}
                                      @elseif($details_couple_last[$loop->index]["pertanyaan_header_caption"] == "Indeks Massa Tubuh")
                                        {{ $details_couple_last[$loop->index]["formula_value"] }} - {{ $details_couple_last[$loop->index]["pertanyaan_bobot_label"] }}
                                      @else
                                        {{ $details_couple_last[$loop->index]["value"] }} - {{ $details_couple_last[$loop->index]["pertanyaan_bobot_label"] }}
                                      @endif
                                    @else
                                      -
                                    @endif
                                    
                                    
                                  </td>
                                </tr>
                              @endif
                            @endforeach
                          @endif
                        </tbody>
                      </table>
                    </div>
                  @endif

                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>

@push('script')
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

@endpush
@endsection