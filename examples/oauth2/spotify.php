<?php

require_once __DIR__.'/../bootstrap.php';

$spotifyService = new \OAuth\Service\Providers\OAuth2\Spotify(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('SPOTIFY_KEY'),
		'secret'      => getenv('SPOTIFY_SECRET'),
		'callbackURL' => getenv('SPOTIFY_CALLBACK_URL'),
	]),
	['user-read-email']
);

if(!empty($_GET['code'])){
	$spotifyService->getOAuth2AccessToken($_GET['code']);

	echo 'result: <pre>'.print_r(json_decode($spotifyService->apiRequest('me')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'spotify'){
	header('Location: '.$spotifyService->getAuthorizationURL());
}
else{
	echo '<a href="?login=spotify">Login with Spotify!</a>';
}

exit;