<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

class Bitly extends OAuth2Service{

	protected $API_BASE              = 'https://api-ssl.bitly.com/v3/';
	protected $authorizationEndpoint = 'https://bitly.com/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://api-ssl.bitly.com/oauth/access_token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_QUERY_STRING;

	public function getOAuth2AccessToken($code, $state = null){
		if($state !== null){
			$this->validateAuthorizationState($state);
		}

		$bodyParams = [
			'code'          => $code,
			'client_id'     => $this->credentials->key,
			'client_secret' => $this->credentials->secret,
			'redirect_uri'  => $this->credentials->callbackURL,
			'grant_type'    => 'authorization_code',
		];

		$responseBody = $this->httpClient->retrieveResponse(
			$this->accessTokenEndpoint,
			$bodyParams,
			$this->extraOAuthHeaders
		);

		// we can scream what we want that we want bitly to return a json encoded string (format=json), but the
		// WOAH WATCH YOUR LANGUAGE ;) service doesn't seem to like screaming, hence we need to manually
		// parse the result
		$parsedResult = [];
		parse_str($responseBody, $parsedResult);

		$token = $this->parseAccessTokenResponse(json_encode($parsedResult));
		$this->storage->storeAccessToken($this->serviceName, $token);

		return $token;
	}
}
