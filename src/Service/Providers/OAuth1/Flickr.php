<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;

class Flickr extends OAuth1Service{

	protected $format;

	protected $API_BASE              = 'https://api.flickr.com/services/rest/';
	protected $requestTokenEndpoint  = 'https://www.flickr.com/services/oauth/request_token';
	protected $authorizationEndpoint = 'https://www.flickr.com/services/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://www.flickr.com/services/oauth/access_token';

	public function apiRequest($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$uri = new Uri($this->API_BASE); // todo: Uri
		$uri->addToQuery('method', $path);

		if(!empty($this->format)){
			$uri->addToQuery('format', $this->format);

			if($this->format === 'json'){
				$uri->addToQuery('nojsoncallback', 1);
			}
		}

		$token               = $this->storage->retrieveAccessToken($this->serviceName);
		$extraHeaders        = array_merge($this->extraApiHeaders, $extraHeaders);
		$authorizationHeader = [
			'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body),
		];
		$headers             = array_merge($authorizationHeader, $extraHeaders);

		return $this->httpClient->retrieveResponse($uri, $body, $headers, $method);
	}

	public function requestRest($path, $method = 'GET', $body = null, array $extraHeaders = []){
		return $this->apiRequest($path, $method, $body, $extraHeaders);
	}

	public function requestXmlrpc($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'xmlrpc';

		return $this->apiRequest($path, $method, $body, $extraHeaders);
	}

	public function requestSoap($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'soap';

		return $this->apiRequest($path, $method, $body, $extraHeaders);
	}

	public function requestJson($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'json';

		return $this->apiRequest($path, $method, $body, $extraHeaders);
	}

	public function requestPhp($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'php_serial';

		return $this->apiRequest($path, $method, $body, $extraHeaders);
	}
}
