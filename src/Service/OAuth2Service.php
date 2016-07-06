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

	/** @const OAUTH_VERSION */
	const OAUTH_VERSION = 2;

	/** @var array */
	protected $scopes;

	/** @var Uri|null */
	protected $baseApiUri;

	/** @var bool */
	protected $stateParameterInAuthUrl;

	/** @var string */
	protected $apiVersion;

	public function __construct(
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$callbackURL, $key, $secret, $scopes = [],
		Uri $baseApiUri = null,
		$stateParameterInAutUrl = false,
		$apiVersion = ''
	){
		parent::__construct($httpClient, $storage, $callbackURL, $key, $secret);
		$this->stateParameterInAuthUrl = $stateParameterInAutUrl;

		foreach($scopes as $scope){
			if(!$this->isValidScope($scope)){
				throw new InvalidScopeException('Scope '.$scope.' is not valid for service '.get_class($this));
			}
		}

		$this->scopes = $scopes;

		$this->baseApiUri = $baseApiUri;

		$this->apiVersion = $apiVersion;
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

		$parameters['scope'] = implode($this->getScopesDelimiter(), $this->scopes);

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
		if(null !== $state){
			$this->validateAuthorizationState($state);
		}

		$bodyParams = [
			'code'          => $code,
			'client_id'     => $this->key,
			'client_secret' => $this->secret,
			'redirect_uri'  => $this->callbackURL,
			'grant_type'    => 'authorization_code',
		];

		$responseBody = $this->httpClient->retrieveResponse(
			new Uri($this->accessTokenEndpoint),
			$bodyParams,
			$this->getExtraOAuthHeaders()
		);

		$token = $this->parseAccessTokenResponse($responseBody);
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

		if($token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES
		   && $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN
		   && time() > $token->getEndOfLife()
		){
			throw new ExpiredTokenException(
				sprintf(
					'Token expired on %s at %s',
					date('m/d/Y', $token->getEndOfLife()),
					date('h:i:s A', $token->getEndOfLife())
				)
			);
		}

		// add the token where it may be needed
		if(self::AUTHORIZATION_METHOD_HEADER_OAUTH === $this->getAuthorizationMethod()){
			$extraHeaders = array_merge(['Authorization' => 'OAuth '.$token->getAccessToken()], $extraHeaders);
		}
		elseif(self::AUTHORIZATION_METHOD_QUERY_STRING === $this->getAuthorizationMethod()){
			$uri->addToQuery('access_token', $token->getAccessToken());
		}
		elseif(self::AUTHORIZATION_METHOD_QUERY_STRING_V2 === $this->getAuthorizationMethod()){
			$uri->addToQuery('oauth2_access_token', $token->getAccessToken());
		}
		elseif(self::AUTHORIZATION_METHOD_QUERY_STRING_V3 === $this->getAuthorizationMethod()){
			$uri->addToQuery('apikey', $token->getAccessToken());
		}
		elseif(self::AUTHORIZATION_METHOD_QUERY_STRING_V4 === $this->getAuthorizationMethod()){
			$uri->addToQuery('auth', $token->getAccessToken());
		}
		elseif(self::AUTHORIZATION_METHOD_HEADER_BEARER === $this->getAuthorizationMethod()){
			$extraHeaders = array_merge(['Authorization' => 'Bearer '.$token->getAccessToken()], $extraHeaders);
		}

		$extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);

		return $this->httpClient->retrieveResponse($uri, $body, $extraHeaders, $method);
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

		$parameters = [
			'grant_type'    => 'refresh_token',
			'type'          => 'web_server',
			'client_id'     => $this->key,
			'client_secret' => $this->secret,
			'refresh_token' => $refreshToken,
		];

		$responseBody = $this->httpClient->retrieveResponse(
			new Uri($this->accessTokenEndpoint),
			$parameters,
			$this->getExtraOAuthHeaders()
		);
		$token        = $this->parseAccessTokenResponse($responseBody);
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
		$reflectionClass = new \ReflectionClass(get_class($this));

		return in_array($scope, $reflectionClass->getConstants(), true);
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
		return md5(rand());
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

	/**
	 * Returns a class constant from ServiceInterface defining the authorization method used for the API
	 * Header is the sane default.
	 *
	 * @return int
	 */
	protected function getAuthorizationMethod(){
		return self::AUTHORIZATION_METHOD_HEADER_OAUTH;
	}

	/**
	 * Returns api version string if is set else retrun empty string
	 *
	 * @return string
	 */
	protected function getApiVersionString(){
		return !(empty($this->apiVersion)) ? "/".$this->apiVersion : "";
	}

	/**
	 * Returns delimiter to scopes in getAuthorizationUri
	 * For services that do not fully respect the Oauth's RFC,
	 * and use scopes with commas as delimiter
	 *
	 * @return string
	 */
	protected function getScopesDelimiter(){
		return ' ';
	}
}
