<?php

namespace OAuthTest\Mocks;

use OAuth\Http\Uri;
use OAuth\Service\ServiceAbstract;

class MockServiceAbstract extends ServiceAbstract{

	/**
	 * Sends an authenticated API request to the path provided.
	 * If the path provided is not an absolute URI, the base API Uri (service-specific) will be used.
	 *
	 * @param string|\OAuth\Http\Uri $path
	 * @param string                 $method       HTTP method
	 * @param array                  $body         Request body if applicable (an associative array will
	 *                                          automatically be converted into a urlencoded body)
	 * @param array                  $extraHeaders Extra headers if applicable. These will override service-specific
	 *                                          any defaults.
	 *
	 * @return string
	 */
	public function apiRequest($path, $method = 'GET', $body = null, array $extraHeaders = []){
	}

	/**
	 * Returns the url to redirect to for authorization purposes.
	 *
	 * @param array $additionalParameters
	 *
	 * @return \OAuth\Http\Uri
	 */
	public function getAuthorizationURL(array $additionalParameters = []){
	}


	public function testDetermineRequestUriFromPath($path){
		return $this->determineRequestUriFromPath($path);
	}
}
