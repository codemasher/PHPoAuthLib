<?php

namespace OAuth\Service;

use OAuth\Token\TokenInterface;

/**
 * Defines the common methods across OAuth 1 services.
 */
interface OAuth1ServiceInterface extends ServiceInterface{

	/**
	 * Retrieves and stores/returns the OAuth1 request token obtained from the service.
	 *
	 * @return TokenInterface $token
	 *
	 * @throws \OAuth\Http\Exception\TokenResponseException
	 */
	public function requestRequestToken();

	/**
	 * Retrieves and stores/returns the OAuth1 access token after a successful authorization.
	 *
	 * @param string $token The request token from the callback.
	 * @param string $verifier
	 * @param string $tokenSecret
	 *
	 * @return TokenInterface $token
	 *
	 * @throws \OAuth\Http\Exception\TokenResponseException
	 */
	public function requestAccessToken($token, $verifier, $tokenSecret);

	/**
	 * @return \OAuth\Http\Uri
	 */
	public function getRequestTokenEndpoint();
}
