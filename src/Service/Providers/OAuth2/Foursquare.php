<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

class Foursquare extends OAuth2Service{

	const FOURSQUARE_API_VERSIONDATE = '20160707';

	protected $API_BASE              = 'https://api.foursquare.com/v2/';
	protected $authorizationEndpoint = 'https://foursquare.com/oauth2/authenticate';
	protected $accessTokenEndpoint   = 'https://foursquare.com/oauth2/access_token';

	public function apiRequest($path, $method = 'GET', $body = null, array $extraHeaders = []){
		parse_str(parse_url($this->API_BASE.$path, PHP_URL_QUERY), $query);

		$query['v'] = self::FOURSQUARE_API_VERSIONDATE;

		return parent::apiRequest($this->API_BASE.$path.'?'.http_build_query($query), $method, $body, $extraHeaders);
	}

}
