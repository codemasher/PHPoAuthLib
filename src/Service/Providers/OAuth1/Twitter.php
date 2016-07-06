<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\Http\Exception\TokenResponseException;
use OAuth\Service\OAuth1Service;
use OAuth\Token\OAuth1Token;

class Twitter extends OAuth1Service{

	protected $API_BASE              = 'https://api.twitter.com/1.1/';
	protected $requestTokenEndpoint  = 'https://api.twitter.com/oauth/request_token';
	protected $authorizationEndpoint = 'https://api.twitter.com/oauth/authenticate';
	protected $accessTokenEndpoint   = 'https://api.twitter.com/oauth/access_token';

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if($data === null || !is_array($data)){
			throw new TokenResponseException('Unable to parse response: '.$responseBody);
		}
		elseif(isset($data['error'])){
			throw new TokenResponseException('Error in retrieving token: "'.$data['error'].'"');
		}
		elseif(!isset($data['oauth_token']) || !isset($data['oauth_token_secret'])){
			throw new TokenResponseException('Invalid response. OAuth Token data not set: '.$responseBody);
		}

		$token = new OAuth1Token;

		$token->setRequestToken($data['oauth_token']);
		$token->setRequestTokenSecret($data['oauth_token_secret']);
		$token->setAccessToken($data['oauth_token']);
		$token->setAccessTokenSecret($data['oauth_token_secret']);

		$token->setEndOfLife(OAuth1Token::EOL_NEVER_EXPIRES);
		unset($data['oauth_token'], $data['oauth_token_secret']);
		$token->setExtraParams($data);

		return $token;
	}
}
