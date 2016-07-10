<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

class SoundCloud extends OAuth2Service{

	protected $API_BASE              = 'https://api.soundcloud.com/';
	protected $authorizationEndpoint = 'https://soundcloud.com/connect';
	protected $accessTokenEndpoint   = 'https://api.soundcloud.com/oauth2/token';
	protected $accessTokenExpires    = true;

}
