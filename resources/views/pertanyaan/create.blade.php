@extends('layouts.master')
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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

                @if ( Session::has( 'success' ))
                <div class="alert alert-custom alert-success" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon2-telegram-logo"></i>
                    </div>
                    <div class="alert-text"><strong>Perhatian</strong><br />{{ Session::get( 'success' ) }}</div>
                </div>
                @endif

                <form class="form" method="POST" action="{{ route('admin.pertanyaan.store', $kuis->id) }}" id="my-awesome-dropzone" enctype="multipart/form-data">
                    @csrf

                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">Kuisioner : {{ $kuis->title }}</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Deskripsi Singkat Pertanyaan</label>
                                <textarea class="tinymce-editor" id="deskripsi" name="deskripsi"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Pertanyaan</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Jenis Pertanyaan</label>
                                <select class="form-control select2 pertanyaan" name="jenis" id="jenis" required oninvalid="this.setCustomValidity('Jenis pertanyaan harus dipilih')" oninput="setCustomValidity('')" >
                                    <option value="">Pilih</option>
                                    <option value="single">Single Question</option>
                                    <option value="combine">Combined Question</option>
                                    <option value="widget">Widget</option>
                                </select>
                            </div>

                            <div id="jenis-komponen"></div>

                            <div id="widget-komponen"></div>

                            <!--<div class="form-group" id="component"></div>-->

                        </div>

                    </div>

                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-header" id="component-header">
                            <h3 class="card-title">Bobot Penilaian</h3>
                        </div>
                    </div>

                    <div id="component"></div>

                    <div class="card card-custom gutter-b example example-compact">
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2" id="button">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.pertanyaan.index', $kuis->id) }}" class="btn btn-danger">Kembali ke Daftar Pertanyaan</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets/plugins/color/src/palette-color-picker.js') }}"></script>

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
    $(document).on('change', '#jenis', function() {
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
                $('#widget-komponen').html('');

                if (data.header == '1') {
                    $('#component-header').show();
                } else {
                    $('#component-header').hide();
                }

                $('#jenis-komponen').html(data.content);

                if (jenis == 'combine') {
                    $('#component').html(data.bobot);
                } else {
                    $('#component').html('');
                }

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
                $('#component').html(data.content);
                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });

    $(document).on('change', '#pilihan', function() {
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
                $('#widget-komponen').html('');

                if (data.header == '1') {
                    $('#component-header').show();
                } else {
                    $('#component-header').hide();
                }

                if (jenisid == '0') {
                    $('#component').html(data.content);
                } else {
                    $('#component-combine-' + jenisid).html(data.content);
                }
                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });

    $(document).on('change', '#bobot', function() {
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
                $('#widget-komponen').html('');

                if (data.header == '1') {
                    $('#component-header').show();
                } else {
                    $('#component-header').hide();
                }

                if (jenisid == '0') {
                    $('#component').html(data.content);
                } else {
                    $('#component-combine-' + jenisid).html(data.content);
                }
                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });

    $(document).on('change', '#widget', function() {
        $.preloader.start({
            modal:true,
            src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
        });

        var group = $(this).parent().parent().parent().attr('id');

        var widgetid = $(this).val();

        var jenis = $('#jenis').val();
        var jenisid = $(this).data("id");

        //alert('b');

        $.ajax({
            type: "POST",
            url: '{{ route('widget') }}',
            data: { "_token": "{{ csrf_token() }}", "widget": widgetid, "jenis": jenis },
            dataType: "json",
            success: function(data) {
                if (data.header == '1') {
                    $('#component-header').show();
                } else {
                    $('#component-header').hide();
                }

                $('#widget-komponen').html(data.content);

                $.preloader.stop();
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });


    $(document).ready(function() {
        $('#component-header').hide();
        $('#publikasi').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('.pertanyaan').select2({
            allowClear: true,
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
