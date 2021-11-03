@extends('layouts.master')

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Kuisioner : {{ $kuis->title }}</h3>
                        <div class="card-toolbar">
                            <div class="example-tools justify-content-center">
                                <a href="{{ route('admin.pertanyaan.index', $kuis->id) }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Deskripsi Singkat Pertanyaan</label>
                            <textarea class="tinymce-editor deskripsi" id="deskripsi" name="deskripsi">{{ $pertanyaan['deskripsi'] }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Pertanyaan</h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Jenis Pertanyaan</label>
                            <select class="form-control select2 pertanyaan" name="jenis" id="jenis" required oninvalid="this.setCustomValidity('Jenis pertanyaan harus dipilih')" oninput="setCustomValidity('')" disabled>
                                <option value="">Pilih</option>
                                <option value="single" {{ $pertanyaan['jenis'] == 'single' ? 'selected' : '' }}>Single Question</option>
                                <option value="combine" {{ $pertanyaan['jenis'] == 'combine' ? 'selected' : '' }}>Combined Question</option>
                                <option value="widget" {{ $pertanyaan['jenis'] == 'widget' ? 'selected' : '' }}>Widget</option>
                            </select>
                        </div>

                        <div id="jenis-komponen"></div>

                        <!--<div class="form-group" id="component"></div>-->

                    </div>

                </div>

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header" id="component-header">
                        <h3 class="card-title">Bobot Penilaian</h3>
                    </div>
                </div>

                <div id="component"></div>

            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#jenis').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var jenis = $('#jenis').val();
            var header = '{{ serialize($pertanyaan) }}';
            var detail = '{{ serialize($detail) }}';
            var bobot = '{{ serialize($bobot) }}';

            $.ajax({
                type: "POST",
                url: '{{ route('jenisedit') }}',
                data: { "_token": "{{ csrf_token() }}", "jenis": jenis, header: header, detail: detail, bobot: bobot, "action": "show" },
                dataType: "json",
                success: function(data) {

                    if (data.header == '1') {
                        $('#component-header').show();
                    } else {
                        $('#component-header').hide();
                    }

                    $('#jenis-komponen').html(data.content);

                    $('.pertanyaan').select2({
                        width: 'resolve',
                        dropdownAutoWidth : true,
                        placeholder: "Pilih",
                        "language": {
                            "noResults": function(){
                                return "Tidak ada data";
                            }
                        },    
                    });

                    if (jenis == 'combine') {
                        $('#component').html(data.bobot);
                    } else {
                        $('#component').html('');
                        var pilihan = '{{ (isset($detail['pilihan'])) ? $detail['pilihan'] : '' }}';
                        var bobot = '{{ (isset($detail['bobot'])) ? $detail['bobot'] : '' }}';
                        var bobotvalue = '{{ serialize($bobot) }}';

                        $.ajax({
                            type: "POST",
                            url: '{{ route('pilihanedit') }}',
                            data: { "_token": "{{ csrf_token() }}", "pilihan": pilihan, "bobot": bobot, "jenis": jenis, bobotvalue: bobotvalue, "action": "show" },
                            dataType: "json",
                            success: function(data) {
                                //if (jenis == 'single') {
                                    $('#component').html(data);
                                //}
                                $.preloader.stop();
                            },
                            failure: function(errMsg) {
                                alert(errMsg);
                            }
                        });
                    }

                    $.preloader.stop();
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });

        }).change();


    });
    
        /*$(document).on('load change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var group = $(this).parent().parent().parent().attr('id');

            var pilihan = $('#' + group).find('select[name="pilihan"]').val();
            var bobot = $('#' + group).find('select[name="have_bobot"]').val();
            var jenis = $('#jenis').val();
            console.log(jenis);
            var jenisid = $(this).data("id");
            console.log(jenisid);

            $.ajax({
                type: "POST",
                url: '{{ route('pilihan') }}',
                data: { "_token": "{{ csrf_token() }}", "pilihan": pilihan, "bobot": bobot, "jenis": jenis },
                dataType: "json",
                success: function(data) {
                    if (jenis == 'single') {
                        $('#component').html(data);
                    //} else {
                    //    $('#component-combine-' + jenisid).html(data);
                    }
                    $.preloader.stop();
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });
        }).change();*/

    /*$(document).on('change', '#jenis', function() {
        $.preloader.start({
            modal:true,
            src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
        });

        var jenis = $('#jenis').val();

        $.ajax({
            type: "POST",
            url: '{{ route('jenis') }}',
            data: { "_token": "{{ csrf_token() }}", "jenis": jenis },
            dataType: "json",
            success: function(data) {
                $('#jenis-komponen').html(data);

                $('.pertanyaan').select2({
                    width: 'resolve',
                    dropdownAutoWidth : true,
                    placeholder: "Pilih",
                    "language": {
                        "noResults": function(){
                            return "Tidak ada data";
                        }
                    },    
                });

                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.ajax({
            type: "POST",
            url: '{{ route('pilihan') }}',
            data: { "_token": "{{ csrf_token() }}", "pilihan": 0, "bobot": 0 },
            dataType: "json",
            success: function(data) {
                $('#component').html(data);
                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    }).change();*/



    /*$(document).on('change', '#pilihan', function() {
        $.preloader.start({
            modal:true,
            src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
        });

        var group = $(this).parent().parent().parent().attr('id');

        var pilihan = $('#' + group).find('select[name="pilihan"]').val();
        var bobot = $('#' + group).find('select[name="have_bobot"]').val();
        var jenis = $('#jenis').val();
        var jenisid = $(this).data("id");

        $.ajax({
            type: "POST",
            url: '{{ route('pilihan') }}',
            data: { "_token": "{{ csrf_token() }}", "pilihan": pilihan, "bobot": bobot, "jenis": jenis },
            dataType: "json",
            success: function(data) {
                if (jenisid == '0') {
                    $('#component').html(data);
                } else {
                    $('#component-combine-' + jenisid).html(data);
                }
                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });*/

    /*$(document).on('change', '#bobot', function() {
        $.preloader.start({
            modal:true,
            src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
        });

        var group = $(this).parent().parent().parent().attr('id');

        var pilihan = $('#' + group).find('select[name="pilihan"]').val();
        var bobot = $('#' + group).find('select[name="have_bobot"]').val();
        var jenis = $('#jenis').val();
        var jenisid = $(this).data("id");

        $.ajax({
            type: "POST",
            url: '{{ route('pilihan') }}',
            data: { "_token": "{{ csrf_token() }}", "pilihan": pilihan, "bobot": bobot, "jenis": jenis },
            dataType: "json",
            success: function(data) {
                if (jenisid == '0') {
                    $('#component').html(data);
                } else {
                    $('#component-combine-' + jenisid).html(data);
                }
                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });*/


    $(document).ready(function() {
        $('#component-header').hide();

        $('.pertanyaan').select2({
            allowClear: true,
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

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
    });
</script>
@endpush

@endsection
