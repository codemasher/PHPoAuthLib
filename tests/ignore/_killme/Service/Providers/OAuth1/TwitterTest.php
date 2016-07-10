<?php

namespace OAuthTest\Unit\Service\Providers\OAuth1;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\OAuthException;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\Providers\OAuth1\Twitter;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;

class TwitterTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
	}

	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}


	public function testGetRequestTokenEndpoint(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.twitter.com/oauth/request_token',
			$service->getRequestTokenEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAuthorizationEndpoint(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
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


	public function testSetAuthorizationEndpoint(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(OAuthException::class);

		$service->setAuthorizationEndpoint('foo');
	}


	public function testGetAccessTokenEndpoint(){
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.twitter.com/oauth/access_token',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getRequestToken();
	}

	public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getRequestToken();
	}

	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getRequestToken();
	}

	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=false'
			)
		)
		;

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getRequestToken();
	}


	public function testParseRequestTokenResponseValid(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
			)
		)
		;

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getRequestToken());
	}

	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

		$token = $this->getMock(Token::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getOauth1AccessToken('foo', 'bar', $token);
	}

	public function testParseAccessTokenResponseValid(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_token=foo&oauth_token_secret=bar'
			)
		)
		;

		$token = $this->getMock(Token::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOauth1AccessToken('foo', 'bar', $token));
	}

	public function testParseAccessTokenErrorTotalBullshit(){
		$client  = $this->getMock(HttpClientInterface::class);
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["hoho"]);
	}

	public function testParseAccessTokenErrorItsAnError(){
		$client  = $this->getMock(HttpClientInterface::class);
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["error=hihihaha"]);
	}

	public function testParseAccessTokenErrorItsMissingOauthToken(){
		$client  = $this->getMock(HttpClientInterface::class);
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);
		$method = new \ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
		$method->setAccessible(true);
		$method->invokeArgs($service, ["oauth_token_secret=1"]);
	}

	public function testParseAccessTokenErrorItsMissingOauthTokenSecret(){
		$client  = $this->getMock(HttpClientInterface::class);
		$service = new Twitter(
			/*$this->getMock(CredentialsInterface::class),*/
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
