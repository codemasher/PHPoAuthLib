<?php

namespace OAuth\Token;

/**
 * Base token implementation for any OAuth version.
 */
abstract class TokenAbstract implements TokenInterface{

	/**
	 * @var string
	 */
	protected $accessToken;

	/**
	 * @var string
	 */
	protected $refreshToken;

	/**
	 * @var int
	 */
	protected $endOfLife;

	/**
	 * @var array
	 */
	protected $extraParams = [];

	/**
	 * @param string $accessToken
	 * @param string $refreshToken
	 * @param int    $lifetime
	 * @param array  $extraParams
	 */
	public function __construct($accessToken = null, $refreshToken = null, $lifetime = null, $extraParams = []){
		$this->accessToken  = $accessToken;
		$this->refreshToken = $refreshToken;
		$this->setLifetime($lifetime);
		$this->extraParams = $extraParams;
	}

	/**
	 * @return string
	 */
	public function getAccessToken(){
		return $this->accessToken;
	}

	/**
	 * @return string
	 */
	public function getRefreshToken(){
		return $this->refreshToken;
	}

	/**
	 * @return int
	 */
	public function getEndOfLife(){
		return $this->endOfLife;
	}

	/**
	 * @param array $extraParams
	 */
	public function setExtraParams(array $extraParams){
		$this->extraParams = $extraParams;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getExtraParams(){
		return $this->extraParams;
	}

	/**
	 * @param string $accessToken
	 */
	public function setAccessToken($accessToken){
		$this->accessToken = $accessToken;

		return $this;
	}

	/**
	 * @param int $endOfLife
	 */
	public function setEndOfLife($endOfLife){
		$this->endOfLife = $endOfLife;

		return $this;
	}

	/**
	 * @param int $lifetime
	 */
	public function setLifetime($lifetime){
		if($lifetime === 0 || $lifetime === self::EOL_NEVER_EXPIRES){
			$this->endOfLife = self::EOL_NEVER_EXPIRES;
		}
		elseif($lifetime !== null){
			$this->endOfLife = intval($lifetime) + time();
		}
		else{
			$this->endOfLife = self::EOL_UNKNOWN;
		}

		return $this;
	}

	/**
	 * @param string $refreshToken
	 */
	public function setRefreshToken($refreshToken){
		$this->refreshToken = $refreshToken;

		return $this;
	}

	public function isExpired(){
		return ($this->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES
		        && $this->getEndOfLife() !== TokenInterface::EOL_UNKNOWN
		        && time() > $this->getEndOfLife());
	}
}
