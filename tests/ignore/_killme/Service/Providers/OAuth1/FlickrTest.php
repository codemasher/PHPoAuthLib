<?php

namespace OAuthTest\Unit\Service\Providers\OAuth1;


use OAuth\Http\HttpClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\Providers\OAuth1\Flickr;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token;

class FlickrTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterfaceWithoutCustomUri(){
		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
	}

	public function testConstructCorrectInstanceWithoutCustomUri(){
		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}

	public function testConstructCorrectInstanceWithCustomUri(){
		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertInstanceOf(ServiceAbstract::class, $service);
	}


	public function testGetRequestTokenEndpoint(){
		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://www.flickr.com/services/oauth/request_token',
			$service->getRequestTokenEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAuthorizationEndpoint(){
		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://www.flickr.com/services/oauth/authorize',
			$service->getAuthorizationEndpoint()->getAbsoluteUri()
		);
	}


	public function testGetAccessTokenEndpoint(){
		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$this->getMock(HttpClientInterface::class),
			$this->getMock(TokenStorageInterface::class),
			$this->getMock(SignatureInterface::class)
		);

		$this->assertSame(
			'https://www.flickr.com/services/oauth/access_token',
			$service->getAccessTokenEndpoint()->getAbsoluteUri()
		);
	}

	public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

		$service = new Flickr(
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

		$service = new Flickr(
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

		$service = new Flickr(
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

		$service = new Flickr(
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

		$service = new Flickr(
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

		$service = new Flickr(
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

		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class)
		);

		$this->assertInstanceOf(Token::class, $service->getOauth1AccessToken('foo', 'bar', $token));
	}

	public function testRequest(){
		$client = $this->getMock(HttpClientInterface::class);
		$client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

		$token = $this->getMock(Token::class);

		$storage = $this->getMock(TokenStorageInterface::class);
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Flickr(
			/*$this->getMock(CredentialsInterface::class),*/
			$client,
			$storage,
			$this->getMock(SignatureInterface::class),
			$this->getMock(Uri::class)
		);

		$this->assertSame('response!', $service->apiRequest('/my/awesome/path'));
	}
}
