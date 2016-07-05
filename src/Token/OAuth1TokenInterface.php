<?php

namespace OAuth\Token;

/**
 * OAuth1 specific token interface
 */
interface OAuth1TokenInterface extends TokenInterface{

	/**
	 * @return string
	 */
	public function getAccessTokenSecret();

	/**
	 * @param string $accessTokenSecret
	 */
	public function setAccessTokenSecret($accessTokenSecret);

	/**
	 * @return string
	 */
	public function getRequestTokenSecret();

	/**
	 * @param string $requestTokenSecret
	 */
	public function setRequestTokenSecret($requestTokenSecret);

	/**
	 * @return string
	 */
	public function getRequestToken();

	/**
	 * @param string $requestToken
	 */
	public function setRequestToken($requestToken);
}
