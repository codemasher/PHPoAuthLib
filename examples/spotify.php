<?php
/**
 * Example of retrieving an authentication token of the Spotify service
 *
 * PHP version 5.4
 *
 * @author     Craig Morris <craig.michael.morris@gmail.com>
 * @author     Ben King <ben.kingsy@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once __DIR__.'/bootstrap.php';

// Instantiate the Spotify service using the credentials, http client and storage mechanism for the token
$spotifyService = new \OAuth\Service\Providers\OAuth2\Spotify(
	new \OAuth\Http\CurlClient,
	new \OAuth\Storage\Session,
	$currentUri->getAbsoluteUri(),
	getenv('SPOTIFY_KEY'),
	getenv('SPOTIFY_SECRET')
);


if(!empty($_GET['code'])){
	// This was a callback request from Spotify, get the token
	$spotifyService->requestAccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($spotifyService->request('me'), true);

	// Show some of the resultant data
	echo 'Your unique user id is: '.$result['id'].' and your name is '.$result['display_name'];

}
elseif(!empty($_GET['login']) && $_GET['login'] === 'spotify'){
	header('Location: '.$spotifyService->getAuthorizationUri());
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=spotify">Login with Spotify!</a>';
}
