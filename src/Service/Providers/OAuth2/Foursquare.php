<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class Foursquare extends OAuth2Service{

	private $apiVersionDate = '20130829';

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$scopes = [],
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://api.foursquare.com/v2/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://foursquare.com/oauth2/authenticate');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://foursquare.com/oauth2/access_token');
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
		$uri->addToQuery('v', $this->apiVersionDate);

		return parent::request($uri, $method, $body, $extraHeaders);
	}
}
