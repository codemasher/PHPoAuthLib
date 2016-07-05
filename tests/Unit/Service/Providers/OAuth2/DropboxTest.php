<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Dropbox;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;
use OAuth\Token\OAuth2TokenInterface;
use OAuth\Token\TokenInterface;

class DropboxTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationUri
	 */
	public function testGetAuthorizationUriWithoutAdditionalParams(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Dropbox(
			$credentials,
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://www.dropbox.com/1/oauth2/authorize?client_id=foo&redirect_uri=bar&response_type=code&scope=',
			$service->getAuthorizationUri()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationUri
	 */
	public function testGetAuthorizationUriWithAdditionalParams(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Dropbox(
			$credentials,
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://www.dropbox.com/1/oauth2/authorize?client_id=foo&redirect_uri=bar&response_type=code&scope=',
			$service->getAuthorizationUri()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://www.dropbox.com/1/oauth2/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://api.dropbox.com/1/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('access_token=foo', $absoluteUri['query']);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new Dropbox(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
