<?php

namespace OAuth\Http;

/**
 * Any HTTP clients to be used with the library should implement this interface.
 */
interface HttpClientInterface{

	/**
	 * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
	 * They should return, in string form, the response body and throw an exception on error.
	 *
	 * @param string $endpoint
	 * @param mixed  $requestBody
	 * @param array  $extraHeaders
	 * @param string $method
	 *
	 * @return string
	 *
	 * @throws \OAuth\OAuthException
	 */
	public function retrieveResponse($endpoint, $requestBody, array $extraHeaders = [], $method = 'POST');
}
