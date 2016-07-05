<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class Vkontakte extends OAuth2Service{

	/**
	 * Defined scopes
	 *
	 * @link http://vk.com/dev/permissions
	 */
	const SCOPE_EMAIL         = 'email';
	const SCOPE_NOTIFY        = 'notify';
	const SCOPE_FRIENDS       = 'friends';
	const SCOPE_PHOTOS        = 'photos';
	const SCOPE_AUDIO         = 'audio';
	const SCOPE_VIDEO         = 'video';
	const SCOPE_DOCS          = 'docs';
	const SCOPE_NOTES         = 'notes';
	const SCOPE_PAGES         = 'pages';
	const SCOPE_APP_LINK      = '';
	const SCOPE_STATUS        = 'status';
	const SCOPE_OFFERS        = 'offers';
	const SCOPE_QUESTIONS     = 'questions';
	const SCOPE_WALL          = 'wall';
	const SCOPE_GROUPS        = 'groups';
	const SCOPE_MESSAGES      = 'messages';
	const SCOPE_NOTIFICATIONS = 'notifications';
	const SCOPE_STATS         = 'stats';
	const SCOPE_ADS           = 'ads';
	const SCOPE_OFFLINE       = 'offline';
	const SCOPE_NOHTTPS       = 'nohttps';

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		$scopes = [],
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://api.vk.com/method/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://oauth.vk.com/authorize');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://oauth.vk.com/access_token');
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
		$token->setLifeTime($data['expires_in']);

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
	protected function getAuthorizationMethod(){
		return static::AUTHORIZATION_METHOD_QUERY_STRING;
	}
}
