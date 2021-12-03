<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1'], function() {
	Route::get('/test',function(){
		return "ok";
   });
	Route::post('register', 'AuthController@register');
	Route::post('login', 'AuthController@login');
	Route::post('forgot', 'AuthController@forgot');
	Route::post('emailcheck', 'AuthController@emailcheck');
	Route::post('resend', 'AuthController@resend');
	Route::post('checkverify', 'AuthController@checkverify');

	Route::post('provinsi', 'HelperController@provinsi');
	Route::post('kabupaten', 'HelperController@kabupaten');
	Route::post('kecamatan', 'HelperController@kecamatan');
	Route::post('kelurahan', 'HelperController@kelurahan');
	Route::post('checknik', 'HelperController@checknik');

	Route::post('home', 'HomeController@index');

	Route::post('newskategori', 'NewsController@listkategori');
	Route::post('newslist', 'NewsController@newslist');
	Route::post('newsdetail', 'NewsController@newsdetail');
	Route::post('newsrelated', 'NewsController@newsrelated');

	Route::post('kuislist', 'KuisController@kuislist');
	Route::post('kuisintro', 'KuisController@kuisintro');
	Route::post('pertanyaan', 'KuisController@pertanyaan');
	Route::post('submitkuis', 'KuisController@submitkuis');
	Route::post('generate', 'KuisController@generate');

	Route::post('resultlist', 'ResultController@resultlist');
	Route::post('resultdetail', 'ResultController@resultdetail');
	Route::post('resultcouple', 'ResultController@resultcouple');

	Route::post('widgetfaskes', 'WidgetController@widgetfaskes');
	Route::post('faskeslist', 'WidgetController@faskeslist');

	Route::post('pagelist', 'PageController@pagelist');
	Route::post('pagedetail', 'PageController@pagedetail');

	Route::post('profile', 'AkunController@index');
	Route::post('updateprofile', 'AkunController@updateprofile');
	Route::post('changepassword', 'AkunController@changepassword');

	Route::post('couplelist', 'AkunController@couplelist');
	Route::post('addcouple', 'AkunController@addcouple');
	Route::post('pendingcouple', 'AkunController@pendingcouple');
	Route::post('confirmcouple', 'AkunController@confirmcouple');
	Route::post('infonotif', 'AkunController@infonotif');

	Route::post('chatlist', 'ChatController@list');
	Route::post('chatsubmit', 'ChatController@submit');

	Route::post('notiflist', 'NotifikasiController@notiflist');
	Route::post('notifdelete', 'NotifikasiController@notifdelete');
	Route::post('notifinsert', 'NotifikasiController@notifinsert');

	Route::post('version', 'SettingController@check_version_code');

  Route::get('kuesioner-hamil/{id}','KuisHamilController@getKuesionerHamilResult');
});
