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
                            <h3 class="card-label">Tambah Role
                        </div>
                    </div>
                    <div class="card-body">

						<form action="{{ route('admin.role.store') }}" method="POST" role="form" id="my-awesome-dropzone" class="form-horizontal m-t-30">
							@csrf

							<div class="row">

								<div class="col-md-12">
									<div class="form-group">
										<label for="nama" class="font-weight-bold">Nama Role</label>
										<input type="text" id="name" name="name" class="form-control" value="{{ $role->name ?? '' }}" autofocus required>
									</div>

									<div class="form-group">
										<label for="nama" class="font-weight-bold">Deskripsi</label>
										<input type="text" id="deskripsi" name="deskripsi" class="form-control" value="{{ $role->deskripsi ?? '' }}" required>
									</div>

									<div class="form-group">
										<label for="nama" class="font-weight-bold">Status</label>
										<select id="status" name="status" class="form-control" required>
											@foreach ($status as $key => $val)
											<option value="{{ $key }}">{{ $val }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-md-5">
									<div class="form-group">
										<label for="nama" class="font-weight-bold">Akses Menu</label>
										<div id="tree"></div>
									</div>
								</div>
								<div class="col-md-7">
									<div class="form-group">
										<label for="nama" class="font-weight-bold">User Pengakses</label>
										<table id="userlist" class="table table-bordered " cellspacing="0" width="100%">
											<thead>
												<tr>
													<th></th>
													<th>Nama User</th>
													<th width="20%">Tanggal Registrasi</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>

                            <div class="form-row">
                                <a href="{{ route('admin.role.index') }}" class="btn btn-danger mr-auto">Batal</a>
                                <button class="mr-1 btn btn-primary" id="button" type="button">Tambah Role</button>
                            </div>

						</form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@push('css')
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
@endpush
@push('script')
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
@endpush

@push('script')
<script type="text/javascript">
	$(document).ready(function() {
		$('.dataTables_filter input[type="search"]').css({'width':'350px','display':'inline-block'});

		var list = <?php echo json_encode($tree); ?>;
		var tree = simTree({
			el: '#tree',
			data: list,
			check: true,
			linkParent: true,
			open:true
		});

		var table = $('#userlist').DataTable({
			initComplete: function () {
				$('.dataTables_filter input[type="search"]').css({ 'width': '275px', 'display': 'inline-block' });
			},
			"bLengthChange": false,
			"ordering": false,
			"dom": '<"float-right"f>rt<"bottom"i><"float-right"p><"clear">',
            "oLanguage": {
                "sSearch": "Cari nama user : ",
                "oPaginate": {
                    "sFirst": "Hal. Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext": "Berikutnya",
                    "sLast": "Hal. Terakhir"
                }
            },
            "language": {
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 dari _MAX_ data",
                "zeroRecords": "Tidak ada data",
                "sInfoFiltered":   "",
            },
			ajax: {
				url: '{{ route('admin.role.userList') }}',
				processing: true,
				serverSide: true,
				dataType: 'json',
				method: "POST",
				data: function (d) {
					d.role_id = '';
					d._token = "{{ csrf_token() }}";
				}
			},
			'columnDefs': [
				{
					'targets': 0,
					'render': function(data, type, row, meta) {
						if (type === 'display') {
							if (row[3] == 1) {
								data = '<div class="checkb"><input type="checkbox" class="dt-checkboxes" checked><label></label></div>';
							} else {
								data = '<div class="checkb"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
							}
						}
						return data;
					},
					'checkboxes': {
						'selectRow': true,
						'selectAllRender': '<div class="checkb"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				}
			],
			'select': 'multi',
			'order': [[1, 'asc']]
		});

		$('#button').on('click', function() {
			var rolelength = $('.sim-tree-checkbox.checked').length;
			var userselected = table.column(0).checkboxes.selected();

			if ($('#name').val() == '' || $('#deskripsi').val() == '' || $('#status').val() == '') {
				bootbox.alert({
					title: 'Perhatian',
					centerVertical: true,
					closeButton: false,
					message: "Semua data harus diisi",
					size: 'small'
				});
			} else if (rolelength < 1) {
				bootbox.alert({
					title: 'Perhatian',
					centerVertical: true,
					closeButton: false,
					message: "Akses menu belum dipilih",
					size: 'small'
				});
			} else {
                $.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });
                
				var point = [];

				$(".sim-tree-checkbox.checked").each(function() {
					point.push($(this).closest('li').data('id'));
				});

				$('.sim-tree-checkbox.sim-tree-semi').each(function() {
					point.push($(this).closest('li').data('id'));
				});


				var user = [];
				$.each(userselected, function(index, rowId) {
					user.push(rowId);
				});

				$.ajax({
					type: "POST",
					url: '{{ route('admin.role.store') }}',
					data: {
						"_token": "{{ csrf_token() }}", 
						name: $('#name').val(),
						deskripsi: $('#deskripsi').val(),
						status: $('#status').val(),
						module: point,
						user: user
					},
					dataType: "json",
					success: function(data) {
						$.preloader.stop();

						var dialog = bootbox.dialog({
							title: 'Perhatian',
							centerVertical: true,
							closeButton: false,
							message: "<p class='text-center'><strong>" + data.message + "</p>",
							buttons: {
								ok: {
									label: "Kembali ke List Role",
									className: 'btn-info',
									callback: function() {
										window.location.href = '{{ route('admin.role.index') }}';
									}
								}
							}
						});
					}
				});
			}
		});
	});
</script>
@endpush

@endsection
