<?php

namespace OAuth\Service;

use OAuth\Http\Uri;
use OAuth\Service\Exception\UnsupportedHashAlgorithmException;

class Signature implements SignatureInterface{

	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * @var string
	 */
	protected $algorithm;

	/**
	 * @var string
	 */
	protected $tokenSecret = null;

	/**
	 * @param string $secret
	 */
	public function __construct($secret){
		$this->secret = $secret;
	}


	/**
	 * @param string $token
	 */
	public function setTokenSecret($token){
		$this->tokenSecret = $token;
	}

	/**
	 * @param \OAuth\Http\Uri $uri
	 * @param array           $params
	 * @param string          $method
	 *
	 * @return string
	 */
	public function getSignature(Uri $uri, array $params, $method = 'POST'){
		parse_str($uri->getQuery(), $queryStringData);

		foreach(array_merge($queryStringData, $params) as $key => $value){
			$signatureData[rawurlencode($key)] = rawurlencode($value);
		}

		ksort($signatureData);

		// determine base uri
		$baseUri = $uri->getScheme().'://'.$uri->getRawAuthority();

		if('/' === $uri->getPath()){
			$baseUri .= $uri->hasExplicitTrailingHostSlash() ? '/' : '';
		}
		else{
			$baseUri .= $uri->getPath();
		}

		$baseString = strtoupper($method).'&';
		$baseString .= rawurlencode($baseUri).'&';
		$baseString .= rawurlencode($this->buildSignatureDataString($signatureData));

		return base64_encode(hash_hmac('sha1', $baseString, $this->getSigningKey(), true));
	}

	/**
	 * @param array $signatureData
	 *
	 * @return string
	 */
	protected function buildSignatureDataString(array $signatureData){
		$signatureString = '';
		$delimiter       = '';
		foreach($signatureData as $key => $value){
			$signatureString .= $delimiter.$key.'='.$value;

			$delimiter = '&';
		}

		return $signatureString;
	}

	/**
	 * @return string
	 */
	protected function getSigningKey(){
		$signingKey = rawurlencode($this->secret).'&';

		if($this->tokenSecret !== null){
			$signingKey .= rawurlencode($this->tokenSecret);
		}

		return $signingKey;
	}

}
