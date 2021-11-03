@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
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
                        <h3 class="card-title">Tambah Kategori Artikel</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.notifikasi.store') }}" id="my-awesome-dropzone" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <div class="form-group row">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Thumbnail</label>
                                        <input id="img-thumb" name="thumbnail" type="hidden">
                                        <div id="dropzone" class="dropzone alert dropzone-default dropzone-success">
                                            <div class="dropzone-msg dz-message">
                                                <h3 class="dropzone-msg-title" style="margin: 5px 5px;">Tarik gambar ke kotak ini atau klik untuk proses upload</h3>
                                                <span class="dropzone-msg-desc">
                                                    <small>
                                                        <br /><span class="text-danger font-weight-bolder"><u>KETENTUAN UPLOAD GAMBAR : </u></span><br />
                                                        <ul class="dropzone-ul" style="margin-right: 10px;">
                                                            <li>Gambar hanya dalam format <span class="text-danger font-weight-bolder">jpg, jpeg, atau png</span></li>
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
                                        <label>Judul</label>
                                        <input type="text" class="form-control" id="title" name="title" maxlength="200" required oninvalid="this.setCustomValidity('Judul harus diisi')" oninput="setCustomValidity('')" />
                                    </div>

                                    <div class="form-group">
                                        <label>Isi Notifikasi</label>
                                        <textarea class="form-control" rows="5" id="content" name="content" required oninvalid="this.setCustomValidity('Isi harus diisi')" oninput="setCustomValidity('')" ></textarea>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="button">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.notifikasi.index') }}" class="btn btn-danger">Batal</a>
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
<script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    Dropzone.autoDiscover = false;
    
    $('#dropzone').dropzone({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('admin.notifikasi.upload') }}', 
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
                    url: '{{ route('admin.notifikasi.upload') }}',
                    data: { action: 'delete', jenis: 'thumbnail', module: 'notifikasi' },
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
                if (file.size > 1000000) {
                    this.removeFile(file);
                    file.rejectSize();
                } else {
                    $("#dropzone .dz-preview").addClass("customPos");
                    $('#dropzone .dz-image').addClass("customPosImg");
                    $('#dropzone .dz-preview .dz-image img').css({"width": "100%"});

                    file.acceptDimensions();
                }
            });

            this.on('sending', function(file, xhr, formData) {
                $.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });
                
                formData.append('jenis', 'thumbnail');
                formData.append('action', 'upload');
                formData.append('module', 'notifikasi');
            });

            this.on("success", function(file, response) {
                var json = JSON.parse(response);

                //$('.dz-remove').hide();
                $("#dropzone .dz-preview").addClass("customPos");
                $('#dropzone .dz-image').addClass("customPosImg");
                $('#dropzone .dz-preview .dz-image').css({"width": "100%"});
                $('#dropzone .dz-preview .dz-image img').css({"width": "100%"});

                $('#img-thumb').val(json.image);

                $.preloader.stop();
                
                /*var dialog = bootbox.dialog({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'><strong>" + json.message + "</strong><br /><br />Apakah Anda ingin tetap disini untuk <br /><strong>menambahkan artikel lainnya</strong> atau kembali ke <strong>daftar artikel</strong> ?</p>",
                    buttons: {
                        cancel: {
                            label: "Tetap disini",
                            className: 'btn-danger',
                            callback: function() {
                                window.location.href = '{{ route('admin.kategori.create') }}';
                            }
                        },
                        ok: {
                            label: "Kembali ke Daftar Artikel",
                            className: 'btn-info',
                            callback: function() {
                                window.location.href = '{{ route('admin.kategori.index') }}';
                            }
                        }
                    }
                });*/
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

@endpush

@endsection
