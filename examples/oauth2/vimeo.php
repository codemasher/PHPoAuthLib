<?php

require_once __DIR__.'/../bootstrap.php';

$vimeoService = new \OAuth\Service\Providers\OAuth2\Vimeo(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('VIMEO_KEY'),
		'secret'      => getenv('VIMEO_SECRET'),
		'callbackURL' => getenv('VIMEO_CALLBACK_URL'),
	]),
	['public', 'private']
);


if(!empty($_GET['code'])){
	$token = $vimeoService->getOAuth2AccessToken($_GET['code'], isset($_GET['state']) ? $_GET['state'] : null);

	echo 'result: <pre>'.print_r(json_decode($vimeoService->apiRequest('me')), true).'</pre>';

}
elseif(!empty($_GET['login']) && $_GET['login'] === 'vimeo'){
      header('Location: '.$vimeoService->getAuthorizationURL());
}
else{
	echo '<a href="?login=vimeo">Login with Vimeo!</a>';
}

exit;
