<?php

namespace OAuthTest\Unit\Token;

use OAuth\Token;

class AbstractTokenTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterface(){
		$token = $this->getMockForAbstractClass(Token::class);

		$this->assertInstanceOf(Token::class, $token);
	}


	public function testGetAccessTokenNotSet(){
		$token = $this->getMockForAbstractClass(Token::class);

		$this->assertNull($token->accessToken);
	}


	public function testGetAccessTokenSet(){
		$token = $this->getMockForAbstractClass(Token::class, [['accessToken' => 'foo']]);

		$this->assertSame('foo', $token->accessToken);
	}

	public function testSetAccessToken(){
		$token = $this->getMockForAbstractClass(Token::class);

		$token->accessToken = 'foo';

		$this->assertSame('foo', $token->accessToken);
	}


	public function testGetRefreshToken(){
		$token = $this->getMockForAbstractClass(Token::class);

		$this->assertNull($token->refreshToken);
	}


	public function testGetRefreshTokenSet(){
		$token = $this->getMockForAbstractClass(Token::class, [['refreshToken' => 'bar']]);

		$this->assertSame('bar', $token->refreshToken);
	}

	public function testSetRefreshToken(){
		$token = $this->getMockForAbstractClass(Token::class);

		$token->refreshToken = 'foo';

		$this->assertSame('foo', $token->refreshToken);
	}


	public function testGetExtraParamsNotSet(){
		$token = $this->getMockForAbstractClass(Token::class);

		$this->assertSame([], $token->extraParams);
	}


	public function testGetExtraParamsSet(){
		$token = $this->getMockForAbstractClass(Token::class, [['extraParams' => ['foo', 'bar']]]);

		$this->assertEquals(['foo', 'bar'], $token->extraParams);
	}

	public function testSetExtraParams(){
		$token = $this->getMockForAbstractClass(Token::class);

		$token->extraParams = ['foo', 'bar'];

		$this->assertSame(['foo', 'bar'], $token->extraParams);
	}

	public function testGetEndOfLifeNotSet(){
		$token = $this->getMockForAbstractClass(Token::class);

		$this->assertSame(Token::EOL_UNKNOWN, $token->expires);
	}

	public function testGetEndOfLifeZero(){
		$token = $this->getMockForAbstractClass(Token::class, [['expires' => 0]]);

		$this->assertSame(Token::EOL_NEVER_EXPIRES, $token->expires);
	}

	public function testGetEndOfLifeNeverExpires(){
		$token = $this->getMockForAbstractClass(Token::class, [['expires' => Token::EOL_NEVER_EXPIRES]]);

		$this->assertSame(Token::EOL_NEVER_EXPIRES, $token->expires);
	}

	public function testGetEndOfLifeNeverExpiresFiveMinutes(){
		$token = $this->getMockForAbstractClass(Token::class, [['expires' => 5 * 60]]);

		$this->assertSame(time() + (5 * 60), $token->expires);
	}


	public function testSetEndOfLife(){
		$token = $this->getMockForAbstractClass(Token::class);

		$token->expires = 10;

		$this->assertSame(time() + 10, $token->expires);
	}
}
