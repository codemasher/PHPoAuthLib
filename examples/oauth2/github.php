<?php

require_once __DIR__.'/../bootstrap.php';

$gitHubService = new \OAuth\Service\Providers\OAuth2\GitHub(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('GITHUB_KEY'),
		'secret'      => getenv('GITHUB_SECRET'),
		'callbackURL' => getenv('GITHUB_CALLBACK_URL'),
	]),
	['user']
);

if(!empty($_GET['code'])){
	$gitHubService->getOAuth2AccessToken($_GET['code']);

	echo 'result: <pre>'.print_r(json_decode($gitHubService->apiRequest('user/emails')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'github'){
	header('Location: '.$gitHubService->getAuthorizationURL());
}
else{
	echo '<a href="?login=github">Login with Github!</a>';
}

exit;
