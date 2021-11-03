@extends('layouts.master')

@push('css')
	<link href="{{ asset('assets/plugins/treeview/simTree.css') }}" rel="stylesheet"/>
@endpush

@push('script')
	<script src="{{ asset('assets/plugins/treeview/simTree.js') }}"></script>
	<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                @if ( Session::has( 'success' ))
                <div class="alert alert-custom alert-success" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon2-telegram-logo"></i>
                    </div>
                    <div class="alert-text"><strong>Perhatian</strong><br />{{ Session::get( 'success' ) }}</div>
                </div>
                @endif

                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Daftar Role
                        </div>
                        <div class="card-toolbar">
                            <a href="{{ route('admin.role.index') }}" class="btn btn-danger font-weight-bolder mr-3">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo6/dist/assets/media/svg/icons/Design/Flatten.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="9" cy="15" r="6" />
                                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                            </span>Kembali</a>

                        </div>
                    </div>
                    <div class="card-body">


						<div class="row">

							<div class="col-md-12">
								<div class="form-group">
									<label for="nama" class="font-weight-bold">Nama Role</label>
									<input type="text" class="form-control" value="{{ $role[0]->name ?? '' }}" disabled>
								</div>

								<div class="form-group">
									<label for="nama" class="font-weight-bold">Deskripsi</label>
									<input type="text" class="form-control" value="{{ $role[0]->deskripsi ?? '' }}" disabled>
								</div>

								<div class="form-group">
									<label for="nama" class="font-weight-bold">Status</label>
									<input type="text" class="form-control" value="{!! Helper::status($role[0]->status) !!}" disabled>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="nama" class="font-weight-bold">Akses Menu</label>
									<div id="tree"></div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="nama" class="font-weight-bold">User Pengakses</label>
									<ul>
										@foreach ($user as $row)
										<li>{{ $row->name }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@push('script')
<script type="text/javascript">
	$(document).ready(function() {
		var list = <?php echo json_encode($tree); ?>;
		var tree = simTree({
			el: '#tree',
			data: list,
			check: true,
			linkParent: true,
			open:true
		});
	});
</script>
@endpush

@endsection
