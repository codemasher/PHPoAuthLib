<?php

namespace OAuthTest\Unit\Service\Providers\OAuth1;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\Providers\OAuth1\Flickr;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth1Token;
use OAuth\Token\OAuth1TokenInterface;

class FlickrTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 */
	public function testGetRequestTokenEndpoint(){
		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://www.flickr.com/services/oauth/request_token',
			$service->getRequestTokenEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://www.flickr.com/services/oauth/authorize',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://www.flickr.com/services/oauth/access_token',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=false'
			)
		)
		;

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
	 * @covers OAuth\OAuth1\Service\Flickr::parseAccessTokenResponse
	 */
	public function testParseRequestTokenResponseValid(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
			)
		)
		;

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestRequestToken());
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

		$token = $this->getMock(OAuth1TokenInterface::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo', 'bar', $token);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::__construct
	 * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Flickr::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValid(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_token=foo&oauth_token_secret=bar'
			)
		)
		;

		$token = $this->getMock(OAuth1TokenInterface::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestAccessToken('foo', 'bar', $token));
	}

	/**
	 * @covers OAuth\OAuth1\Service\Flickr::request
	 */
	public function testRequest(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

		$token = $this->getMock(OAuth1TokenInterface::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Flickr(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame('response!', $service->request('/my/awesome/path'));
	}
}
