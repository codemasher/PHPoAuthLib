<?php

namespace OAuthTest\Unit\Service;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth1Token;
use OAuth\Token\OAuth1TokenInterface;
use OAuthTest\Mocks\MockOAuth1Service;

class Oauth1ServiceTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterface(){
		$service = $this->getMockForAbstractClass(
			OAuth1Service::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				$this->getMock(SignatureInterface::class),
				$this->getMock(Uri::class),
			]
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
	}

	/**

	 */
	public function testConstructCorrectParent(){
		$service = $this->getMockForAbstractClass(
			OAuth1Service::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
				$this->getMock(SignatureInterface::class),
				$this->getMock(Uri::class),
			]
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	/**








	 */
	public function testRequestRequestTokenBuildAuthHeaderTokenRequestWithoutParams(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnCallback(
				function($endpoint, $array, $headers){
					\PHPUnit_Framework_Assert::assertSame('http://pieterhordijk.com/token', $endpoint->getAbsoluteUri());
				}
			)
		)
		;

		$service = new \OAuthTest\Mocks\MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestRequestToken());
	}

	/**


	 */
	public function testGetAuthorizationUriWithoutParameters(){
		$service = new \OAuthTest\Mocks\MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame('http://pieterhordijk.com/auth', $service->getAuthorizationUri()->getAbsoluteUri());
	}

	/**


	 */
	public function testGetAuthorizationUriWithParameters(){
		$service = new \OAuthTest\Mocks\MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?foo=bar&baz=beer', $service->getAuthorizationUri(
			[
				'foo' => 'bar',
				'baz' => 'beer',
			]
		)->getAbsoluteUri()
		);
	}

	/**










	 */
	public function testRequestAccessTokenWithoutSecret(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnCallback(
				function($endpoint, $array, $headers){
					\PHPUnit_Framework_Assert::assertSame('http://pieterhordijk.com/access', $endpoint->getAbsoluteUri());
				}
			)
		)
		;

		$token = $this->getMock(OAuth1TokenInterface::class);
		$token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestAccessToken('foo', 'bar'));
	}

	/**










	 */
	public function testRequestAccessTokenWithSecret(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnCallback(
				function($endpoint, $array, $headers){
					\PHPUnit_Framework_Assert::assertSame('http://pieterhordijk.com/access', $endpoint->getAbsoluteUri());
				}
			)
		)
		;

		$token = $this->getMock(OAuth1TokenInterface::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestAccessToken('foo', 'bar', $token));
	}

	/**









	 */
	public function testRequest(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

		$token = $this->getMock(OAuth1TokenInterface::class);
		//$token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame('response!', $service->request('/my/awesome/path'));
	}

	/**
	 * This test only captures a regression in php 5.3.
	 *

	 */
	public function testRequestNonArrayBody(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

		$token = $this->getMock(OAuth1TokenInterface::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new \OAuthTest\Mocks\MockOAuth1Service(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame('response!', $service->request('/my/awesome/path', 'GET', 'A text body'));
	}

}
