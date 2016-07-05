<?php

namespace OAuthTest\Mocks;

use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;
use OAuth\Token\OAuth1Token;

class MockOAuth1Service extends OAuth1Service{

	public function getRequestTokenEndpoint(){
		return new Uri('http://pieterhordijk.com/token');
	}

	public function getAuthorizationEndpoint(){
		return new Uri('http://pieterhordijk.com/auth');
	}

	public function getAccessTokenEndpoint(){
		return new Uri('http://pieterhordijk.com/access');
	}

	protected function parseRequestTokenResponse($responseBody){
		return new OAuth1Token();
	}

	protected function parseAccessTokenResponse($responseBody){
		return new OAuth1Token();
	}
}
