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

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Page</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.page.update', $page->id) }}">
                        @method('PUT')
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" class="form-control" name="title" required oninvalid="this.setCustomValidity('Judul harus diisi')" oninput="setCustomValidity('')" value="{{ $page->title }}" />
                            </div>

                            <div class="form-group">
                                <label>Deskripsi Singkat</label>
                                <textarea class="tinymce-editor" id="deskripsi" name="deskripsi">{{ $page->content }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Status Publikasi</label>
                                <select class="form-control select2" id="publikasi" name="publikasi" required oninvalid="this.setCustomValidity('Status publikasi harus dipilih')" oninput="setCustomValidity('')" >
                                    @foreach ($status as $key => $val)
                                    <option value="{{ $key }}" {{ (isset($page->status) && ($page->status == $key)) ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.page.index') }}" class="btn btn-danger">Batal</a>
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
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#publikasi').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        tinymce.init({
            height : "400",
            selector:'#deskripsi',
            menubar: false,
            statusbar: false,
            forced_root_block : "",
            toolbar_items_size: 'medium',
            plugins: 'autoresize anchor autolink charmap code codesample directionality help hr image imagetools insertdatetime link lists media nonbreaking pagebreak preview print searchreplace table template textpattern toc visualblocks visualchars code',
            toolbar1: "undo redo | table | cut copy paste searchreplace | bullist numlist | outdent indent blockquote hr | h1 h2 bold italic strikethrough blockquote backcolor | link image media | preview code help"
        });
    });
</script>
@endpush

@endsection
