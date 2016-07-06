<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class Bitly extends OAuth2Service{

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$scopes = [],
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://api-ssl.bitly.com/v3/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://bitly.com/oauth/authorize');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://api-ssl.bitly.com/oauth/access_token');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getAuthorizationMethod(){
		return static::AUTHORIZATION_METHOD_QUERY_STRING;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
		$data = json_decode($responseBody, true);

		if(null === $data || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(isset($data['error'])){
			throw new TokenResponseException('Error in retrieving token: "'.$data['error'].'"');
		}

		$token = new OAuth2Token();
		$token->setAccessToken($data['access_token']);
		// I'm invincible!!!
		$token->setEndOfLife(OAuth2Token::EOL_NEVER_EXPIRES);
		unset($data['access_token']);

		$token->setExtraParams($data);

		return $token;
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
			$this->getAccessTokenEndpoint(),
			$bodyParams,
			$this->getExtraOAuthHeaders()
		);

		// we can scream what we want that we want bitly to return a json encoded string (format=json), but the
		// WOAH WATCH YOUR LANGUAGE ;) service doesn't seem to like screaming, hence we need to manually
		// parse the result
		$parsedResult = [];
		parse_str($responseBody, $parsedResult);

		$token = $this->parseAccessTokenResponse(json_encode($parsedResult));
		$this->storage->storeAccessToken($this->service(), $token);

		return $token;
	}
}
