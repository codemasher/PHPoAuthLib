<?php

namespace OAuthTest\Mocks;

use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Token\OAuth2Token;

class MockOAuth2Service extends OAuth2Service{

	const SCOPE_MOCK   = 'mock';
	const SCOPE_MOCK_2 = 'mock2';

	private $authorizationMethod = null;

	public function getAuthorizationEndpoint(){
		return new Uri('http://pieterhordijk.com/auth');
	}

	public function getAccessTokenEndpoint(){
		return new Uri('http://pieterhordijk.com/access');
	}

	protected function parseAccessTokenResponse($responseBody){
		return new OAuth2Token();
	}

	// this allows us to set different auth methods for tests
	public function setAuthorizationMethod($method){
		$this->authorizationMethod = $method;
	}

	/**
	 * Returns a class constant from ServiceInterface defining the authorization method used for the API
	 * Header is the sane default.
	 *
	 * @return int
	 */
	protected function getAuthorizationMethod(){
		switch($this->authorizationMethod){
			case 'querystring':
				return static::AUTHORIZATION_METHOD_QUERY_STRING;

			case 'querystring2':
				return static::AUTHORIZATION_METHOD_QUERY_STRING_V2;

			case 'bearer':
				return static::AUTHORIZATION_METHOD_HEADER_BEARER;
		}

		return parent::getAuthorizationMethod();
	}
}
