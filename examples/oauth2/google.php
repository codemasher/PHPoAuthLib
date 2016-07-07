<?php

/**
 * Example of retrieving an authentication token of the Google service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Service\Providers\OAuth2\Google;

require_once __DIR__.'/../bootstrap.php';



$googleService = new Google(
	$httpClient,
	$storage,
	$currentUri->getAbsoluteUri(),
	getenv('GOOGLE_KEY'),
	getenv('GOOGLE_SECRET'),
	[Google::SCOPE_EMAIL, Google::SCOPE_PROFILE]
);


if(!empty($_GET['code'])){
	// retrieve the CSRF state parameter
	$state = isset($_GET['state']) ? $_GET['state'] : null;

	// This was a callback request from google, get the token
	$googleService->requestAccessToken($_GET['code'], $state);

	// Send a request with it
	$result = json_decode($googleService->request('userinfo'), true);

	echo 'result: <pre>'.print_r($result, true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'google'){
	header('Location: '.$googleService->getAuthorizationUri());
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=google">Login with Google!</a>';
}
