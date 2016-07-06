<?php

namespace OAuthTest\Unit\Service\Providers\OAuth2;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\OauthException;
use OAuth\Service\OAuth2Service;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth2\Facebook;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth2Token;
use OAuth\Token\OAuth2TokenInterface;
use OAuth\Token\TokenInterface;

class FacebookTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
	}

	/**

	 */
	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Service::class, $service);
	}

	/**

	 */
	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Facebook(
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
		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://www.facebook.com/dialog/oauth', $service->getAuthorizationEndpoint()->getAbsoluteUri());
	}

	/**


	 */
	public function testGetAccessTokenEndpoint(){
		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('https://graph.facebook.com/oauth/access_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
	}

	/**

	 */
	public function testGetAuthorizationMethod(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

		$token = $this->getMock(OAuth2TokenInterface::class);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage
		);

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('OAuth foo', $headers, true));
	}

	/**


	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(ClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

		$service = new Facebook(
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
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('access_token=foo&expires=bar'));

		$service = new Facebook(
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
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('access_token=foo&expires=bar&refresh_token=baz'));

		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2Token::class, $service->requestAccessToken('foo'));
	}

	/**


	 */
	public function testGetDialogUriRedirectUriMissing(){
		$client = $this->getMock(ClientInterface::class);

		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$this->setExpectedException(OauthException::class);

		$service->getDialogUri('feed', []);
	}

	/**


	 */
	public function testGetDialogUriInstanceofUri(){
		$client = $this->getMock(ClientInterface::class);

		$service = new Facebook(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$dialogUri = $service->getDialogUri(
			'feed',
			[
				'redirect_uri' => 'http://www.facebook.com',
				'state'        => 'Random state',
			]
		);
		$this->assertInstanceOf(Uri::class, $dialogUri);
	}

	/**


	 */
	public function testGetDialogUriContainsAppIdAndOtherParameters(){
		$client      = $this->getMock(ClientInterface::class);
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())->method('getConsumerId')->will($this->returnValue('application_id'));

		$service = new Facebook(
			$credentials,
			$client,
			$this->getMock(TokenStorageInterface::class)
		);

		$dialogUri = $service->getDialogUri(
			'feed',
			[
				'redirect_uri' => 'http://www.facebook.com',
				'state'        => 'Random state',
			]
		);

		$queryString = $dialogUri->getQuery();
		parse_str($queryString, $queryArray);

		$this->assertArrayHasKey('app_id', $queryArray);
		$this->assertArrayHasKey('redirect_uri', $queryArray);
		$this->assertArrayHasKey('state', $queryArray);
	}
}
