<?php

/**
 * Vimeo service.
 *
 * Example of retrieving an authentication token of the vimeo service
 *
 * @author      Pedro Amorim <contact@pamorim.fr>
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        https://developer.vimeo.com/api/authentication
 */

use OAuth\Service\Providers\OAuth2\Vimeo;

require_once __DIR__.'/../bootstrap.php';

$vimeoService = new \OAuth\Service\Providers\OAuth2\Vimeo(
	$httpClient,
	$storage,
	$currentUri->getAbsoluteUri(),
	getenv('VIMEO_KEY'),
	getenv('VIMEO_SECRET'),
	[Vimeo::SCOPE_PUBLIC, Vimeo::SCOPE_PRIVATE]
);


if(!empty($_GET['code'])){
	// retrieve the CSRF state parameter
	$state = isset($_GET['state']) ? $_GET['state'] : null;
	// This was a callback request from vimeo, get the token
	$token = $vimeoService->requestAccessToken($_GET['code'], $state);
	// Send a request now that we have access token
	$result = json_decode($vimeoService->request('/me'));
	// Show some of the resultant data
	echo 'result: <pre>'.print_r($result, true).'</pre>';

}
elseif(!empty($_GET['login']) && $_GET['login'] === 'vimeo'){
      header('Location: '.$vimeoService->getAuthorizationUri());
}
else{
	echo '<a href="'.$currentUri->getRelativeUri().'?login=vimeo">Login with Vimeo!</a>';
}
