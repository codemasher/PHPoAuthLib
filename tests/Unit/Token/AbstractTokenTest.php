<?php

namespace OAuthTest\Unit\Token;

use OAuth\Token\TokenAbstract;
use OAuth\Token\TokenInterface;

class AbstractTokenTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterface(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertInstanceOf(TokenInterface::class, $token);
	}

	/**


	 */
	public function testGetAccessTokenNotSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertNull($token->getAccessToken());
	}

	/**


	 */
	public function testGetAccessTokenSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo']);

		$this->assertSame('foo', $token->getAccessToken());
	}

	/**



	 */
	public function testSetAccessToken(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setAccessToken('foo');

		$this->assertSame('foo', $token->getAccessToken());
	}

	/**


	 */
	public function testGetRefreshToken(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertNull($token->getRefreshToken());
	}

	/**


	 */
	public function testGetRefreshTokenSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo', 'bar']);

		$this->assertSame('bar', $token->getRefreshToken());
	}

	/**



	 */
	public function testSetRefreshToken(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setRefreshToken('foo');

		$this->assertSame('foo', $token->getRefreshToken());
	}

	/**


	 */
	public function testGetExtraParamsNotSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertSame([], $token->getExtraParams());
	}

	/**


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



	 */
	public function testSetExtraParams(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setExtraParams(['foo', 'bar']);

		$this->assertSame(['foo', 'bar'], $token->getExtraParams());
	}

	/**



	 */
	public function testGetEndOfLifeNotSet(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$this->assertSame(TokenAbstract::EOL_UNKNOWN, $token->getEndOfLife());
	}

	/**



	 */
	public function testGetEndOfLifeZero(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo', 'bar', 0]);

		$this->assertSame(TokenAbstract::EOL_NEVER_EXPIRES, $token->getEndOfLife());
	}

	/**



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



	 */
	public function testGetEndOfLifeNeverExpiresFiveMinutes(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class, ['foo', 'bar', 5 * 60]);

		$this->assertSame(time() + (5 * 60), $token->getEndOfLife());
	}

	/**




	 */
	public function testSetEndOfLife(){
		$token = $this->getMockForAbstractClass(TokenAbstract::class);

		$token->setEndOfLife(10);

		$this->assertSame(10, $token->getEndOfLife());
	}
}
