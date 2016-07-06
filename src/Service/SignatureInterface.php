<?php

namespace OAuth\Service;

use OAuth\Http\Uri;

interface SignatureInterface{

	/**
	 * @param string $algorithm
	 */
	public function setHashingAlgorithm($algorithm);

	/**
	 * @param string $token
	 */
	public function setTokenSecret($token);

	/**
	 * @param Uri    $uri
	 * @param array  $params
	 * @param string $method
	 *
	 * @return string
	 */
	public function getSignature(Uri $uri, array $params, $method = 'POST');
}
