<?php

namespace OAuth\Service;

use OAuth\Token\OAuth1TokenInterface;
use OAuth\Token\TokenInterface;

/**
 * Defines the common methods across OAuth 1 services.
 */
interface OAuth1ServiceInterface extends ServiceInterface{

	/** @const OAUTH_VERSION */
	const OAUTH_VERSION = 1;

	/**
	 * Retrieves and stores/returns the OAuth1 request token obtained from the service.
	 *
	 * @return TokenInterface $token
	 *
	 * @throws \OAuth\Http\Exception\TokenResponseException
	 */
	public function getRequestToken();

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
	public function getAccessToken($token, $verifier, $tokenSecret);

	/**
	 * Refreshes an OAuth1 access token
	 *
	 * @param  OAuth1TokenInterface $token
	 *
	 * @return OAuth1TokenInterface $token
	 */
	public function refreshAccessToken(OAuth1TokenInterface $token);

}
