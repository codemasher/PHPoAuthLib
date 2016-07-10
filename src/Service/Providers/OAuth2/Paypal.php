<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\OAuthException;
use OAuth\Service\OAuth2Service;
use OAuth\Token;

/**
 * @link https://developer.paypal.com/webapps/developer/docs/integration/direct/log-in-with-paypal/detailed/
 */
class Paypal extends OAuth2Service{

	protected $API_BASE              = 'https://api.paypal.com/v1/';
	protected $authorizationEndpoint = 'https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize';
	protected $accessTokenEndpoint   = 'https://api.paypal.com/v1/identity/openidconnect/tokenservice';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_HEADER_BEARER;

	protected function parseAccessTokenResponse($responseBody){
		$data = json_decode($responseBody, true);

		if(null === $data || !is_array($data)){
			throw new OAuthException('Unable to parse response.');
		}
		elseif(isset($data['message'])){
			throw new OAuthException('Error in retrieving token: "'.$data['message'].'"');
		}
		elseif(isset($data['name'])){
			throw new OAuthException('Error in retrieving token: "'.$data['name'].'"');
		}

		$token = new Token(['accessToken' => $data['access_token'], 'expires' => $data['expires_in']]);

		if(isset($data['refresh_token'])){
			$token->refreshToken = $data['refresh_token'];
			unset($data['refresh_token']);
		}

		unset($data['access_token']);
		unset($data['expires_in']);

		$token->extraParams = $data;

		return $token;
	}
}
