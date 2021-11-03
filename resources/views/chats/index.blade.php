@extends('layouts.master')

@push('css')
<style>
    .chat {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .chat li {
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px dotted #B3A9A9;
    }

    .chat li .chat-body p {
        margin: 0;
        color: #777777;
    }

    .card-body {
        overflow-y: scroll;
        height: 350px;
        overflow: auto;
    }

    ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #F5F5F5;
    }

    ::-webkit-scrollbar {
        width: 12px;
        background-color: #F5F5F5;
    }

    ::-webkit-scrollbar-thumb {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
        background-color: #555;
    }
</style>
@endpush

@section('content')

    <div class="container" id="app">
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pesan Antar Pengguna</h3>
                    </div>

                    <div class="card-body" ref="scrollParent">
                        <dw-messages :messages="messages"></dw-messages>
                    </div>
                    <div class="card-footer">
                        <dw-form
                            v-on:sent="addMessage"
                            :user="{{ Auth::user() }}"
                        ></dw-form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush