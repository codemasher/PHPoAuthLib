<?php

/**
 * Example of retrieving an authentication token of the Flickr service
 *
 * @author     Christian Mayer <thefox21at@gmail.com>
 * @copyright  Copyright (c) 2013 The authors
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
	getenv('FLICKR_KEY'),
	getenv('FLICKR_SECRET'),
	$currentUri->getAbsoluteUri()
);

// Instantiate the Flickr service using the credentials, http client and storage mechanism for the token
/** @var $flickrService \OAuth\Service\Providers\OAuth1\Flickr */
$flickrService = $serviceFactory->createService('Flickr', $credentials, $storage);

$step = isset($_GET['step']) ? (int)$_GET['step'] : null;

$oauth_token    = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : null;
$oauth_verifier = isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : null;

if($oauth_token && $oauth_verifier){
	$step = 2;
}

switch($step){
	default:
		print "<a href='".$currentUri->getRelativeUri().'?step=1'."'>Login with Flickr!</a>";
		break;

	case 1:

		if($token = $flickrService->getRequestToken()){
			$oauth_token = $token->getAccessToken();
			$secret      = $token->getAccessTokenSecret();

			if($oauth_token && $secret){
				$url = $flickrService->getAuthorizationUri(['oauth_token' => $oauth_token, 'perms' => 'write']);
				header('Location: '.$url);
			}
		}

		break;

	case 2:
		$token  = $storage->retrieveAccessToken('Flickr');
		$secret = $token->getAccessTokenSecret();

		if($token = $flickrService->getAccessToken($oauth_token, $oauth_verifier, $secret)){
			$oauth_token = $token->getAccessToken();
			$secret      = $token->getAccessTokenSecret();

			$storage->storeAccessToken('Flickr', $token);

			header('Location: '.$currentUri->getAbsoluteUri().'?step=3');
		}
		break;

	case 3:
		$xml = simplexml_load_string($flickrService->request('flickr.test.login'));
		print "status: ".(string)$xml->attributes()->stat."\n";
		break;
}
