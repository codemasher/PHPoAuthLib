<?php

require_once __DIR__.'/../bootstrap.php';

$foursquareService = new \OAuth\Service\Providers\OAuth2\Foursquare(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('FOURSQUARE_KEY'),
		'secret'      => getenv('FOURSQUARE_SECRET'),
		'callbackURL' => getenv('FOURSQUARE_CALLBACK_URL'),
	])
);

if(!empty($_GET['code'])){
	$foursquareService->getOAuth2AccessToken($_GET['code']);

	echo 'result: <pre>'.print_r(json_decode($foursquareService->apiRequest('users/self')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'foursquare'){
      header('Location: '.$foursquareService->getAuthorizationURL());
}
else{
	echo '<a href="?login=foursquare">Login with Foursquare!</a>';
}

exit;
