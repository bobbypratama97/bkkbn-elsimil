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

                @if ( Session::has( 'success' ))
                <div class="alert alert-custom alert-success" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon2-telegram-logo"></i>
                    </div>
                    <div class="alert-text"><strong>Perhatian</strong><br />{{ Session::get( 'success' ) }}</div>
                </div>
                @endif


                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Password</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.profile.store') }}" id="form-post-artikel" enctype="multipart/form-data">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Password Lama</label>
                                <input type="password" class="form-control" id="old" name="old" required autofocus oninvalid="this.setCustomValidity('Password lama harus diisi')" oninput="setCustomValidity('')" >
                            </div>

                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" required autofocus oninvalid="this.setCustomValidity('Password baru harus diisi')" oninput="setCustomValidity('')" onChange="onChange()" >
                            </div>

                            <div class="form-group">
                                <label>Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="confirm" name="confirm" required autofocus oninvalid="this.setCustomValidity('Konfirmasi password baru harus diisi')" oninput="setCustomValidity('')" >
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="button">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@push('script')
<script type="text/javascript">
        function onChange() {
            const password = document.querySelector('input[name=password]');
            const confirm = document.querySelector('input[name=confirm]');
            if (confirm.value === password.value) {
                confirm.setCustomValidity('');
            } else {
                confirm.setCustomValidity('Password baru dan Konfirmasi password tidak sama');
            }
        }
</script>
@endpush

@endsection
