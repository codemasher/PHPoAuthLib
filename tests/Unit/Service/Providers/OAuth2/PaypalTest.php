<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Paypal;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;
use OAuth\Token\OAuth2TokenInterface;
use OAuth\Token\TokenInterface;

class PaypalTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://api.paypal.com/v1/identity/openidconnect/tokenservice',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, true));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnMessage(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('message=some_error'));

		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnName(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('name=some_error'));

		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Paypal::__construct
	 * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new Paypal(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
