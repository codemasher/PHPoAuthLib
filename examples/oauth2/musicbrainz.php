<?php

$musicbrainzService = new \OAuth\Service\Providers\OAuth2\MusicBrainz(
	$httpClient,
	$storage,
	new \OAuth\Credentials([
		'key'         => getenv('MUSICBRAINZ_KEY'),
		'secret'      => getenv('MUSICBRAINZ_SECRET'),
		'callbackURL' => getenv('MUSICBRAINZ_CALLBACK_URL'),
	]),
	['profile', 'email']
);

if(!empty($_GET['code'])){
	$musicbrainzService->getOAuth2AccessToken($_GET['code'], isset($_GET['state']) ? $_GET['state'] : null);

	echo 'result: <pre>'.print_r(json_decode($musicbrainzService->apiRequest('user/emails')), true).'</pre>';
}
elseif(!empty($_GET['login']) && $_GET['login'] === 'musicbrainz'){
	header('Location: '.$musicbrainzService->getAuthorizationURL());
}
else{
	echo '<a href="?login=musicbrainz">Login with MusicBrainz!</a>';
}

exit;
