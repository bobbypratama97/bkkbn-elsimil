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

                @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                </div>
                @endif
                
                <div class="card card-custom card-sticky gutter-b example example-compact" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $kuis->title }}</h3>
                        <div class="card-toolbar">
                            <div class="example-tools justify-content-center">
                                <a class="btn btn-primary unclick mr-3">Status : {!! Helper::approval($kuis->apv) !!}</a>
                                <form method="POST" action="{{ route('admin.kuis.apply') }}" class="mr-3">
                                    <input type="hidden" name="cid" value="{{ $kuis->id }}">
                                    <button type="submit" class="btn btn-success">Ajukan Approval</button>
                                </form>
                                <a href="{{ route('admin.kuis.index') }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Thumbnail</label>
                                    <div id="dropzone" class="dropzone alert dropzone-default dropzone-success unclick">
                                        <img src="{{ URL::to('/') }}/uploads/kuesioner/{{ $kuis->thumbnail }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label>Gambar Intro</label>
                                    <div id="dropzone" class="dropzone alert dropzone-default dropzone-success unclick">
                                        <img src="{{ URL::to('/') }}/uploads/kuesioner/{{ $kuis->image }}" style="width:34%;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! $kuis->deskripsi !!}

                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4">

                        @for ($i = 0; $i < count($summary); $i++)
                        <div class="card card-custom gutter-b">
                            <div class="card-body pt-2">
                                <a href="#" class="btn btn-block btn-sm font-weight-bolder text-uppercase py-4 mb-4 mt-4" style="background-color: {{ $summary[$i]['rating_color'] }}">{{ $summary[$i]['label'] }}</a>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Kondisi</td>
                                            <td>Nilai</td>
                                            <td>Rating</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ Helper::kondisiKuis($summary[$i]['kondisi']) }}</td>
                                            <td>{{ $summary[$i]['nilai'] }}</td>
                                            <td>{{ $summary[$i]['rating'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p class="mb-2">Deskripsi:</p>
                                <p class="mb-2">{{ $summary[$i]['deskripsi'] }}</p>

                                <div class="accordion accordion-light accordion-toggle-arrow" id="accordionExample{{$i}}">
                                    <div class="card">
                                        <div class="card-header" id="headingOne{{$i}}">
                                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne{{$i}}" aria-expanded="false">
                                            <i class="flaticon2-file"></i>Template PDF Hasil Penilaian</div>
                                        </div>
                                        <div id="collapseOne{{$i}}" class="collapse" data-parent="#accordionExample{{$i}}" style="">
                                            <div class="card-body">{!! $summary[$i]['template'] !!}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        @endfor

                    </div>

                    <div class="col-xl-8">
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-header h-auto py-4">
                                <div class="card-title">
                                    <i class="flaticon-clipboard mr-3 font-weight-bolder"></i> <h3 class="card-label">Pertanyaan Kuesioner</h3>
                                </div>
                            </div>
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="timeline timeline-3">
                                    <div class="timeline-items">
                                        @foreach ($pertanyaan as $key => $row)
                                        <div class="timeline-item">
                                            <div class="timeline-media" style="background-color: #f3f6f9; border: 2px solid  #f3f6f9 !important;">
                                                <i class="flaticon2-writing font-weight-bold text-dark"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="mr-2">
                                                        <a href="#" class="text-dark font-weight-bolder">{{ $row['caption'] }}</a><br />
                                                        <span class="text-muted ml-2">{!! $row['deskripsi'] !!}</span>
                                                    </div>
                                                </div>
                                                @if ($row['jenis'] == 'widget')
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>Komponen</td>
                                                            <td>Bentuk</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($row['detail'] as $keys => $rows)
                                                        <tr>
                                                            <td>{{ $rows['title'] }}</td>
                                                            <td>{{ $rows['pilihan'] }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif

                                                @if ($row['jenis'] == 'single')
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="mr-2">
                                                        <a href="#" class="text-dark font-weight-bolder">{{ (isset($row['detail']['title'])) ? $row['detail']['title'] : '' }}</a><br />
                                                    </div>
                                                </div>

                                                @foreach ($row['detail']['bobot'] as $keys => $rows)
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="mr-2">
                                                        <a class="btn btn-sm btn-primary unclick mr-3">{{ $rows['label'] }}</a>
                                                    </div>
                                                </div>

                                                @if ($row['detail']['pilihan'] == 'angka')
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>Kondisi</td>
                                                            <td>Nilai</td>
                                                            <td>Bobot</td>
                                                            <td>Rating</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ Helper::kondisiKuis($rows['kondisi']) }}</td>
                                                            <td>{{ $rows['nilai'] }}</td>
                                                            <td>{{ $rows['bobot'] }}</td>
                                                            <td>{{ $rows['rating'] }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @endif

                                                @if ($row['detail']['pilihan'] == 'dropdown' || $row['detail']['pilihan'] == 'radio')
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>Pilihan</td>
                                                            <td>Bobot</td>
                                                            <td>Rating</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $rows['label'] }}</td>
                                                            <td>{{ $rows['bobot'] }}</td>
                                                            <td>{{ $rows['rating'] }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @endif

                                                @if (isset($rows['file']) && !empty($rows['file']))
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>File</td>
                                                            <td>Nilai</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($rows['file'] as $keyz => $rowz)
                                                        <tr>
                                                            <td>{{ $rowz['name'] }}</td>
                                                            <td><a target="_blank" href="{{ URL::to('/') }}/uploads/kuis/{{ $rowz['file'] }}">Lihat File</a></td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif
                                                
                                                @endforeach
                                                @endif

                                                @if ($row['jenis'] == 'combine')
                                                @foreach ($row['detail'] as $keys => $rows)
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="mr-2">
                                                        <a href="#" class="text-dark font-weight-bolder">{{ $rows['title'] }}</a><br />
                                                    </div>
                                                </div>
                                                @endforeach

                                                @if (isset($row['bobot']) && !empty($row['bobot']))
                                                @foreach($row['bobot'] as $keys => $rows)
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>Kondisi</td>
                                                            <td>Label</td>
                                                            <td>Bobot</td>
                                                            <td>Rating</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ Helper::kondisiKuis($rows['kondisi']) }}</td>
                                                            <td>{{ $rows['label'] }}</td>
                                                            <td>{{ $rows['bobot'] }}</td>
                                                            <td>{{ $rows['rating'] }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                @if (isset($rows['file']) && !empty($rows['file']))
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>File</td>
                                                            <td>Nilai</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($rows['file'] as $keyz => $rowz)
                                                        <tr>
                                                            <td>{{ $rowz['name'] }}</td>
                                                            <td><a target="_blank" href="{{ URL::to('/') }}/uploads/kuis/{{ $rowz['file'] }}">Lihat File</a></td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif
                                                @endforeach
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                            <!--end::Body-->
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
