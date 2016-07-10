<?php

namespace OAuthTest\Unit\Token;

use OAuth\Token;

class StdTokenAbstractTest extends \PHPUnit_Framework_TestCase{

	/**
	 *
	 */
	public function testConstructCorrectInterfaces(){
		$token = new \OAuth\Token();

		$this->assertInstanceOf(\OAuth\Token::class, $token);
		$this->assertInstanceOf(\OAuth\Token::class, $token);
	}


	public function testGetRequestToken(){
		$token = new \OAuth\Token();
		$token->requestToken = ('foo');

		$this->assertSame('foo', $token->requestToken);
	}


	public function testGetRequestTokenSecret(){
		$token = new \OAuth\Token();
		$token->requestTokenSecret = 'foo';
		$this->assertSame('foo', $token->requestTokenSecret);
	}


	public function testGetAccessTokenSecret(){
		$token = new Token();
		$token->accessTokenSecret = 'foo';
		$this->assertSame('foo', $token->accessTokenSecret);
	}
}
