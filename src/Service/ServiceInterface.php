<?php

namespace OAuth\Service;

use OAuth\Token;

/**
 * Defines methods common among all OAuth services.
 */
interface ServiceInterface{

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (service-specific) will be used.
	 *
	 * @param string|\OAuth\Http\Uri $path
	 * @param string                 $method       HTTP method
	 * @param array                  $body         Request body if applicable (an associative array will
	 *                                             automatically be converted into a urlencoded body)
	 * @param array                  $extraHeaders Extra headers if applicable. These will override service-specific
	 *                                             any defaults.
	 *
	 * @return string
	 */
	public function apiRequest($path, $method = 'GET', $body = null, array $extraHeaders = []);

	/**
	 * Returns the url to redirect to for authorization purposes.
	 *
	 * @param array $additionalParameters
	 *
	 * @return \OAuth\Http\Uri
	 */
	public function getAuthorizationURL(array $additionalParameters = []);

	/**
	 * Refreshes an OAuth access token
	 *
	 * @param  \OAuth\Token $token
	 *
	 * @return \OAuth\Token $token
	 */
	public function refreshAccessToken(Token $token);

}
