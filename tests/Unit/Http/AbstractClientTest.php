<?php

namespace OAuthTest\Unit\Http;

use OAuth\Http\AbstractHttpClient;
use OAuth\Http\ClientInterface;

class AbstractClientTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::__construct
	 */
	public function testConstructCorrectInterface(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$this->assertInstanceOf(ClientInterface::class, $client);
	}

	/**
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::__construct
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::setMaxRedirects
	 */
	public function testSetMaxRedirects(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$this->assertInstanceOf(AbstractHttpClient::class, $client->setMaxRedirects(10));
		$this->assertInstanceOf(ClientInterface::class, $client->setMaxRedirects(10));
	}

	/**
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::__construct
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::setTimeout
	 */
	public function testSetTimeout(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$this->assertInstanceOf(AbstractHttpClient::class, $client->setTimeout(25));
		$this->assertInstanceOf(ClientInterface::class, $client->setTimeout(25));
	}

	/**
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::__construct
	 * @covers OAuth\Common\Http\Client\AbstractHttpClient::normalizeHeaders
	 */
	public function testNormalizeHeaders(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$original = [
			'lowercasekey' => 'lowercasevalue',
			'UPPERCASEKEY' => 'UPPERCASEVALUE',
			'mIxEdCaSeKey' => 'MiXeDcAsEvAlUe',
			'31i71casekey' => '31i71casevalue',
		];

		$goal = [
			'lowercasekey' => 'Lowercasekey: lowercasevalue',
			'UPPERCASEKEY' => 'Uppercasekey: UPPERCASEVALUE',
			'mIxEdCaSeKey' => 'Mixedcasekey: MiXeDcAsEvAlUe',
			'31i71casekey' => '31i71casekey: 31i71casevalue',
		];

		$client->normalizeHeaders($original);

		$this->assertSame($goal, $original);
	}
}
