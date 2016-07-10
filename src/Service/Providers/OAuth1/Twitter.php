<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\Service\OAuth1Service;

class Twitter extends OAuth1Service{

	protected $API_BASE              = 'https://api.twitter.com/1.1/';
	protected $requestTokenEndpoint  = 'https://api.twitter.com/oauth/request_token';
	protected $authorizationEndpoint = 'https://api.twitter.com/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://api.twitter.com/oauth/access_token';

}
