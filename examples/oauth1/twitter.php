<?php

require_once __DIR__.'/../bootstrap.php';

$twitterService = new \OAuth\Service\Providers\OAuth1\Twitter(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('TWITTER_KEY'),
		'secret'      => getenv('TWITTER_SECRET'),
		'callbackURL' => getenv('TWITTER_CALLBACK_URL'),
	])
);

if(!empty($_GET['oauth_token'])){

	$twitterService->getOauth1AccessToken(
		$_GET['oauth_token'],
		$_GET['oauth_verifier'],
		$storage->retrieveAccessToken('Twitter')->requestTokenSecret
	);

	echo 'result: <pre>'.print_r(json_decode($twitterService->apiRequest('account/verify_credentials.json')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'twitter'){

	header('Location: '.$twitterService->getAuthorizationURL([
		'oauth_token' => $twitterService->getRequestToken()->requestToken
	]));

}
else{
	echo '<a href="?login=twitter">Login with Twitter!</a>';
}

exit;
