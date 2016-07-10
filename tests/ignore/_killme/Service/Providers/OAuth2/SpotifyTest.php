<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Spotify;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;
use OAuth\TokenInterface;

class SpotifyTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}


	public function testGetAuthorizationEndpoint(){
		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://accounts.spotify.com/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}


	public function testGetAccessTokenEndpoint(){
		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://accounts.spotify.com/api/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}


	public function testGetAuthorizationMethod(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(Token::class);
		$token->expects($this->once())->method('getExpiry')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getOauth1AccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage
		);

		$headers = $service->apiRequest('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, true));
	}


	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Spotify(
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

		$service = new Spotify(
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

		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOAuth2AccessToken('foo'));
	}


	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new Spotify(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOAuth2AccessToken('foo'));
	}


	public function testGetExtraOAuthHeaders(){
		$client = $this->getMock(HttpClientInterface::class);
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

		$service = new Spotify(
			$credentials,
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOAuth2AccessToken('foo'));
	}
}
