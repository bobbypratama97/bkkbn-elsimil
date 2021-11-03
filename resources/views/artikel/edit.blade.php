@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet"/>
<style>
    .dropzone.dropzone-default { padding: 1px !important; border: 2px dashed #ebedf3 !important; }
    .dropzone.dropzone-default .dropzone-msg-desc { color: #3f4254 !important; }
    .dropzone-ul { padding-inline-start: 25px !important; text-align: left !important; }
    .customPos { margin-left: auto !important; margin-right: auto !important; display: block !important; margin: 0px !important; }
    .customPosImg { border-radius: 0px !important; margin-left: auto !important; margin-right: auto !important; width: 100% !important; height: 100% !important; }
    .customPosOne { margin-left: auto !important; margin-right: auto !important; display: block !important; margin: 0px !important; }
    .customPosImgOne { border-radius: 0px !important; margin-left: auto !important; margin-right: auto !important; width: 34% !important; height: 34% !important; }
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
                    <form class="form" method="POST" action="{{ route('admin.artikel.update', $data->id) }}" id="my-awesome-dropzone" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        
                        <div class="card-body">

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Thumbnail</label>
                                        <input id="img-thumb" name="thumbnail" type="hidden" value="{{ $data->thumbnail }}">
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
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $data->title }}" required autofocus>
                                    </div>

                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select name="kategori_id" id="kategori" class="form-control" required>
                                            <option value="">Pilih</option>
                                            @foreach ($kategori as $row)
                                            <option value="{{ $row->id }}" {{ $row->id == $data->kategori_id ? 'selected' : '' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Deskripsi Singkat</label>
                                        <textarea class="form-control" rows="3" id="deskripsi" name="deskripsi" required>{{ $data->deskripsi }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Status Publikasi</label>
                                        <select class="form-control select2" id="publikasi" name="publikasi" required oninvalid="this.setCustomValidity('Status publikasi harus dipilih')" oninput="setCustomValidity('')" >
                                            @foreach ($status as $key => $val)
                                            <option value="{{ $key }}" {{ $key == $data->status ? 'selected' : '' }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Gambar Utama</label>
                                <input id="img-original" name="original" type="hidden" value="{{ $data->gambar }}">
                                <div id="dropzone1" class="dropzone alert dropzone-default dropzone-success">
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

                            <div class="form-group">
                                <label>Isi Artikel</label>
                                <textarea class="tinymce-editor my-editor" id="content" name="content">{{ $data->content }}</textarea>
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="button">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.artikel.index') }}" class="btn btn-danger">Batal</a>
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
    });
</script>

<script>
    var editor_config = {
        height : "400",
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

<script type="text/javascript">
    var maxImageWidth = 500, maxImageHeight = 500;

    Dropzone.autoDiscover = false;
    
    $('#dropzone').dropzone({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('admin.artikel.upload') }}', 
        addRemoveLinks: true,
        autoProcessQueue: true,
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

            var urlthumb = baseurl + "/uploads/artikel/ori/" + "{{ $data->thumbnail }}";
            var mockFile = { name: "{{ $data->thumbnail }}", size: 12345, type: 'image/jpeg' };
            this.options.addedfile.call(this, mockFile);
            this.options.thumbnail.call(this, mockFile, urlthumb);
            mockFile.previewElement.classList.add('dz-success');
            mockFile.previewElement.classList.add('dz-complete');

            $("#dropzone .dz-preview").addClass("customPos");
            $('#dropzone .dz-image').addClass("customPosImg");
            $('#dropzone .dz-preview .dz-image').css({"width": "100%"});
            $('#dropzone .dz-preview .dz-image img').css({"width": "100%"});

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

            this.on("removedfile", function(file) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route('admin.artikel.upload') }}',
                    data: { action: 'delete', jenis: 'thumbnail', module: 'artikel' },
                    success: function(data) {
                        console.log('Thumbnail dihapus');
                        $('#img-thumb').val('');
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
                /*var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;*/
            });

            this.on("thumbnail", function(file, dataUrl) {
                if (file.width > maxImageWidth || file.height > maxImageHeight) {
                    this.removeFile(file);
                    file.rejectDimensions();
                } else if (file.size > 1000000) {
                    this.removeFile(file);
                    file.rejectSize();
                } else {
                    $("#dropzone .dz-preview").addClass("customPos");
                    $('#dropzone .dz-image').addClass("customPosImg");
                    $('#dropzone .dz-preview .dz-image').css({"width": "100%"});
                    $('#dropzone .dz-preview .dz-image img').css({"width": "100%"});

                    file.acceptDimensions();
                }
            });

            this.on('sending', function(file, xhr, formData) {
                $.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });
                
                formData.append('kuis_id', '0');
                formData.append('jenis', 'thumbnail');
                formData.append('module', 'artikel');
                formData.append('action', 'upload');
            });

            this.on("success", function(file, response) {
                var json = JSON.parse(response);

                $("#dropzone .dz-preview").addClass("customPos");
                $('#dropzone .dz-image').addClass("customPosImg");
                $('#dropzone .dz-preview .dz-image').css({"width": "100%"});
                $('#dropzone .dz-preview .dz-image img').css({"width": "100%"});

                $('#img-thumb').val(json.image);

                $.preloader.stop();
            });
        },
        accept: function(file, done) {
            file.rejectDimensions = function() { 
                bootbox.alert({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'>Ukuran file melebihi <span class='text-danger font-weight-bolder'>500px x 500px</span>. <br />Silahkan upload ulang gambar.</p>",
                    size: 'small'
                });
            };

            file.rejectSize = function() {
                bootbox.alert({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'>Ukuran file melebihi <span class='text-danger'>1 MB</span>. <br />Silahkan upload ulang gambar.</p>",
                    size: 'small'
                });
            };

            file.acceptDimensions = done;
        }
    });
</script>

<script type="text/javascript">
    Dropzone.autoDiscover = false;
    
    $('#dropzone1').dropzone({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('admin.artikel.upload') }}', 
        addRemoveLinks: true,
        autoProcessQueue: true,
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

            var urlimage = baseurl + "/uploads/artikel/ori/" + "{{ $data->gambar }}";
            var mockFile = { name: "{{ $data->gambar }}", size: 12345, type: 'image/jpeg' };
            this.options.addedfile.call(this, mockFile);
            this.options.thumbnail.call(this, mockFile, urlimage);
            mockFile.previewElement.classList.add('dz-success');
            mockFile.previewElement.classList.add('dz-complete');

            $("#dropzone1 .dz-preview").addClass("customPosOne");
            $('#dropzone1 .dz-image').addClass("customPosImgOne");
            $('#dropzone1 .dz-preview .dz-image').css({"width": "100%"});
            $('#dropzone1 .dz-preview .dz-image img').css({"width": "100%"});

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

            this.on("removedfile", function(file) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route('admin.artikel.upload') }}',
                    data: { action: 'delete', jenis: 'original', module: 'artikel' },
                    success: function(data) {
                        console.log('Thumbnail dihapus');
                        $('#img-original').val('');
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
                /*var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;*/
            });

            this.on("thumbnail", function(file, dataUrl) {
                if (file.size > 1000000) {
                    this.removeFile(file);
                    file.rejectSize();
                } else {
                    $("#dropzone1 .dz-preview").addClass("customPosOne");
                    $('#dropzone1 .dz-image').addClass("customPosImgOne");
                    $('#dropzone1 .dz-preview .dz-image').css({"width": "100%"});
                    $('#dropzone1 .dz-preview .dz-image img').css({"width": "100%"});

                    file.acceptDimensions();
                }
            });

            this.on('sending', function(file, xhr, formData) {
                $.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });
                
                formData.append('kuis_id', '0');
                formData.append('jenis', 'original');
                formData.append('module', 'artikel');
                formData.append('action', 'upload');
            });

            this.on("success", function(file, response) {
                var json = JSON.parse(response);

                //$('.dz-remove').hide();
                $("#dropzone1 .dz-preview").addClass("customPosOne");
                $('#dropzone1 .dz-image').addClass("customPosImgOne");
                $('#dropzone1 .dz-preview .dz-image').css({"width": "100%"});
                $('#dropzone1 .dz-preview .dz-image img').css({"width": "100%"});

                $('#img-original').val(json.image);

                $.preloader.stop();
            });
        },
        accept: function(file, done) {
            file.rejectSize = function() {
                bootbox.alert({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'>Ukuran file melebihi <span class='text-danger'>1 MB</span>. <br />Silahkan upload ulang gambar.</p>",
                    size: 'small'
                });
            };

            file.acceptDimensions = done;
        }
    });
</script>

@endpush

@endsection
