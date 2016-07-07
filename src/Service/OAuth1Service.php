<?php

namespace OAuth\Service;

use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth1TokenInterface;

abstract class OAuth1Service extends ServiceAbstract implements OAuth1ServiceInterface{

	/** @var SignatureInterface */
	protected $signature;

	/** @var Uri */
	protected $baseApiUri;

	protected $requestTokenEndpoint;

	/**
	 * {@inheritDoc}
	 */
	public function __construct(ClientInterface $httpClient, TokenStorageInterface $storage, $callbackURL, $key, $secret){

		parent::__construct($httpClient, $storage, $callbackURL, $key, $secret);

		$this->baseApiUri = new Uri($this->API_BASE);
		$this->signature = new Signature($secret);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRequestToken(){
		$token = $this->parseRequestTokenResponse($this->httpClient->retrieveResponse(
			new Uri($this->requestTokenEndpoint),
			[],
			array_merge(['Authorization' => $this->buildAuthorizationHeaderForTokenRequest()], $this->getExtraOAuthHeaders())
		));

		$this->storage->storeAccessToken($this->service(), $token);

		return $token;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationUri(array $additionalParameters = []){
		// Build the url
		$url = new Uri($this->authorizationEndpoint);

		foreach($additionalParameters as $key => $val){
			$url->addToQuery($key, $val);
		}

		return $url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAccessToken($token, $verifier, $tokenSecret = null){

		if(!$tokenSecret){
			$tokenSecret = $this->storage->retrieveAccessToken($this->service())->getRequestTokenSecret();
		}

		$this->signature->setTokenSecret($tokenSecret);

		$bodyParams = [
			'oauth_verifier' => $verifier,
		];

		$token = $this->parseAccessTokenResponse($this->httpClient->retrieveResponse(
			new Uri($this->accessTokenEndpoint),
			$bodyParams,
			array_merge([
				'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
					'POST',
					new Uri($this->accessTokenEndpoint),
					$this->storage->retrieveAccessToken($this->service()),
					$bodyParams
				),
			], $this->getExtraOAuthHeaders())
		));

		$this->storage->storeAccessToken($this->service(), $token);

		return $token;
	}

	/**
	 * Refreshes an OAuth1 access token
	 *
	 * @param  OAuth1TokenInterface $token
	 *
	 * @return OAuth1TokenInterface $token
	 */
	public function refreshAccessToken(OAuth1TokenInterface $token){
		return $token;
	}

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
	 *
	 * @param string|\OAuth\Http\Uri $path
	 * @param string                 $method       HTTP method
	 * @param array                  $body         Request body if applicable (key/value pairs)
	 * @param array                  $extraHeaders Extra headers if applicable.
	 *                                             These will override service-specific any defaults.
	 *
	 * @return string
	 */
	public function request($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);

		return $this->httpClient->retrieveResponse(
			$uri,
			$body,
			array_merge(
				['Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $this->storage->retrieveAccessToken($this->service()), $body)],
				array_merge($this->getExtraApiHeaders(), $extraHeaders)
			),
			$method
		);
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
	 * Builds the authorization header for getting an access or request token.
	 *
	 * @param array $extraParameters
	 *
	 * @return string
	 */
	protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = []){
		$parameters = array_merge($this->getBasicAuthorizationHeaderInfo(), $extraParameters);

		$parameters['oauth_signature'] = $this->signature->getSignature(new Uri($this->requestTokenEndpoint), $parameters, 'POST');

		$authorizationHeader = 'OAuth ';
		$delimiter = '';
		foreach($parameters as $key => $value){
			$authorizationHeader .= $delimiter.rawurlencode($key).'="'.rawurlencode($value).'"';

			$delimiter = ', ';
		}

		return $authorizationHeader;
	}

	/**
	 * Builds the authorization header for an authenticated API request
	 *
	 * @param string               $method
	 * @param Uri                  $uri        The uri the request is headed
	 * @param OAuth1TokenInterface $token
	 * @param array                $bodyParams Request body if applicable (key/value pairs)
	 *
	 * @return string
	 */
	protected function buildAuthorizationHeaderForAPIRequest($method, Uri $uri, OAuth1TokenInterface $token, $bodyParams = null){

		$this->signature->setTokenSecret($token->getAccessTokenSecret());
		$authParameters = $this->getBasicAuthorizationHeaderInfo();

		if(isset($authParameters['oauth_callback'])){
			unset($authParameters['oauth_callback']);
		}

		$authParameters = array_merge($authParameters, ['oauth_token' => $token->getAccessToken()]);

		$signatureParams = (is_array($bodyParams)) ? array_merge($authParameters, $bodyParams) : $authParameters;
		$authParameters['oauth_signature'] = $this->signature->getSignature($uri, $signatureParams, $method);

		if(is_array($bodyParams) && isset($bodyParams['oauth_session_handle'])){
			$authParameters['oauth_session_handle'] = $bodyParams['oauth_session_handle'];
			unset($bodyParams['oauth_session_handle']);
		}

		$authorizationHeader = 'OAuth ';
		$delimiter = '';

		foreach($authParameters as $key => $value){
			$authorizationHeader .= $delimiter.rawurlencode($key).'="'.rawurlencode($value).'"';
			$delimiter = ', ';
		}

		return $authorizationHeader;
	}

	/**
	 * Builds the authorization header array.
	 *
	 * @return array
	 */
	protected function getBasicAuthorizationHeaderInfo(){
		return [
			'oauth_callback'         => $this->callbackURL,
			'oauth_consumer_key'     => $this->key,
			'oauth_nonce'            => bin2hex(random_bytes(32)),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => (new \DateTime())->format('U'),
			'oauth_version'          => '1.0',
		];
	}

	/**
	 * Parses the request token response and returns a OAuth1TokenInterface.
	 * This is only needed to verify the `oauth_callback_confirmed` parameter. The actual
	 * parsing logic is contained in the access token parser.
	 *
	 * @abstract
	 *
	 * @param string $responseBody
	 *
	 * @return OAuth1TokenInterface
	 *
	 * @throws \OAuth\Http\Exception\TokenResponseException
	 */
	protected function parseRequestTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if(!$data || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] !== 'true'){
			throw new TokenResponseException('Error in retrieving token.');
		}

		return $this->parseAccessTokenResponse($responseBody);
	}

	/**
	 * Parses the access token response and returns a OAuth1TokenInterface.
	 *
	 * @abstract
	 *
	 * @param string $responseBody
	 *
	 * @return OAuth1TokenInterface
	 *
	 * @throws \OAuth\Http\Exception\TokenResponseException
	 */
	abstract protected function parseAccessTokenResponse($responseBody);
}
