<?php

namespace OAuth\Service;

use OAuth\Credentials;
use OAuth\Http\HttpClientInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;

/**
 * Abstract OAuth service, version-agnostic
 */
abstract class ServiceAbstract implements ServiceInterface{

	protected $serviceName;

	protected $API_BASE;
	protected $authorizationEndpoint;
	protected $accessTokenEndpoint;

	protected $extraOAuthHeaders = [];
	protected $extraApiHeaders   = [];

	/** @var Credentials */
	protected $credentials;

	/** @var HttpClientInterface */
	protected $httpClient;

	/** @var TokenStorageInterface */
	protected $storage;

	/**
	 * @var string
	 */
	protected $tokenSecret = null;

	/**
	 * ServiceAbstract constructor.
	 *
	 * @param \OAuth\Http\HttpClientInterface      $httpClient
	 * @param \OAuth\Storage\TokenStorageInterface $storage
	 * @param \OAuth\Credentials                   $credentials
	 */
	public function __construct(HttpClientInterface $httpClient, TokenStorageInterface $storage, Credentials $credentials){
		$this->httpClient  = $httpClient;
		$this->storage     = $storage;
		$this->credentials = $credentials;

		$this->serviceName = (new \ReflectionClass($this))->getShortName();
	}

	/**
	 * @param array $additionalParameters
	 *
	 * @return \OAuth\Http\Uri
	 */
	public function getAuthorizationURL(array $additionalParameters = []){

		$url = $this->authorizationEndpoint;
		$url .= !empty($additionalParameters)
			? '?'.http_build_query($additionalParameters)
			: '';

		return $url;
	}

	/**
	 * Refreshes an access token
	 *
	 * @param  \OAuth\Token $token
	 *
	 * @return \OAuth\Token $token
	 */
	public function refreshAccessToken(Token $token){
		return $token;
	}

}
