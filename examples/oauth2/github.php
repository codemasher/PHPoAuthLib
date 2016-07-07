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

require_once __DIR__.'/../bootstrap.php';

// Instantiate the GitHub service using the credentials, http client and storage mechanism for the token
$gitHub = new \OAuth\Service\Providers\OAuth2\GitHub(
	$httpClient,
	$storage,
	$currentUri->getAbsoluteUri(),
	getenv('GITHUB_KEY'),
	getenv('GITHUB_SECRET'),
	[\OAuth\Service\Providers\OAuth2\GitHub::SCOPE_USER]
);

if(!empty($_GET['code'])){
	// This was a callback request from github, get the token
	$gitHub->requestAccessToken($_GET['code']);

	$result = json_decode($gitHub->request('user/emails'), true);

	echo 'result: <pre>'.print_r($result, true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'github'){
	header('Location: '.$gitHub->getAuthorizationUri());
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=github">Login with Github!</a>';
}
