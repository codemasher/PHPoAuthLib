<?php

namespace OAuthTest\Unit\Token;

use OAuth\Token\TokenAbstract;
use OAuth\Token\TokenInterface;

class AbstractTokenTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 */
	public function testConstructCorrectInterface(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertInstanceOf(TokenInterface::class, $token);
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getAccessToken
	 */
	public function testGetAccessTokenNotSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertNull($token->getAccessToken());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getAccessToken
	 */
	public function testGetAccessTokenSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo']);

		$this->assertSame('foo', $token->getAccessToken());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getAccessToken
	 * @covers OAuth\Common\Token\AbstractToken::setAccessToken
	 */
	public function testSetAccessToken(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setAccessToken('foo');

		$this->assertSame('foo', $token->getAccessToken());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getRefreshToken
	 */
	public function testGetRefreshToken(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertNull($token->getRefreshToken());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getRefreshToken
	 */
	public function testGetRefreshTokenSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo', 'bar']);

		$this->assertSame('bar', $token->getRefreshToken());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getRefreshToken
	 * @covers OAuth\Common\Token\AbstractToken::setRefreshToken
	 */
	public function testSetRefreshToken(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setRefreshToken('foo');

		$this->assertSame('foo', $token->getRefreshToken());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getExtraParams
	 */
	public function testGetExtraParamsNotSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertSame([], $token->getExtraParams());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::getExtraParams
	 */
	public function testGetExtraParamsSet(){
		$token = $this->getMockForAbstractClass(
			TokenAbstract::class, [
				                    'foo',
				                    'bar',
				                    null,
				                    ['foo', 'bar'],
			                    ]
		);

		$this->assertEquals(['foo', 'bar'], $token->getExtraParams());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::setExtraParams
	 * @covers OAuth\Common\Token\AbstractToken::getExtraParams
	 */
	public function testSetExtraParams(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setExtraParams(['foo', 'bar']);

		$this->assertSame(['foo', 'bar'], $token->getExtraParams());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::setLifetime
	 * @covers OAuth\Common\Token\AbstractToken::getEndOfLife
	 */
	public function testGetEndOfLifeNotSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertSame(TokenAbstract::EOL_UNKNOWN, $token->getEndOfLife());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::setLifetime
	 * @covers OAuth\Common\Token\AbstractToken::getEndOfLife
	 */
	public function testGetEndOfLifeZero(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo', 'bar', 0]);

		$this->assertSame(TokenAbstract::EOL_NEVER_EXPIRES, $token->getEndOfLife());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::setLifetime
	 * @covers OAuth\Common\Token\AbstractToken::getEndOfLife
	 */
	public function testGetEndOfLifeNeverExpires(){
		$token = $this->getMockForAbstractClass(
			TokenAbstract::class, [
				                    'foo',
				                    'bar',
				                    TokenAbstract::EOL_NEVER_EXPIRES,
			                    ]
		);

		$this->assertSame(TokenAbstract::EOL_NEVER_EXPIRES, $token->getEndOfLife());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::setLifetime
	 * @covers OAuth\Common\Token\AbstractToken::getEndOfLife
	 */
	public function testGetEndOfLifeNeverExpiresFiveMinutes(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo', 'bar', 5 * 60]);

		$this->assertSame(time() + (5 * 60), $token->getEndOfLife());
	}

	/**
	 * @covers OAuth\Common\Token\AbstractToken::__construct
	 * @covers OAuth\Common\Token\AbstractToken::setLifetime
	 * @covers OAuth\Common\Token\AbstractToken::getEndOfLife
	 * @covers OAuth\Common\Token\AbstractToken::setEndOfLife
	 */
	public function testSetEndOfLife(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setEndOfLife(10);

		$this->assertSame(10, $token->getEndOfLife());
	}
}
