@extends('layouts.master')
@push('css')
<style>
    a.unclick { pointer-events: none; cursor: default; }
    .unclick { pointer-events: none; cursor: default; }
</style>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="card card-custom card-sticky gutter-b example example-compact" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $kuis->title }}</h3>
                        <div class="card-toolbar">
                            <div class="example-tools justify-content-center">
                                <a class="btn btn-success unclick mr-3">Status : {!! Helper::approval($kuis->apv) !!}</a>
                                <a href="{{ route('admin.kuis.index') }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Judul Kuisioner</label>
                            <input type="text" class="form-control" value="{{ $kuis->title }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" class="form-control" value="{!! Helper::statusGender($kuis->gender) !!}" disabled>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi Singkat</label>
                            <textarea class="tinymce-editor deskripsi" disabled>{{ $kuis->deskripsi }}</textarea>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Thumbnail</label>
                                    <div id="dropzone" class="dropzone alert dropzone-default dropzone-success unclick">
                                        <img style="width: 100%;" src="{{ URL::to('/') }}/uploads/kuesioner/{{ $kuis->thumbnail }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label>Gambar Intro</label>
                                    <div id="dropzone" class="dropzone alert dropzone-default dropzone-success unclick">
                                        <img src="{{ URL::to('/') }}/uploads/kuesioner/{{ $kuis->image }}" style="width:100%;">
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
                            <input type="number" class="form-control" value="{{ $kuis->max_point }}" disabled>
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
                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <label>Kondisi</label>
                                                <select class="form-control" name="kondisi[{{ $i + 1 }}]" disabled>
                                                    @foreach ($kondisi as $key => $row)
                                                    <option value="{{ $key }}" {{ (isset($summary[$i]['kondisi']) && ($summary[$i]['kondisi'] == $key)) ? 'selected' : '' }}>{{ $row }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Nilai</label>
                                                <input type="text" class="form-control" value="{{ (isset($summary[$i]['nilai'])) ? $summary[$i]['nilai'] : '' }}" name="nilai[{{ $i + 1 }}]" disabled>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Label</label>
                                                <input type="text" class="form-control" value="{{ (isset($summary[$i]['label'])) ? $summary[$i]['label'] : '' }}" name="label[{{ $i + 1 }}]" disabled>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Warna Background Rating</label>
                                                <br />
                                                <select class="form-control background-dropdown" name="background[{{ $i + 1 }}]" disabled>
                                                    @foreach ($color as $key => $row)
                                                    <option value="{{ $key }}" data-class="{{ $row['class'] }}" {{ (isset($summary[$i]['rating_color']) && ($summary[$i]['rating_color'] == $key )) ? 'selected' : '' }}>{{ $row['title'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Deskripsi Penilaian</label>
                                            <textarea class="form-control deskripsicustom" rows="10" name="deskripsi[{{ $i + 1 }}]" disabled>{{ (isset($summary[$i]['deskripsi'])) ? $summary[$i]['deskripsi'] : '' }}</textarea>
                                            <div class="counter"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Template PDF Hasil Penilaian</label>
                                            <textarea class="tinymce-editor deskripsi" id="template" name="template[{{ $i + 1 }}]" disabled>{{  (isset($summary[$i]['template'])) ? $summary[$i]['template'] : '' }}</textarea>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>                    
                    </div>
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

        $(".deskripsicustom").on('keyup paste', function() {
            var Characters = $("#deskripsicustom").val().replace(/(<([^>]+)>)/ig,"").length;
            $(".counter").text("Characters left: " + (1500 - Characters));
        });
</script>

<script>
    var editor_config = {
        height : "400",
        readonly : 1,
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
