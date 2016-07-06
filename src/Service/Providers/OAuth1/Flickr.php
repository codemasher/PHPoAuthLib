<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth1Token;

class Flickr extends \OAuth\Service\OAuth1Service{

	protected $format;

	public function __construct(
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		SignatureInterface $signature,
		$callbackURL, $key, $secret,
		Uri $baseApiUri = null
	){
		parent::__construct($httpClient, $storage, $signature, $callbackURL, $key, $secret, $baseApiUri);
		if($baseApiUri === null){
			$this->baseApiUri = new Uri('https://api.flickr.com/services/rest/');
		}
	}

	public function getRequestTokenEndpoint(){
		return new Uri('https://www.flickr.com/services/oauth/request_token');
	}

	public function getAuthorizationEndpoint(){
		return new Uri('https://www.flickr.com/services/oauth/authorize');
	}

	public function getAccessTokenEndpoint(){
		return new Uri('https://www.flickr.com/services/oauth/access_token');
	}

	protected function parseRequestTokenResponse($responseBody){
		parse_str($responseBody, $data);
		if(null === $data || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] != 'true'){
			throw new TokenResponseException('Error in retrieving token.');
		}

		return $this->parseAccessTokenResponse($responseBody);
	}

	protected function parseAccessTokenResponse($responseBody){
		parse_str($responseBody, $data);
		if($data === null || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(isset($data['error'])){
			throw new TokenResponseException('Error in retrieving token: "'.$data['error'].'"');
		}

		$token = new OAuth1Token();
		$token->setRequestToken($data['oauth_token']);
		$token->setRequestTokenSecret($data['oauth_token_secret']);
		$token->setAccessToken($data['oauth_token']);
		$token->setAccessTokenSecret($data['oauth_token_secret']);
		$token->setEndOfLife(OAuth1Token::EOL_NEVER_EXPIRES);
		unset($data['oauth_token'], $data['oauth_token_secret']);
		$token->setExtraParams($data);

		return $token;
	}

	public function request($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$uri = $this->determineRequestUriFromPath('/', $this->baseApiUri);
		$uri->addToQuery('method', $path);

		if(!empty($this->format)){
			$uri->addToQuery('format', $this->format);

			if($this->format === 'json'){
				$uri->addToQuery('nojsoncallback', 1);
			}
		}

		$token               = $this->storage->retrieveAccessToken($this->service());
		$extraHeaders        = array_merge($this->getExtraApiHeaders(), $extraHeaders);
		$authorizationHeader = [
			'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body),
		];
		$headers             = array_merge($authorizationHeader, $extraHeaders);

		return $this->httpClient->retrieveResponse($uri, $body, $headers, $method);
	}

	public function requestRest($path, $method = 'GET', $body = null, array $extraHeaders = []){
		return $this->request($path, $method, $body, $extraHeaders);
	}

	public function requestXmlrpc($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'xmlrpc';

		return $this->request($path, $method, $body, $extraHeaders);
	}

	public function requestSoap($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'soap';

		return $this->request($path, $method, $body, $extraHeaders);
	}

	public function requestJson($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'json';

		return $this->request($path, $method, $body, $extraHeaders);
	}

	public function requestPhp($path, $method = 'GET', $body = null, array $extraHeaders = []){
		$this->format = 'php_serial';

		return $this->request($path, $method, $body, $extraHeaders);
	}
}
