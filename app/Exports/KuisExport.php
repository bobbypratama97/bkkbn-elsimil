<?php

namespace App\Exports;

use App\User;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class KuisExport implements FromView {

	private $data;

	public function __construct($data) {
		$this->data = $data;
	}

	use Exportable;

	public function view(): View {
		return view('report.kuesioner', [
			'result' => $this->data
		]);
	}
}