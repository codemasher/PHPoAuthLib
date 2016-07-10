<?php

require_once __DIR__.'/../bootstrap.php';

$soundcloudService = new \OAuth\Service\Providers\OAuth2\SoundCloud(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('SOUNDCLOUD_KEY'),
		'secret'      => getenv('SOUNDCLOUD_SECRET'),
		'callbackURL' => getenv('SOUNDCLOUD_CALLBACK_URL'),
	])
);

if(!empty($_GET['code'])){
	$soundcloudService->getOAuth2AccessToken($_GET['code']);

	echo 'result: <pre>'.print_r(json_decode($soundcloudService->apiRequest('me.json')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'soundcloud'){
      header('Location: '.$soundcloudService->getAuthorizationURL());
}
else{
	echo '<a href="?login=soundcloud">Login with SoundCloud!</a>';
}

exit;
