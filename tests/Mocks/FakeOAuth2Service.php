<?php

namespace OAuthTest\Mocks;

use OAuth\Service\OAuth2Service;

class FakeOAuth2Service extends OAuth2Service{

	const SCOPE_FOO    = 'https://www.pieterhordijk.com/auth';
	const SCOPE_CUSTOM = 'custom';

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
	}

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
	}
}
