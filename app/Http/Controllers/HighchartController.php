<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;

use Helper;

class HighchartController extends Controller
{
    public function handleChart()
    {

		$process = Helper::storeRoute();
		print_r ($process);

		die;
          
        return view('charts', compact('userData'));
    }
}