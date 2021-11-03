@extends('layouts.master')
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/plugins/color/src/palette-color-picker.css') }}">
<style>
    .ui-selectmenu-button.ui-button { 
        width: 100% !important;
        display: block;
        height: calc(1.5em + 1.3rem + 2px);
        display: block;
        width: 100%;
        height: calc(1.5em + 1.3rem + 2px);
        padding: .65rem 1rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #3f4254;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #e4e6ef;
        border-radius: .675rem;
        -webkit-box-shadow: none;
        box-shadow: none;
        -webkit-transition: border-color .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
    }
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active { background: none !important;  }

    /*.ui-selectmenu-menu .ui-menu.customicons .ui-menu-item-wrapper { padding: 0.5em 0 0.5em 3em; }

    .ui-selectmenu-menu .ui-menu.customicons .ui-menu-item .ui-icon { height: 24px; width: 24px; top: 0.1em; }*/

    .ui-icon.box-red { background: url({{ asset('assets/media/bg/box-red.png') }}) 0 0 no-repeat; }
    .ui-icon.box-yellow { background: url({{ asset('assets/media/bg/box-yellow.png') }}) 0 0 no-repeat; }
    .ui-icon.box-green { background: url({{ asset('assets/media/bg/box-green.png') }}) 0 0 no-repeat; }
    .ui-icon.box-blue { background: url({{ asset('assets/media/bg/box-blue.png') }}) 0 0 no-repeat; }

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

                @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                </div>
                @endif
                
                <form class="form" method="POST" action="{{ route('admin.kuis.update', $kuis->id) }}" id="my-awesome-dropzone" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">Ubah Kuesioner : {{ $kuis->title }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Judul Kuisioner</label>
                                <input type="text" class="form-control" value="{{ $kuis->title }}" id="title" name="title" required oninvalid="this.setCustomValidity('Judul harus diisi')" oninput="setCustomValidity('')">
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <select class="form-control" name="gender" required oninvalid="this.setCustomValidity('Gender harus dipilih')" oninput="setCustomValidity('')">
                                    @foreach ($gender as $key => $val)
                                    <option value="{{ $key }}" {{ (isset($kuis->gender) && ($kuis->gender == $key)) ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi Singkat</label>
                                <textarea class="tinymce-editor deskripsi" id="deskripsi" name="deskrip">{{ $kuis->deskripsi }}</textarea>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Thumbnail</label>
                                        <input id="img-thumb" name="thumbnail" type="hidden" value="{{ $kuis->thumbnail }}">
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
                                        <label>Gambar Intro</label>
                                        <input id="img-original" name="original" type="hidden" value="{{ $kuis->image }}">
                                        <div id="dropzone1" class="dropzone alert dropzone-default dropzone-success">
                                            <div class="dropzone-msg dz-message">
                                                <h3 class="dropzone-msg-title" style="margin: 5px 5px;">Tarik gambar ke kotak ini atau klik untuk proses upload</h3>
                                                <span class="dropzone-msg-desc">
                                                    <small>
                                                        <br /><span class="text-danger font-weight-bolder"><u>KETENTUAN UPLOAD GAMBAR : </u></span><br />
                                                        Gambar hanya dalam format <span class="text-danger font-weight-bolder">jpg, jpeg, atau png</span><br />
                                                        Max ukuran gambar <span class="text-danger font-weight-bolder">harus kurang dari 1MB</span>
                                                    </small>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header text-center">
                            <h3 class="card-title text-center">Summary Bobot Penilaian Kuisioner</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nilai Maksimal</label>
                                <input type="number" class="form-control" value="{{ $kuis->max_point }}" id="maxpoint" name="max_point" required oninvalid="this.setCustomValidity('Nilai maksimal penilaian harus diisi')" oninput="setCustomValidity('')">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card card-custom gutter-b">
                                <div class="card-header card-header-tabs-line">
                                    <div class="card-toolbar">
                                        <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                            @for ($i = 0; $i < 4; $i++)
                                            <li class="nav-item">
                                                <a class="nav-link {{ ($i == 0) ? 'active' : '' }}" data-toggle="tab" href="#kt_tab_pane_{{ $i }}_4">
                                                    <span class="nav-icon"><i class="flaticon2-writing"></i></span>
                                                    <span class="nav-text">Bobot Penilaian {{ $i + 1 }}</span>
                                                </a>
                                            </li>
                                            @endfor
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        @for ($i = 0; $i < 4; $i++)
                                        <div class="tab-pane fade {{ ($i == 0) ? 'show active' : '' }}" id="kt_tab_pane_{{ $i }}_4" role="tabpanel" aria-labelledby="kt_tab_pane_{{ $i }}_4">
                                            <input type="hidden" name="detail_id[{{ $i + 1 }}]" value="{{ (isset($summary[$i]['id'])) ? $summary[$i]['id'] : '' }}">
                                            <input type="hidden" class="form-control" name="rating[{{ $i + 1 }}]" value="">
                                            <div class="row mb-5">
                                                <div class="col-lg-12">
                                                    <button type="button" class="btn btn-light-danger float-right kosong" data-id="{{ $i + 1 }}"><i class="flaticon-delete"></i> Hapus bobot penilaian {{ $i + 1 }}</button>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Kondisi</label>
                                                    <select class="form-control" name="kondisi[{{ $i + 1 }}]" id="kondisi_{{ $i + 1 }}">
                                                        @foreach ($kondisi as $key => $row)
                                                        <option value="{{ $key }}" {{ (isset($summary[$i]['kondisi']) && ($summary[$i]['kondisi'] == $key)) ? 'selected' : '' }}>{{ $row }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Nilai</label>
                                                    <input type="text" class="form-control" value="{{ (isset($summary[$i]['nilai'])) ? $summary[$i]['nilai'] : '' }}" name="nilai[{{ $i + 1 }}]" id="nilai_{{ $i + 1 }}">
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Label</label>
                                                    <input type="text" class="form-control" value="{{ (isset($summary[$i]['label'])) ? $summary[$i]['label'] : '' }}" name="label[{{ $i + 1 }}]" id="label_{{ $i + 1 }}">
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Warna Background Rating</label>
                                                    <br />
                                                    <select class="form-control background-dropdown" name="background[{{ $i + 1 }}]" id="background_{{ $i + 1 }}">
                                                        @foreach ($color as $key => $row)
                                                        <option value="{{ $key }}" data-class="{{ $row['class'] }}" {{ (isset($summary[$i]['rating_color']) && ($summary[$i]['rating_color'] == $key )) ? 'selected' : '' }}>{{ $row['title'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Deskripsi Penilaian</label>
                                                <textarea class="form-control deskripsicustom" id="deskripsicustom-{{$i + 1}}" rows="10" name="deskripsi[{{ $i + 1 }}]">{{ (isset($summary[$i]['deskripsi'])) ? $summary[$i]['deskripsi'] : '' }}</textarea>
                                                <div class="float-right mt-2" id="character_count-{{ $i + 1 }}">Panjang karakter maksimal : 160</div>
                                            </div>
                                            <div class="form-group">
                                                <label>Template PDF Hasil Penilaian</label>
                                                <textarea class="deskripsi" id="template_{{ $i + 1 }}" name="template[{{ $i + 1 }}]">{{  (isset($summary[$i]['template'])) ? $summary[$i]['template'] : '' }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>* Keyword:</label><br>
                                                <label>[logo_elsimil_200x100] [provinsi_member] [nama_member] [hasil_kuesioner] [id_kuesioner]
                                                        [deskripsi_hasil_kuesioner] [tanggal_widget_kesehatan] [nama_fasilitas_kesehatan] [rekapan_hasil_kuesioner]</label>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>                    
                        </div>
                    </div>

                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col col-md-9 text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="button">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.kuis.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

@push('script')
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
<script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets/plugins/color/src/palette-color-picker.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.deskripsicustom').on('keyup keydown', function() {
            var id = $(this).attr('id');
            var cid = $('#' + id).val();

            var split = id.split('-');

            var max = 160;
            var count = cid.length;
            if (count > max) {
                var last = cid.substring(0, max);
                $('#' + id).val(last);
                $('#character_count-' + split[1]).html('<span class="text-danger font-weight-bolder">Panjang karakter maksimal : ' + max + '</span>');
            } else {
                var used = max - count;
                $('#character_count-' + split[1]).html('Panjang karakter maksimal : <span class="text-danger font-weight-bolder">' + max + '.</span> Sisa karakter : <span class="text-danger font-weight-bolder">' + used + '</span>');
            }
        });

        $('.kosong').on('click', function() {
            var id = $(this).data('id');

            $('#kondisi_' + id + ' option:selected').prop("selected", false);
            $('#nilai_' + id).val('');
            $('#label_' + id).val('');
            //$('#background_' + id + ' option:selected').prop("selected", false);
            $('#deskripsicustom-' + id).val('');
            tinyMCE.get('template_' + id).setContent('');

            $("#background_" + id).iconselectmenu('destroy');
            $("#background_" + id).prop('selectedIndex',0);
            $("#background_" + id).iconselectmenu();
        });
    });
</script>

<script>
    $( function() {
        $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
            _renderItem: function( ul, item ) {
                var li = $( "<li>" ),
                wrapper = $( "<div>", { text: item.label } );

                if ( item.disabled ) {
                    li.addClass( "ui-state-disabled" );
                }

                $( "<span>", {
                    style: item.element.attr( "data-style" ),
                    "class": "ui-icon " + item.element.attr( "data-class" )
                }).appendTo( wrapper );

                return li.append( wrapper ).appendTo( ul );
            }
        });

        $( ".background-dropdown" ).iconselectmenu().iconselectmenu( "menuWidget").addClass( "ui-menu-icons customicons" );
    });
</script>

<script type="text/javascript">
    var maxImageWidth = 500, maxImageHeight = 500;

    Dropzone.autoDiscover = false;
    
    $('#dropzone').dropzone({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('admin.kuis.upload') }}', 
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

            var urlthumb = baseurl + "/uploads/kuesioner/" + "{{ $kuis->thumbnail }}";
            var mockFile = { name: "{{ $kuis->thumbnail }}", size: 12345, type: 'image/jpeg' };
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
                    url: '{{ route('admin.kuis.upload') }}',
                    data: { action: 'delete', jenis: 'thumbnail', module: 'kuesioner' },
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
                formData.append('module', 'kuesioner');
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
        url: '{{ route('admin.kuis.upload') }}', 
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

            var urlimage = baseurl + "/uploads/kuesioner/" + "{{ $kuis->image }}";
            var mockFile = { name: "{{ $kuis->image }}", size: 12345, type: 'image/jpeg' };
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
                    url: '{{ route('admin.kuis.upload') }}',
                    data: { action: 'delete', jenis: 'original', module: 'kuesioner' },
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
                    bootbox.alert({
                        title: 'Perhatian',
                        centerVertical: true,
                        closeButton: false,
                        message: "<p class='text-center'>Ukuran file melebihi <span class='text-danger'>1 MB</span>. <br />Silahkan upload ulang gambar.</p>",
                        size: 'small'
                    });
                    this.removeFile(file);
                } else {
                    $("#dropzone1 .dz-preview").addClass("customPosOne");
                    $('#dropzone1 .dz-image').addClass("customPosImgOne");
                    $('#dropzone1 .dz-preview .dz-image').css({"width": "100%"});
                    $('#dropzone1 .dz-preview .dz-image img').css({"width": "100%"});
                }
            });

            this.on('sending', function(file, xhr, formData) {
                $.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });
                
                formData.append('kuis_id', '0');
                formData.append('jenis', 'original');
                formData.append('module', 'kuesioner');
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
        }
    });
</script>

<!--<script>
function CountCharacters(id) {
    var body = tinymce.get(id).getBody();
    var content = tinymce.trim(body.innerText || body.textContent);
    return content.length;
};
    var editor_config = {
        charLimit : 10,
        height : "200",
        path_absolute : "/",
        selector: "textarea.deskripsicustom",
        branding: false,
        statusbar: false,
        menubar: false,
        setup: function (ed) {
            ed.on('KeyDown', function (e) { 
                console.log($(this).attr('id'));
                var id = $(this).attr('id');
                var max = 160;
                var count = CountCharacters(id);
                if (count >= max) {
                        if(e.keyCode != 8 && e.keyCode != 46)
                          tinymce.dom.Event.cancel(e);
                          document.getElementById("character_count").innerHTML = "Panjang karakter maksimal : 160";

                } else {
                    var used = max - count;
                    document.getElementById("character_count").innerHTML = "Sisa karakter : " + used;
                 }
            });

        }
    };

    tinymce.init(editor_config);
</script>-->

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
