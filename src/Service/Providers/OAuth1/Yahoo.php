<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;
use OAuth\Token\OAuth1Token;
use OAuth\Token\OAuth1TokenInterface;

class Yahoo extends OAuth1Service{

	protected $API_BASE              = 'https://social.yahooapis.com/v1/';
	protected $requestTokenEndpoint  = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
	protected $authorizationEndpoint = 'https://api.login.yahoo.com/oauth/v2/request_auth';
	protected $accessTokenEndpoint   = 'https://api.login.yahoo.com/oauth/v2/get_token';

	/**
	 * {@inheritdoc}
	 */
	public function refreshAccessToken(OAuth1TokenInterface $token){
		$extraParams = $token->getExtraParams();
		$bodyParams  = ['oauth_session_handle' => $extraParams['oauth_session_handle']];

		$authorizationHeader = [
			'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
				'POST',
				new Uri($this->accessTokenEndpoint),
				$this->storage->retrieveAccessToken($this->service()),
				$bodyParams
			),
		];

		$headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders(), []);

		$responseBody = $this->httpClient->retrieveResponse(new Uri($this->accessTokenEndpoint), $bodyParams, $headers);

		$token = $this->parseAccessTokenResponse($responseBody);
		$this->storage->storeAccessToken($this->service(), $token);

		return $token;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if($data === null || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(isset($data['error'])){
			throw new TokenResponseException('Error in retrieving token: "'.$data['error'].'"');
		}

		$token = new OAuth1Token();

		$token->setRequestToken($data['oauth_token']);
		$token->setRequestTokenSecret($data['oauth_token_secret']);
		$token->setAccessToken($data['oauth_token']);
		$token->setAccessTokenSecret($data['oauth_token_secret']);

		if(isset($data['oauth_expires_in'])){
			$token->setLifetime($data['oauth_expires_in']);
		}
		else{
			$token->setEndOfLife(OAuth1Token::EOL_NEVER_EXPIRES);
		}

		unset($data['oauth_token'], $data['oauth_token_secret']);
		$token->setExtraParams($data);

		return $token;
	}

}
