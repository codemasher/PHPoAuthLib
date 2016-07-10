<?php

namespace OAuthTest\Unit\Service\Providers\OAuth1;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\Providers\OAuth1\Yahoo;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;

class YahooTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
	}

	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}


	public function testGetRequestTokenEndpoint(){
		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.login.yahoo.com/oauth/v2/get_request_token',
			$service->getRequestTokenEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAuthorizationEndpoint(){
		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.login.yahoo.com/oauth/v2/request_auth',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAccessTokenEndpoint(){
		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://api.login.yahoo.com/oauth/v2/get_token',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestToken;
	}

	public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestToken;
	}

	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestToken;
	}

	public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=false'
			)
		)
		;

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->requestToken;
	}


	public function testParseRequestTokenResponseValid(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will(
			$this->returnValue(
				'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
			)
		)
		;

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->requestToken);
	}

	public function testParseAccessTokenResponseThrowsExceptionOnError(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

		$token = $this->getMock(Token::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->setExpectedException(TokenResponseException::class);

		$service->getAccessToken('foo', 'bar', $token);
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

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getAccessToken('foo', 'bar', $token));
	}

	public function testRequest(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

		$token = $this->getMock(Token::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Yahoo(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame('response!', $service->request('/my/awesome/path'));
	}
}
