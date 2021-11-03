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
                                        <a class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3 unclick">{{ $page->title }}
                                        </a>
                                    </div>
                                    <div class="my-lg-0 my-1">
                                        <a class="btn btn-sm {{ ($page->status == '1') ? 'btn-light-info' : 'btn-light-success' }} font-weight-bolder text-uppercase mr-3 unclick">Status Publikasi : {!! Helper::status($page->status) !!}</a>
                                        <a href="{{ route('admin.page.index') }}" class="btn btn-sm btn-danger font-weight-bolder text-uppercase">Kembali ke Daftar Page</a>
                                    </div>
                                </div>

                                <div class="separator separator-solid my-7"></div>

                                <div>
                                    <div class="font-weight-bold text-dark-50 mb-10">
                                        {!! $page->content !!}
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
