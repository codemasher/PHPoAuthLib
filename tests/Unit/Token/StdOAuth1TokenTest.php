<?php

namespace OAuthTest\Unit\Token;

use OAuth\Token\OAuth1Token;
use OAuth\Token\OAuth1TokenInterface;
use OAuth\Token\TokenAbstract;

class StdOAuth1TokenTest extends \PHPUnit_Framework_TestCase{

	/**
	 *
	 */
	public function testConstructCorrectInterfaces(){
		$token = new OAuth1Token();

		$this->assertInstanceOf(OAuth1TokenInterface::class, $token);
		$this->assertInstanceOf(TokenAbstract::class, $token);
	}

	/**

	 */
	public function testSetRequestToken(){
		$token = new OAuth1Token();

		$this->assertNull($token->setRequestToken('foo'));
	}

	/**


	 */
	public function testGetRequestToken(){
		$token = new OAuth1Token();

		$this->assertNull($token->setRequestToken('foo'));
		$this->assertSame('foo', $token->getRequestToken());
	}

	/**

	 */
	public function testSetRequestTokenSecret(){
		$token = new OAuth1Token();

		$this->assertNull($token->setRequestTokenSecret('foo'));
	}

	/**


	 */
	public function testGetRequestTokenSecret(){
		$token = new OAuth1Token();

		$this->assertNull($token->setRequestTokenSecret('foo'));
		$this->assertSame('foo', $token->getRequestTokenSecret());
	}

	/**

	 */
	public function testSetAccessTokenSecret(){
		$token = new OAuth1Token();

		$this->assertNull($token->setAccessTokenSecret('foo'));
	}

	/**


	 */
	public function testGetAccessTokenSecret(){
		$token = new OAuth1Token();

		$this->assertNull($token->setAccessTokenSecret('foo'));
		$this->assertSame('foo', $token->getAccessTokenSecret());
	}
}
