<?php

namespace OAuthTest\Mocks;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;

class FakeOAuth1Service extends OAuth1Service{

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		SignatureInterface $signature,
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequestTokenEndpoint(){
	}

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
	protected function parseRequestTokenResponse($responseBody){
	}

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
	}
}
