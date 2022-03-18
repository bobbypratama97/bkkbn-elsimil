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
              @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
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
                    </div>
                    <div class="col-md-6">
                      {{-- <div>Usia: {{ $member->usia }}</div> --}}
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon-calendar-with-a-clock-time-tools"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">Tempat/ Tanggal Lahir: {{ $member->tempat_lahir }} / {{ $member->tgl_lahir }}</span>
                        </div>
                      </div>
                      <div class="form-group row my-0">
                        <label class="mr-5 col-form-label"><i class="flaticon2-calendar-8"></i></label>
                        <div class="">
                            <span class="form-control-plaintext h6">Rencana Menikah: {{ $member->rencana_pernikahan }}</span>
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
                      {{-- <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div> --}}
                    </div>

                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="suplemenMakanan" id="suplemenMakanan" {{ $logbook->suplemen_makanan ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="suplemenMakanan">
                        Suplemen Makanan
                      </label>
                      {{-- <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div> --}}
                    </div>

                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="kie" id="kie" {{ $logbook->kie ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="kie">
                        KIE
                      </label>
                      {{-- <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div> --}}
                    </div>

                    <div class="form-check mb-4">
                      <input class="form-check-input mr-3" role="button" type="checkbox" value="1" name="rujukan" id="rujukan" {{ $logbook->rujukan ? 'checked' : '' }}>
                      <label class="form-check-label h6 ml-4" for="rujukan">
                        Rujukan
                      </label>
                      {{-- <div class="text-muted my-1 ml-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus imperdiet nisl egestas nisi venenatis, at sodales enim finibus. Aenean tempus placerat ultricies. Mauris a venenatis risus. Vestibulum eu eros gravida.
                      </div> --}}
                    </div>

                    <button type="submit" class="btn btn-success btn-lg btn-block mt-6"><span class="font-weight-boldest">Simpan</span></button>
                  </div>
                </form>
              </div>

              <div class="card card-custom gutter-b">
                <div class="card-body flex-wrap py-3">
                  <h5 class="card-title mb-0">
                    <div class="row align-items-center">
                      <div class="col-sm-8">
                        Hasil Kuesioner Terakhir
                        <br>
                        <h6 class="mt-1">Tanggal : {{ ($last_result ? $last_result["created_at"] : '-') }}</h6>
                      </div>
                      <div class="col-sm-4">
                        @if($last_result)
                          <span class="badge text-white ml-3" style="background-color: {{ $last_result["rating_color"] }}">{{ $last_result["label"] }}</span>
                        @else
                          -
                        @endif
                        </div>
                    </div>
                  </h5>
                </div>
              </div>

              <div class="card card-custom gutter-b">
                <div class="card-body flex-wrap py-3">
                  <h5 class="card-title mb-8">
                    Status Pengisian Logbook Pendamping
                  </h5>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tanggal Pengisian</th>
                      </tr>
                    </thead>
                    <tbody class="h6">
                      @foreach ($members_logbooks_status as $key => $detail)
                        <tr>
                          <td>{{ $detail["name"] }}</td>
                          <td>
                            @if($detail["status"])
                              <span class="badge bg-success text-white">Sudah Mengisi</span>
                            @else
                              <span class="badge bg-secondary">Belum Mengisi</span>
                            @endif
                          </td>
                          <td>{{ $detail["updated_at"] }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>

                </div>
              </div>

            </div>

            <div class="col-sm-7">
              <div class="card card-custom gutter-b">
                <div class="card-body flex-wrap py-3">
                  <h5 class="card-title mt-1 mb-8">Riwayat Intervensi</h3>
                  <h6 class="card-subtitle text-muted">Tabel ini untuk melihat riwayat intervensi pendamping dan pengisian kuesioner catin</h6>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Aksi</th>
                        <th scope="col">Catatan</th>
                      </tr>
                    </thead>
                    <tbody class="h6">
                      @foreach ($histories as $key => $detail)
                        <tr>
                          <td>{{  $detail["date"] }}</td>
                          <td>
                            @if($detail["log_type"]==1)
                              Intervensi dilakukan oleh: <br>{{ $detail["name"] }}
                            @else
                              Catin mengisi kuesioner
                            @endif

                          </td>
                          <td>
                            @if($detail["log_type"]==1)
                              <div class="form-check pl-0">
                                @if($detail['meta_data']['suplemen_darah'])
                                  <i class="flaticon2-accept"></i>
                                  <label class="form-check-label" for="logcheck1">
                                    Suplemen Penambah Darah
                                  </label>
                                  @endif
                              </div>

                              <div class="form-check pl-0">
                                @if($detail['meta_data']['suplemen_makanan'])
                                  <i class="flaticon2-accept"></i>
                                  <label class="form-check-label" for="logcheck2">
                                    Suplemen Makanan
                                  </label>
                                @endif

                              </div>

                               <div class="form-check pl-0">
                                @if($detail['meta_data']['kie'])
                                  <i class="flaticon2-accept"></i>
                                  <label class="form-check-label" for="logcheck3">
                                    KIE
                                  </label>
                                @endif
                              </div>

                              <div class="form-check pl-0">
                                @if($detail['meta_data']['rujukan'])
                                  <i class="flaticon2-accept"></i>
                                  <label class="form-check-label" for="logcheck4">
                                    Rujukan
                                  </label>
                                @endif
                              </div>

                            @else

                              Hasil Kuesioner :
                              {{-- <button type="button" class="btn font-size-sm unclick" style="background-color: {{ $detail['meta_data']["rating_color"] }}"><span class="font-weight-bolder text-white">{{ $detail['meta_data']["label"] }}</span></button> --}}
                              <span class="badge text-white" style="background-color: {{ $detail['meta_data']["rating_color"] }}">{{ $detail['meta_data']["label"] }}</span>

                            @endif

                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  {{ $logbook_histories->links() }}
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
