<?php

/**
 * Example of retrieving an authentication token of the Facebook service
 *
 * PHP version 5.4
 *
 * @author     Benjamin Bender <bb@codepoet.de>
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
	getenv('FACEBOOK_KEY'),
	getenv('FACEBOOK_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
/** @var $facebookService \OAuth\Service\Providers\OAuth2\Facebook */
$facebookService = $serviceFactory->createService('facebook', $credentials, $storage, []);

if(!empty($_GET['code'])){
	// retrieve the CSRF state parameter
	$state = isset($_GET['state']) ? $_GET['state'] : null;

	// This was a callback request from facebook, get the token
	$token = $facebookService->requestAccessToken($_GET['code'], $state);

	// Send a request with it
	$result = json_decode($facebookService->request('/me'), true);

	// Show some of the resultant data
	echo 'Your unique facebook user id is: '.$result['id'].' and your name is '.$result['name'];

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $facebookService->getAuthorizationUri();
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Facebook!</a>";
}
