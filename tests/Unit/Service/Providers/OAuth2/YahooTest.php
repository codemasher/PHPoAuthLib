<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Yahoo;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;
use OAuth\Token\OAuth2TokenInterface;
use OAuth\Token\TokenInterface;

class YahooTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://api.login.yahoo.com/oauth2/request_auth', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://api.login.yahoo.com/oauth2/get_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, true));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new Yahoo(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yahoo::__construct
	 * @covers OAuth\OAuth2\Service\Yahoo::getExtraOAuthHeaders
	 */
	public function testGetExtraOAuthHeaders(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnCallback(
				function($uri, $params, $extraHeaders){
					\PHPUnit_Framework_Assert::assertTrue(array_key_exists('Authorization', $extraHeaders));
					\PHPUnit_Framework_Assert::assertSame('Basic '.base64_encode('foo:bar'), $extraHeaders['Authorization']);

					return '{"access_token":"foo","expires_in":"bar"}';
				}
			)
		)
		;

		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->any())->method('getConsumerSecret')->will($this->returnValue('bar'));

		$service = new Yahoo(
			$credentials,
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
