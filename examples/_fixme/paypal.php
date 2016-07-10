<?php

/**
 * Example of retrieving an authentication token of the PayPal service
 *
 * PHP version 5.4
 *
 * @author     Flávio Heleno <flaviohbatista@gmail.com>
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
	getenv('PAYPAL_KEY'),
	getenv('PAYPAL_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the PayPal service using the credentials, http client, storage mechanism for the token and profile/openid scopes
/** @var $paypalService \OAuth\Service\Providers\OAuth2\PayPal */
$paypalService = $serviceFactory->createService('paypal', $credentials, $storage, ['profile', 'openid']);

if(!empty($_GET['code'])){
	// This was a callback request from PayPal, get the token
	$token = $paypalService->getOAuth2AccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($paypalService->apiRequest('/identity/openidconnect/userinfo/?schema=openid'), true);

	// Show some of the resultant data
	echo 'Your unique PayPal user id is: '.$result['user_id'].' and your name is '.$result['name'];

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $paypalService->getAuthorizationURL();
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with PayPal!</a>";
}
