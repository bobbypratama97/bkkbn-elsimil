@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/chat/style.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/chat/light.mode.css') }}" rel="stylesheet" />

<style>
    .unclick { pointer-events: none; cursor: default; }

    /* NProgress background */
    #nprogress .bar { background: #2180f3 !important; }
    #nprogress .peg { box-shadow: 0 0 10px #2180f3, 0 0 5px #2180f3 !important; }
    #nprogress .spinner-icon { border-top-color: #2180f3 !important; border-left-color: #2180f3 !important; }

    .m-header svg { color: #2180f3; }

    .m-list-active, .m-list-active:hover, .m-list-active:focus { background: #c9f7f5; }
    .m-list-active b { background: #7e8299 !important; color: #2180f3 !important; }

    .m-list-deactive, .m-list-deactive:hover, .m-list-deactive:focus { background: #f8d7da; }
    .m-list-deactive b { background: #f8d7da !important; color: #f8d7da !important; }

    .messenger-list-item td b { background: #2180f3; }
    .messenger-infoView nav a { color: #2180f3; }
    .messenger-infoView-btns a.default { color: #2180f3; }
    .mc-sender p { background: #2180f3; }
    .messenger-sendCard button svg { color: #2180f3; }
    .messenger-listView-tabs a, .messenger-listView-tabs a:hover, .messenger-listView-tabs a:focus { color: #2180f3; }
    .active-tab { border-bottom: 2px solid #2180f3; }
    .lastMessageIndicator { color: #2180f3 !important; }
    .messenger-favorites div.avatar { box-shadow: 0px 0px 0px 2px #2180f3; }
    .dark-mode-switch { color: #2180f3; }
    .m-list-active .activeStatus { border-color: #2180f3 !important; }

    .nopadding { padding: 0px !important; }
</style>

@endpush

@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Chat-->
            <div class="d-flex flex-row">
                <!--begin::Content-->
                <div class="flex-row-fluid ml-lg-8" id="kt_chat_content">
                    <!--begin::Card-->
                    <div class="card card-custom">
                        <!--begin::Header-->
                        <div class="card-header align-items-center px-4 py-3">
                            <div class="text-left flex-grow-1">
                                <button class="btn btn-clean btn-icon btn-sm btn-icon-md" id="refreshchat" data-toggle="tooltip" data-placement="top" title="Refresh Isi Chat">
                                    <i class="flaticon2-refresh"></i>
                                </button>
                                <!--end::Dropdown Menu-->
                            </div>
                            <div class="text-center flex-grow-1">
                                <div class="text-dark-75 font-weight-bold font-size-h5" id="name">ELSIMIL</div>
                                <div>
                                    <span id="pin"><i class="flaticon-placeholder-3 mr-2"></i></span>
                                    <span class="font-weight-bold text-muted font-size-sm" id="lokasi"> </span>
                                </div>
                            </div>
                            <div class="text-right flex-grow-1">
                                <!--begin::Dropdown Menu-->
                                <button class="btn btn-clean btn-icon btn-sm btn-icon-md" id="history" data-toggle="tooltip" data-placement="top" title="Histori Chat">
                                    <i class="flaticon2-rectangular"></i>
                                </button>
                                <!--end::Dropdown Menu-->
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body">
                            <!--begin::Scroll-->
                            <div class="scroll scroll-pull" data-mobile-height="350">

                                <div id="intro">
                                    <img src="{{ asset('assets/media/logos/logo-new.png') }}" style="margin: 0; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px;">
                                </div>

                                <!--begin::Messages-->
                                <div class="messages" id="chat-list"></div>

                                <!--end::Messages-->
                            </div>
                            <!--end::Scroll-->
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer align-items-center">
                            <!--begin::Compose-->
                            <div>

                                <input type="hidden" name="chat_id" id="chat_id">
                                <input type="hidden" name="member_id" id="member_id">
                                <textarea class="form-control p-2" rows="2" id="message" style="display:none;"></textarea>

                                <textarea class="form-control p-2" rows="2" id="chat-enter" placeholder="Ketik pesan.." style="resize: none !important;"></textarea>
                                <div class="d-flex align-items-center justify-content-between mt-5 mb-10">
                                    <div class="mr-3">
                                        <a href="{{ route('admin.chat.index') }}" class="btn btn-danger btn-md font-weight-bold py-2 px-6">Kembali </a>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6" id="chat-send">Send <i class="flaticon2-paper-plane"></i></button>
                                        
                                    </div>
                                </div>

                            </div>
                            <!--begin::Compose-->
                        </div>
                        <!--end::Footer-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Chat-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>

<!-- Modal-->
<div class="modal fade" id="exampleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">History Chat</h5>
            </div>
            <div class="modal-body">
                <div data-scroll="true" data-height="300">
                    <div class="messages" id="chat-lists"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
<script src="{{ asset('assets/js/pages/custom/chat/chat.js') }}"></script>
<!--<script src="{{ asset('assets/plugins/chat/code.js') }}"></script>-->
<script>
  // Messenger global variable - 0 by default
  messenger = "0";
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#refreshchat').hide();
        $('#pin').hide();
        $('#lokasi').hide();
        $('#history').hide();
        $('#chat-list').hide();
        $('#chat-enter').prop('disabled', true);
        $('.chat-send').addClass("unclick");

        var id = "{{ $id }}";
        var member = $(this).data('member');
        var user = $(this).data('user');
        var lokasi = $(this).data('lokasi');

        $.preloader.start({
            modal:true,
            src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
        });

        $.ajax({
            url: '{{ route('admin.chat.detail') }}',
            type: 'POST',
            data: {id : id, '_token': "{{ csrf_token() }}"},
            dataType: 'json',
            success: function( data ) {
                $.preloader.stop();

                $('#intro').hide();
                $('#name').html(data.member.name);
                $('#pin').show();
                $('#lokasi').show();
                $('#lokasi').html(data.member.lokasi);

                $('#refreshchat').show();
                $('#refreshchat').attr("data-user", id);
                $('#history').show();
                $('#history').attr("data-user", id);

                $('#chat-list').show();
                $('#chat-list').html(data.detail);

                $('#chat_id').val(id);
                $('#member_id').val(data.member.id);

                $('#message').val('');
                $('#chat-enter').val('');

                $('.messenger-list-item').removeClass("m-list-active");
                $('#memberchat-' + id).addClass('m-list-active');

                if (data.current == data.user) {
                    $('#chat-enter').prop('disabled', false);
                    $('.chat-send').removeClass("unclick");
                } else {
                    if (data.locked == '0') {
                        $('#chat-enter').prop('disabled', false);
                        $('.chat-send').removeClass("unclick");
                    }
                }

                /*$.ajax({
                    url: '{{ route('admin.chat.active') }}',
                    type: 'POST',
                    data: {id : id, '_token': "{{ csrf_token() }}"},
                    dataType: 'json',
                    success: function(data) {}
                });*/
            }
        });

        $("#chat-enter").on('keydown', function (e) {
            $('#message').val($(this).val());

            if (e.key === 'Enter' || e.keyCode === 13) {
                var chat = $('#chat_id').val();
                var member = $('#member_id').val();
                var message = $('#message').val();

                $(this).val('');

                $.ajax({
                    url: '{{ route('admin.chat.send') }}',
                    type: 'POST',
                    data: {chatid: chat, member: member, message: message, '_token': "{{ csrf_token() }}"},
                    dataType: 'json',
                    success: function( data ) {
                    }
                });
            }
        });

        $('#chat-send').on('click', function() {
            var chat = $('#chat_id').val();
            var member = $('#member_id').val();
            var message = $('#message').val();

            $('#chat-enter').val('');

            $.ajax({
                url: '{{ route('admin.chat.send') }}',
                type: 'POST',
                data: { chatid: chat, member: member, message: message, '_token': "{{ csrf_token() }}" },
                dataType: 'json',
                success: function( data ) {}
            });
        });

        $('#refreshchat').on('click', function() {
            $.preloader.start({
                modal: true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var id = $('#chat_id').val();
            $('#chat-list').html('');

            $.ajax({
                url: '{{ route('admin.chat.detail') }}',
                type: 'POST',
                data: { id: id, '_token': "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(data) {
                    $('#intro').hide();

                    $('#chat-list').show();
                    $('#chat-list').html(data.detail);

                    $('#message').val('');
                    $('#chat-enter').val('');

                    $.preloader.stop();
                }
            });
        });

        $('#history').on('click', function() {
            $.preloader.start({
                modal: true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var id = $('#chat_id').val();

            $.ajax({
                url: '{{ route('admin.chat.history') }}',
                type: 'POST',
                data: { id: id, '_token': "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(data) {
                    $('#chat-lists').html(data.detail);
                    $("#exampleModal").modal('show');

                    $.preloader.stop();
                }
            });
        });

        $('#refreshmember').on('click', function() {
            $.preloader.start({
                modal: true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            $.ajax({
                url: '{{ route('admin.chat.refresh') }}',
                type: 'POST',
                data: { '_token': "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(data) {
                    if (data.count == '0') {
                        $('#listMember').html('<p class="message-hint"><span>List chat kosong</span></p>');
                    } else {
                        $('#listMember').html(data.output);
                    }

                    if (data.chatid != '') {
                        $.ajax({
                            url: '{{ route('admin.chat.detail') }}',
                            type: 'POST',
                            data: {id : data.chatid, '_token': "{{ csrf_token() }}"},
                            dataType: 'json',
                            success: function( datas ) {
                                $('#intro').hide();
                                $('#name').html(data.nama);
                                $('#pin').show();
                                $('#lokasi').show();
                                $('#lokasi').html(data.lokasi);

                                $('#refreshchat').show();
                                $('#refreshchat').attr("data-user", data.chatid);
                                $('#history').show();
                                $('#history').attr("data-user", data.chatid);

                                $('#chat-list').show();
                                $('#chat-list').html(datas.detail);

                                $('#chat_id').val(data.chatid);
                                $('#member_id').val(data.memberid);

                                $('#message').val('');
                                $('#chat-enter').val('');

                                $('#chat-enter').prop('disabled', false);
                                $('#chat-send').removeClass("unclick");

                                $.ajax({
                                    url: '{{ route('admin.chat.active') }}',
                                    type: 'POST',
                                    data: {id : data.memberid, '_token': "{{ csrf_token() }}"},
                                    dataType: 'json',
                                    success: function(data) {}
                                });
                            }
                        });
                    } else {
                        $('#intro').show();

                        $('#name').html('ELSIMIL');
                        $('#pin').val('');
                        $('#pin').hide();
                        $('#lokasi').val('');
                        $('#lokasi').hide();

                        $('#refreshchat').hide();
                        $('#history').hide();

                        $('#chat_id').val('');
                        $('#member_id').val('');

                        $('#chat-list').html('');
                        $('#chat-list').hide();

                        $('#chat-enter').prop('disabled', true);
                        $('#chat-send').addClass("unclick");
                    }

                    $.preloader.stop();
                }
            });
        });

        $('#carimember').on('click', function() {
            var nama = $('#members').val();

            $.preloader.start({
                modal: true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            $.ajax({
                url: '{{ route('admin.chat.search') }}',
                type: 'POST',
                data: { nama: nama, '_token': "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(data) {
                    if (data.count == '0') {
                        $('#listMember').html('<p class="message-hint"><span>List chat kosong</span></p>');
                    } else {
                        $('#listMember').html(data.output);
                    }

                    if (data.chatid != '') {
                        $.ajax({
                            url: '{{ route('admin.chat.detail') }}',
                            type: 'POST',
                            data: {id : data.chatid, '_token': "{{ csrf_token() }}"},
                            dataType: 'json',
                            success: function( datas ) {
                                $('#intro').hide();
                                $('#name').html(data.nama);
                                $('#pin').show();
                                $('#lokasi').show();
                                $('#lokasi').html(data.lokasi);

                                $('#refreshchat').show();
                                $('#refreshchat').attr("data-user", data.chatid);
                                $('#history').show();
                                $('#history').attr("data-user", data.chatid);

                                $('#chat-list').show();
                                $('#chat-list').html(datas.detail);

                                $('#chat_id').val(data.chatid);
                                $('#member_id').val(data.memberid);

                                $('#message').val('');
                                $('#chat-enter').val('');

                                $('#chat-enter').prop('disabled', false);
                                $('#chat-send').removeClass("unclick");

                                $.ajax({
                                    url: '{{ route('admin.chat.active') }}',
                                    type: 'POST',
                                    data: {id : data.memberid, '_token': "{{ csrf_token() }}"},
                                    dataType: 'json',
                                    success: function(data) {}
                                });
                            }
                        });
                    } else {
                        $('#intro').show();

                        $('#name').html('ELSIMIL');
                        $('#pin').val('');
                        $('#pin').hide();
                        $('#lokasi').val('');
                        $('#lokasi').hide();

                        $('#refreshchat').hide();
                        $('#history').hide();

                        $('#chat_id').val('');
                        $('#member_id').val('');

                        $('#chat-list').html('');
                        $('#chat-list').hide();

                        $('#chat-enter').prop('disabled', true);
                        $('#chat-send').addClass("unclick");
                    }

                    $.preloader.stop();
                }
            });
        });

        $('#leave').on('click', function() {
            $.preloader.start({
                modal: true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            $.ajax({
                url: '{{ route('admin.chat.leave') }}',
                type: 'POST',
                data: { '_token': "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(data) {
                    $.preloader.stop();

                    bootbox.dialog({
                        title: 'Perhatian',
                        centerVertical: true,
                        closeButton: false,
                        message: "<p class='text-center'>Anda telah meninggalkan semua chat</p>",
                        buttons: {
                            ok: {
                                label: "OK",
                                className: 'btn-info',
                                callback: function() {
                                    window.location.href = '{{ route('admin.chat.index') }}';
                                }
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush

@endsection