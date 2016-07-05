<?php

namespace OAuthTest\Unit\Storage;

use OAuth\Storage\Exception\TokenNotFoundException;
use OAuth\Storage\Memory;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\TokenInterface;

class MemoryTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 */
	public function testConstructCorrectInterface(){
		$storage = new Memory();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::storeAccessToken
	 */
	public function testStoreAccessToken(){
		$storage = new Memory();

		$this->assertInstanceOf(
			Memory::class,
			$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class))
		);
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::storeAccessToken
	 * @covers OAuth\Common\Storage\Memory::retrieveAccessToken
	 * @covers OAuth\Common\Storage\Memory::hasAccessToken
	 */
	public function testRetrieveAccessTokenValid(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertInstanceOf(TokenInterface::class, $storage->retrieveAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::retrieveAccessToken
	 * @covers OAuth\Common\Storage\Memory::hasAccessToken
	 */
	public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound(){
		$this->setExpectedException(TokenNotFoundException::class);

		$storage = new Memory();

		$storage->retrieveAccessToken('foo');
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::storeAccessToken
	 * @covers OAuth\Common\Storage\Memory::hasAccessToken
	 */
	public function testHasAccessTokenTrue(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::hasAccessToken
	 */
	public function testHasAccessTokenFalse(){
		$storage = new Memory();

		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::clearToken
	 */
	public function testClearTokenIsNotSet(){
		$storage = new Memory();

		$this->assertInstanceOf(Memory::class, $storage->clearToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::storeAccessToken
	 * @covers OAuth\Common\Storage\Memory::clearToken
	 */
	public function testClearTokenSet(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertInstanceOf(Memory::class, $storage->clearToken('foo'));
		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Memory::__construct
	 * @covers OAuth\Common\Storage\Memory::storeAccessToken
	 * @covers OAuth\Common\Storage\Memory::clearAllTokens
	 */
	public function testClearAllTokens(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));
		$storage->storeAccessToken('bar', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertTrue($storage->hasAccessToken('bar'));
		$this->assertInstanceOf(Memory::class, $storage->clearAllTokens());
		$this->assertFalse($storage->hasAccessToken('foo'));
		$this->assertFalse($storage->hasAccessToken('bar'));
	}
}
