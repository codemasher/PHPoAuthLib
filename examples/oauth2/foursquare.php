<?php

/**
 * Example of retrieving an authentication token of the Foursquare service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once __DIR__.'/../bootstrap.php';

$foursquareService = new \OAuth\Service\Providers\OAuth2\Foursquare(
	$httpClient,
	$storage,
	$currentUri->getAbsoluteUri(),
	getenv('FOURSQUARE_KEY'),
	getenv('FOURSQUARE_SECRET')
);

if(!empty($_GET['code'])){
	// This was a callback request from foursquare, get the token
	$foursquareService->requestAccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($foursquareService->request('users/self'), true);

	echo 'result: <pre>'.print_r($result, true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'foursquare'){
      header('Location: '.$foursquareService->getAuthorizationUri());
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=foursquare">Login with Foursquare!</a>';
}
