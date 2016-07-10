<?php

namespace OAuth\Service;

use OAuth\Credentials;
use OAuth\Http\HttpClientInterface;
use OAuth\OAuthException;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;

abstract class OAuth2Service extends ServiceAbstract implements OAuth2ServiceInterface{

	/** @var array */
	protected $scopes;

	/** @var bool */
	protected $stateParameterInAuthUrl;

	protected $authorizationMethod = self::AUTHORIZATION_METHOD_HEADER_OAUTH;
	protected $scopesDelimiter     = ' ';
	protected $accessTokenExpires  = false;

	/**
	 * OAuth2Service constructor.
	 *
	 * @param \OAuth\Http\HttpClientInterface      $httpClient
	 * @param \OAuth\Storage\TokenStorageInterface $storage
	 * @param \OAuth\Credentials                   $credentials
	 * @param array                                $scopes
	 * @param bool                                 $stateParameterInAutUrl
	 *
	 * @throws \OAuth\OAuthException
	 */
	public function __construct(HttpClientInterface $httpClient, TokenStorageInterface $storage, Credentials $credentials, $scopes = [], $stateParameterInAutUrl = false){
		parent::__construct($httpClient, $storage, $credentials);

		$this->scopes                  = $scopes;
		$this->stateParameterInAuthUrl = $stateParameterInAutUrl;
	}

	/**
	 * @param array $additionalParameters
	 *
	 * @return string
	 */
	public function getAuthorizationURL(array $additionalParameters = []){

		$parameters = array_merge(
			$additionalParameters, [
			'type'          => 'web_server',
			'client_id'     => $this->credentials->key,
			'redirect_uri'  => $this->credentials->callbackURL,
			'response_type' => 'code',
			'scope'         => implode($this->scopesDelimiter, $this->scopes),
		]
		);

		if($this->stateParameterInAuthUrl){

			if(!isset($parameters['state'])){
				$parameters['state'] = sha1(random_bytes(256));
			}

			$this->storage->storeAuthorizationState($this->serviceName, $parameters['state']);
		}

		return $this->authorizationEndpoint.'?'.http_build_query($parameters);
	}

	/**
	 * @param string $code
	 * @param null   $state
	 *
	 * @return \OAuth\Token
	 * @throws \OAuth\OAuthException
	 */
	public function getOAuth2AccessToken($code, $state = null){

		if(!is_null($state)){
			$this->validateAuthorizationState($state);
		}

		$token = $this->parseAccessTokenResponse(
			$this->httpClient->retrieveResponse(
				$this->accessTokenEndpoint, [
				'code'          => $code,
				'client_id'     => $this->credentials->key,
				'client_secret' => $this->credentials->secret,
				'redirect_uri'  => $this->credentials->callbackURL,
				'grant_type'    => 'authorization_code',
			], $this->extraOAuthHeaders
			)
		);

		$this->storage->storeAccessToken($this->serviceName, $token);

		return $token;
	}

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
	 *
	 * @param string $path
	 * @param string $method                    HTTP method
	 * @param array  $body                      Request body if applicable.
	 * @param array  $extraHeaders              Extra headers if applicable. These will override service-specific
	 *                                          any defaults.
	 *
	 * @return string
	 *
	 * @throws \OAuth\OAuthException
	 */
	public function apiRequest($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$token = $this->storage->retrieveAccessToken($this->serviceName);

		if($token->isExpired()){
			throw new OAuthException(sprintf('Token expired on %s at %s', date('m/d/Y', $token->expires), date('h:i:s A', $token->expires)));
		}

		parse_str(parse_url($this->API_BASE.$path, PHP_URL_QUERY), $query);

		if(array_key_exists($this->authorizationMethod, self::AUTH_METHODS_QUERY)){
			$query[self::AUTH_METHODS_QUERY[$this->authorizationMethod]] = $token->accessToken;
		}
		elseif(array_key_exists($this->authorizationMethod, self::AUTH_METHODS_HEADER)){
			$extraHeaders = array_merge(['Authorization' => self::AUTH_METHODS_HEADER[$this->authorizationMethod].$token->accessToken], $extraHeaders);
		}
		else{
			throw new OAuthException('invalid auth type');
		}

		$url = $this->API_BASE.$path;

		if(!empty($query)){
			$url .= '?'.http_build_query($query);
		}

		return $this->httpClient->retrieveResponse($url, $body, array_merge($this->extraApiHeaders, $extraHeaders), $method);
	}

	/**
	 * Refreshes an OAuth2 access token.
	 *
	 * @param \OAuth\Token $token
	 *
	 * @return \OAuth\Token $token
	 *
	 * @throws \OAuth\OAuthException
	 */
	public function refreshAccessToken(Token $token){
		$refreshToken = $token->refreshToken;

		if(empty($refreshToken)){
			throw new OAuthException('refresh token missing');
		}

		$token = $this->parseAccessTokenResponse(
			$this->httpClient->retrieveResponse(
				$this->accessTokenEndpoint, [
				'grant_type'    => 'refresh_token',
				'type'          => 'web_server',
				'client_id'     => $this->credentials->key,
				'client_secret' => $this->credentials->secret,
				'refresh_token' => $refreshToken,
			], $this->extraOAuthHeaders
			)
		);

		$this->storage->storeAccessToken($this->serviceName, $token);

		return $token;
	}

	/**
	 * Validates the authorization state against a given one
	 *
	 * @param string $state
	 *
	 * @throws \OAuth\OAuthException
	 */
	protected function validateAuthorizationState($state){
		if($this->storage->retrieveAuthorizationState($this->serviceName) !== $state){
			throw new OAuthException('invalid authorization state');
		}
	}

	/**
	 * Parses the access token response and returns a Token.
	 *
	 * @abstract
	 *
	 * @param string $responseBody
	 *
	 * @return \OAuth\Token
	 * @throws \OAuth\OAuthException
	 */
	protected function parseAccessTokenResponse($responseBody){
		$data = json_decode($responseBody, true);

		if(!$data || !is_array($data)){
			throw new OAuthException('TokenResponse: Unable to parse response.');
		}
		elseif(isset($data['error_description'])){
			throw new OAuthException('TokenResponse: Error in retrieving token: "'.$data['error_description'].'"');
		}
		elseif(isset($data['error'])){
			throw new OAuthException('TokenResponse: Error in retrieving token: "'.$data['error'].'"');
		}

		$token = new Token(['accessToken' => $data['access_token']]);

		if($this->accessTokenExpires){
			if(isset($data['expires_in'])){
				$token->expires = $data['expires_in'];
				unset($data['expires_in']);
			}

			if(isset($data['refresh_token'])){
				$token->refreshToken = $data['refresh_token'];
				unset($data['refresh_token']);
			}
		}
		else{
			$token->expires = Token::EOL_NEVER_EXPIRES;
		}

		unset($data['access_token']);

		$token->extraParams = $data;

		return $token;
	}

}
