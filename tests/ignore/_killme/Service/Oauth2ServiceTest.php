<?php

namespace OAuthTest\Unit\Service;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;
use OAuth\OAuthException;
use OAuthTest\Mocks\MockOAuth2Service;

class Oauth2ServiceTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterface(){
		$service = $this->getMockForAbstractClass(
			OAuth2Service::class,
			[
				/*$this->getMock(CredentialsInterface::class),*/
				$this->getMock(HttpClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				'', '', '',
				[],
			]
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	public function testConstructCorrectParent(){
		$service = $this->getMockForAbstractClass(
			OAuth2Service::class,
			[
				/*$this->getMock(CredentialsInterface::class),*/
				$this->getMock(HttpClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				'', '', '',
				[],
			]
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	public function testConstructCorrectParentCustomUri(){
		$service = $this->getMockForAbstractClass(
			OAuth2Service::class,
			[
				/*$this->getMock(CredentialsInterface::class),*/
				$this->getMock(HttpClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				'', '', '',
				[],
				$this->getMock(Uri::class),
			]
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}


	public function testConstructThrowsExceptionOnInvalidScope(){
		$this->setExpectedException(OAuthException::class);

		$service = new MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'','','',
			['invalidscope']
		);
	}

	public function testGetAuthorizationUriWithoutParametersOrScopes(){
#		$credentials = $this->getMock(CredentialsInterface::class);
#		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
#		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new MockOAuth2Service(
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
			,'bar', 'foo', ''
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
			$service->getAuthorizationURL()->getAbsoluteUri()
		);
	}

	public function testGetAuthorizationUriWithParametersWithoutScopes(){
#		$credentials = $this->getMock(CredentialsInterface::class);
#		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
#		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'bar', 'foo', ''
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?foo=bar&baz=beer&type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
			$service->getAuthorizationURL(['foo' => 'bar', 'baz' => 'beer'])->getAbsoluteUri()
		);
	}


	public function testGetAuthorizationUriWithParametersAndScopes(){
#		$credentials = $this->getMock(CredentialsInterface::class);
#		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
#		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'bar', 'foo', '',
			['mock', 'mock2']
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?foo=bar&baz=beer&type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=mock+mock2',
			$service->getAuthorizationURL(['foo' => 'bar', 'baz' => 'beer'])->getAbsoluteUri()
		);
	}

	/**






	 */
	public function testRequestAccessToken(){
		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'','',''
		);

		$this->assertInstanceof(Token::class, $service->getOAuth2AccessToken('code'));
	}

	public function testRequestThrowsExceptionWhenTokenIsExpired(){
		$tokenExpiration = new \DateTime('26-03-1984 00:00:00');

		$token = $this->getMock(Token::class);
		$token->expects($this->any())->method('getExpiry')->will($this->returnValue($tokenExpiration->format('U')));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$storage,
			'','',''
		);

		$this->setExpectedException(OAuthException::class, 'Token expired on 03/26/1984 at 12:00:00 AM');

		$service->apiRequest('https://pieterhordijk.com/my/awesome/path');
	}

	/**







	 */
	public function testRequestOauthAuthorizationMethod(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getExpiry')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getOauth1AccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			'','',''
		);

		$headers = $service->apiRequest('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('OAuth foo', $headers, true));
	}

	/**







	 */
	public function testRequestQueryStringMethod(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getExpiry')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getOauth1AccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			'','',''
		);

		$service->setAuthorizationMethod('querystring');

		$uri         = $service->apiRequest('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('access_token=foo', $absoluteUri['query']);
	}

	/**







	 */
	public function testRequestQueryStringTwoMethod(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getExpiry')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getOauth1AccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			'','',''
		);

		$service->setAuthorizationMethod('querystring2');

		$uri         = $service->apiRequest('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('oauth2_access_token=foo', $absoluteUri['query']);
	}

	/**







	 */
	public function testRequestBearerMethod(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getExpiry')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getOauth1AccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			'','',''
		);

		$service->setAuthorizationMethod('bearer');

		$headers = $service->apiRequest('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, true));
	}



	/**





	 */
	public function testRefreshAccessTokenSuccess(){
		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'','',''
		);

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getRefreshToken')->will($this->returnValue('foo'));

		$this->assertInstanceOf(Token::class, $service->refreshAccessToken($token));
	}


	public function testIsValidScopeTrue(){
		$service = new MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'','',''
		);

		$this->assertTrue($service->isValidScope('mock'));
	}


	public function testIsValidScopeFalse(){
		$service = new \OAuthTest\Mocks\MockOAuth2Service(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			'','',''
		);

		$this->assertFalse($service->isValidScope('invalid'));
	}
}
