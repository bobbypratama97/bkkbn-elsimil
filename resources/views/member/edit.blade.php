@extends('layouts.master')
@push('css')
<style>
    a.unclick { pointer-events: none; cursor: default; }
</style>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <form method="POST" action="{{ route('admin.member.update', $member->id) }}">
                @csrf
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            <div class="form mr-3 pt-3">
                                <input type="hidden" name="cid" value="{{ $member->id }}">
                                <button type="submit" class="btn btn-success font-weight-bold py-3 px-6 mb-2 text-center btn-block">Update Data Catin</button>
                            </div>
                            <a href="{{ route('admin.member.index') }}" class="btn btn-danger">Kembali</a>
                        </div>
                    </div>
                </div>

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Data Admin CMS :</h3>
                    </div>

                    <form method="POST" action="{{ route('admin.member.update', $member->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" class="form-control" value="{!! Helper::decryptNik($member->no_ktp) !!}" disabled />
                            </div>

                            <div class="form-group">
                                <label>No Telepon</label>
                                <input type="text" class="form-control" value="{{ $member->no_telp }}" disabled />
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{ $member->email }}" disabled />
                            </div>

                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ $member->name }}" required />
                            </div>

                            <div class="form-group row">
                                <div class="col-7">
                                    <div class="form-group">
                                        <label>Alamat Sesuai Domisili</label>
                                        <input name="alamat" class="form-control" value="{{ $member->alamat }}" required/>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label>RT</label>
                                        <input name="rt" class="form-control" value="{{ $member->rt }}" required/>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label>RW</label>
                                        <input name="rw" class="form-control" value="{{ $member->rw }}" required/>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kode POS</label>
                                        <input name="kodepos" class="form-control" value="{{ $member->kodepos }}" required/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Provinsi</label>
                                        <!-- <select class="form-control select2" id="provinsi" name="provinsi_id" required> -->
                                        <input type="text" class="form-control" value="{{ $member->provinsi_id }}" disabled />
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kabupaten</label>
                                        <input type="text" class="form-control" value="{{ $member->provinsi_id }}" disabled />
                                        <!-- <select class="form-control select2" id="kabupaten" name="kabupaten_id" required> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <input type="text" class="form-control" value="{{ $member->provinsi_id }}" disabled />
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kelurahan</label>
                                        <input type="text" class="form-control" value="{{ $member->provinsi_id }}" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-control" id="jk" name="gender" required>
                                            <option value="1" {{$member->gender == 1 ? "selected" : null}}>Laki - Laki</option>
                                            <option value="2" {{$member->gender == 2 ? "selected" : null}}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Tempat</label>
                                        <input type="text" name="tempat_lahir" class="form-control" value="{{$member->tempat_lahir}}" required />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="text" id="date" name="tgl_lahir" class="form-control" value="{{$member->tgl_lahir}}" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                </form>
            </div>
        </div>
    </div>

@push('script')
<script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
<script src="{{ asset('assets/plugins/bootbox/bootbox.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>


<script type="text/javascript">
    $('#date').datepicker({
        format: 'yyyy-mm-dd'
    });
</script>
@endpush

@endsection
