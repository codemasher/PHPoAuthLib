<?php

namespace OAuth\Http;

use OAuth\OAuthException;

/**
 * Client implementation for streams/file_get_contents
 */
class StreamClient extends AbstractHttpClient{

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
	 * @throws OAuthException
	 * @throws \InvalidArgumentException
	 */
	public function retrieveResponse($endpoint, $requestBody, array $extraHeaders = [], $method = 'POST'){
		$method = strtoupper($method);

		$parsedURL = parse_url($endpoint);

		$this->normalizeHeaders($extraHeaders);

		if($method === 'GET' && !empty($requestBody)){
			throw new \InvalidArgumentException('No body expected for "GET" request.');
		}

		if(!isset($extraHeaders['Content-Type']) && $method === 'POST' && is_array($requestBody)){
			$extraHeaders['Content-Type'] = 'Content-Type: application/x-www-form-urlencoded';
		}

		$extraHeaders['Host']       = 'Host: '.$parsedURL['host'].(!empty($parsedURL['port']) ? ':'.$parsedURL['host'] : '');
		$extraHeaders['Connection'] = 'Connection: close';

		if(is_array($requestBody)){
			$requestBody = http_build_query($requestBody, '', '&');
		}

		$extraHeaders['Content-length'] = 'Content-length: '.strlen($requestBody);

		$context = stream_context_create([
			'http' => [
				'method'           => $method,
				'header'           => implode("\r\n", array_values($extraHeaders)),
				'content'          => $requestBody,
				'protocol_version' => '1.1',
				'user_agent'       => $this->userAgent,
				'max_redirects'    => $this->maxRedirects,
				'timeout'          => $this->timeout,
			],
			/**
			 * @link http://www.docnet.nu/tech-portal/2014/06/26/ssl-and-php-streams-part-1-you-are-doing-it-wrongtm/C0
			 */
#			'ssl' => [
#				'verify_peer'         => true,
#				'cafile'              => '/path/to/cafile.pem',
#				'CN_match'            => 'example.com',
#				'ciphers'             => 'HIGH:!SSLv2:!SSLv3',
#				'disable_compression' => true,
#			],
		]);

		/**
		 * @link http://stackoverflow.com/a/1342760
		 */
		$response = file_get_contents($endpoint, false, $context);

		if($response === false){
			$lastError = error_get_last();

			if(is_null($lastError)){
				throw new OAuthException('Failed to request resource. HTTP Code: '.((isset($http_response_header[0])) ? $http_response_header[0] : 'No response'));
			}

			throw new OAuthException($lastError['message']);
		}

		return $response;
	}

}
