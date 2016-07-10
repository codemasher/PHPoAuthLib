<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * @link http://developer.github.com/v3/oauth/ for scope definitions.
 */
class GitHub extends OAuth2Service{

	protected $API_BASE              = 'https://api.github.com/';
	protected $authorizationEndpoint = 'https://github.com/login/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://github.com/login/oauth/access_token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_QUERY_STRING;
	protected $scopesDelimiter       = ',';
	protected $extraOAuthHeaders     = ['Accept' => 'application/json'];
	protected $extraApiHeaders       = ['Accept' => 'application/vnd.github.beta+json'];

}
