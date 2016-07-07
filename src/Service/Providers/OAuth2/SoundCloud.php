<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class SoundCloud extends OAuth2Service{

	protected $API_BASE = 'https://api.soundcloud.com/';
	protected $authorizationEndpoint = 'https://soundcloud.com/connect';
	protected $accessTokenEndpoint   = 'https://api.soundcloud.com/oauth2/token';

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
		$data = json_decode($responseBody, true);

		if(!$data || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(isset($data['error'])){
			throw new TokenResponseException('Error in retrieving token: "'.$data['error'].'"');
		}

		$token = new OAuth2Token();
		$token->setAccessToken($data['access_token']);

		if(isset($data['expires_in'])){
			$token->setLifetime($data['expires_in']);
			unset($data['expires_in']);
		}

		if(isset($data['refresh_token'])){
			$token->setRefreshToken($data['refresh_token']);
			unset($data['refresh_token']);
		}

		unset($data['access_token']);

		$token->setExtraParams($data);

		return $token;
	}
}
