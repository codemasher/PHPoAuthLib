<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * @link http://instagram.com/developer/authentication/#scope for scope definitions.
 */
class Instagram extends OAuth2Service{

	protected $API_BASE              = 'https://api.instagram.com/v1/';
	protected $authorizationEndpoint = 'https://api.instagram.com/oauth/authorize/';
	protected $accessTokenEndpoint   = 'https://api.instagram.com/oauth/access_token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_QUERY_STRING;

}
