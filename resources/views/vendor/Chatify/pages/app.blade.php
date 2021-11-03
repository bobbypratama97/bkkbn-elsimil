@extends('layouts.master')
@push('css')
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="route" content="{{ $route }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.path') }}" data-user="{{ Auth::user()->id }}">

<script src="{{ asset('js/chatify/font.awesome.min.js') }}"></script>
<script src="{{ asset('js/chatify/autosize.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

<link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>
<link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/chatify/light.mode.css') }}" rel="stylesheet" />
<!--<link href="{{ asset('css/app.css') }}" rel="stylesheet" />-->

<style>
    /* NProgress background */
    #nprogress .bar { background: {{ $messengerColor }} !important; }
    #nprogress .peg { box-shadow: 0 0 10px {{ $messengerColor }}, 0 0 5px {{ $messengerColor }} !important; }
    #nprogress .spinner-icon { border-top-color: {{ $messengerColor }} !important; border-left-color: {{ $messengerColor }} !important; }

    .m-header svg { color: {{ $messengerColor }}; }
    .m-list-active, .m-list-active:hover, .m-list-active:focus { background: {{ $messengerColor }}; }
    .m-list-active b { background: #fff !important; color: {{ $messengerColor }} !important; }
    .messenger-favorites div.avatar { box-shadow: 0px 0px 0px 2px {{ $messengerColor }}; }

    .messenger-list-item td b { background: {{ $messengerColor }}; }
    .messenger-infoView nav a { color: {{ $messengerColor }}; }
    .messenger-infoView-btns a.default { color: {{ $messengerColor }}; }
    .mc-sender p { background: {{ $messengerColor }} !important; }
    .messenger-sendCard button svg { color: {{ $messengerColor }}; }
    .messenger-listView-tabs a, .messenger-listView-tabs a:hover, .messenger-listView-tabs a:focus { color: {{ $messengerColor }}; }
    .messenger-favorites div.avatar { box-shadow: 0px 0px 0px 2px {{ $messengerColor }}; }
    .lastMessageIndicator { color: {{ $messengerColor }} !important; }

    .active-tab { border-bottom: 2px solid {{ $messengerColor }}; }
    .dark-mode-switch { color: {{ $messengerColor }}; }
</style>

@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom gutter-b" style="height:100% !important;">
                    <div class="card-body">

                        <div class="messenger">
                            {{-- ----------------------Users/Groups lists side---------------------- --}}
                            <div class="messenger-listView">
                                <div class="m-header">
                                    <nav>
                                        <a href="#"><i class="far fa-comments"></i> <span class="messenger-headTitle">Chat</span> </a>
                                    </nav>
                                </div>

                                <div class="m-body">

                                   <div class="@if($route == 'user') show @endif messenger-tab app-scroll" data-view="users">
                                        <div class="messenger-favorites app-scroll-thin" style="display:none;"></div>

                                       {{-- {!! view('Chatify::layouts.listItem', ['get' => 'saved','id' => $id])->render() !!} --}}

                                       <div class="listOfContacts" style="width: 100%;height: calc(100% - 200px);"></div>
                                   </div>

                                   <div class="@if($route == 'group') show @endif messenger-tab app-scroll" data-view="groups">
                                        <p style="text-align: center;color:grey;">Soon will be available</p>
                                     </div>

                                   <div class="messenger-tab app-scroll" data-view="search">
                                        <p class="messenger-title">Cari</p>
                                        <div class="search-records">
                                            <p class="message-hint"><span>Ketik untuk mencari...</span></p>
                                        </div>
                                     </div>

                                </div>
                            </div>

                            <div class="messenger-messagingView">

                                {{-- header title [conversation name] amd buttons --}}
                                <div class="m-header m-header-messaging">
                                    <nav>
                                        {{-- header back button, avatar and user name --}}
                                        <div style="display: inline-flex;">
                                            <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                                            <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                                            </div>
                                            <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                                        </div>
                                        {{-- header buttons --}}
                                        <nav class="m-header-right">
                                            {{-- <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a> --}}
                                            {{-- <a href="{{ route('home') }}"><i class="fas fa-home"></i></a> --}}
                                            <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                                        </nav>
                                    </nav>
                                </div>

                                {{-- Internet connection --}}
                                <div class="internet-connection">
                                    <span class="ic-connected">Terhubung</span>
                                    <span class="ic-connecting">Menghubungkan...</span>
                                    <span class="ic-noInternet">Tidak ada koneksi internet</span>
                                </div>

                                {{-- Messaging area --}}
                                <div class="m-body app-scroll">
                                    <div class="messages">
                                        <p class="message-hint" style="margin-top: calc(30% - 126.2px);"><span>Pilih chat untuk memulai percakapan</span></p>
                                    </div>
                                    {{-- Typing indicator --}}
                                    <div class="typing-indicator">
                                        <div class="message-card typing">
                                            <p>
                                                <span class="typing-dots">
                                                    <span class="dot dot-1"></span>
                                                    <span class="dot dot-2"></span>
                                                    <span class="dot dot-3"></span>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    {{-- Send Message Form --}}
                                    @include('Chatify::layouts.sendForm')
                                </div>
                            </div>

                            {{-- ---------------------- Info side ---------------------- --}}
                            <div class="messenger-infoView app-scroll">
                                {{-- nav actions --}}
                                <nav>
                                    <a href="#"><i class="fas fa-times"></i></a>
                                </nav>
                                {!! view('Chatify::layouts.info')->render() !!}
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
<script>
  // Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;
  var pusher = new Pusher("{{ config('chatify.pusher.key') }}", {
    encrypted: true,
    cluster: "{{ config('chatify.pusher.options.cluster') }}",
    authEndpoint: '{{route("pusher.auth")}}',
    auth: {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }
  });
</script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
<script>
  // Messenger global variable - 0 by default
  messenger = "{{ @$id }}";
</script>
@endpush
