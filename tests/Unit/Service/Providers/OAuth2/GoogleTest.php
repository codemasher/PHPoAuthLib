<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Google;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class GoogleTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://accounts.google.com/o/oauth2/auth?access_type=online',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);

		// Verify that 'offine' works
		$service->setAccessType('offline');
		$this->assertSame(
			'https://accounts.google.com/o/oauth2/auth?access_type=offline',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);

	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpointException(){
		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException('OAuth\Service\Exception\InvalidAccessTypeException');

		try{
			$service->setAccessType('invalid');
		}
		catch(InvalidAccessTypeException $e){
			return;
		}
		$this->fail('Expected InvalidAccessTypeException not thrown');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://accounts.google.com/o/oauth2/token',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Google::__construct
	 * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new Google(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
