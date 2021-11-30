<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Mail\UserVerify;
use App\Mail\Approval;
use App\Mail\ApprovalCms;

use Auth;
use DB;

use App\Module;
use App\Configuration;
use App\ProdukHistoriesStatus;
use App\Page;
use App\Faq;

class Helper {

	public static function randomString($length) {
	$abjad = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
	$number = '1234578';
	$characters=$abjad.$number;
	
    $charactersLength = strlen($characters);
    $randomString =$abjad[date('m')]. date('y');
	$loop=$length-strlen($randomString);
	
     for ($i = 0; $i < ($loop); $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    	return $randomString;
    }
	
	public static function separateText($text='') {
		$content = implode(' ', preg_split('#([A-Z][^A-Z]*)#', ucfirst($text), null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY));

		return $content;
	}

    public static function sendMail($data=array()) {
    	$id = $data['id'];
    	$tipe = $data['tipe'];
    	$name = $data['name'];
    	$email = $data['email'];
    	$cc = (isset($data['cc'])) ? $data['cc'] : [];
    	$url = $data['url'];
    	$content = (isset($data['content'])) ? $data['content'] : '';
    	$date = (isset($data['date'])) ? $data['date'] : '';

    	$generate = self::randomString(100);

        $now = strtotime(date("Y-m-d H:i:s", strtotime("now")));
        $expired = strtotime(date("Y-m-d H:i:s", strtotime("+30 minutes")));

        $link = $generate . 'bkkbn_' . $id . '_' . $tipe . '_rq-' . $now . '_lm-' . $expired;
        $link = ($url == 'lgn') ? '' : $url . '/' . base64_encode($link);

        $obj = new \stdClass();
        $obj->name = $name;
        $obj->sender = 'ELSIMIL';
        $obj->link = $link;
        $obj->content = $content;
        $obj->date = $date;

        if ($url == 'vrf') {
        	Mail::to($email)->send(new UserVerify($obj));
        }

        if ($url == 'cpw') {
        	Mail::to($email)->send(new ForgotPassword($obj));
        }

        if ($url == 'apv') {
        	Mail::to($email)->cc($cc)->send(new Approval($obj));
        }

        if ($url == 'lgn') {
        	Mail::to($email)->send(new ApprovalCms($obj));
        }

        return true;
    }

    public static function encryptNik($data) {
    	$front = str_replace('=', '', base64_encode(rand(0, 10000) . strtotime(date('Y-m-d H:i:s')) . 'elsimil'));
    	$end = str_replace('=', '', base64_encode(strtotime(date('Y-m-d H:i:s')) . 'elsimil'));
    	$data = str_replace('=', '', base64_encode($data));
    	$output = $front . '.' . $data . '.' . $end;
    	$output = str_replace('=', '', $output);

    	return $output;
    }

    public static function decryptNik($data) {
    	$output = '-';

    	if (!empty($data) && $data != '-') {
    		$expl = explode('.', $data);
	    	// $output = base64_decode($expl[1]);
			if(isset($expl[1])) $output = base64_decode($expl[1]);
    	}

    	return $output;
    }

    public static function dcNik($data) {
    	$output = '.' . str_replace('=', '', base64_encode($data)) . '.';
    	return $output;
    }

	public static function multi_array_search($array, $search) {
    	$result = array();

    	foreach ($array as $key => $value) {
    		foreach ($search as $k => $v) {
    			if (!isset($value[$k]) || $value[$k] != $v) {
    				continue 2;
    			}
    		}
    		$result[] = $key;
    	}

    	return $result;
	}

    public static function diffDate($start, $end, $condition='') {
    	$start = (empty($start)) ? date('Y-m-d') : $start;
    	$diff = abs(strtotime($end) - strtotime($start));

    	$year = floor($diff / (365 * 60 * 60 * 24));
    	$month = floor(($diff - $year * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    	$day = floor(($diff - $year * 365 * 60 * 60 * 24 - $month * 30 * 60 * 60 * 24)/ (60 * 60 * 24));

    	$year = $year . ' tahun';
    	$month = $month . ' bulan';
    	$day = $day . ' hari';

    	if ($condition == 'y') {
    		$result = $year;
    	} else if ($condition == 'm') {
    		$result = $month;
    	} else if ($condition == 'd') {
    		$result = $day;
    	} else if ($condition == 'ym') {
    		$result = $year . ' ' . $month;
    	} else if ($condition == 'yd') {
    		$result = $year . ' ' . $day;
    	} else if ($condition == 'md') {
    		$result = $month . ' ' . $day;
    	} else if ($condition == 'yd') {
    		$result = $year . ' ' . $day;
    	} else if ($condition == 'ymd') {
    		$result = $year . ' ' . $month . ' ' . $day;
    	} else {
    		$result = $year . ' ' . $month . ' ' . $day;
    	}

    	return $result;
    }

	public static function customDate($date, $format='') {
		if (empty($date)) {
			return '';
		}

		if (empty($format)) {
			return \Carbon\Carbon::parse($date)->isoFormat('D MMM Y HH:mm');
		} else {
			return \Carbon\Carbon::parse($date)->isoFormat($format);
		}
	}

	public static function customDateKuis($date, $format='') {
		if (empty($date)) {
			return '';
		}

		if (empty($format)) {
			return \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y HH:mm');
		} else {
			return \Carbon\Carbon::parse($date)->isoFormat($format);
		}
	}

	public static function customDateMember($date, $format='') {
		if (empty($date)) {
			return '';
		}

		$bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

		$date = explode('-', $date);
		$tanggal = $date[2] . ' ' . $bulan[$date[1]] . ' ' . $date[0];
		return $tanggal;
	}

	public static function customUser($data) {
		return ucwords(strtolower($data));
	}

	public static function rupiah($data) {
		return number_format($data, 0, ".", ".");
	}

	public static function humanFilesize($bytes, $dec = 2) {
		$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}

	public static function createSlug($page='', $title, $id = 0) {
		$slug = Str::slug($title);

		$allSlugs = self::getRelatedSlugs($page, $slug, $id);

		if (! $allSlugs->contains('slug', $slug)) {
			return $slug;
		}

		for ($i = 1; $i <= 10; $i++) {
			$newSlug = $slug.'-'.$i;
			if (! $allSlugs->contains('slug', $newSlug)) {
				return $newSlug;
			}
		}
	}

	public static function getRelatedSlugs($page, $slug, $id = 0) {
		if ($page == 'page') {
			return Page::select('slug')->where('slug', 'like', $slug.'%')->where('id', '<>', $id)->get();
		}
	}

	public static function compare($array1, $array2, $strict = false)
    {
        if (!is_array($array2)) {
            return $array1;
        }

        $result = [];

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value) && count($value) > 0) {
                $recursiveArrayDiff = self::compare($value, $array2[$key], $strict);

                if (count($recursiveArrayDiff) > 0) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            $value1 = $value;
            $value2 = $array2[$key];

            if ($strict ? is_float($value1) && is_float($value2) : is_float($value1) || is_float($value2)) {
                $value1 = (string) $value1;
                $value2 = (string) $value2;
            }

            if ($strict ? $value1 !== $value2 : $value1 != $value2) {
                $result[$key] = $value;
            }
        }

        $output = [];
        foreach ($result as $key => $val) {
            $output[] = $array1[$key];
        }

        return $output;
    }


	public static function background($data='') {
		$color = [
			'1' => 'style="background-color:none;"',
			'2' => 'style="background-color:#ffffe5;"',
			'3' => 'style="background-color:#ffe5e5;"'
		];

		return $color[$data];
	}

    public static function storeRoute() {
		$routeCollection = Route::getRoutes();

		$modifyController = [
			'Kuis' => 'Kuesioner',
			'Pertanyaan' => 'Pertanyaan Kuesioner', 
			'Kategori' => 'Kategori Artikel', 
			'User' => 'Admin CMS',
			'Repkuis' => 'Reporting Kuesioner'
		];
		$excludeMenu = ['Profile', 'Messages', 'Auth', 'Helper', 'Verif', 'ForgotPassword', 'Login', 'Register', 'ResetPassword', 'ConfirmPassword', 'Highchart'];
		$excludeAction = [
			'admin.profile.index',
			'admin.profile.store',
			'admin.dashboard.gender', 
			'admin.dashboard.umur', 
			'admin.dashboard.top', 
			'admin.dashboard.bottom', 
			'admin.dashboard.kuis',
			'admin.widget.update',
			'admin.kuis.submit', 
			'admin.kuis.apply', 
			'admin.kuis.submitreview', 
			'admin.kuis.upload', 
			'admin.kuis.store', 
			'admin.kuis.update',
			'admin.pertanyaan.store', 
			'admin.pertanyaan.submit', 
			'admin.pertanyaan.update', 
			'admin.kategori.upload', 
			'admin.kategori.store', 
			'admin.kategori.update',
			'admin.artikel.upload', 
			'admin.artikel.store', 
			'admin.artikel.update',
			'admin.chat.detail', 
			'admin.chat.active', 
			'admin.chat.send', 
			'admin.chat.search', 
			'admin.chat.history', 
			'admin.chat.refresh', 
			'admin.chat.leave',
			'admin.user.update',
			'admin.role.userList', 
			'admin.role.update', 
			'admin.role.store',
			'admin.page.store', 
			'admin.page.update',
			'admin.notifikasi.upload', 
			'admin.notifikasi.store', 
			'admin.notifikasi.update',
			'admin.repkuis.search', 
			'admin.repkuis.detail', 
			'admin.repkuis.details', 
			'admin.repkuis.update',
			'admin.provinsi.store', 
			'admin.provinsi.update',
			'admin.kota.store', 
			'admin.kota.update', 
			'admin.kecamatan.store', 
			'admin.kecamatan.update', 
			'admin.kelurahan.store', 
			'admin.kelurahan.update',
			'admin.penduduk.store'
		];


		$module = [];
		foreach ($routeCollection as $key => $value) {
			if (strpos($value->getActionName(), "App\Http\Controllers") !== false) {
				if (strpos($value->getName(), 'admin.') !== false) {
					list($controller, $method) = explode('@', $value->getActionName());
					$controller = str_replace('Controller', '', preg_replace('/.*\\\/', '', $controller));

					$prefix = explode('/', $value->getPrefix());
					$last = count($prefix) - 1;

					$ctrl = (array_key_exists($controller, $modifyController)) ? $modifyController[$controller] : $controller;
					$naming = str_replace(' ', '', $ctrl);

					$module[] = [
						'method' => $value->methods()[0],
						'uri' => $value->uri(),
						'module' => $value->getActionName(),
						'name' => $value->getName(),
						'menu_parent' => ucwords(str_replace('-', ' ', $prefix[$last])),
						'controller' => $ctrl,
						'menu' => self::separateText($naming),
						'menu_sub' => ($method == 'index') ? 1 : 0,
						'value' => $method
					];
				}
			}
		}

		$finModule = [];
		foreach ($module as $key => $val) {
			if (!in_array($val['controller'], $excludeMenu)) {
				$finModule[$val['controller']][] = $val;
			}
		}

		foreach ($finModule as $key => $row) {
			foreach ($row as $keys => $vals) {
				if (in_array($vals['name'], $excludeAction)) {
					unset($finModule[$key][$keys]);
				}
			}
		}

		foreach ($finModule as $key => $row) {
			$finModule[$key] = array_values($row);
		}

		foreach ($finModule as $key => $row) {
			foreach ($row as $keys => $vals) {
				if ($keys == '0') {
					$find = Module::where('name', $key)->first();

					if (!$find) {
						$insert = new Module();
						$insert->controller = $vals['controller'];
						$insert->menu_parent = $vals['menu_parent'];
						$insert->menu = $vals['menu'];
						$insert->name = $key;
						$insert->value = '';
						$insert->created_at = date('Y-m-d H:i:s');
						$insert->created_by = Auth::id();

						$insert->save();

						$lastInsert = $insert->id;
					} else {
						$lastInsert = $find->id;
					}
				}

				$findSub = Module::where('name', $vals['name'])->where('method', $vals['value'])->first();

				if (!$findSub) {

					$level = ['index' => 1, 'create' => 100, 'show' => 200, 'edit' => 300, 'delete' => 400];
					$value = ['index' => 'List', 'create' => 'Tambah', 'show' => 'Detail', 'edit' => 'Ubah', 'delete' => 'Hapus'];

					$sub = new Module();
					$sub->parent_id = $lastInsert;
					$sub->controller = $vals['controller'];
					$sub->uri = $vals['uri'];
					$sub->menu_parent = $vals['menu_parent'];
					$sub->menu = $vals['menu'];
					$sub->menu_sub = $vals['menu_sub'];
					$sub->name = $vals['name'];
					$sub->value = (array_key_exists($vals['value'], $value)) ? $value[$vals['value']] : ucfirst($vals['value']);
					$sub->method = $vals['value'];
					$sub->level = (array_key_exists($vals['value'], $level)) ? $level[$vals['value']] : 1000;
					$sub->created_at = date('Y-m-d H:i:s');
					$sub->created_by = Auth::id();

					$sub->save();
				}
			}
		}
	}

	public static function iconMenu($data='') {
		$icon = [
			'dashboard' => 'icon-home',
			'admin' => 'icon-home',
			'iklan' => 'icon-basket-loaded',
			'list' => 'icon-list',
			'create' => 'icon-plus',
			'payment' => 'icon-wallet',
			'saldo' => 'icon-wallet',
			'mutasi' => 'icon-notebook',
			'kategori' => 'icon-layers',
			'brand' => 'icon-briefcase',
			'tag' => 'icon-bubbles',
			'lokasi' => 'icon-location-pin',
			'provinsi' => 'icon-location-pin',
			'kabupaten-kota' => 'icon-location-pin',
			'user' => 'icon-people',
			'role' => 'icon-list',
			'profil' => 'icon-emotsmile',
			'konfigurasi' => 'icon-settings',
			'index' => 'icon-list',
			'create' => 'icon-plus',
			'saldoMember' => 'icon-wallet',
			'mutasiBank' => 'icon-notebook',
			'page' => 'icon-notebook',
			'faq' => 'icon-info'
		];

		return $icon[$data];
	}

	public static function countSub($data=array()) {
		$count = 0;
		foreach ($data as $key => $val) {
			if ($val['menu_sub'] == '1') {
				$count++;
			}
		}

		return $count;
	}

	public static function userMenu($result=array()) {
		$module = [];
		foreach ($result as $key => $val) {
			$module[$val['menu_parent']][$val['menu']][] = json_decode(json_encode($val), true);
		}
		//echo '<pre>'; 
		//print_r ($module);
		//die;

		$excludeMenu = ['Profil'];

		$menu = '<ul class="metismenu list-unstyled" id="side-menu">';
		$parentMenu = [];
		foreach ($module as $key => $val) {
			$menu .= '<li class="menu-title">' . $key . '</li>';

			foreach ($val as $keys => $vals) {
				$count = self::countSub($vals);
				if (!in_array($keys, $excludeMenu)) {
					//if (count($vals) < 3) {
					if ($count < 2) {
						$icon = self::iconMenu($vals[1]['method']);
						$host = request()->getSchemeAndHttpHost();
						$menu .= '
							<li>
								<a href="' . $host . '/' . $vals[1]['uri'] . '" class="waves-effect">
									<div class="d-inline-block icons-sm mr-1"><i class="icon ' . self::iconMenu(str_replace(' ', '-', strtolower($keys))) . '"></i></div>
									<span>' . $keys . '</span>
								</a>
							</li>
						';
					} else {
						$menu .= '
							<li>
								<a href="javascript: void(0);" class="has-arrow waves-effect">
									<div class="d-inline-block icons-sm mr-1"><i class="icon ' . self::iconMenu(str_replace(' ', '-', strtolower($keys))) . '"></i></div>
									<span>' . $keys . '</span>
								</a>
						';

						$menu .= '<ul class="sub-menu" aria-expanded="false">';
						foreach ($vals as $k => $v) {
							if ($v['menu_sub'] == 1) {
								$icon = self::iconMenu($v['method']);
								$host = request()->getSchemeAndHttpHost();
								//$pattern = app('router')->getRoutes()->getByName($v['name'])->uri;

								$value = self::separateText($v['value']);
								if ($v['value'] == 'Index') {
									$value = 'List ' . $v['menu'];
								}
								if ($v['value'] == 'Create') {
									$value = 'Tambah ' . $v['menu'];
								}

								$menu .= '
									<li>
										<a href="' . $host . '/' . $v['uri'] . '">
											<div class="d-inline-block icons-sm mr-1"><i class="icon ' . $icon . '"></i></div>
											' . $value . '
										</a>
									</li>
								';
							}
						}
						$menu .= '</ul>';
					}
				}
			}
			$menu .= '</li>';
		}
		$menu .= '</ul>';

		return $menu;
	}

	public static function status($data='') {
		$status = ['' => 'Pilih Status', '1' => 'Draft', '2' => 'Publish'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function statusAdmin($data='') {
		$status = ['' => 'Pilih Status', '1' => 'Approve', '2' => 'Belum Verifikasi', '3' => 'Menunggu Approval', '4' => 'Suspend', '5' => 'Ditolak'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function statusApproval($data='') {
		$status = ['' => 'Pilih Status', '1' => 'Menunggu Approval', '2' => 'Approve', '3' => 'Revisi', '4' => 'Ditolak'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function statusGender($data='') {
		$status = ['' => 'Pilih', '1' => 'Pria', '2' => 'Wanita', 'all' => 'Pria & Wanita'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function kondisiKuis($data='') {
		$kondisi = ['' => 'Pilih', '1' => 'Kurang dari', '2' => 'Sama dengan', '3' => 'Diantara', '4' => 'Lebih dari'];

		if (!empty($data)) {
			$kondisi = $kondisi[$data];
		}

		return $kondisi;
	}

	public static function statusUser($data='') {
		$status = ['' => 'Pilih Status', '1' => 'Aktif', '2' => 'Tidak Aktif', '3' => 'Banned', '4' => 'Belum Verifikasi'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function jenisKelamin($data='') {
		$jk = ['' => 'Pilih Jenis Kelamin', '1' => 'Pria', '2' => 'Wanita'];

		if (!empty($data)) {
			$jk = $jk[$data];
		}

		return $jk;
	}

	public static function jenisPertanyaan($data='') {
		$jenis = ['' => 'Pilih', 'single' => 'Single Question', 'combine' => 'Combined Question', 'widget' => 'Widget'];

		if (!empty($data)) {
			$jenis = $jenis[$data];
		}

		return $jenis;
	}

	public static function approval($data='') {
		$status = ['' => 'Pilih', 'APV100' => 'Draft', 'APV200' => 'Menunggu Approval', 'APV300' => 'Approve', 'APV400' => 'Revisi', 'APV500' => 'Ditolak'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function approvalCode($data='') {
		$status = ['' => 'Pilih', '1' => 'APV100', '2' => 'APV200', '3' => 'APV300', '4' => 'APV400', '5' => 'APV500'];

		if (!empty($data)) {
			$status = $status[$data];
		}

		return $status;
	}

	public static function colorPicker($data='') {
		$color = [
			'' => ['title' => 'Pilih', 'class' => ''],
			'#b0413e' => ['title' => 'Merah', 'class' => 'box-red'],
			'#dab707' => ['title' => 'Kuning', 'class' => 'box-yellow'],
			'#4d9078' => ['title' => 'Hijau', 'class' => 'box-green'], 
			'#3f5aa6' => ['title' => 'Biru', 'class' => 'box-blue']
		];

		if (!empty($data)) {
			$color = $color[$data];
		}

		return $color;
	}

	public static function successResponse($msg='') {
		return ['error' => false, 'code' => 200, 'message' => $msg];
	}

	public static function errorResponse($msg='') {
		return ['error' => true, 'code' => 200, 'message' => $msg];
	}

	public static function apisuccessResponse($data=array(), $msg='') {
		$msg = (empty($msg)) ? 'Success' : $msg;
		return ['error' => false, 'code' => 200, 'message' => $msg, 'data' => $data['data']];
	}

	public static function apierrorResponse($msg='', $data=array()) {
		$msg = (empty($msg)) ? 'Failed' : $msg;
		return ['error' => true, 'code' => 200, 'message' => $msg, 'data' => $data];
	}

	public static function apiexpiredResponse($msg='', $data=array()) {
		$msg = (empty($msg)) ? 'Token Expired' : $msg;
		return ['error' => true, 'code' => 401, 'message' => $msg, 'data' => $data];
	}

	public static function apiinvalidResponse($msg='', $data=array()) {
		$msg = (empty($msg)) ? 'Token Invalid' : $msg;
		return ['error' => true, 'code' => 401, 'message' => $msg, 'data' => $data];
	}

	public static function apiexceptionResponse($msg='', $data=array()) {
		$msg = (empty($msg)) ? 'Token Missing' : $msg;
		return ['error' => true, 'code' => 500, 'message' => $msg, 'data' => $data];
	}

	public static function phoneNumber($phone)
    {
        $phone = preg_replace("/\D/", "", $phone);

        return (substr($phone,0,2) == "62" || substr($phone,0,3) == "+62")
            ? substr($phone,2)
            : $phone;
    }
}
