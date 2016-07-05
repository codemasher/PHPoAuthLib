<?php

/**
 * Example of retrieving an authentication token of the Github service
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

// Instantiate the GitHub service using the credentials, http client and storage mechanism for the token
/** @var $gitHub \OAuth\Service\Providers\OAuth2\GitHub */
$gitHub = $serviceFactory->createService(
	'GitHub', new Credentials(
	getenv('GITHUB_KEY'),
	getenv('GITHUB_SECRET'),
	$currentUri->getAbsoluteUri()
), $storage, ['user']
);

if(!empty($_GET['code'])){
	// This was a callback request from github, get the token
	$gitHub->requestAccessToken($_GET['code']);

	$result = json_decode($gitHub->request('user/emails'), true);

	echo 'The first email on your github account is '.$result[0];

}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $gitHub->getAuthorizationUri();
	header('Location: '.$url);

}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Github!</a>";
}
