<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

Route::get('/', 'DashboardController@dashboard')->name('dashboard');

Route::get('vrf/{pwd}', 'VerifController@verify')->name('vrf');
Route::post('/forgot', 'Auth\ForgotPasswordController@index')->name('forgot');
Route::get('cpw/{pwd}', 'VerifController@changepswd')->name('cpw');
Route::post('/submitchange', 'VerifController@submitchange')->name('submitchange');
Route::get('apv/{id}', 'VerifController@approve')->name('apv');
Route::post('/resend', 'VerifController@resend')->name('resend');
Route::get('lgn/{id}', 'VerifController@lgn')->name('lgn');

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/carinik', 'HelperController@carinik')->name('carinik');
Route::post('/provinsi', 'HelperController@provinsi')->name('provinsi');
Route::post('/kabupaten', 'HelperController@kabupaten')->name('kabupaten');
Route::post('/kecamatan', 'HelperController@kecamatan')->name('kecamatan');
Route::post('/kelurahan', 'HelperController@kelurahan')->name('kelurahan');
Route::post('/member', 'HelperController@member')->name('member');
Route::post('/newskategori', 'HelperController@newsKategori')->name('newskategori');
Route::post('/subnewskategori', 'HelperController@subNewsKategori')->name('subnewskategori');
Route::post('/jenis', 'HelperController@jenis')->name('jenis');
Route::post('/pilihan', 'HelperController@pilihan')->name('pilihan');
Route::post('/widget', 'HelperController@widgetcomponent')->name('widget');
Route::post('/jenisedit', 'HelperController@jenisedit')->name('jenisedit');
Route::post('/pilihanedit', 'HelperController@pilihanedit')->name('pilihanedit');
Route::post('/widgetedit', 'HelperController@widgetedit')->name('widgetedit');

Route::post('/roles', 'HelperController@getRole')->name('getrole');
Route::get('/rolechild/{id}', 'HelperController@getRoleChild')->name('getrolechild');

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
     \UniSharp\LaravelFilemanager\Lfm::routes();
 });

Route::post('/result', 'KuaController@result')->name('kua.result');
Route::get('kua/{id}/couples', 'KuaController@couples')->name('kua.couples');
Route::resource('kua', 'KuaController');

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function() {

	Route::prefix('dashboard')->group(function() {
		Route::get('/', 'DashboardController@dashboard')->name('dashboard');
		Route::post('gender', 'DashboardController@gender')->name('dashboard.gender');
		Route::post('umur', 'DashboardController@umur')->name('dashboard.umur');
		Route::post('top', 'DashboardController@top')->name('dashboard.top');
		Route::post('bottom', 'DashboardController@bottom')->name('dashboard.bottom');
		Route::post('kuis', 'DashboardController@kuis')->name('dashboard.kuis');
		Route::resource('dashboard', 'DashboardController', [
			'only' => ['dashboard']
		]);
	});

	Route::prefix('profile')->group(function() {
		Route::resource('profile', 'ProfileController')->except(['show', 'edit', 'update', 'destroy']);
	});

	Route::prefix('kuesioner')->group(function() {
		Route::resource('widget', 'WidgetController')->except(['destroy', 'create', 'store', 'show']);

		Route::get('kuis/sort', 'KuisController@sort')->name('kuis.sort');
		Route::post('kuis/submit', 'KuisController@submit')->name('kuis.submit');
		Route::post('kuis/delete', 'KuisController@delete')->name('kuis.delete');
		Route::get('kuis/approve', 'KuisController@approve')->name('kuis.approve');
		Route::get('kuis/preview/{id}', 'KuisController@preview')->name('kuis.preview');
		Route::post('kuis/apply', 'KuisController@apply')->name('kuis.apply');
		Route::get('kuis/review/{id}', 'KuisController@review')->name('kuis.review');
		Route::post('kuis/submitreview', 'KuisController@submitreview')->name('kuis.submitreview');
		Route::post('kuis/upload', 'KuisController@upload')->name('kuis.upload');
		Route::resource('kuis', 'KuisController')->except(['destroy']);

		Route::get('/pertanyaan/index/{id}', 'PertanyaanController@index')->name('pertanyaan.index');
		Route::get('/pertanyaan/create/{id}', 'PertanyaanController@create')->name('pertanyaan.create');
		Route::post('/pertanyaan/{id}/store', 'PertanyaanController@store')->name('pertanyaan.store');
		Route::post('pertanyaan/delete', 'PertanyaanController@delete')->name('pertanyaan.delete');
		Route::get('/pertanyaan/sort/{id}', 'PertanyaanController@sort')->name('pertanyaan.sort');
		Route::post('/pertanyaan/submit', 'PertanyaanController@submit')->name('pertanyaan.submit');
		Route::resource('pertanyaan', 'PertanyaanController')->except(['index', 'create', 'store', 'destroy']);

		//Route::post('approval/delete', 'ApprovalController@delete')->name('approval.delete');
		//Route::resource('approval', 'ApprovalController')->except(['destroy']);
	});

	Route::prefix('artikel')->group(function() {
		Route::get('kategori/sort', 'KategoriController@sort')->name('kategori.sort');
		Route::post('kategori/submit', 'KategoriController@submit')->name('kategori.submit');
		Route::post('kategori/delete', 'KategoriController@delete')->name('kategori.delete');
		Route::post('kategori/upload', 'KategoriController@upload')->name('kategori.upload');
		Route::resource('kategori', 'KategoriController')->except(['destroy']);

		Route::post('artikel/upload', 'ArtikelController@upload')->name('artikel.upload');
		Route::post('artikel/delete', 'ArtikelController@delete')->name('artikel.delete');
		Route::resource('artikel', 'ArtikelController')->except(['destroy']);
	});

	Route::prefix('inbox')->group(function() {
		Route::post('check', 'ChatController@check')->name('chat.check');
		Route::post('detail', 'ChatController@detail')->name('chat.detail');
		Route::post('active', 'ChatController@active')->name('chat.active');
		Route::post('send', 'ChatController@send')->name('chat.send');
		Route::get('search', 'ChatController@search')->name('chat.search');
		Route::post('history', 'ChatController@history')->name('chat.history');
		Route::post('refresh', 'ChatController@refresh')->name('chat.refresh');
		Route::post('leave', 'ChatController@leave')->name('chat.leave');
		Route::resource('chat', 'ChatController')->except(['create', 'edit', 'store', 'update', 'destroy']);
	});

	Route::prefix('user-management')->group(function() {
		Route::post('member/blokir', 'MemberController@blokir')->name('member.blokir');
		Route::get('member/{id}/result', 'MemberController@result')->name('member.result');
		Route::get('member/{id}/logbook', 'MemberController@logbook')->name('member.logbook');
		Route::post('member/logbook-update', 'MemberController@logbookUpdate')->name('member.logbook_update');
		Route::post('member/kelola', 'MemberController@kelola')->name('member.kelola');
		Route::put('member/update/{id}', 'MemberController@update')->name('member.update');
		Route::resource('member', 'MemberController')->except(['create', 'store', 'destroy', 'update']);

        Route::prefix('member/{id}/kuesioner-ibu-hamil')->group(function(){
            #halaman index
            Route::get('/', 'MemberController@indexIbuHamil')->name('member.ibuhamil');
            #kontak awal
            Route::get('kontak-awal/create','KuisHamilController@indexKontakAwal')->name('kontakawal-create');
            Route::post('kontak-awal/save','KuisHamilController@storeKontakAwal')->name('kontakawal-save');
            #12-minggu
            Route::get('periode-12-minggu/create','KuisHamilController@indexPeriode12Minggu')->name('periode12minggu-create');
            Route::post('periode-12-minggu/save','KuisHamilController@storePeriode12Minggu')->name('periode12minggu-save');
            #16-minggu
            Route::get('periode-16-minggu/create','KuisHamilController@indexPeriode16Minggu')->name('periode16minggu-create');
            Route::post('periode-16-minggu/save','KuisHamilController@storePeriode16Minggu')->name('periode16minggu-save');
            #20-36-minggu
            Route::get('periode-20-minggu/create/{periode}','KuisHamilController@indexHamilIbuJanin')->name('periodeIbuJanin-create');
            Route::post('periode-20-minggu/save/{periode}','KuisHamilController@storeHamilIbuJanin')->name('periodeIbuJanin-save');
            #persalinan
            Route::get('persalinan/create','KuisHamilController@indexPersalinan')->name('periodePersalinan-create');
            Route::post('persalinan/save','KuisHamilController@storePersalinan')->name('periodePersalinan-save');
            #nifas
            Route::get('nifas/create','KuisHamilController@indexNifas')->name('periodeNifas-create');
            Route::post('nifas/save','KuisHamilController@storeNifas')->name('periodeNifas-save');

        });

		Route::get('user/{id}/delegasi', 'UserController@delegasi')->name('user.delegasi');
		Route::post('user/submit', 'UserController@submit')->name('user.submit');
		Route::post('user/move', 'UserController@move')->name('user.move');
		Route::post('user/delete', 'UserController@delete')->name('user.delete');
		Route::resource('user', 'UserController')->except(['create', 'store', 'destroy']);

		Route::post('role/userList', 'RoleController@userList')->name('role.userList');
		Route::get('role/setting', 'RoleController@setting')->name('role.setting');
		Route::post('role/delete', 'RoleController@delete')->name('role.delete');
		Route::match(['post'], 'role/{role}','RoleController@update')->name('role.update');
		Route::resource('role', 'RoleController')->except(['update', 'destroy']);

		Route::get('role/{roleid}/child', 'RoleController@getChild')->name('role.child');
	});

	Route::prefix('page')->group(function() {
		Route::post('page/delete', 'PageController@delete')->name('page.delete');
		Route::resource('page', 'PageController')->except(['destroy']);
	});

	Route::prefix('notifikasi')->group(function() {
		Route::post('notifikasi/upload', 'NotifikasiController@upload')->name('notifikasi.upload');
		Route::post('notifikasi/send', 'NotifikasiController@send')->name('notifikasi.send');
		Route::post('notifikasi/delete', 'NotifikasiController@delete')->name('notifikasi.delete');
		Route::resource('notifikasi', 'NotifikasiController')->except(['destroy']);
	});

	Route::prefix('reporting')->group(function() {
		//Route::post('result/delete', 'ResultController@delete')->name('result.delete');
		//Route::resource('result', 'ResultController')->except(['destroy']);

		Route::post('repkuis/search', 'RepkuisController@search')->name('repkuis.search');
		Route::post('repkuis/download', 'RepkuisController@download')->name('repkuis.download');
		Route::get('repkuis/detail', 'RepkuisController@detail')->name('repkuis.detail');
		Route::get('repkuis/sdetail', 'RepkuisController@sdetail')->name('repkuis.sdetail');
		Route::post('repkuis/details', 'RepkuisController@details')->name('repkuis.details');
		Route::get('repkuis/kuis/{id}/member/{cid}', 'RepkuisController@history')->name('repkuis.history');
		Route::resource('repkuis', 'RepkuisController')->except(['create', 'store', 'edit', 'show', 'destroy']);
		//Route::post('reporting/kuesioner', 'ReportingController@kuesioner')->name('reporting.kuesioner');
		//Route::resource('reporting', 'ReportingController')->except(['index', 'create', 'show', 'edit', 'destroy']);
	});

	Route::prefix('master-data')->group(function() {
		Route::get('provinsi/upload', 'ProvinsiController@upload')->name('provinsi.upload');
		Route::get('provinsi/template', 'ProvinsiController@downloadExcel')->name('provinsi.template');
		Route::post('provinsi/delete', 'ProvinsiController@delete')->name('provinsi.delete');
		Route::resource('provinsi', 'ProvinsiController')->except(['create', 'show', 'destroy']);

		Route::post('kota/delete', 'KotaController@delete')->name('kota.delete');
		Route::resource('kota', 'KotaController')->except(['create', 'show', 'destroy']);

		Route::post('kecamatan/delete', 'KecamatanController@delete')->name('kecamatan.delete');
		Route::resource('kecamatan', 'KecamatanController')->except(['create', 'show', 'destroy']);

		Route::post('kelurahan/delete', 'KelurahanController@delete')->name('kelurahan.delete');
		Route::resource('kelurahan', 'KelurahanController')->except(['create', 'show', 'destroy']);

		Route::get('penduduk/upload', 'PendudukController@upload')->name('penduduk.upload');
		Route::get('penduduk/template', 'PendudukController@downloadExcel')->name('penduduk.template');
		Route::post('penduduk/delete', 'PendudukController@delete')->name('penduduk.delete');
		Route::resource('penduduk', 'PendudukController')->except(['create', 'edit', 'update', 'show', 'destroy']);
	});

});

