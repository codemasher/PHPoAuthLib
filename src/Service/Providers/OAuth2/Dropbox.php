<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * Dropbox service.
 *
 * @author Flávio Heleno <flaviohbatista@gmail.com>
 * @link   https://www.dropbox.com/developers/core/docs
 */
class Dropbox extends OAuth2Service{

	protected $API_BASE              = 'https://api.dropbox.com/1/';
	protected $authorizationEndpoint = 'https://www.dropbox.com/1/oauth2/authorize';
	protected $accessTokenEndpoint   = 'https://api.dropbox.com/1/oauth2/token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_QUERY_STRING;
	protected $accessTokenExpires    = true;

	public function getAuthorizationURL(array $additionalParameters = []){

		$parameters = array_merge(
			$additionalParameters, [
			'client_id'     => $this->credentials->key,
			'redirect_uri'  => $this->credentials->callbackURL,
			'response_type' => 'code',
			'scope'         => implode(' ', $this->scopes),
		]
		);

		return $this->authorizationEndpoint.'?'.http_build_query($parameters);
	}

}
