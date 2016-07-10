<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * @link https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf
 */
class Amazon extends OAuth2Service{

	protected $API_BASE              = 'https://api.amazon.com/';
	protected $authorizationEndpoint = 'https://www.amazon.com/ap/oa';
	protected $accessTokenEndpoint   = 'https://www.amazon.com/ap/oatoken';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_HEADER_BEARER;
	protected $accessTokenExpires    = true;

}
