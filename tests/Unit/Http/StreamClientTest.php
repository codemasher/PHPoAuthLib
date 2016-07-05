<?php

namespace OAuthTest\Unit\Http;

use InvalidArgumentException;
use OAuth\Http\AbstractHttpClient;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\StreamClient;
use OAuth\Http\Uri;

class StreamClientTest extends \PHPUnit_Framework_TestCase{

	/**
	 *
	 */
	public function testConstructCorrectInstance(){
		$client = new StreamClient();

		$this->assertInstanceOf(AbstractHttpClient::class, $client);
	}

	/**
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 */
	public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody(){
		$this->setExpectedException(InvalidArgumentException::class);

		$client = new StreamClient();

		$client->retrieveResponse(
			$this->getMock(Uri::class),
			'foo',
			[],
			'GET'
		);
	}

	/**
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 */
	public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper(){
		$this->setExpectedException(InvalidArgumentException::class);

		$client = new StreamClient();

		$client->retrieveResponse(
			$this->getMock(Uri::class),
			'foo',
			[],
			'get'
		);
	}

	/**
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient();

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient('My Super Awesome Http Client');

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient();

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient();

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient();

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient();

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
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

		$client = new StreamClient();

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
	 * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
	 * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
	 */
	public function testRetrieveResponseThrowsExceptionOnInvalidRequest(){
		$this->setExpectedException(TokenResponseException::class);

		$endPoint = $this->getMock(Uri::class);
		$endPoint->expects($this->any())
		         ->method('getHost')
		         ->will($this->returnValue('dskjhfckjhekrsfhkehfkreljfrekljfkre'))
		;
		$endPoint->expects($this->any())
		         ->method('getAbsoluteUri')
		         ->will($this->returnValue('dskjhfckjhekrsfhkehfkreljfrekljfkre'))
		;

		$client = new StreamClient();

		$response = $client->retrieveResponse(
			$endPoint,
			'',
			['Content-Type' => 'foo/bar'],
			'get'
		);

		$response = json_decode($response, true);

		$this->assertSame('foo/bar', $response['headers']['Content-Type']);
	}
}
