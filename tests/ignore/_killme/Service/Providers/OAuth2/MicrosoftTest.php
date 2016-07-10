<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Microsoft;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;
use OAuth\TokenInterface;

class MicrosoftTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}


	public function testGetAuthorizationEndpoint(){
		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://login.live.com/oauth20_authorize.srf',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAccessTokenEndpoint(){
		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame(
			'https://login.live.com/oauth20_token.srf',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAuthorizationMethod(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getExpiry')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getOauth1AccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage
		);

		$uri         = $service->apiRequest('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($uri->getAbsoluteUri());

		$this->assertSame('access_token=foo', $absoluteUri['query']);
	}


	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getOAuth2AccessToken('foo');
	}


	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getOAuth2AccessToken('foo');
	}


	public function testParseAccessTokenResponseValidWithoutRefreshToken(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOAuth2AccessToken('foo'));
	}


	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new Microsoft(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOAuth2AccessToken('foo'));
	}
}
