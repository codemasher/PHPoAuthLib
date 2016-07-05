<?php

namespace OAuthTest\Unit\Storage;

use OAuth\Storage\Exception\TokenNotFoundException;
use OAuth\Storage\Session;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\TokenAbstract;
use OAuth\Token\TokenInterface;

class SessionTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 *
	 * @runInSeparateProcess
	 */
	public function testConstructCorrectInterface(){
		$storage = new Session();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 *
	 * @runInSeparateProcess
	 */
	public function testConstructWithoutStartingSession(){
		session_start();

		$storage = new Session(false);

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 *
	 * @runInSeparateProcess
	 */
	public function testConstructTryingToStartWhileSessionAlreadyExists(){
		session_start();

		$storage = new Session();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 *
	 * @runInSeparateProcess
	 */
	public function testConstructWithExistingSessionKey(){
		session_start();

		$_SESSION['lusitanian_oauth_token'] = [];

		$storage = new Session();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testStoreAccessTokenIsAlreadyArray(){
		$storage = new Session();

		$this->assertInstanceOf(
			Session::class,
			$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class))
		);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testStoreAccessTokenIsNotArray(){
		$storage = new Session();

		$_SESSION['lusitanian_oauth_token'] = 'foo';

		$this->assertInstanceOf(
			Session::class,
			$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class))
		);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 * @covers OAuth\Common\Storage\Session::retrieveAccessToken
	 * @covers OAuth\Common\Storage\Session::hasAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testRetrieveAccessTokenValid(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertInstanceOf(TokenInterface::class, $storage->retrieveAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::retrieveAccessToken
	 * @covers OAuth\Common\Storage\Session::hasAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound(){
		$this->setExpectedException(TokenNotFoundException::class);

		$storage = new Session();

		$storage->retrieveAccessToken('foo');
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 * @covers OAuth\Common\Storage\Session::hasAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testHasAccessTokenTrue(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::hasAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testHasAccessTokenFalse(){
		$storage = new Session();

		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::clearToken
	 *
	 * @runInSeparateProcess
	 */
	public function testClearTokenIsNotSet(){
		$storage = new Session();

		$this->assertInstanceOf(Session::class, $storage->clearToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 * @covers OAuth\Common\Storage\Session::clearToken
	 *
	 * @runInSeparateProcess
	 */
	public function testClearTokenSet(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertInstanceOf(Session::class, $storage->clearToken('foo'));
		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 * @covers OAuth\Common\Storage\Session::clearAllTokens
	 *
	 * @runInSeparateProcess
	 */
	public function testClearAllTokens(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(TokenInterface::class));
		$storage->storeAccessToken('bar', $this->getMock(TokenInterface::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertTrue($storage->hasAccessToken('bar'));
		$this->assertInstanceOf(Session::class, $storage->clearAllTokens());
		$this->assertFalse($storage->hasAccessToken('foo'));
		$this->assertFalse($storage->hasAccessToken('bar'));
	}

	/**
	 * @covers OAuth\Common\Storage\Session::__construct
	 * @covers OAuth\Common\Storage\Session::__destruct
	 *
	 * @runInSeparateProcess
	 */
	public function testDestruct(){
		$storage = new Session();

		unset($storage);
	}

	/**
	 * @covers OAuth\Common\Storage\Session::storeAccessToken
	 * @covers OAuth\Common\Storage\Session::retrieveAccessToken
	 *
	 * @runInSeparateProcess
	 */
	public function testSerializeUnserialize(){
		$mock = $this->getMock(TokenAbstract::class, ['__sleep']);
		$mock->expects($this->once())
		     ->method('__sleep')
		     ->will($this->returnValue(['accessToken']))
		;

		$storage = new Session();
		$storage->storeAccessToken('foo', $mock);
		$retrievedToken = $storage->retrieveAccessToken('foo');

		$this->assertInstanceOf(TokenAbstract::class, $retrievedToken);
	}
}
