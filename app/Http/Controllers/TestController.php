<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Helper;
use OneSignal;

class TestController extends Controller
{
    public function test()
    {
		/*$parameters = [
			'include_player_ids' => 'a39fc1d8-6048-47af-b2ac-44f0dee13275', //this is because i'm running this on a foreach loop (planing to just create an array later so ignore it)
			'api_key' => 'YTQ5YWVmOGEtN2YwMy00YWYzLWE3MDQtYjJmNDg2YWFlNDA5',
			'app_id' => '6d354bb7-68f2-436a-9fd0-24e003384003',
			'language' => 'en',
			'contents' => 'ABC',
			'headings' => 'Test',
			'subtitle' => 'Subtitle',
			'ios_badgeType' => "Increase",
			'ios_badgeCount' => 1,
			'url' => "https://mySite.com/duh",
			//'send_after' => "$osDate". "GMT-0600",
		];

		$oSignal = OneSignal::sendNotificationCustom($parameters);
		$oneSignalResponse = $oSignal->getBody();
		$decodedResponse = json_decode($oneSignalResponse);*/

		//print_r ($decodedResponse);

		$test = OneSignal::sendNotificationToUser(
                    "Testing onesignal",
                    '65727e7c-3ccc-40b1-8d50-592deac3f3f2',
                    $url = null,
                    $data = null,
                    $buttons = null,
                    $schedule = null
                );
		print_r ($test);
		echo 'a';
    }
}