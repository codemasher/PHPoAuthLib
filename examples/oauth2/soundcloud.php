<?php

/**
 * Example of retrieving an authentication token of the SoundCloud service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once __DIR__.'/../bootstrap.php';

$soundcloudService = new \OAuth\Service\Providers\OAuth2\SoundCloud(
	$httpClient,
	$storage,
	$currentUri->getAbsoluteUri(),
	getenv('SOUNDCLOUD_KEY'),
	getenv('SOUNDCLOUD_SECRET')
);

if(!empty($_GET['code'])){
	// This was a callback request from SoundCloud, get the token
	$soundcloudService->requestAccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($soundcloudService->request('me.json'), true);

	// Show some of the resultant data
	echo 'result: <pre>'.print_r($result, true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'soundcloud'){
      header('Location: '.$gitHub->getAuthorizationUri());
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=soundcloud">Login with SoundCloud!</a>';
}

