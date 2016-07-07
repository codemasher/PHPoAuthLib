<?php

/**
 * Example of retrieving an authentication token of the Twitter service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once __DIR__.'/../bootstrap.php';


// Instantiate the twitter service using the credentials, http client and storage mechanism for the token
$twitterService = new \OAuth\Service\Providers\OAuth1\Twitter(
	$httpClient,
	$storage,
	$currentUri->getAbsoluteUri(),
	getenv('TWITTER_KEY'),
	getenv('TWITTER_SECRET')
);

if(!empty($_GET['oauth_token'])){
	$token = $storage->retrieveAccessToken('Twitter');

	// This was a callback request from twitter, get the token
	$twitterService->getAccessToken($_GET['oauth_token'], $_GET['oauth_verifier'], $token->getRequestTokenSecret());

	// Send a request now that we have access token
	$result = json_decode($twitterService->request('account/verify_credentials.json'));

	echo 'result: <pre>'.print_r($result, true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'twitter'){
	// extra request needed for oauth1 to request a request token :-)
	$token = $twitterService->getRequestToken();

	header('Location: '.$twitterService->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]));
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=twitter">Login with Twitter!</a>';
}
