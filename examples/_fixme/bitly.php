<?php

/**
 * Example of retrieving an authentication token of the Bitly service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\_killme\Credentials;
use OAuth\Storage\Session;

/**
 * Bootstrap the example
 */
require_once __DIR__.'/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	getenv('BITLY_KEY'),
	getenv('BITLY_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the Bitly service using the credentials, http client and storage mechanism for the token
/** @var $bitlyService \OAuth\Service\Providers\OAuth2\Bitly */
$bitlyService = $serviceFactory->createService('bitly', $credentials, $storage);

if(!empty($_GET['code'])){
	// This was a callback request from bitly, get the token
	$bitlyService->getOAuth2AccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($bitlyService->apiRequest('user/info'), true);

	// Show some of the resultant data
	echo 'Your unique user id is: '.$result['data']['login'].' and your name is '.$result['data']['display_name'];

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $bitlyService->getAuthorizationURL();
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Bitly!</a>";
}
