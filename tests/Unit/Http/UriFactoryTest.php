<?php

namespace OAuthTest\Unit\Common\Http;

use OAuth\Http\Uri;
use OAuth\Http\UriFactory;

class UriFactoryTest extends \PHPUnit_Framework_TestCase{

	/**
	 *
	 */
	public function testConstructCorrectInterface(){
		$factory = new UriFactory();

		$this->assertInstanceOf(UriFactory::class, $factory);
	}

	/**


	 */
	public function testCreateFromSuperGlobalArrayUsingProxyStyle(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(['REQUEST_URI' => 'http://example.com']);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayHttp(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTPS'        => 'off',
				'HTTP_HOST'    => 'example.com',
				'REQUEST_URI'  => '/foo',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**
	 * This looks wonky David. Should the port really fallback to 80 even when supplying https as scheme?
	 *








	 */
	public function testCreateFromSuperGlobalArrayHttps(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTPS'        => 'on',
				'HTTP_HOST'    => 'example.com',
				'REQUEST_URI'  => '/foo',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('https://example.com:80/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayPortSupplied(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'    => 'example.com',
				'SERVER_PORT'  => 21,
				'REQUEST_URI'  => '/foo',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com:21/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayPortNotSet(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'    => 'example.com',
				'REQUEST_URI'  => '/foo',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayRequestUriSet(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'    => 'example.com',
				'REQUEST_URI'  => '/foo',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayRedirectUrlSet(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'    => 'example.com',
				'REDIRECT_URL' => '/foo',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayThrowsExceptionOnDetectingPathMissingIndices(){
		$factory = new UriFactory();

		$this->setExpectedException('\\RuntimeException');

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'    => 'example.com',
				'QUERY_STRING' => 'param1=value1',
			]
		);
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayWithQueryString(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'    => 'example.com',
				'REQUEST_URI'  => '/foo?param1=value1',
				'QUERY_STRING' => 'param1=value1',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayWithoutQueryString(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'   => 'example.com',
				'REQUEST_URI' => '/foo',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo', $uri->getAbsoluteUri());
	}

	/**








	 */
	public function testCreateFromSuperGlobalArrayHostWithColon(){
		$factory = new UriFactory();

		$uri = $factory->createFromSuperGlobalArray(
			[
				'HTTP_HOST'   => 'example.com:80',
				'REQUEST_URI' => '/foo',
			]
		);

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com/foo', $uri->getAbsoluteUri());
	}

	/**

	 */
	public function testCreateFromAbsolute(){
		$factory = new UriFactory();

		$uri = $factory->createFromAbsolute('http://example.com');

		$this->assertInstanceOf(
			Uri::class,
			$uri
		);

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}
}
