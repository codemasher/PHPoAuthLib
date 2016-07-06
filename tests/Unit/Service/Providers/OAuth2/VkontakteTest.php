<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Vkontakte;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;

class VkontakteTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**

	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**

	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
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
		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://oauth.vk.com/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}

	/**


	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://oauth.vk.com/access_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}

	/**


	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
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

		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
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

		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
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

		$service = new Vkontakte(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}
}
