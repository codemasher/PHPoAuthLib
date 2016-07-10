<?php

namespace OAuthTest\Mocks;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;

class FakeOAuth1Service extends OAuth1Service{

	public function __construct(
		HttpClientInterface $httpClient,
		TokenStorageInterface $storage,
		SignatureInterface $signature,
		$callbackURL, $key, $secret,
		Uri $baseApiUri = null
	){
		parent::__construct($httpClient, $storage, $signature,
			$callbackURL, $key, $secret, $baseApiUri);
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
