<?php

namespace OAuth;

/**
 * Base token implementation for any OAuth version.
 *
 * // Oauth1
 *
 * @property string $requestToken
 * @property string $requestTokenSecret
 * @property string $accessTokenSecret
 *
 * @property string $accessToken
 * @property string $refreshToken
 * @property array  $extraParams
 * @property int    $expires
 */
class Token extends Container{

	/**
	 * Denotes an unknown end of life time.
	 */
	const EOL_UNKNOWN = -9001;

	/**
	 * Denotes a token which never expires, should only happen in OAuth1.
	 */
	const EOL_NEVER_EXPIRES = -9002;
	/**
	 * @var string
	 */
	protected $requestToken;

	/**
	 * @var string
	 */
	protected $requestTokenSecret;

	/**
	 * @var string
	 */
	protected $accessTokenSecret;

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
	protected $expires = self::EOL_UNKNOWN;

	/**
	 * @var array
	 */
	protected $extraParams = [];

	/**
	 * Jet-setter
	 *
	 * @param $property
	 * @param $value
	 *
	 * @return bool
	 */
	public function __set($property, $value){

		if(property_exists($this, $property)){
			if($property === 'expires'){
				$this->setExpiry($value);
			}
			else{
				$this->{$property} = $value;
			}
		}

	}

	/**
	 * @param int $expires
	 *
	 * @return $this
	 */
	public function setExpiry($expires){

		if($expires === 0 || $expires === self::EOL_NEVER_EXPIRES){
			$this->expires = self::EOL_NEVER_EXPIRES;
		}
		elseif((int)$expires > 0){
			$this->expires = time() + $expires;
		}
		else{
			$this->expires = self::EOL_UNKNOWN;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isExpired(){
		return $this->expires !== self::EOL_NEVER_EXPIRES
		       && $this->expires !== self::EOL_UNKNOWN
		       && time() > $this->expires;
	}

}
