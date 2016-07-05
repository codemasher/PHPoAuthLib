<?php

namespace OAuth\Unit\_killme;

use OAuth\_killme\Credentials;
use OAuth\_killme\CredentialsInterface;

class CredentialsTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\Common\Consumer\Credentials::__construct
	 */
	public function testConstructCorrectInterface(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertInstanceOf(CredentialsInterface::class, $credentials);
	}

	/**
	 * @covers OAuth\Common\Consumer\Credentials::__construct
	 * @covers OAuth\Common\Consumer\Credentials::getConsumerId
	 */
	public function testGetConsumerId(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertSame('foo', $credentials->getConsumerId());
	}

	/**
	 * @covers OAuth\Common\Consumer\Credentials::__construct
	 * @covers OAuth\Common\Consumer\Credentials::getConsumerSecret
	 */
	public function testGetConsumerSecret(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertSame('bar', $credentials->getConsumerSecret());
	}

	/**
	 * @covers OAuth\Common\Consumer\Credentials::__construct
	 * @covers OAuth\Common\Consumer\Credentials::getCallbackUrl
	 */
	public function testGetCallbackUrl(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertSame('baz', $credentials->getCallbackUrl());
	}
}
