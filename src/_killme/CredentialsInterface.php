<?php

namespace OAuth\_killme;

/**
 * Value object for the credentials of an OAuth service.
 */
interface CredentialsInterface{

	/**
	 * @return string
	 */
	public function getCallbackUrl();

	/**
	 * @return string
	 */
	public function getConsumerId();

	/**
	 * @return string
	 */
	public function getConsumerSecret();
}