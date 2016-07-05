<?php

/**
 * Example of retrieving an authentication token of the Reddit service
 *
 * PHP version 5.4
 *
 * @author     Connor Hindley <conn.hindley@gmail.com>
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
	getenv('REDDIT_KEY'),
	getenv('REDDIT_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the Reddit service using the credentials, http client and storage mechanism for the token
/** @var $reddit \OAuth\Service\Providers\OAuth2\Reddit */
$reddit = $serviceFactory->createService('Reddit', $credentials, $storage, ['identity']);

if(!empty($_GET['code'])){
	// retrieve the CSRF state parameter
	$state = isset($_GET['state']) ? $_GET['state'] : null;

	// This was a callback request from reddit, get the token
	$reddit->requestAccessToken($_GET['code'], $state);

	$result = json_decode($reddit->request('api/v1/me.json'), true);

	echo 'Your unique reddit user id is: '.$result['id'].' and your username is '.$result['name'];

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $reddit->getAuthorizationUri();
	header('Location: '.$url);

}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Reddit!</a>";
}
