<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Bitly;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;
use OAuth\Token\OAuth2TokenInterface;
use OAuth\Token\TokenInterface;

class BitlyTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 * @covers OAuth\OAuth2\Service\Bitly::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://bitly.com/oauth/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 * @covers OAuth\OAuth2\Service\Bitly::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://api-ssl.bitly.com/oauth/access_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 * @covers OAuth\OAuth2\Service\Bitly::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('access_token=foo', $absoluteUri['query']);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 * @covers OAuth\OAuth2\Service\Bitly::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Bitly::__construct
	 * @covers OAuth\OAuth2\Service\Bitly::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\Bitly::requestAccessToken
	 */
	public function testParseAccessTokenResponseValid(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('access_token=foo'));

		$service = new Bitly(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
