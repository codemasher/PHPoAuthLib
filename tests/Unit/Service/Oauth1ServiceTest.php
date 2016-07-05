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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::__construct
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::__construct
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::requestRequestToken
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::buildAuthorizationHeaderForTokenRequest
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getVersion
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getExtraOAuthHeaders
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::parseRequestTokenResponse
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getAuthorizationUri
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getAuthorizationEndpoint
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getAuthorizationUri
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getAuthorizationEndpoint
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::requestAccessToken
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::service
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::buildAuthorizationHeaderForAPIRequest
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getVersion
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getAccessTokenEndpoint
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getExtraOAuthHeaders
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::parseAccessTokenResponse
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::requestAccessToken
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::service
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::buildAuthorizationHeaderForAPIRequest
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getVersion
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getAccessTokenEndpoint
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getExtraOAuthHeaders
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::parseAccessTokenResponse
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::request
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::determineRequestUriFromPath
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::service
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getExtraApiHeaders
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::buildAuthorizationHeaderForAPIRequest
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::getVersion
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
	 * @covers OAuth\OAuth1\Service\AbstractServiceOauth1::request
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
