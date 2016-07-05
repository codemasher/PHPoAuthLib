<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Token\OAuth2Token;

class Yahoo extends OAuth2Service{

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://api.login.yahoo.com/oauth2/request_auth');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://api.login.yahoo.com/oauth2/get_token');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getAuthorizationMethod(){
		return static::AUTHORIZATION_METHOD_HEADER_BEARER;
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
		$token->setLifetime($data['expires_in']);

		if(isset($data['refresh_token'])){
			$token->setRefreshToken($data['refresh_token']);
			unset($data['refresh_token']);
		}

		unset($data['access_token']);
		unset($data['expires_in']);

		$token->setExtraParams($data);

		return $token;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getExtraOAuthHeaders(){
		$encodedCredentials = base64_encode(
			$this->credentials->getConsumerId().':'.$this->credentials->getConsumerSecret()
		);

		return ['Authorization' => 'Basic '.$encodedCredentials];
	}
}
