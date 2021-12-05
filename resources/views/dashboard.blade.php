@extends('layouts.master')

@section('content')

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row mt-4">
                    <div class="col-xl-12">
					
                        <div class="card card-custom gutter-b bg-light-primary">
                            <div class="card-body p-5">
                                <div class="text-primary font-weight-bolder mb-3">* Pemberitahuan</div>
                                 
                                 
								
							<div class="list-group">
							  <a href="{{ route('admin.chat.index') }}" class="list-group-item justify-content-between  d-flex  align-items-center">
								 <div><i class="fas fa-fw fa-comment-dots text-success"></i> Chat yang belum anda tanggapi</div>
								<span class="badge badge-primary badge-pill">{{ $resnorespondchat[0]->total }}</span>
							  </a> 
							  
							  <a href="{{ route('admin.repkuis.detail', ['kuesioner' => '', 'tanggal' => '', 'provinsi' => $provcur, 'kabupaten' => $kabcur, 'kecamatan' => $keccur, 'kelurahan' => '', 'nik' => '', 'nama' => '', 'gender' => '']) }}" class="list-group-item d-flex justify-content-between align-items-center">
								 <div><i class="fas fa-fw fa-notes-medical text-success"></i> Kuesioner yang belum anda komentari</div>
								<span class="badge badge-primary  badge-pill">{{ $resnorespondkuis[0]->total }}</span>
							  </a> 
							   
							  
							</div>
							<br>
							
								<div class="text-primary font-weight-bolder mb-3">* Daerah Anda</div>

							<div class="list-group">
							  
							  
							  <a href="{{ route('admin.chat.index') }}" class="list-group-item d-flex justify-content-between align-items-center">
							 <div><i class="fas fa-fw fa-comment-dots text-danger"></i> Chat yang belum ditanggapi.</div>
								<span class="badge badge-primary  badge-pill">{{ $resallnorespchat[0]->total }}</span>
							  </a> 
							  
							  
							  
							  <a href="{{ route('admin.repkuis.detail', ['kuesioner' => '', 'tanggal' => '', 'provinsi' => $provcur, 'kabupaten' => $kabcur, 'kecamatan' => $keccur, 'kelurahan' => '', 'nik' => '', 'nama' => '', 'gender' => '']) }}" class="list-group-item d-flex justify-content-between align-items-center">
								 <div><i class="fas fa-fw fa-notes-medical text-danger"></i> Kuesioner yang belum dikomentari.</div>
								<span class="badge badge-primary  badge-pill">{{ $resallnorespondkuis[0]->total }}</span>
							  </a> 
							  
							  
							  <a href="{{ route('admin.member.index', ['s' => 'nh']) }}" class="list-group-item d-flex justify-content-between align-items-center">
							 <div><i class="fas fa-fw fa-user-clock text-danger"></i> Catin belum punya ptgs. pendamping. </div>
								<span class="badge badge-primary  badge-pill">{{ $resallunmap[0]->total }}/{{ number_format($members['kecamatan']['count'], 0, ',', '.') }}</span>
							  </a> 
							  
							  
							  
							</div>
							
							
                            </div>
							
                        </div>
						
						
						
						
						
						
						
						
						
						
						
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-xl-3">
                        <!--begin::Stats Widget 13-->
                        <a href="#" class="card card-custom bg-danger bg-hover-state-danger card-stretch gutter-b unclick">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="font-weight-bolder text-inverse-danger font-size-h1" style="font-size:4rem!important;">{{ number_format($members['total']['count'], 0, ',', '.') }}</div>
                                <div class="text-inverse-danger font-weight-bolder font-size-h5 mb-2">{{ $members['total']['label'] }}</div>
                                <div class="font-weight-bold text-inverse-danger font-size-xs">{{ $members['total']['text'] }}</div>
                            </div>
                            <!--end::Body-->
                        </a>
                        <!--end::Stats Widget 13-->
                    </div>
                    <div class="col-6 col-xl-3">
                        <!--begin::Stats Widget 14-->
                        <a href="#" class="card card-custom bg-primary bg-hover-state-primary card-stretch gutter-b unclick">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="font-weight-bolder text-inverse-primary font-size-h1" style="font-size:4rem!important;">{{ number_format($members['provinsi']['count'], 0, ',', '.') }}</div>
                                <div class="text-inverse-primary font-weight-bolder font-size-h5 mb-2">{{ $members['provinsi']['label'] }}</div>
                                <div class="font-weight-bold text-inverse-primary font-size-xs">{{ $members['provinsi']['text'] }}</div>
                            </div>
                            <!--end::Body-->
                        </a>
                        <!--end::Stats Widget 14-->
                    </div>
                    <div class="col-6 col-xl-3">
                        <!--begin::Stats Widget 15-->
                        <a href="#" class="card card-custom bg-primary bg-hover-state-success card-stretch gutter-b unclick">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="font-weight-bolder text-inverse-primary font-size-h1" style="font-size:4rem!important;">{{ number_format($members['kabupaten']['count'], 0, ',', '.') }}</div>
                                <div class="text-inverse-success font-weight-bolder font-size-h5 mb-2">{{ $members['kabupaten']['label'] }}</div>
                                <div class="font-weight-bold text-inverse-success font-size-xs">{{ $members['kabupaten']['text'] }}</div>
                            </div>
                            <!--end::Body-->
                        </a>
                        <!--end::Stats Widget 15-->
                    </div>
                    <div class="col-6 col-xl-3">
                        <!--begin::Stats Widget 15-->
                        <a href="#" class="card card-custom bg-primary bg-hover-state-success card-stretch gutter-b unclick">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="font-weight-bolder text-inverse-primary font-size-h1" style="font-size:4rem!important;">{{ number_format($members['kecamatan']['count'], 0, ',', '.') }}</div>
                                <div class="text-inverse-success font-weight-bolder font-size-h5 mb-2">{{ $members['kecamatan']['label'] }}</div>
                                <div class="font-weight-bold text-inverse-success font-size-xs">{{ $members['kecamatan']['text'] }}</div>
                            </div>
                            <!--end::Body-->
                        </a>
                        <!--end::Stats Widget 15-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-shadowless gutter-b">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title font-weight-bolder">
                                    <div class="card-label">
                                        Catin
                                        <div class="font-size-sm text-muted mt-2">
                                            <button type="button" class="btn btn-success btn-block unclick font-weight-bolder" id="jumlahMember">0 catin terdaftar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <canvas id="genderChart" width="100%" height="50%"></canvas>
                                    <div class="text-center font-weight-bolder" id="genderNoData"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-shadowless gutter-b">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title font-weight-bolder">
                                    <div class="card-label">Usia</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <canvas id="umurChart" width="100%" height="50%"></canvas>
                                    <div class="text-center font-weight-bolder" id="umurNoData"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--<div class="row">
                    <div class="col-xl-2">
                        <div class="d-flex align-items-center bg-info rounded p-0 gutter-b" style="min-height: 275px;">
                            <!--begin::Body-->
                            <div class="card-body">
                                <span class="svg-icon svg-icon-4x svg-icon-white ml-n2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                                            <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="card-title font-weight-bolder text-white font-size-h1 mb-0 mt-2 d-block">{{ number_format($chatalloc[0]->total, 0, ',', '.') }}</span>
                                <span class="font-weight-bold text-white font-size-sm">Chat Belum Dialokasikan</span>
                                <a href="{{ route('admin.chat.index') }}" class="btn btn-block btn-white mt-5">Lihat Chat</a>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="d-flex align-items-center bg-info rounded p-0 gutter-b" style="min-height: 275px;">
                            <!--begin::Body-->
                            <div class="card-body">
                                <span class="svg-icon svg-icon-4x svg-icon-white ml-n2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                                            <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="card-title font-weight-bolder text-white font-size-h1 mb-0 mt-2 d-block">{{ number_format($chat[0]->total, 0, ',', '.') }}</span>
                                <span class="font-weight-bold text-white font-size-sm">Chat Belum Direspon</span>
                                <a href="{{ route('admin.chat.index') }}" class="btn btn-block btn-white mt-5">Lihat Chat</a>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="d-flex align-items-center bg-success rounded p-0 gutter-b" style="min-height: 275px;">
                            <!--begin::Body-->
                            <div class="card-body">
                                <span class="svg-icon svg-icon-4x svg-icon-white ml-n2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="card-title font-weight-bolder text-white font-size-h1 mb-0 mt-2 d-block">{{ number_format($review[0]->total, 0, ',', '.') }}</span>
                                <span class="font-weight-bold text-white font-size-sm">Review Belum Dialokasikan</span>
                                <a href="{{ route('admin.repkuis.detail', ['kuesioner' => '', 'tanggal' => '', 'provinsi' => '', 'kabupaten' => '', 'kecamatan' => '', 'kelurahan' => '', 'nik' => '', 'nama' => '', 'gender' => '']) }}" class="btn btn-block btn-white mt-5">Lihat Review</a>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="d-flex align-items-center bg-success rounded p-0 gutter-b" style="min-height: 275px;">
                            <!--begin::Body-->
                            <div class="card-body">
                                <span class="svg-icon svg-icon-4x svg-icon-white ml-n2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="card-title font-weight-bolder text-white font-size-h1 mb-0 mt-2 d-block">{{ number_format($review[0]->total, 0, ',', '.') }}</span>
                                <span class="font-weight-bold text-white font-size-sm">Review Belum Direspon</span>
                                <a href="{{ route('admin.repkuis.detail', ['kuesioner' => '', 'tanggal' => '', 'provinsi' => '', 'kabupaten' => '', 'kecamatan' => '', 'kelurahan' => '', 'nik' => '', 'nama' => '', 'gender' => '']) }}" class="btn btn-block btn-white mt-5">Lihat Review</a>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="d-flex align-items-center bg-warning rounded p-0 gutter-b" style="min-height: 275px;">
                            <!--begin::Body-->
                            <div class="card-body">
                                <span class="svg-icon svg-icon-4x svg-icon-white ml-n2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="card-title font-weight-bolder text-white font-size-h1 mb-0 mt-2 d-block">{{ number_format($member[0]->total, 0, ',', '.') }} / {{ number_format($membertotal[0]->total, 0, ',', '.') }}</span>
                                <span class="font-weight-bold text-white font-size-sm">Catin Terbaru</span>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="d-flex align-items-center bg-danger rounded p-0 gutter-b" style="min-height: 275px;">
                            <!--begin::Body-->
                            <div class="card-body">
                                <span class="svg-icon svg-icon-4x svg-icon-white ml-n2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                            <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="card-title font-weight-bolder text-white font-size-sm mb-0 mt-5 d-block">Kuesioner Terbaru</span>
                                <span class="font-weight-bold text-white font-size-h5">{{ $kuis[0]->title }}</span>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                </div>--}}

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-shadowless gutter-b">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title font-weight-bolder">
                                    <div class="card-label" id="kuistitle-0">
                                        Kuesioner
                                        <div class="font-size-sm text-muted mt-2">Peserta Kuesioner : </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <canvas id="kuis-0" width="100%" height="50%"></canvas>
                                    <div class="text-center font-weight-bolder" id="kuisNoData-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-shadowless gutter-b">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title font-weight-bolder">
                                    <div class="card-label" id="kuistitle-1">
                                        Kuesioner
                                        <div class="font-size-sm text-muted mt-2">Peserta Kuesioner : </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <canvas id="kuis-1" width="100%" height="50%"></canvas>
                                    <div class="text-center font-weight-bolder" id="kuisNoData-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-shadowless gutter-b">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title font-weight-bolder">
                                    <div class="card-label">
                                        10 Lokasi Tertinggi Catin Peserta Kuesioner
                                        <div class="font-size-sm text-muted mt-2">10 daerah catin yang mengikuti kuesioner paling banyak</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <canvas id="topChart" width="100%" height="70%"></canvas>
                                    <div class="text-center font-weight-bolder" id="topNoData"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-shadowless gutter-b">
                            <div class="card-header border-0 pt-5">
                                <div class="card-title font-weight-bolder">
                                    <div class="card-label">
                                        10 Lokasi Terendah Catin Peserta Kuesioner
                                        <div class="font-size-sm text-muted mt-2">10 daerah catin yang mengikuti kuesioner paling sedikit</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <canvas id="bottomChart" width="100%" height="70%"></canvas>
                                    <div class="text-center font-weight-bolder" id="bottomNoData"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Row-->
                <div class="card card-custom card-shadowless gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Hasil Kuesioner Terbaru</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Daftar hasil kuesioner terbaru yang diisi oleh catin</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0 pb-3">
                        <div class="tab-content">
                            <!--begin::Table-->
                            <div class="table-responsive">
                                <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                    <thead>
                                        <tr class="text-left text-uppercase">
                                            <th style="min-width: 250px" class="pl-7">
                                                <span class="text-dark-75">Kuesioner</span>
                                            </th>
                                            <th style="min-width: 100px">Nama Catin</th>
                                            <th style="min-width: 100px">Gender</th>
                                            <th style="min-width: 80px">Lokasi</th>
                                            <th style="min-width: 100px">Hasil</th>
                                            <th style="min-width: 100px">Diulas ?</th>
                                            <th style="min-width: 80px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($resp))
                                        @foreach ($resp as $key => $row)
                                        <tr>
                                            <td>
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $row->kuis_title }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark-75 d-block font-size-lg">{{ $row->name }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark-75 d-block font-size-lg">{!! Helper::statusGender($row->gender) !!}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark-75 d-block font-size-lg">{{ $row->provinsi }}, {{ $row->kabupaten }}, {{ $row->kecamatan }}, {{ $row->kelurahan }}</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn font-size-sm unclick" style="background-color: {{ $row->rating_color }}"><span class="font-weight-bolder text-white">{{ $row->label }}</span></button>
                                            </td>
                                            <td>
                                                 <span class="text-dark-75 d-block font-size-lg">{{ (isset($row->komentar) && !empty($row->komentar)) ? 'Sudah' : 'Belum' }}</span>
                                            </td>
                                            <td class="pr-0 text-right">
                                                <form method="POST" action="{{ route('admin.repkuis.details') }}">
                                                    <input type="hidden" name="cu" value="{{ url()->full() }}">
                                                    <input type="hidden" name="cid" value="{{ $row->id }}">
                                                    <button type="submit" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Detail Hasil Kuis"><i class="flaticon2-writing"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada catin yang mengikuti kuesioner</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Advance Table Widget 4-->
                <!--end::Dashboard-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<script>
    $(document).ready(function() {
        $.ajax({
            type: 'POST',
            url: '{{ route('admin.dashboard.gender') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                if (data.count == '0') {
                    $('#genderChart').hide();
                    $('#genderNoData').show();
                    $('#jumlahMember').html(data.count + ' catin terdaftar');
                    $('#genderNoData').html('Belum ada data');
                } else {
                    $('#jumlahMember').html(data.count + ' catin terdaftar');
                    $('#genderNoData').hide();
                    $('#genderNoData').html('');
                    $('#genderChart').show();

                    var coloR = [];
                    var dynamicColors = function() {
                        var r = Math.floor((Math.random() * 130) + 100);
                        var g = Math.floor((Math.random() * 130) + 100);
                        var b = Math.floor((Math.random() * 130) + 100);
                        return "rgb(" + r + "," + g + "," + b + ")";
                    };

                    for (var i in data.data.data) {
                        coloR.push(dynamicColors());
                    }

                    var ctxs = document.getElementById('genderChart').getContext('2d');
                    var genderChart = new Chart(ctxs, {
                        type: 'pie',
                        data: {
                            labels: data.data.label,
                            datasets: [{
                                label: 'Jumlah Member',
                                data: data.data.data,
                                backgroundColor: coloR
                            }],
                        },
                        options: {
                            responsive: true,
                            showAllTooltips: true,
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItems, data) {  
                                        return data.labels[tooltipItems.index];
                                    }
                                }
                            },
                            legend: {
                                position: 'right'
                            }
                        }
                    });
                }
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('admin.dashboard.umur') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                if (data.count == '0') {
                    $('#umurChart').hide();
                    $('#umurNoData').show();
                    $('#genderNoData').html('Belum ada data');
                } else {
                    $('#umurNoData').hide();
                    $('#umurNoData').html('');
                    $('#umurChart').show();

                    var coloR = [];
                    var dynamicColors = function() {
                        var r = Math.floor((Math.random() * 130) + 100);
                        var g = Math.floor((Math.random() * 130) + 100);
                        var b = Math.floor((Math.random() * 130) + 100);
                        return "rgb(" + r + "," + g + "," + b + ")";
                    };

                    for (var i in data.data.data) {
                        coloR.push(dynamicColors());
                    }

                    var ctxs = document.getElementById('umurChart').getContext('2d');
                    var umurChart = new Chart(ctxs, {
                        type: 'pie',
                        data: {
                            labels: data.data.label,
                            datasets: [{
                                label: 'Usia',
                                data: data.data.data,
                                backgroundColor: coloR
                            }],
                        },
                        options: {
                            responsive: true,
                            showAllTooltips: true,
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItems, data) {  
                                        return data.labels[tooltipItems.index];
                                    }
                                }
                            },
                            legend: {
                                position: 'right'
                            }
                        }
                    });
                }
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('admin.dashboard.kuis') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                $.each(data.data, function(index, item) {

                    if (item.total == '0') {
                        $('#kuis-' + index).hide();
                        $('#kuisNoData-' + index).html('Belum ada data');

                        $('#kuistitle-' + index).html(item.legend + '<div class="font-size-sm text-muted mt-2">Belum ada catin mengikuti kuesioner</div>');
                    } else {
                        $('#kuistitle-' + index).html(item.legend + '<div class="font-size-sm text-muted mt-2">Hasil dari ' + item.total + ' catin yang mengikuti kuesioner</div>');

                        var ctx = document.getElementById('kuis-' + index).getContext('2d');
                        var myChart = new Chart(ctx, {
                            animation: 'easeInQuad',
                            type: 'pie',
                            data: {
                                labels: item.label,
                                datasets: [{
                                    data: item.value,
                                    backgroundColor: item.color
                                }],
                            },
                            options: {
                                showAllTooltips: true,
                                responsive: true,
                                title: {
                                    display: false
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItems, data) {  
                                            return data.labels[tooltipItems.index];
                                        }
                                    }
                                },
                                legend: {
                                    position: 'right'
                                },
                            }
                        });
                    }

                });
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('admin.dashboard.top') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                if (data.count == '0') {
                    $('#topChart').hide();
                    $('#topNoData').show();
                    $('#topNoData').html('Belum ada data');
                } else {
                    $('#topNoData').hide();
                    $('#topNoData').html('');
                    $('#topChart').show();

                    var coloR = ['#004e00', '#006200', '#007600', '#008900', '#009d00', '#00b100', '#00c400', '#00d800', '#00eb00', '#00ff00'];

                    var ctxs = document.getElementById('topChart').getContext('2d');
                    var umurChart = new Chart(ctxs, {
                        type: 'horizontalBar',
                        data: {
                            labels: data.data.label,
                            datasets: [{
                                label: 'Top Ten',
                                data: data.data.data,
                                backgroundColor: coloR
                            }],
                        },
                        options: {
                            responsive: true,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        min: 0,
                                        max: 100
                                    }
                                }],
                                yAxes: [{
                                    stacked: true
                                }],
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Persentase'
                                }
                            },
                            tooltips: {
                                enabled: false
                            },
                            "hover": {
                                "animationDuration": 0
                            },
                            "animation": {
                                "duration": 1,
                                "onComplete": function() {
                                    var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;
                                    ctx.font = '10px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    this.data.datasets.forEach(function(dataset, i) {
                                        var meta = chartInstance.controller.getDatasetMeta(i);
                                        meta.data.forEach(function(bar, index) {
                                            var data = dataset.data[index] + '%';
                                            if(dataset.data[index] > 0 && dataset.data[index] <= 80){
                                                ctx.fillText(data, bar._model.x + 25, bar._model.y + 5);    
                                            }else if(dataset.data[index] > 80){
                                                // ctx.fillStyle = '#fff';
                                                ctx.fillText(data, bar._model.x - 25, bar._model.y + 5);
                                            }
                                            // ctx.fillStyle = '#000';
                                            // ctx.fillText(data, bar._model.x - 25, bar._model.y + 5);
                                        });
                                    });
                                }
                            },
                            title: {
                                display: false,
                                text: ''
                            },
                        }
                    });
                }
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('admin.dashboard.bottom') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                if (data.count == '0') {
                    $('#bottomChart').hide();
                    $('#bottomNoData').show();
                    $('#bottomNoData').html('Belum ada data');
                } else {
                    $('#bottomNoData').hide();
                    $('#bottomNoData').html('');
                    $('#bottomChart').show();

                    var coloR = ['#ffe5e5', '#ffcccc', '#ffb2b2', '#ff9999', '#ff7f7f', '#ff6666', '#ff4c4c', '#ff3232', '#ff1919', '#ff0000'];

                    var ctxs = document.getElementById('bottomChart').getContext('2d');
                    var umurChart = new Chart(ctxs, {
                        type: 'horizontalBar',
                        data: {
                            labels: data.data.label,
                            datasets: [{
                                label: 'Bottom Ten',
                                data: data.data.data,
                                backgroundColor: coloR
                            }],
                        },
                        options: {
                            responsive: true,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        min: 0,
                                        max: 100
                                    }
                                }],
                                yAxes: [{
                                    stacked: true
                                }],
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Persentase'
                                }
                            },
                            tooltips: {
                                enabled: false
                            },
                            "hover": {
                                "animationDuration": 0
                            },
                            "animation": {
                                "duration": 1,
                                "onComplete": function() {
                                    var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;
                                    ctx.font = '10px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillStyle = '#000';
                                    this.data.datasets.forEach(function(dataset, i) {
                                        var meta = chartInstance.controller.getDatasetMeta(i);
                                        meta.data.forEach(function(bar, index) {
                                            var data = dataset.data[index] + '%';
                                            if(dataset.data[index] > 0 && dataset.data[index] <= 80){
                                                ctx.fillText(data, bar._model.x + 25, bar._model.y + 5);    
                                            }else if(dataset.data[index] > 80){
                                                ctx.fillText(data, bar._model.x - 25, bar._model.y + 5);
                                            }
                                            // ctx.fillText(data, bar._model.x - 25, bar._model.y + 5);
                                        });
                                    });
                                }
                            },
                            title: {
                                display: false,
                                text: ''
                            },
                        }
                    });
                }
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

    });
        
</script>

@endpush

@endsection
