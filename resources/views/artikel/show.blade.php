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
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="mr-3">
                                        <a class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3 unclick">{{ $news->title }}
                                        </a>
                                        <div class="d-flex flex-wrap my-2">
                                            <a class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 unclick">
                                            <i class="flaticon2-list pr-1"></i> {{ $news->parent }}</a>

                                            <a class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 unclick">
                                            <i class="flaticon2-avatar pr-1"></i> {{ $news->nama }}</a>

                                            <a class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 unclick">
                                            <i class="flaticon2-paper-plane pr-1"></i> {{ $news->created_at }}</a>
                                        </div>
                                    </div>
                                    <div class="my-lg-0 my-1">
                                        <a class="btn btn-sm {{ ($news->status == '1') ? 'btn-light-info' : 'btn-light-success' }} font-weight-bolder text-uppercase mr-3 unclick">Status Publikasi : {!! Helper::status($news->status) !!}</a>
                                        <a href="{{ route('admin.artikel.index') }}" class="btn btn-sm btn-danger font-weight-bolder text-uppercase">Kembali ke Daftar Artikel</a>
                                    </div>
                                </div>

                                <div class="separator separator-solid my-7"></div>

                                <img src="{{ URL::to('/') }}/uploads/artikel/ori/{{ $news->gambar }}" class="d-block mx-auto" alt="Gambar artikel {{ $news->title }}" style="max-width: 100% !important;">

                                <div>
                                    <span class="d-block font-weight-bold mt-10 mb-3">Deskripsi Singkat :</span>
                                    <div class="font-weight-bold text-dark-50 mb-10">
                                        {{ $news->deskripsi }}
                                    </div>

                                    <div class="separator separator-solid my-7"></div>

                                    <span class="d-block font-weight-bold mt-10 mb-3">Isi Artikel :</span>
                                    <div class="font-weight-bold text-dark-50">
                                        {!! $news->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
