<?php

require_once __DIR__.'/../bootstrap.php';

use OAuth\Service\Providers\OAuth2\Instagram;

$instagramService = new Instagram(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('INSTAGRAM_KEY'),
		'secret'      => getenv('INSTAGRAM_SECRET'),
		'callbackURL' => getenv('INSTAGRAM_CALLBACK_URL'),
	]),
	['basic', 'comments', 'relationships', 'likes']
);


if(!empty($_GET['code'])){
	$instagramService->getOAuth2AccessToken($_GET['code'], isset($_GET['state']) ? $_GET['state'] : null);

	echo 'result: <pre>'.print_r(json_decode($instagramService->apiRequest('users/self')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'instagram'){
	header('Location: '.$instagramService->getAuthorizationURL());
}
else{
	echo '<a href="?login=instagram">Login with Instagram!</a>';
}

exit;
