<?php

/**
 * Bootstrap the library
 */
use Dotenv\Dotenv;
use OAuth\_killme\ServiceFactory;
use OAuth\Http\UriFactory;

require_once __DIR__.'/../vendor/autoload.php';

/**
 * Setup error reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Setup the timezone
 */
ini_set('date.timezone', 'Europe/Amsterdam');


(new Dotenv(__DIR__.'/../config'))->load();

/**
 * Create a new instance of the URI class with the current URI, stripping the query string
 */
$uriFactory = new UriFactory();
$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
$currentUri->setQuery('');

/** @var $serviceFactory \OAuth\_killme\ServiceFactory An OAuth service factory. */
$serviceFactory = new ServiceFactory();
