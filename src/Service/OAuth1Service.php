<?php

namespace OAuth\Service;

use OAuth\OAuthException;
use OAuth\Token;

abstract class OAuth1Service extends ServiceAbstract implements OAuth1ServiceInterface{

	protected $requestTokenEndpoint;

	/**
	 * {@inheritDoc}
	 */
	public function getRequestToken(){
		$token = $this->parseRequestTokenResponse(
			$this->httpClient->retrieveResponse(
				$this->requestTokenEndpoint,
				[],
				array_merge(
					[
						'Authorization' => $this->buildAuthorizationHeaderForTokenRequest(),
					], $this->extraOAuthHeaders
				)
			)
		);

		$this->storage->storeAccessToken($this->serviceName, $token);

		return $token;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOauth1AccessToken($token, $verifier, $tokenSecret = null){

		if(!$tokenSecret){
			$tokenSecret = $this->storage->retrieveAccessToken($this->serviceName)->requestTokenSecret;
		}

		$this->tokenSecret = $tokenSecret;

		$bodyParams = [
			'oauth_verifier' => $verifier,
		];

		$token = $this->parseAccessTokenResponse(
			$this->httpClient->retrieveResponse(
				$this->accessTokenEndpoint,
				$bodyParams,
				array_merge(
					[
						'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
							'POST',
							$this->accessTokenEndpoint,
							$this->storage->retrieveAccessToken($this->serviceName),
							$bodyParams
						),
					], $this->extraOAuthHeaders
				)
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
	 * @param string $method                       HTTP method
	 * @param array  $body                         Request body if applicable (key/value pairs)
	 * @param array  $extraHeaders                 Extra headers if applicable.
	 *                                             These will override service-specific any defaults.
	 *
	 * @return string
	 */
	public function apiRequest($path, $method = 'GET', $body = null, array $extraHeaders = []){

		return $this->httpClient->retrieveResponse(
			$this->API_BASE.$path,
			$body,
			array_merge(
				[
					'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $this->API_BASE.$path, $this->storage->retrieveAccessToken($this->serviceName), $body),
				], array_merge($this->extraApiHeaders, $extraHeaders)
			),
			$method
		);
	}

	/**
	 * @param array $parameters
	 *
	 * @return string
	 */
	protected function buildAuthHeader(array $parameters){
		$authorizationHeader = 'OAuth ';
		$delimiter           = '';

		foreach($parameters as $key => $value){
			$authorizationHeader .= $delimiter.rawurlencode($key).'="'.rawurlencode($value).'"';

			$delimiter = ', ';
		}

		return $authorizationHeader;
	}

	/**
	 * Builds the authorization header for getting an access or request token.
	 *
	 * @param array $extraParameters
	 *
	 * @return string
	 */
	protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = []){

		$parameters = array_merge(
			[
				'oauth_callback'         => $this->credentials->callbackURL,
				'oauth_consumer_key'     => $this->credentials->key,
				'oauth_nonce'            => bin2hex(random_bytes(32)),
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp'        => (new \DateTime())->format('U'),
				'oauth_version'          => '1.0',
			], $extraParameters
		);

		$parameters['oauth_signature'] = $this->getSignature($this->requestTokenEndpoint, $parameters, 'POST');

		return $this->buildAuthHeader($parameters);
	}

	/**
	 * Builds the authorization header for an authenticated API request
	 *
	 * @param string       $method
	 * @param string       $url        The uri the request is headed
	 * @param \OAuth\Token $token
	 * @param array        $bodyParams Request body if applicable (key/value pairs)
	 *
	 * @return string
	 */
	protected function buildAuthorizationHeaderForAPIRequest($method, $url, Token $token, $bodyParams = null){

		$this->tokenSecret = $token->accessTokenSecret;

		$parameters = [
			'oauth_consumer_key'     => $this->credentials->key,
			'oauth_nonce'            => bin2hex(random_bytes(32)),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => (new \DateTime())->format('U'),
			'oauth_token'            => $token->accessToken,
			'oauth_version'          => '1.0',
		];

		$signatureParams = is_array($bodyParams)
			? array_merge($parameters, $bodyParams)
			: $parameters;

		$parameters['oauth_signature'] = $this->getSignature($url, $signatureParams, $method);

		if(is_array($bodyParams) && isset($bodyParams['oauth_session_handle'])){
			$parameters['oauth_session_handle'] = $bodyParams['oauth_session_handle'];
			unset($bodyParams['oauth_session_handle']);
		}

		return $this->buildAuthHeader($parameters);
	}

	/**
	 * Parses the request token response and returns a Token.
	 * This is only needed to verify the `oauth_callback_confirmed` parameter. The actual
	 * parsing logic is contained in the access token parser.
	 *
	 * @abstract
	 *
	 * @param string $responseBody
	 *
	 * @return Token
	 *
	 * @throws \OAuth\OAuthException
	 */
	protected function parseRequestTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if(!$data || !is_array($data)){
			throw new OAuthException('Unable to parse response.');
		}
		elseif(!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] !== 'true'){
			throw new OAuthException('Error in retrieving token.');
		}

		return $this->parseAccessTokenResponse($responseBody);
	}

	/**
	 * Parses the access token response and returns a Token.
	 *
	 * @abstract
	 *
	 * @param string $responseBody
	 *
	 * @return \OAuth\Token
	 *
	 * @throws \OAuth\OAuthException
	 */
	protected function parseAccessTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if(!$data || !is_array($data)){
			throw new OAuthException('Unable to parse response: '.$responseBody);
		}
		elseif(isset($data['error'])){
			throw new OAuthException('Error in retrieving token: "'.$data['error'].'"');
		}
		elseif(!isset($data['oauth_token']) || !isset($data['oauth_token_secret'])){
			throw new OAuthException('Invalid response. OAuth Token data not set: '.$responseBody);
		}

		$token = new Token(
			[
				'requestToken'       => $data['oauth_token'],
				'requestTokenSecret' => $data['oauth_token_secret'],
				'accessToken'        => $data['oauth_token'],
				'accessTokenSecret'  => $data['oauth_token_secret'],
				'expires'            => Token::EOL_NEVER_EXPIRES,
			]
		);

		unset($data['oauth_token'], $data['oauth_token_secret']);

		$token->extraParams = $data;

		return $token;
	}

	/**
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 *
	 * @return string
	 */
	public function getSignature($url, array $params, $method = 'POST'){
		parse_str(parse_url($url, PHP_URL_QUERY), $queryStringData);

		$signatureData = array_merge($queryStringData, $params);

		ksort($signatureData);

		$baseString = strtoupper($method).'&'.rawurlencode($url).'&'.rawurlencode(http_build_query($signatureData));
		$signingKey = rawurlencode($this->credentials->secret).'&'.($this->tokenSecret !== null ? rawurlencode($this->tokenSecret) : '');

		return base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));
	}

}
