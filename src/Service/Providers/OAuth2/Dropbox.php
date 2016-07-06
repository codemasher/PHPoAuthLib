<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

/**
 * Dropbox service.
 *
 * @author Flávio Heleno <flaviohbatista@gmail.com>
 * @link   https://www.dropbox.com/developers/core/docs
 */
class Dropbox extends OAuth2Service{

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$scopes = [],
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://api.dropbox.com/1/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationUri(array $additionalParameters = []){
		$parameters = array_merge(
			$additionalParameters,
			[
				'client_id'     => $this->key,
				'redirect_uri'  => $this->callbackURL,
				'response_type' => 'code',
			]
		);

		$parameters['scope'] = implode(' ', $this->scopes);

		// Build the url
		$url = clone $this->getAuthorizationEndpoint();
		foreach($parameters as $key => $val){
			$url->addToQuery($key, $val);
		}

		return $url;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://www.dropbox.com/1/oauth2/authorize');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://api.dropbox.com/1/oauth2/token');
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

		if(isset($data['refresh_token'])){
			$token->setRefreshToken($data['refresh_token']);
			unset($data['refresh_token']);
		}

		unset($data['access_token']);

		$token->setExtraParams($data);

		return $token;
	}
}
