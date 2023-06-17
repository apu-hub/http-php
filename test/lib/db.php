<?php

use App\DB;
use App\Utils;

require(__DIR__."/../../vendor/autoload.php");

// load environment variables 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../../");
$dotenv->load();

// $data=DB::table('users')->select("*")->first();

$data=DB::table('users')->select("*")->where('name',"a1")->count("name");
// $data=DB::table('users')->select("*")->where('name',"a1")->average("password");
$data=DB::table('users')->select("*")->where('name',"a1")->sum("password");

Utils::dd($data);