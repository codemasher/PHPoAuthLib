<?php

/**
 * Example of retrieving an authentication token of the Dropbox service
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
	getenv('DROPBOX_KEY'),
	getenv('DROPBOX_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the Dropbox service using the credentials, http client and storage mechanism for the token
/** @var $dropboxService \OAuth\Service\Providers\OAuth2\Dropbox */
$dropboxService = $serviceFactory->createService('dropbox', $credentials, $storage, []);

if(!empty($_GET['code'])){
	// This was a callback request from Dropbox, get the token
	$token = $dropboxService->getOAuth2AccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($dropboxService->apiRequest('/account/info'), true);

	// Show some of the resultant data
	echo 'Your unique Dropbox user id is: '.$result['uid'].' and your name is '.$result['display_name'];

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $dropboxService->getAuthorizationURL();
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Dropbox!</a>";
}
