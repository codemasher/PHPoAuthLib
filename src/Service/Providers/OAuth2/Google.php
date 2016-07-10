<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * @link https://developers.google.com/oauthplayground/ for scope definitions.
 */
class Google extends OAuth2Service{

	protected $API_BASE              = 'https://www.googleapis.com/oauth2/v1/';
	protected $authorizationEndpoint = 'https://accounts.google.com/o/oauth2/auth?access_type=online'; // todo: access type
	protected $accessTokenEndpoint   = 'https://accounts.google.com/o/oauth2/token';
	protected $accessTokenExpires    = true;

}
