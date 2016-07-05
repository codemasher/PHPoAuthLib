<?php

namespace OAuthTest\Unit\Service\Providers\OAuth1;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\OauthException;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\Providers\OAuth1\Twitter;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth1Token;
use OAuth\Token\OAuth1TokenInterface;

class TwitterTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 */
	public function testGetRequestTokenEndpoint(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.twitter.com/oauth/request_token',
			$service->getRequestTokenEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertTrue(
			in_array(
				strtolower($service->getAuthorizationEndpoint()->getAbsoluteUri()),
				[
					Twitter::ENDPOINT_AUTHENTICATE,
					Twitter::ENDPOINT_AUTHORIZE,
				]
			)
		);

		$service->setAuthorizationEndpoint(Twitter::ENDPOINT_AUTHORIZE);

		$this->assertTrue(
			in_array(
				strtolower($service->getAuthorizationEndpoint()->getAbsoluteUri()),
				[
					Twitter::ENDPOINT_AUTHENTICATE,
					Twitter::ENDPOINT_AUTHORIZE,
				]
			)
		);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::setAuthorizationEndpoint
	 */
	public function testSetAuthorizationEndpoint(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(OauthException::class);

		$service->setAuthorizationEndpoint('foo');
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.twitter.com/oauth/access_token',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
	 */
	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=false'
			)
		)
		;

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestRequestToken();
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
	 */
	public function testParseRequestTokenResponseValid(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
			)
		)
		;

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestRequestToken());
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

		$token = $this->getMock(OAuth1TokenInterface::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo', 'bar', $token);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::__construct
	 * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
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

		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1Token::class, $service->requestAccessToken('foo', 'bar', $token));
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
	 */
	public function testParseAccessTokenErrorTotalBullshit(){
		$client  = $this->getMock(ClientInterface::class);
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["hoho"]);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
	 */
	public function testParseAccessTokenErrorItsAnError(){
		$client  = $this->getMock(ClientInterface::class);
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["error=hihihaha"]);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
	 */
	public function testParseAccessTokenErrorItsMissingOauthToken(){
		$client  = $this->getMock(ClientInterface::class);
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["oauth_token_secret=1"]);
	}

	/**
	 * @covers OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
	 */
	public function testParseAccessTokenErrorItsMissingOauthTokenSecret(){
		$client  = $this->getMock(ClientInterface::class);
		$service = new Twitter(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["oauth_token=1"]);
	}
}
