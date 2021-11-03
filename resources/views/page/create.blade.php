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
                        <h3 class="card-title">Tambah Page</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.page.store') }}">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" class="form-control" name="title" required oninvalid="this.setCustomValidity('Judul harus diisi')" oninput="setCustomValidity('')" />
                            </div>

                            <div class="form-group">
                                <label>Deskripsi Singkat</label>
                                <textarea class="tinymce-editor deskripsi" id="deskripsi" name="deskripsi"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Status Publikasi</label>
                                <select class="form-control select2" id="publikasi" name="publikasi" required oninvalid="this.setCustomValidity('Status publikasi harus dipilih')" oninput="setCustomValidity('')" >
                                    @foreach ($status as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
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
    });
</script>

<script>
    var editor_config = {
        height : "400",
        path_absolute : "/",
        selector: "textarea.deskripsi",
        branding: false,
        statusbar: false,
        menubar: "edit insert format table",
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar: "insertfile undo redo | sizeselect | fontselect |  fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | preview code",
        fontsize_formats: "8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 18pt 20pt 24pt 28pt 32pt 36pt",
        relative_urls: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + "&type=Images";
            } else {
                cmsURL = cmsURL + "&type=Files";
            }

            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'Filemanager',
                width : x * 0.8,
                height : y * 0.8,
                resizable : "yes",
                close_previous : "no"
            });
        }
    };

    tinymce.init(editor_config);
</script>
@endpush

@endsection
