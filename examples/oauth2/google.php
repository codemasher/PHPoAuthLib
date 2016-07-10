<?php

require_once __DIR__.'/../bootstrap.php';

use OAuth\Service\Providers\OAuth2\Google;

$googleService = new Google(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('GOOGLE_KEY'),
		'secret'      => getenv('GOOGLE_SECRET'),
		'callbackURL' => getenv('GOOGLE_CALLBACK_URL'),
	]),
	['email', 'profile']
);


if(!empty($_GET['code'])){
	$googleService->getOAuth2AccessToken($_GET['code'], isset($_GET['state']) ? $_GET['state'] : null);

	echo 'result: <pre>'.print_r(json_decode($googleService->apiRequest('userinfo')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'google'){
	header('Location: '.$googleService->getAuthorizationURL());
}
else{
	echo '<a href="?login=google">Login with Google!</a>';
}

exit;
