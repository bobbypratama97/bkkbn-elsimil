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
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            @if($member->link_token && Auth::user()->roleChild == 3)
                            <input readonly id="copy_link" class="form mr-3 pt-3 form-control" style="width: 500px;white-space: nowrap; text-overflow: ellipsis;overflow: hidden;" value="{{$member->link_token}}"/>
                            <button value="copy" class="btn btn-warning font-weight-bold mr-3 pt-3 text-center" onclick="copyToClipboard('copy_link')">Copy Link</button>
                            @endif
                            @can('access', [\App\Member::class, Auth::user()->role, 'blokir'])
                            @if ($member->is_active != '4')
                            <form method="POST" action="{{ route('admin.member.blokir') }}" class="form mr-3 pt-3">
                                @csrf
                                <input type="hidden" name="cid" value="{{ $member->id }}">
                                <button type="submit" class="btn btn-success font-weight-bold py-3 px-6 mb-2 text-center btn-block">{{ ($member->is_active == '3') ? 'Buka Blokir Catin : ' . $member->name : 'Blokir Catin : ' . $member->name }}</button>
                            </form>
                            @endif
                            @endcan

                            <a href="{{ route('admin.member.index') }}" class="btn btn-danger">Kembali</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title font-weight-bolder">Data Diri Catin</h3>
                            </div>

                            <!--begin::Body-->
                            <div class="card-body pt-4">
                                <!--begin::User-->
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                        <div class="symbol symbol-50 symbol-lg-120">
                                            <img src="{{ $member->gambar }}" alt="image">
                                        </div>
                                        <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                                            <span class="font-size-h3 symbol-label font-weight-boldest">JM</span>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $member->name }}</a>
                                        <div class="text-dark-50 font-weight-bold">{{ Helper::jenisKelamin($member->gender) }}</div>
                                        <div class="text-dark-50 font-weight-bold">Bergabung sejak {{ $member->created_at }}</div>
                                        <div class="mt-2">
                                            <a href="#" class="btn btn-sm btn-primary btn-block font-weight-bold mr-2 py-2 px-3 px-xxl-5 my-1 unclick">{{ Helper::statusUser($member->is_active) }}</a>
                                        </div>
                                    </div>
                                </div>
                                <!--end::User-->
                                <!--begin::Contact-->
                                <div class="pt-8 pb-6">
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-success font-weight-bold mr-2">Kode Profil:</span>
                                        <span class="text-dark">{{ $member->profile_code }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-success font-weight-bold mr-2">NIK KTP:</span>
                                        <span class="text-dark">{!! Helper::decryptNik($member->no_ktp) !!}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-success font-weight-bold mr-2">Phone:</span>
                                        <span class="text-dark">{{ $member->no_telp }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-success font-weight-bold mr-2">Email:</span>
                                        <a href="#" class="text-dark text-hover-primary">{{ $member->email }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-success font-weight-bold mr-2">Tempat / Tgl Lahir:</span>
                                        <span class="text-dark text-right">{{ $member->tempat_lahir }}, {{ (!empty($member->tgl_lahir)) ? Helper::customDateMember($member->tgl_lahir) : '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-success font-weight-bold mr-2">Alamat:</span>
                                        <span class="text-dark text-right">{{ $member->alamat }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-success font-weight-bold mr-2">Lokasi:</span>
                                        <span class="text-dark text-right">{{ $member->kelurahan }}, {{ $member->kecamatan }}, {{ $member->kabupaten }}, {{ $member->provinsi }}</span>
                                    </div>
                                </div>
                                <!--end::Contact-->
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title font-weight-bolder">Pasangan</h3>
                            </div>

                            <div class="card-body pt-4">
                                <!--begin::User-->
                                @if (empty($couple))
                                    <div class="d-flex align-items-center">
                                        <p>Belum ada pasangan yang didaftarkan</p>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                            <div class="symbol symbol-50 symbol-lg-120">
                                                <img src="http://bkkbn.local/assets/media/users/300_7.jpg" alt="image">
                                            </div>
                                            <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                                                <span class="font-size-h3 symbol-label font-weight-boldest">JM</span>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $couple->name }}</a>
                                            <div class="text-dark-50 font-weight-bold">{{ Helper::jenisKelamin($couple->gender) }}</div>
                                            <div class="text-dark-50 font-weight-bold">Bergabung sejak {{ $couple->created_at }}</div>
                                            <div class="mt-2">
                                                <a href="#" class="btn btn-sm btn-primary btn-block font-weight-bold mr-2 py-2 px-3 px-xxl-5 my-1 unclick">{{ Helper::statusUser($couple->is_active) }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::User-->
                                    <!--begin::Contact-->
                                    <div class="pt-8 pb-6">
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-success font-weight-bold mr-2">Kode Profil:</span>
                                            <span class="text-dark">{{ $couple->profile_code }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-success font-weight-bold mr-2">NIK KTP:</span>
                                            <span class="text-dark">{!! Helper::decryptNik($couple->no_ktp) !!}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-success font-weight-bold mr-2">Phone:</span>
                                            <span class="text-dark">{{ $couple->no_telp }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-success font-weight-bold mr-2">Email:</span>
                                            <a href="#" class="text-dark text-hover-primary">{{ $couple->email }}</a>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-success font-weight-bold mr-2">Tempat / Tgl Lahir:</span>
                                            <span class="text-dark text-right">{{ $couple->tempat_lahir }}, {{ (!empty($couple->tgl_lahir)) ? Helper::customDateMember($couple->tgl_lahir) : '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span class="text-success font-weight-bold mr-2">Alamat:</span>
                                            <span class="text-dark text-right">{{ $couple->alamat }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-success font-weight-bold mr-2">Lokasi:</span>
                                            <span class="text-dark text-right">{{ $couple->kelurahan }}, {{ $couple->kecamatan }}, {{ $couple->kabupaten }}, {{ $couple->provinsi }}</span>
                                        </div>
                                    </div>
                                    <!--end::Contact-->
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            @if($is_dampingi)
                            <button class="btn btn-sm btn-warning kelola" id="kelola" width="100%" title="Dampingi catin" data-id="{{ $member->id }}">
                                <i class="flaticon-businesswoman"></i> Dampingi Catin 
                            </button>
                            @else
                            <button disabled class="btn btn-sm btn-warning kelola" id="kelola" width="100%" title="Dampingi catin" data-id="{{ $member->id }}">
                                <i class="flaticon-businesswoman"></i> Dampingi Catin 
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    function copyToClipboard(id) {
        console.log(id)
        document.getElementById(id).select();
        document.execCommand('copy');
    }

    $(document).ready(function() {
        $('#kategori').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#publikasi').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('.kelola').on('click', function () {
            var id = $(this).attr('data-id');

            bootbox.confirm({
                title: 'Perhatian',
                message: "<p class='text-center'>Apakah Anda akan mendampingi catin ini ?</p>",
                centerVertical: true,
                closeButton: false,
                buttons: {
                    confirm: { label: 'Yakin', className: 'btn-success' },
                    cancel: { label: 'Batalkan', className: 'btn-danger' }
                },
                callback: function (result) {
                    if (result == true) {
                        $.preloader.start({
                            modal:true,
                            src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                        });

                        $.ajax({
                            url: '{{ route('admin.member.kelola') }}',
                            type: 'POST',
                            data: {id : id, '_token': "{{ csrf_token() }}"},
                            dataType: 'json',
                            success: function( data ) {
                                $.preloader.stop();

                                if (data.count == '0') {
                                    bootbox.dialog({
                                        title: 'Perhatian',
                                        centerVertical: true,
                                        closeButton: false,
                                        message: "<p class='text-center'>" + data.message + "</p>",
                                        buttons: {
                                            ok: {
                                                label: "OK",
                                                className: 'btn-info',
                                                callback: function() {
                                                    //window.location.href = '{{ route('admin.member.index') }}';
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    bootbox.dialog({
                                        title: 'Perhatian',
                                        centerVertical: true,
                                        closeButton: false,
                                        message: "<p class='text-center'>" + data.message + "</p>",
                                        buttons: {
                                            ok: {
                                                label: "OK",
                                                className: 'btn-info',
                                                callback: function() {
                                                    window.location.href = '{{ route('admin.member.index') }}';
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        })
                    }
                }
            });
        });
    });
</script>
@endpush

@endsection
