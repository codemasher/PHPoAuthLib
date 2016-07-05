<?php

namespace OAuthTest\Unit\Storage;

use OAuth\Storage\Exception\TokenNotFoundException;
use OAuth\Storage\Memory;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\TokenInterface;

class MemoryTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterface(){
		$storage = new Memory();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**


	 */
	public function testStoreAccessToken(){
		$storage = new Memory();

		$this->assertInstanceOf(
			Memory::class,
			$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class))
		);
	}

	/**




	 */
	public function testRetrieveAccessTokenValid(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertInstanceOf(TokenInterface::class, $storage->retrieveAccessToken('foo'));
	}

	/**



	 */
	public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound(){
		$this->setExpectedException(TokenNotFoundException::class);

		$storage = new Memory();

		$storage->retrieveAccessToken('foo');
	}

	/**



	 */
	public function testHasAccessTokenTrue(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
	}

	/**


	 */
	public function testHasAccessTokenFalse(){
		$storage = new Memory();

		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**


	 */
	public function testClearTokenIsNotSet(){
		$storage = new Memory();

		$this->assertInstanceOf(Memory::class, $storage->clearToken('foo'));
	}

	/**



	 */
	public function testClearTokenSet(){
		$storage = new Memory();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertInstanceOf(Memory::class, $storage->clearToken('foo'));
		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**



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
