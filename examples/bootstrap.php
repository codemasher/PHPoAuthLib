<?php

/**
 * Bootstrap the library
 */
use Dotenv\Dotenv;

const LIB_PATH = __DIR__.'/..';

require_once LIB_PATH.'/vendor/autoload.php';

/**
 * Setup error reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Setup the timezone
 */
ini_set('date.timezone', 'Europe/Amsterdam');


(new Dotenv(LIB_PATH.'/config'))->load();

$httpClient = new \OAuth\Http\CurlClient;
$storage    = new \OAuth\Storage\Session;
