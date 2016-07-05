<?php

namespace OAuthTest\Unit\Service;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Uri;
use OAuth\Service\Exception\InvalidScopeException;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\Exception\ExpiredTokenException;
use OAuth\Token\OAuth2Token;
use OAuth\Token\OAuth2TokenInterface;
use OAuth\Token\TokenInterface;
use OAuthTest\Mocks\MockOAuth2Service;

class Oauth2ServiceTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 */
	public function testConstructCorrectInterface(){
		$service = $this->getMockForAbstractClass(
			OAuth2Service::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				[],
			]
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 */
	public function testConstructCorrectParent(){
		$service = $this->getMockForAbstractClass(
			OAuth2Service::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				[],
			]
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 */
	public function testConstructCorrectParentCustomUri(){
		$service = $this->getMockForAbstractClass(
			OAuth2Service::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				[],
				$this->getMock(Uri::class),
			]
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::isValidScope
	 */
	public function testConstructThrowsExceptionOnInvalidScope(){
		$this->setExpectedException(InvalidScopeException::class);

		$service = new MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			['invalidscope']
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithoutParametersOrScopes(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new MockOAuth2Service(
			$credentials,
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
			$service->getAuthorizationUri()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithParametersWithoutScopes(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$credentials,
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?foo=bar&baz=beer&type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
			$service->getAuthorizationUri(['foo' => 'bar', 'baz' => 'beer'])->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::isValidScope
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithParametersAndScopes(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$credentials,
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			['mock', 'mock2']
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?foo=bar&baz=beer&type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=mock+mock2',
			$service->getAuthorizationUri(['foo' => 'bar', 'baz' => 'beer'])->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::requestAccessToken
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAccessTokenEndpoint
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getExtraOAuthHeaders
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::service
	 */
	public function testRequestAccessToken(){
		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceof(OAuth2Token::class, $service->requestAccessToken('code'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::request
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::determineRequestUriFromPath
	 */
	public function testRequestThrowsExceptionWhenTokenIsExpired(){
		$tokenExpiration = new \DateTime('26-03-1984 00:00:00');

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->any())->method('getEndOfLife')->will($this->returnValue($tokenExpiration->format('U')));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$storage
		);

		$this->setExpectedException(ExpiredTokenException::class, 'Token expired on 03/26/1984 at 12:00:00 AM');

		$service->request('https://pieterhordijk.com/my/awesome/path');
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::request
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::service
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getExtraApiHeaders
	 */
	public function testRequestOauthAuthorizationMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('OAuth foo', $headers, true));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::request
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::service
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getExtraApiHeaders
	 */
	public function testRequestQueryStringMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$service->setAuthorizationMethod('querystring');

		$uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('access_token=foo', $absoluteUri['query']);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::request
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::service
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getExtraApiHeaders
	 */
	public function testRequestQueryStringTwoMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$service->setAuthorizationMethod('querystring2');

		$uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('oauth2_access_token=foo', $absoluteUri['query']);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::request
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::service
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getExtraApiHeaders
	 */
	public function testRequestBearerMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage
		);

		$service->setAuthorizationMethod('bearer');

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, true));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getStorage
	 */
	public function testGetStorage(){
		$service = new MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(TokenStorageInterface::class, $service->getStorage());
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::refreshAccessToken
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getAccessTokenEndpoint
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::getExtraOAuthHeaders
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::parseAccessTokenResponse
	 */
	public function testRefreshAccessTokenSuccess(){
		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$token = $this->getMock(OAuth2Token::class);
		$token->expects($this->once())->method('getRefreshToken')->will($this->returnValue('foo'));

		$this->assertInstanceOf(OAuth2Token::class, $service->refreshAccessToken($token));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::isValidScope
	 */
	public function testIsValidScopeTrue(){
		$service = new MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertTrue($service->isValidScope('mock'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::__construct
	 * @covers OAuth\OAuth2\Service\AbstractServiceOauth2::isValidScope
	 */
	public function testIsValidScopeFalse(){
		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertFalse($service->isValidScope('invalid'));
	}
}
