<?php

/**
 * Bootstrap the library
 */
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

/**
 * Create a new instance of the URI class with the current URI, stripping the query string
 */
$uriFactory = new UriFactory();
$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
$currentUri->setQuery('');

/**
 * @var array A list of all the credentials to be used by the different services in the examples
 */
$servicesCredentials = [
	'amazon'            => [
		'key'    => '',
		'secret' => '',
	],
	'bitbucket'         => [
		'key'    => '',
		'secret' => '',
	],
	'bitly'             => [
		'key'    => '',
		'secret' => '',
	],
	'bitrix24'          => [
		'key'    => '',
		'secret' => '',
	],
	'box'               => [
		'key'    => '',
		'secret' => '',
	],
	'buffer'            => [
		'key'    => '',
		'secret' => '',
	],
	'dailymotion'       => [
		'key'    => '',
		'secret' => '',
	],
	'delicious'         => [
		'key'    => '',
		'secret' => '',
	],
	'deezer'            => [
		'key'    => '',
		'secret' => '',
	],
	'deviantart'        => [
		'key'    => '',
		'secret' => '',
	],
	'dropbox'           => [
		'key'    => '',
		'secret' => '',
	],
	'etsy'              => [
		'key'    => '',
		'secret' => '',
	],
	'eveonline'         => [
		'key'    => '',
		'secret' => '',
	],
	'facebook'          => [
		'key'    => '',
		'secret' => '',
	],
	'fitbit'            => [
		'key'    => '',
		'secret' => '',
	],
	'fivehundredpx'     => [
		'key'    => '',
		'secret' => '',
	],
	'flickr'            => [
		'key'    => '',
		'secret' => '',
	],
	// https://foursquare.com/developers/
	'foursquare'        => [
		'key'    => '',
		'secret' => '',
	],
	// https://github.com/settings/applications/
	'github'            => [
		'key'    => '',
		'secret' => '',
	],
	// https://console.developers.google.com/apis/credentials
	'google'            => [
		'key'    => '',
		'secret' => '',
	],
	'hubic'             => [
		'key'    => '',
		'secret' => '',
	],
	'instagram'         => [
		'key'    => '',
		'secret' => '',
	],
	'linkedin'          => [
		'key'    => '',
		'secret' => '',
	],
	'mailchimp'         => [
		'key'    => '',
		'secret' => '',
	],
	'microsoft'         => [
		'key'    => '',
		'secret' => '',
	],
	'nest'              => [
		'key'    => '',
		'secret' => '',
	],
	'netatmo'           => [
		'key'    => '',
		'secret' => '',
	],
	'parrotFlowerPower' => [
		'key'    => '',
		'secret' => '',
	],
	'paypal'            => [
		'key'    => '',
		'secret' => '',
	],
	'pinterest'         => [
		'key'    => '',
		'secret' => '',
	],
	'pocket'            => [
		'key' => '',
	],
	'quickbooks'        => [
		'key'    => '',
		'secret' => '',
	],
	'reddit'            => [
		'key'    => '',
		'secret' => '',
	],
	'redmine'           => [
		'key'    => '',
		'secret' => '',
	],
	'runkeeper'         => [
		'key'    => '',
		'secret' => '',
	],
	'salesforce'        => [
		'key'    => '',
		'secret' => '',
	],
	'scoopit'           => [
		'key'    => '',
		'secret' => '',
	],
	// http://soundcloud.com/you/apps
	'soundcloud'        => [
		'key'    => '',
		'secret' => '',
	],
	// https://developer.spotify.com/my-applications/
	'spotify'           => [
		'key'    => '',
		'secret' => '',
	],
	'strava'            => [
		'key'    => '',
		'secret' => '',
	],
	'tumblr'            => [
		'key'    => '',
		'secret' => '',
	],
	// https://apps.twitter.com/
	'twitter'           => [
		'key'    => '',
		'secret' => '',
	],
	'ustream'           => [
		'key'    => '',
		'secret' => '',
	],
	// https://developer.vimeo.com/
	'vimeo'             => [
		'key'    => '',
		'secret' => '',
	],
	'yahoo'             => [
		'key'    => '',
		'secret' => '',
	],
	'yammer'            => [
		'key'    => '',
		'secret' => '',
	],
];

/** @var $serviceFactory \OAuth\_killme\ServiceFactory An OAuth service factory. */
$serviceFactory = new ServiceFactory();
