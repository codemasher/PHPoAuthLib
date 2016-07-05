<?php
/**
 *
 * @filesource   Credentials.php
 * @created      05.07.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace OAuth\_killme;

/**
 * Value object for the credentials of an OAuth service.
 */
class Credentials implements CredentialsInterface{

	/**
	 * @var string
	 */
	public $consumerId;

	/**
	 * @var string
	 */
	public $consumerSecret;

	/**
	 * @var string
	 */
	public $callbackUrl;

	/**
	 * @param string $consumerId
	 * @param string $consumerSecret
	 * @param string $callbackUrl
	 */
	public function __construct($consumerId, $consumerSecret, $callbackUrl){
		$this->consumerId     = $consumerId;
		$this->consumerSecret = $consumerSecret;
		$this->callbackUrl    = $callbackUrl;
	}

	/**
	 * @return string
	 */
	public function getCallbackUrl(){
		return $this->callbackUrl;
	}

	/**
	 * @return string
	 */
	public function getConsumerId(){
		return $this->consumerId;
	}

	/**
	 * @return string
	 */
	public function getConsumerSecret(){
		return $this->consumerSecret;
	}
}
