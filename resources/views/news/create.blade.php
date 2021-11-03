@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet"/>
<style>
    .dropzone.dropzone-default { padding: 1px !important; border: 2px dashed #ebedf3 !important; }
    .dropzone.dropzone-default .dropzone-msg-desc { color: #3f4254 !important; }
    .dropzone-ul { padding-inline-start: 25px !important; text-align: left !important; }
    .customPos { margin-left: auto !important; margin-right: auto !important; display: block !important; margin: 0px !important; }
    .customPosImg { border-radius: 0px !important; margin-left: auto !important; margin-right: auto !important; width: 100% !important; height: 100% !important; }
</style>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <!--<div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>Perhatian</strong><br />Data telah dihapus</div>
                </div>-->

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Artikel</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('news.store') }}" id="my-awesome-dropzone" enctype="multipart/form-data">
                        <div class="card-body">

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Thumbnail</label>
                                        <div id="dropzone" class="dropzone alert dropzone-default dropzone-success">
                                            <div class="dropzone-msg dz-message">
                                                <h3 class="dropzone-msg-title">Tarik gambar ke kotak ini atau klik untuk proses upload</h3>
                                                <span class="dropzone-msg-desc">
                                                    <small>
                                                        <br /><span class="text-danger font-weight-bolder"><u>KETENTUAN UPLOAD GAMBAR : </u></span><br />
                                                        <ul class="dropzone-ul">
                                                            <li>Gambar hanya dalam format <span class="text-danger font-weight-bolder">jpg, jpeg, atau png</span></li>
                                                            <li>Ukuran gambar tidak boleh melebihi <span class="text-danger font-weight-bolder">lebar 500 pixel</span> dan <span class="text-danger font-weight-bolder">tinggi 500 pixel</span></li>
                                                            <li>Max ukuran gambar <span class="text-danger font-weight-bolder">harus kurang dari 1MB</span></li>
                                                        </ul>
                                                    </small>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <label>Judul Artikel</label>
                                        <input type="text" class="form-control" id="title" name="title" required autofocus oninvalid="this.setCustomValidity('Judul harus diisi')" oninput="setCustomValidity('')" >
                                    </div>

                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select name="kategori_id" id="kategori" class="form-control" required oninvalid="this.setCustomValidity('Kategori harus dipilih')" oninput="setCustomValidity('')" >
                                            <option value="">Pilih</option>
                                            @foreach ($kategori as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Deskripsi Singkat</label>
                                        <textarea class="form-control" rows="3" id="deskripsi" name="deskripsi" required oninvalid="this.setCustomValidity('Deskripsi harus diisi')" oninput="setCustomValidity('')" ></textarea>
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
                            </div>

                            <div class="form-group">
                                <label>Isi Artikel</label>
                                <textarea class="tinymce-editor my-editor" id="content" name="content"></textarea>
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="button">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('news.index') }}" class="btn btn-danger">Batal</a>
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
<script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#kategori').select2({
            allowClear: true,
            placeholder: "Pilih Kategori",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#publikasi').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        /*tinymce.init({
            selector:'#content',
            menubar: false,
            statusbar: false,
            forced_root_block : "",
            toolbar_items_size: 'medium',
            plugins: 'autoresize anchor autolink charmap code codesample directionality help hr image imagetools insertdatetime link lists media nonbreaking pagebreak preview print searchreplace table template textpattern toc visualblocks visualchars code',
            toolbar1: "undo redo | table | cut copy paste searchreplace | bullist numlist | outdent indent blockquote hr | h1 h2 bold italic strikethrough blockquote backcolor | link image media | preview code help"
        });*/

    });
</script>

<script>
    var editor_config = {
        path_absolute : "/",
        selector: "textarea.my-editor",
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

<script>
    var maxImageWidth = 500, maxImageHeight = 500;
    Dropzone.options.dropzone = {
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('news.store') }}', 
        addRemoveLinks: true,
        autoProcessQueue: false,
        uploadMultiple: false,
        parallelUploads: 100,
        acceptedFiles: '.jpeg, .jpg, .png',
        maxFiles: 1,
        maxFileSize: 1,
        thumbnailWidth: 600,
        thumbnailHeight: 400,
        dictRemoveFile: "Hapus gambar",
        init: function() {
            var myDropzone = this;

            $("#button").click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                var files = $('#dropzone').get(0).dropzone.getAcceptedFiles();
                var editorContent = tinyMCE.get('content').getContent();

                if ($('#title').val() == '' || $('#kategori').val() == '' || $('#deskripsi').val() == '' || editorContent == '' || $('#publikasi').val() == '') {
                    bootbox.alert({
                        title: 'Perhatian',
                        centerVertical: true,
                        closeButton: false,
                        message: "Semua data harus diisi",
                        size: 'small'
                    });
                } else if (files.length == 0) {
                    bootbox.alert({
                        title: 'Perhatian',
                        centerVertical: true,
                        closeButton: false,
                        message: "Harus ada gambar yang diupload",
                        size: 'small'
                    });
                } else {
                    myDropzone.processQueue();
                }

            });

            this.on("maxfilesexceeded", function(file) {
                bootbox.alert({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "Hanya bisa upload 1 gambar saja.",
                    size: 'small'
                });
                this.removeFile(file);
            });

            this.on("thumbnail", function(file, dataUrl) {
                if (file.width > maxImageWidth || file.height > maxImageHeight) {
                    bootbox.alert({
                        title: 'Perhatian',
                        centerVertical: true,
                        closeButton: false,
                        message: "<p class='text-center'>Ukuran file melebihi <span class='text-danger font-weight-bolder'>500px x 500px</span>. <br />Silahkan upload ulang gambar.</p>",
                        size: 'small'
                    });
                    this.removeFile(file);
                } else if (file.size > 1000000) {
                    bootbox.alert({
                        title: 'Perhatian',
                        centerVertical: true,
                        closeButton: false,
                        message: "<p class='text-center'>Ukuran file melebihi <span class='text-danger'>1 MB</span>. <br />Silahkan upload ulang gambar.</p>",
                        size: 'small'
                    });
                    this.removeFile(file);
                } else {
                    $(".dz-preview").addClass("customPos");
                    $('.dz-image').addClass("customPosImg");
                    $('.dropzone .dz-preview .dz-image img').css({"width": "100%"});
                }
            });

            this.on('sending', function(file, xhr, formData) {
                $.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });
                
                var data = $('#my-awesome-dropzone').serializeArray();
                $.each(data, function(key, el) {
                    formData.append(el.name, el.value);
                });
                formData.append('content', tinyMCE.get('content').getContent());
            });

            this.on("success", function(file, response) {
                var json = JSON.parse(response);

                $('.dz-remove').hide();
                $(".dz-preview").addClass("customPos");
                $('.dz-image').addClass("customPosImg");
                $('.dropzone .dz-preview .dz-image img').css({"width": "100%"});

                $.preloader.stop();
                
                var dialog = bootbox.dialog({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'><strong>" + json.message + "</strong><br /><br />Apakah Anda ingin tetap disini untuk <br /><strong>menambahkan artikel lainnya</strong> atau kembali ke <strong>daftar artikel</strong> ?</p>",
                    buttons: {
                        cancel: {
                            label: "Tetap disini",
                            className: 'btn-danger',
                            callback: function() {
                                window.location.href = '{{ route('news.create') }}';
                            }
                        },
                        ok: {
                            label: "Kembali ke Daftar Artikel",
                            className: 'btn-info',
                            callback: function() {
                                window.location.href = '{{ route('news.index') }}';
                            }
                        }
                    }
                });
            });
        }
    }
</script>
@endpush

@endsection
