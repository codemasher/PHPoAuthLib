<?php

namespace OAuth\Service;

use OAuth\Http\ClientInterface;
use OAuth\Http\Uri;
use OAuth\Service\Exception;
use OAuth\Service\Exception\InvalidAuthorizationStateException;
use OAuth\Service\Exception\InvalidScopeException;
use OAuth\Service\Exception\MissingRefreshTokenException;
use OAuth\Service\ServiceAbstract as BaseAbstractService;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\Exception\ExpiredTokenException;
use OAuth\Token\TokenInterface;

abstract class OAuth2Service extends BaseAbstractService implements OAuth2ServiceInterface{

	/** @var array */
	protected $scopes;

	/** @var Uri|null */
	protected $baseApiUri;

	/** @var bool */
	protected $stateParameterInAuthUrl;

	protected $authorizationMethod = self::AUTHORIZATION_METHOD_HEADER_OAUTH;

	protected $scopesDelimiter = ' ';

	public function __construct(ClientInterface $httpClient, TokenStorageInterface $storage, $callbackURL, $key, $secret, $scopes = [], $stateParameterInAutUrl = false){
		parent::__construct($httpClient, $storage, $callbackURL, $key, $secret);

		$this->stateParameterInAuthUrl = $stateParameterInAutUrl;
		$this->scopes = $scopes;
		$this->baseApiUri = new Uri($this->API_BASE);

		foreach($this->scopes as $scope){
			if(!$this->isValidScope($scope)){
				throw new InvalidScopeException('Scope '.$scope.' is not valid for service '.get_class($this));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationUri(array $additionalParameters = []){
		$parameters = array_merge(
			$additionalParameters,
			[
				'type'          => 'web_server',
				'client_id'     => $this->key,
				'redirect_uri'  => $this->callbackURL,
				'response_type' => 'code',
			]
		);

		$parameters['scope'] = implode($this->scopesDelimiter, $this->scopes);

		if($this->needsStateParameterInAuthUrl()){
			if(!isset($parameters['state'])){
				$parameters['state'] = $this->generateAuthorizationState();
			}
			$this->storeAuthorizationState($parameters['state']);
		}

		// Build the url
		$url = new Uri($this->authorizationEndpoint);

		foreach($parameters as $key => $val){
			$url->addToQuery($key, $val);
		}

		return $url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function requestAccessToken($code, $state = null){

		if(!is_null($state)){
			$this->validateAuthorizationState($state);
		}

		$token = $this->parseAccessTokenResponse($this->httpClient->retrieveResponse(
			new Uri($this->accessTokenEndpoint),
			[
				'code'          => $code,
				'client_id'     => $this->key,
				'client_secret' => $this->secret,
				'redirect_uri'  => $this->callbackURL,
				'grant_type'    => 'authorization_code',
			],
			$this->getExtraOAuthHeaders()
		));

		$this->storage->storeAccessToken($this->service(), $token);

		return $token;
	}

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
	 *
	 * @param string|Uri $path
	 * @param string     $method                HTTP method
	 * @param array      $body                  Request body if applicable.
	 * @param array      $extraHeaders          Extra headers if applicable. These will override service-specific
	 *                                          any defaults.
	 *
	 * @return string
	 *
	 * @throws ExpiredTokenException
	 * @throws Exception
	 */
	public function request($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$uri   = $this->determineRequestUriFromPath($path, $this->baseApiUri);
		$token = $this->storage->retrieveAccessToken($this->service());

		if($token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES && $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN && time() > $token->getEndOfLife()){
			throw new ExpiredTokenException(sprintf('Token expired on %s at %s', date('m/d/Y', $token->getEndOfLife()), date('h:i:s A', $token->getEndOfLife())));
		}

		// add the token where it may be needed
		switch($this->authorizationMethod){
			case self::AUTHORIZATION_METHOD_HEADER_OAUTH:
				$extraHeaders = array_merge(['Authorization' => 'OAuth '.$token->getAccessToken()], $extraHeaders);
				break;
			case self::AUTHORIZATION_METHOD_QUERY_STRING:
				$uri->addToQuery('access_token', $token->getAccessToken());
				break;
			case self::AUTHORIZATION_METHOD_QUERY_STRING_V2:
				$uri->addToQuery('oauth2_access_token', $token->getAccessToken());
				break;
			case self::AUTHORIZATION_METHOD_QUERY_STRING_V3:
				$uri->addToQuery('apikey', $token->getAccessToken());
				break;
			case self::AUTHORIZATION_METHOD_QUERY_STRING_V4:
				$uri->addToQuery('auth', $token->getAccessToken());
				break;
			case self::AUTHORIZATION_METHOD_HEADER_BEARER:
				$extraHeaders = array_merge(['Authorization' => 'Bearer '.$token->getAccessToken()], $extraHeaders);
				break;
		}

		return $this->httpClient->retrieveResponse($uri, $body, array_merge($this->getExtraApiHeaders(), $extraHeaders), $method);
	}

	/**
	 * Accessor to the storage adapter to be able to retrieve tokens
	 *
	 * @return TokenStorageInterface
	 */
	public function getStorage(){
		return $this->storage;
	}

	/**
	 * Refreshes an OAuth2 access token.
	 *
	 * @param TokenInterface $token
	 *
	 * @return TokenInterface $token
	 *
	 * @throws MissingRefreshTokenException
	 */
	public function refreshAccessToken(TokenInterface $token){
		$refreshToken = $token->getRefreshToken();

		if(empty($refreshToken)){
			throw new MissingRefreshTokenException();
		}

		$token = $this->parseAccessTokenResponse($this->httpClient->retrieveResponse(
			new Uri($this->accessTokenEndpoint),
			[
				'grant_type'    => 'refresh_token',
				'type'          => 'web_server',
				'client_id'     => $this->key,
				'client_secret' => $this->secret,
				'refresh_token' => $refreshToken,
			],
			$this->getExtraOAuthHeaders()
		));

		$this->storage->storeAccessToken($this->service(), $token);

		return $token;
	}

	/**
	 * Return whether or not the passed scope value is valid.
	 *
	 * @param string $scope
	 *
	 * @return bool
	 */
	public function isValidScope($scope){
		return in_array($scope, (new \ReflectionClass(get_class($this)))->getConstants(), true);
	}

	/**
	 * Check if the given service need to generate a unique state token to build the authorization url
	 *
	 * @return bool
	 */
	public function needsStateParameterInAuthUrl(){
		return $this->stateParameterInAuthUrl;
	}

	/**
	 * Validates the authorization state against a given one
	 *
	 * @param string $state
	 *
	 * @throws InvalidAuthorizationStateException
	 */
	protected function validateAuthorizationState($state){
		if($this->retrieveAuthorizationState() !== $state){
			throw new InvalidAuthorizationStateException();
		}
	}

	/**
	 * Generates a random string to be used as state
	 *
	 * @return string
	 */
	protected function generateAuthorizationState(){
		return md5(random_bytes(256));
	}

	/**
	 * Retrieves the authorization state for the current service
	 *
	 * @return string
	 */
	protected function retrieveAuthorizationState(){
		return $this->storage->retrieveAuthorizationState($this->service());
	}

	/**
	 * Stores a given authorization state into the storage
	 *
	 * @param string $state
	 */
	protected function storeAuthorizationState($state){
		$this->storage->storeAuthorizationState($this->service(), $state);
	}

	/**
	 * Return any additional headers always needed for this service implementation's OAuth calls.
	 *
	 * @return array
	 */
	protected function getExtraOAuthHeaders(){
		return [];
	}

	/**
	 * Return any additional headers always needed for this service implementation's API calls.
	 *
	 * @return array
	 */
	protected function getExtraApiHeaders(){
		return [];
	}

	/**
	 * Parses the access token response and returns a OAuth2TokenInterface.
	 *
	 * @abstract
	 *
	 * @param string $responseBody
	 *
	 * @return TokenInterface
	 *
	 * @throws \OAuth\Http\Exception\TokenResponseException
	 */
	abstract protected function parseAccessTokenResponse($responseBody);

}
