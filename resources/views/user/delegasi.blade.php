@extends('layouts.master')

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                </div>
                @endif

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <span class="card-icon">
                                        <i class="flaticon-warning text-danger"></i>
                                    </span>
                                    <h3 class="card-label text-danger font-weight-bolder">Perhatian</h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="{{ route('admin.user.index') }}" class="btn btn-danger font-weight-bolder btn-md">Kembali</a>
                                </div>
                            </div>
                            <div class="card-body text-center text-danger font-weight-boldest">Perubahan data Petugas dan Catin akan mengakibatkan perubahan data Petugas terhadap Pasangannya</div>
                        </div>
                    </div>
                </div>

                @if ($user->total_member != 0)
                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Pindahkan Catin milik Petugas {{ $user->name }}</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.user.move') }}">
                        @csrf
                        <input type="hidden" name="cid" value="{{ $user->id }}">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Pindahkan catin ke petugas</label>
                                <select name="user" class="form-control" id="user">
                                    <option value="">Pilih</option>
                                    @foreach ($users as $key => $row)
                                    <option value="{{ $row->user_id }}">{!! Helper::customUser($row->nama) !!} - {{ $row->provinsi }}, {{ $row->kabupaten }}, Kec. {{ $row->kecamatan}}, Kel. {{ $row->kelurahan }} (Jumlah Catin : {{ $row->total }})</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left"></div>
                                <div class="col text-right">
                                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @endif

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Tambah / Ubah Catin Petugas {{ $user->name }}</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.user.submit') }}">
                        @csrf
                        <input type="hidden" name="cid" value="{{ $user->id }}">
                        <div class="card-body">

                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Provinsi</label>
                                    <input type="hidden" id="provinsi" value="{{ $user->provinsi_id }}">
                                    <input type="text" value="{{ $user->provinsi }}" class="form-control" disabled>
                                </div>
                                <div class="col-lg-4">
                                    <label>Kabupaten</label>
                                    <input type="hidden" id="kabupaten" value="{{ $user->kabupaten_id }}">
                                    <input type="text" value="{{ $user->kabupaten }}" class="form-control" disabled>
                                </div>
                                <div class="col-lg-4">
                                    <label>Kecamatan</label>
                                    <input type="hidden" id="kecamatan" value="{{ $user->kecamatan_id }}">
                                    <input type="text" value="{{ $user->kecamatan }}" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Catin yang telah ditambahkan</label>
                                <select name="oldmember[]" id="oldmember" class="form-control">
                                    <option value="">Pilih</option>
                                    @foreach ($oldmember as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Catin Baru</label>
                                <select name="newmember[]" id="newmember" class="form-control">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                        </div>

                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left"></div>
                                <div class="col text-right">
                                    @if(Auth::user()->role < $user->role_id || $user->id == Auth::user()->id)
                                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                    @else
                                    <button type="button" class="btn btn-default mr-2 btn-disabled">Simpan</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#user').select2({
            placeholder: "Pilih",
            allowClear: true,
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#newmember').select2({
            multiple: true,
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#oldmember').select2({
            multiple: true,
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        }).val([{{ $valold }}]).trigger('change');


        $('#kecamatan').on('change', function() {
            var provinsi = $('#provinsi').val();
            var kabupaten = $('#kabupaten').val();
            var kecamatan = $('#kecamatan').val();
            //var kecamatan = $(this).val();

            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            $.ajax({
                url: '{{ route('member') }}',
                type: 'POST',
                data: {provinsi: provinsi, kabupaten: kabupaten, kecamatan : kecamatan, '_token': "{{ csrf_token() }}"},
                dataType: 'json',
                success: function( data ) {
                    $.preloader.stop();
                    
                    $('#newmember').html(data.content);
                }
            });
        }).change();
    });
</script>
@endpush

@endsection
