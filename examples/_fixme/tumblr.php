<?php

/**
 * Example of retrieving an authentication token of the Tumblr service
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

// We need to use a persistent storage to save the token, because oauth1 requires the token secret received before'
// the redirect (request token request) in the access token request.
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	getenv('TUMBLR_KEY'),
	getenv('TUMBLR_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the tumblr service using the credentials, http client and storage mechanism for the token
/** @var $tumblrService \OAuth\Service\Providers\OAuth1\Tumblr */
$tumblrService = $serviceFactory->createService('tumblr', $credentials, $storage);

if(!empty($_GET['oauth_token'])){
	$token = $storage->retrieveAccessToken('Tumblr');

	// This was a callback request from tumblr, get the token
	$tumblrService->getOauth1AccessToken(
		$_GET['oauth_token'],
		$_GET['oauth_verifier'],
		$token->requestTokenSecret
	);

	// Send a request now that we have access token
	$result = json_decode($tumblrService->apiRequest('user/info'));

	echo 'result: <pre>'.print_r($result, true).'</pre>';

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	// extra request needed for oauth1 to request a request token :-)
	$token = $tumblrService->getRequestToken();

	$url = $tumblrService->getAuthorizationURL(['oauth_token' => $token->requestToken]);
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Tumblr!</a>";
}
