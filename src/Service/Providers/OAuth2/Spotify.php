<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Credentials;
use OAuth\Http\HttpClientInterface;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;

/**
 * @link https://developer.spotify.com/web-api/using-scopes/ for scope definitions.
 */
class Spotify extends OAuth2Service{

	protected $API_BASE              = 'https://api.spotify.com/v1/';
	protected $authorizationEndpoint = 'https://accounts.spotify.com/authorize';
	protected $accessTokenEndpoint   = 'https://accounts.spotify.com/api/token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_HEADER_BEARER;
	protected $accessTokenExpires    = true;

	public function __construct(HttpClientInterface $httpClient, TokenStorageInterface $storage, Credentials $credentials, array $scopes = [], $stateParameterInAutUrl = false){
		parent::__construct($httpClient, $storage, $credentials, $scopes, $stateParameterInAutUrl);

		$this->extraOAuthHeaders = ['Authorization' => 'Basic '.base64_encode($this->credentials->key.':'.$this->credentials->secret)];
	}

}
