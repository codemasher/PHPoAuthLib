<?php

namespace OAuthTest\Unit\Http;

use InvalidArgumentException;
use OAuth\Http\AbstractHttpClient;
use OAuth\Http\CurlClient;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;

class CurlClientTest extends \PHPUnit_Framework_TestCase{

	/**
	 *
	 */
	public function testConstructCorrectInstance(){
		$client = new CurlClient();

		$this->assertInstanceOf(AbstractHttpClient::class, $client);
	}

	/**

	 */
	public function testSetForceSSL3(){
		$client = new CurlClient();

		$this->assertInstanceOf(CurlClient::class, $client->setForceSSL3(true));
	}

	/**

	 */
	public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody(){
		$this->setExpectedException(InvalidArgumentException::class);

		$client = new CurlClient();

		$client->retrieveResponse(
			$this->getMock(Uri::class),
			'foo',
			[],
			'GET'
		);
	}

	/**

	 */
	public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper(){
		$this->setExpectedException(InvalidArgumentException::class);

		$client = new CurlClient();

		$client->retrieveResponse(
			$this->getMock(Uri::class),
			'foo',
			[],
			'get'
		);
	}

	/**


	 */
	public function testRetrieveResponseDefaultUserAgent(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/get'))
		;

		$client = new CurlClient();

		$response = $client->retrieveResponse(
			$endPoint,
			'',
			[],
			'get'
		);

		$response = json_decode($response, true);

		$this->assertSame('PHPoAuthLib', $response['headers']['User-Agent']);
	}

	/**


	 */
	public function testRetrieveResponseCustomUserAgent(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/get'))
		;

		$client = new CurlClient('My Super Awesome Http Client');

		$response = $client->retrieveResponse(
			$endPoint,
			'',
			[],
			'get'
		);

		$response = json_decode($response, true);

		$this->assertSame('My Super Awesome Http Client', $response['headers']['User-Agent']);
	}

	/**

	 */
	public function testRetrieveResponseWithCustomContentType(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/get'))
		;

		$client = new CurlClient();

		$response = $client->retrieveResponse(
			$endPoint,
			'',
			['Content-Type' => 'foo/bar'],
			'get'
		);

		$response = json_decode($response, true);

		$this->assertSame('foo/bar', $response['headers']['Content-Type']);
	}

	/**

	 */
	public function testRetrieveResponseWithFormUrlEncodedContentType(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/post'))
		;

		$client = new CurlClient();

		$response = $client->retrieveResponse(
			$endPoint,
			['foo' => 'bar', 'baz' => 'fab'],
			[],
			'POST'
		);

		$response = json_decode($response, true);

		$this->assertSame('application/x-www-form-urlencoded', $response['headers']['Content-Type']);
		$this->assertEquals(['foo' => 'bar', 'baz' => 'fab'], $response['form']);
	}

	/**

	 */
	public function testRetrieveResponseHost(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/post'))
		;

		$client = new CurlClient();

		$response = $client->retrieveResponse(
			$endPoint,
			['foo' => 'bar', 'baz' => 'fab'],
			[],
			'POST'
		);

		$response = json_decode($response, true);

		$this->assertSame('httpbin.org', $response['headers']['Host']);
	}

	/**

	 */
	public function testRetrieveResponsePostRequestWithRequestBodyAsString(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/post'))
		;

		$formData = ['baz' => 'fab', 'foo' => 'bar'];

		$client = new CurlClient();

		$response = $client->retrieveResponse(
			$endPoint,
			$formData,
			[],
			'POST'
		);

		$response = json_decode($response, true);

		$this->assertSame($formData, $response['form']);
	}

	/**

	 */
	public function testRetrieveResponsePutRequestWithRequestBodyAsString(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/put'))
		;

		$formData = ['baz' => 'fab', 'foo' => 'bar'];

		$client = new CurlClient();

		$response = $client->retrieveResponse(
			$endPoint,
			$formData,
			[],
			'PUT'
		);

		$response = json_decode($response, true);

		$this->assertSame($formData, $response['form']);
	}

	/**

	 */
	public function testRetrieveResponsePutRequestWithRequestBodyAsStringNoRedirects(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/put'))
		;

		$formData = ['baz' => 'fab', 'foo' => 'bar'];

		$client = new CurlClient();

		$client->setMaxRedirects(0);

		$response = $client->retrieveResponse(
			$endPoint,
			$formData,
			[],
			'PUT'
		);

		$response = json_decode($response, true);

		$this->assertSame($formData, $response['form']);
	}

	/**

	 */
	public function testRetrieveResponseWithForcedSsl3(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('https://httpbin.org/get'))
		;

		$client = new CurlClient();

		$client->setForceSSL3(true);

		try{
			$response = $client->retrieveResponse(
				$endPoint,
				'',
				['Content-Type' => 'foo/bar'],
				'get'
			);
		}
		catch(TokenResponseException $e){
			if(strpos($e->getMessage(), 'cURL Error # 35') !== false){
				$this->markTestSkipped('SSL peer handshake failed: '.$e->getMessage());
			}
		}

		$response = json_decode($response, true);

		$this->assertSame('foo/bar', $response['headers']['Content-Type']);
	}

	/**

	 */
	public function testRetrieveResponseThrowsExceptionOnInvalidUrl(){
		$this->setExpectedException(TokenResponseException::class);

		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('jkehfkefcmekjhcnkerjh'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('jkehfkefcmekjhcnkerjh'))
		;

		$client = new CurlClient();

		$client->setForceSSL3(true);

		$response = $client->retrieveResponse(
			$endPoint,
			'',
			['Content-Type' => 'foo/bar'],
			'get'
		);

		$response = json_decode($response, true);

		$this->assertSame('foo/bar', $response['headers']['Content-Type']);
	}

	public function testAdditionalParameters(){
		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('httpbin.org'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('http://httpbin.org/gzip'))
		;

		$client = new CurlClient();
		$client->setCurlParameters(
			[
				CURLOPT_ENCODING => 'gzip',
			]
		);

		$response = $client->retrieveResponse(
			$endPoint,
			'',
			[],
			'get'
		);

		$response = json_decode($response, true);

		$this->assertNotNull($response);
		$this->assertSame('gzip', $response['headers']['Accept-Encoding']);
		$this->assertTrue($response['gzipped']);
	}
}
