<?php

namespace OAuth\Service;

use OAuth\Http\Exception\TokenResponseException;
use OAuth\Token\TokenInterface;

/**
 * Defines the common methods across OAuth 2 services.
 */
interface OAuth2ServiceInterface extends ServiceInterface{

	/** @const OAUTH_VERSION */
	const OAUTH_VERSION = 2;

	/**
	 * Authorization methods for various services
	 */
	const AUTHORIZATION_METHOD_HEADER_OAUTH    = 0;
	const AUTHORIZATION_METHOD_HEADER_BEARER   = 1;
	const AUTHORIZATION_METHOD_QUERY_STRING    = 2;
	const AUTHORIZATION_METHOD_QUERY_STRING_V2 = 3;
	const AUTHORIZATION_METHOD_QUERY_STRING_V3 = 4;
	const AUTHORIZATION_METHOD_QUERY_STRING_V4 = 5;

	/**
	 * Retrieves and stores/returns the OAuth2 access token after a successful authorization.
	 *
	 * @param string $code The access code from the callback.
	 *
	 * @return TokenInterface $token
	 *
	 * @throws TokenResponseException
	 */
	public function requestAccessToken($code);
}
