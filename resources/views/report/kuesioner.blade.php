<table>
	<tr>
		<th><strong>Judul Kuesioner</strong></th>
		<th><strong>Kode</strong></th>
		<th><strong>Nama Member</strong></th>
		<th><strong>Jenis Kelamin</strong></th>
		<th><strong>No Telp</strong></th>
		<th><strong>Provinsi</strong></th>
		<th><strong>Kabupaten</strong></th>
		<th><strong>Kecamatan</strong></th>
		<th><strong>Kelurahan</strong></th>
		<th><strong>Nilai</strong></th>
		<th><strong>Label Hasil</strong></th>
		<th><strong>Caption Pertanyaan</strong></th>
		<th><strong>Formula Pertanyaan</strong></th>
		<th><strong>Judul Pertanyaan</strong></th>
		<th><strong>Label Pertanyaan</strong></th>
		<th><strong>Kondisi</strong></th>
		<th><strong>Nilai</strong></th>
		<th><strong>Jawaban Member</strong></th>
		<th><strong>Perhitungan Formula</strong></th>
		<th><strong>Bobot</strong></th>
		<th><strong>Ulasan Petugas KB</strong></th>
		<th><strong>Tanggal Kuesioner</strong></th>
	</tr>
	@foreach ($result as $key => $val)
	<tr>
		<th>{{ $val['kuis_title'] }}</th>
		<th>{{ $val['kuis_code'] }}</th>
		<th>{{ $val['nama'] }}</th>
		<th>{{ $val['gender'] }}</th>
		<th>{{ $val['no_telp'] }}</th>
		<th>{{ $val['provinsi'] }}</th>
		<th>{{ $val['kabupaten'] }}</th>
		<th>{{ $val['kecamatan'] }}</th>
		<th>{{ $val['kelurahan'] }}</th>
		<th>{{ $val['member_kuis_nilai'] }}/{{ $val['kuis_max_nilai'] }}</th>
		<th>{{ $val['label'] }}</th>
		<th>{{ $val['pertanyaan_header_caption'] }}</th>
		<th>{{ $val['pertanyaan_header_formula'] }}</th>
		<th>{{ $val['pertanyaan_detail_title'] }}</th>
		<th>{{ $val['pertanyaan_bobot_label'] }}</th>
		<th>{{ $val['pertanyaan_bobot_kondisi'] }}</th>
		<th>{{ $val['pertanyaan_bobot_nilai'] }}</th>
		<th>{{ $val['member_jawaban'] }}</th>
		<th>{{ $val['formula_value'] }}</th>
		<th>{{ $val['pertanyaan_bobot'] }}</th>
		<th>{{ $val['komentar'] }}</th>
		<th>{{ $val['created_at'] }}</th>
	</tr>
	@endforeach
</table>