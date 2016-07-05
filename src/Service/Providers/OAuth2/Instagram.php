<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class Instagram extends OAuth2Service{

	/**
	 * Defined scopes
	 *
	 * @link http://instagram.com/developer/authentication/#scope
	 */
	const SCOPE_BASIC          = 'basic';
	const SCOPE_PUBLIC_CONTENT = 'public_content';
	const SCOPE_COMMENTS       = 'comments';
	const SCOPE_RELATIONSHIPS  = 'relationships';
	const SCOPE_LIKES          = 'likes';
	const SCOPE_FOLLOWER_LIST  = 'follower_list';

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$scopes = [],
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, true);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://api.instagram.com/v1/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://api.instagram.com/oauth/authorize/');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://api.instagram.com/oauth/access_token');
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
		// Instagram tokens evidently never expire...
		$token->setEndOfLife(OAuth2Token::EOL_NEVER_EXPIRES);
		unset($data['access_token']);

		$token->setExtraParams($data);

		return $token;
	}
}
