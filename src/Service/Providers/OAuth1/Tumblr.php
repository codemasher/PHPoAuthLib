<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\Service\OAuth1Service;

class Tumblr extends OAuth1Service{

	protected $API_BASE              = 'https://api.tumblr.com/v2/';
	protected $requestTokenEndpoint  = 'https://www.tumblr.com/oauth/request_token';
	protected $authorizationEndpoint = 'https://www.tumblr.com/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://www.tumblr.com/oauth/access_token';

}
