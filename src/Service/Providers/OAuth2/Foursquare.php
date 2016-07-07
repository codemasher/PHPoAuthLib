<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Http\Exception\TokenResponseException;
use OAuth\Service\OAuth2Service;
use OAuth\Token\OAuth2Token;

class Foursquare extends OAuth2Service{

	const FOURSQUARE_API_VERSIONDATE = '20160707';

	protected $API_BASE = 'https://api.foursquare.com/v2/';
	protected $authorizationEndpoint = 'https://foursquare.com/oauth2/authenticate';
	protected $accessTokenEndpoint   = 'https://foursquare.com/oauth2/access_token';

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
		// Foursquare tokens evidently never expire...
		$token->setEndOfLife(OAuth2Token::EOL_NEVER_EXPIRES);
		unset($data['access_token']);

		$token->setExtraParams($data);

		return $token;
	}

	/**
	 * {@inheritdoc}
	 */
	public function request($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);
		$uri->addToQuery('v', self::FOURSQUARE_API_VERSIONDATE);

		return parent::request($uri, $method, $body, $extraHeaders);
	}
}
