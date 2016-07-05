<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class DeviantArt extends OAuth2Service{

	/**
	 * DeviantArt www url - used to build dialog urls
	 */
	const WWW_URL = 'https://www.deviantart.com/';

	/**
	 * Defined scopes
	 *
	 * If you don't think this is scary you should not be allowed on the web at all
	 *
	 * @link https://www.deviantart.com/developers/authentication
	 * @link https://www.deviantart.com/developers/http/v1/20150217
	 */
	const SCOPE_FEED       = 'feed';
	const SCOPE_BROWSE     = 'browse';
	const SCOPE_COMMENT    = 'comment.post';
	const SCOPE_STASH      = 'stash';
	const SCOPE_USER       = 'user';
	const SCOPE_USERMANAGE = 'user.manage';

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$scopes = [],
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://www.deviantart.com/api/v1/oauth2/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://www.deviantart.com/oauth2/authorize');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://www.deviantart.com/oauth2/token');
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

		if(isset($data['expires_in'])){
			$token->setLifeTime($data['expires_in']);
		}

		if(isset($data['refresh_token'])){
			$token->setRefreshToken($data['refresh_token']);
			unset($data['refresh_token']);
		}

		unset($data['access_token']);
		unset($data['expires_in']);

		$token->setExtraParams($data);

		return $token;
	}
}
