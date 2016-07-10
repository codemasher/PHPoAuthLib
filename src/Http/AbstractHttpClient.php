<?php

namespace OAuth\Http;

/**
 * Abstract HTTP client
 */
abstract class AbstractHttpClient implements HttpClientInterface{

	/**
	 * @var string The user agent string passed to services
	 */
	protected $userAgent;

	/**
	 * @var int The maximum number of redirects
	 */
	protected $maxRedirects = 5;

	/**
	 * @var int The maximum timeout
	 */
	protected $timeout = 15;

	/**
	 * Creates instance
	 *
	 * @param string $userAgent The UA string the client will use
	 */
	public function __construct($userAgent = 'PHPoAuthLib'){
		$this->userAgent = $userAgent;
	}

	/**
	 * @param int $redirects Maximum redirects for client
	 *
	 * @return HttpClientInterface
	 */
	public function setMaxRedirects($redirects){
		$this->maxRedirects = $redirects;

		return $this;
	}

	/**
	 * @param int $timeout Request timeout time for client in seconds
	 *
	 * @return HttpClientInterface
	 */
	public function setTimeout($timeout){
		$this->timeout = $timeout;

		return $this;
	}

	/**
	 * @param array $headers
	 *
	 * @return HttpClientInterface
	 */
	public function normalizeHeaders(&$headers){
		// Normalize headers
		array_walk($headers, function(&$val, &$key){
			$key = ucfirst(strtolower($key));
			$val = ucfirst(strtolower($key)).': '.$val;
		});

		return $this;
	}
}
