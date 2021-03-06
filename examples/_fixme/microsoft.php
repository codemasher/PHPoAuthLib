<?php

/**
 * Example of retrieving an authentication token of the Microsoft service
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
	getenv('MICROSOFT_KEY'),
	getenv('MICROSOFT_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the Microsoft service using the credentials, http client and storage mechanism for the token
/** @var $microsoft \OAuth\Service\Providers\OAuth2\Microsoft */
$microsoft = $serviceFactory->createService('microsoft', $credentials, $storage, ['basic']);

if(!empty($_GET['code'])){
	// This was a callback request from Microsoft, get the token
	$token = $microsoft->getOAuth2AccessToken($_GET['code']);

	var_dump($token);

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $microsoft->getAuthorizationURL();
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Microsoft!</a>";
}
