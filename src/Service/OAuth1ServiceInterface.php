<?php

namespace OAuth\Service;

/**
 * Defines the common methods across OAuth 1 services.
 */
interface OAuth1ServiceInterface extends ServiceInterface{

	/** @const OAUTH_VERSION */
	const OAUTH_VERSION = 1;

	/**
	 * Retrieves and stores/returns the OAuth1 request token obtained from the service.
	 *
	 * @return string $requestToken
	 *
	 * @throws \OAuth\OAuthException
	 */
	public function getRequestToken();

	/**
	 * Retrieves and stores/returns the OAuth1 access token after a successful authorization.
	 *
	 * @param string $token The request token from the callback.
	 * @param string $verifier
	 * @param string $tokenSecret
	 *
	 * @return string $accessToken
	 *
	 * @throws \OAuth\OAuthException
	 */
	public function getOauth1AccessToken($token, $verifier, $tokenSecret);

}
