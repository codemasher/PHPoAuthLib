<?php

namespace OAuth\Unit\_killme;

use OAuth\_killme\Credentials;
use OAuth\_killme\CredentialsInterface;

class CredentialsTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterface(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertInstanceOf(CredentialsInterface::class, $credentials);
	}

	/**


	 */
	public function testGetConsumerId(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertSame('foo', $credentials->getConsumerId());
	}

	/**


	 */
	public function testGetConsumerSecret(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertSame('bar', $credentials->getConsumerSecret());
	}

	/**


	 */
	public function testGetCallbackUrl(){
		$credentials = new Credentials('foo', 'bar', 'baz');

		$this->assertSame('baz', $credentials->getCallbackUrl());
	}
}
