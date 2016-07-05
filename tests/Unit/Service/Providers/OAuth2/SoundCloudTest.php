<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\SoundCloud;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class SoundCloudTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**

	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**

	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			[],
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**


	 */
	public function testGetAuthorizationEndpoint(){
		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://soundcloud.com/connect', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}

	/**


	 */
	public function testGetAccessTokenEndpoint(){
		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://api.soundcloud.com/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}

	/**


	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**


	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestAccessToken('foo');
	}

	/**


	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**


	 */
	public function testParseAccessTokenResponseValidWithRefreshToken(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$service = new SoundCloud(
			$this->getMock(CredentialsInterface::class),
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
