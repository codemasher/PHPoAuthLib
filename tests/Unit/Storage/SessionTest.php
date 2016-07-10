<?php

namespace OAuthTest\Unit\Storage;

use OAuth\OAuthException;
use OAuth\Storage\Session;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;

class SessionTest extends \PHPUnit_Framework_TestCase{

	/**

	 *
	 * @runInSeparateProcess
	 */
	public function testConstructCorrectInterface(){
		$storage = new Session();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**

	 *
	 * @runInSeparateProcess
	 */
	public function testConstructWithoutStartingSession(){
		session_start();

		$storage = new Session(false);

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**

	 *
	 * @runInSeparateProcess
	 */
	public function testConstructTryingToStartWhileSessionAlreadyExists(){
		session_start();

		$storage = new Session();

		$this->assertInstanceOf(TokenStorageInterface::class, $storage);
	}

	/**

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


	 *
	 * @runInSeparateProcess
	 */
	public function testStoreAccessTokenIsAlreadyArray(){
		$storage = new Session();

		$this->assertInstanceOf(
			Session::class,
			$storage->storeAccessToken('foo', $this->getMock(Token::class))
		);
	}

	/**


	 *
	 * @runInSeparateProcess
	 */
	public function testStoreAccessTokenIsNotArray(){
		$storage = new Session();

		$_SESSION['lusitanian_oauth_token'] = 'foo';

		$this->assertInstanceOf(
			Session::class,
			$storage->storeAccessToken('foo', $this->getMock(Token::class))
		);
	}

	/**




	 *
	 * @runInSeparateProcess
	 */
	public function testRetrieveAccessTokenValid(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(Token::class));

		$this->assertInstanceOf(Token::class, $storage->retrieveAccessToken('foo'));
	}

	/**



	 *
	 * @runInSeparateProcess
	 */
	public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound(){
		$this->setExpectedException(OAuthException::class);

		$storage = new Session();

		$storage->retrieveAccessToken('foo');
	}

	/**



	 *
	 * @runInSeparateProcess
	 */
	public function testHasAccessTokenTrue(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(Token::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
	}

	/**


	 *
	 * @runInSeparateProcess
	 */
	public function testHasAccessTokenFalse(){
		$storage = new Session();

		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**


	 *
	 * @runInSeparateProcess
	 */
	public function testClearTokenIsNotSet(){
		$storage = new Session();

		$this->assertInstanceOf(Session::class, $storage->clearToken('foo'));
	}

	/**



	 *
	 * @runInSeparateProcess
	 */
	public function testClearTokenSet(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(Token::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertInstanceOf(Session::class, $storage->clearToken('foo'));
		$this->assertFalse($storage->hasAccessToken('foo'));
	}

	/**



	 *
	 * @runInSeparateProcess
	 */
	public function testClearAllTokens(){
		$storage = new Session();

		$storage->storeAccessToken('foo', $this->getMock(Token::class));
		$storage->storeAccessToken('bar', $this->getMock(Token::class));

		$this->assertTrue($storage->hasAccessToken('foo'));
		$this->assertTrue($storage->hasAccessToken('bar'));
		$this->assertInstanceOf(Session::class, $storage->clearAllTokens());
		$this->assertFalse($storage->hasAccessToken('foo'));
		$this->assertFalse($storage->hasAccessToken('bar'));
	}

	/**


	 *
	 * @runInSeparateProcess
	 */
	public function testDestruct(){
		$storage = new Session();

		unset($storage);
	}

	/**


	 *
	 * @runInSeparateProcess
	 */
	public function testSerializeUnserialize(){
		$mock = $this->getMock(Token::class, ['__sleep']);
		$mock->expects($this->once())
		     ->method('__sleep')
		     ->will($this->returnValue(['accessToken']))
		;

		$storage = new Session();
		$storage->storeAccessToken('foo', $mock);
		$retrievedToken = $storage->retrieveAccessToken('foo');

		$this->assertInstanceOf(Token::class, $retrievedToken);
	}
}
