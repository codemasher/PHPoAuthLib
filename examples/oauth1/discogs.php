<?php

require_once __DIR__.'/../bootstrap.php';

$discogsService = new \OAuth\Service\Providers\OAuth1\Discogs(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('DISCOGS_KEY'),
		'secret'      => getenv('DISCOGS_SECRET'),
		'callbackURL' => getenv('DISCOGS_CALLBACK_URL'),
	])
);

if(!empty($_GET['oauth_token'])){

	$discogsService->getOauth1AccessToken(
		$_GET['oauth_token'],
		$_GET['oauth_verifier'],
		$storage->retrieveAccessToken('Discogs')->requestTokenSecret
	);

	echo 'result: <pre>'.print_r(json_decode($discogsService->apiRequest('oauth/identity')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'discogs'){

	header('Location: '.$discogsService->getAuthorizationURL([
		'oauth_token' => $discogsService->getRequestToken()->requestToken
	]));

}
else{
	echo '<a href="?login=discogs">Login with Discogs!</a>';
}

exit;


