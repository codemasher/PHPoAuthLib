<?php

namespace OAuth\Http;

use OAuth\Http\Exception\TokenResponseException;

/**
 * Any HTTP clients to be used with the library should implement this interface.
 */
interface ClientInterface{

	/**
	 * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
	 * They should return, in string form, the response body and throw an exception on error.
	 *
	 * @param Uri $endpoint
	 * @param mixed        $requestBody
	 * @param array        $extraHeaders
	 * @param string       $method
	 *
	 * @return string
	 *
	 * @throws TokenResponseException
	 */
	public function retrieveResponse(
		Uri $endpoint,
		$requestBody,
		array $extraHeaders = [],
		$method = 'POST'
	);
}
