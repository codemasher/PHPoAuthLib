<?php

namespace OAuthTest\Unit\Http;

use OAuth\Http\AbstractHttpClient;
use OAuth\Http\HttpClientInterface;

class AbstractClientTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterface(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$this->assertInstanceOf(HttpClientInterface::class, $client);
	}


	public function testSetMaxRedirects(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$this->assertInstanceOf(AbstractHttpClient::class, $client->setMaxRedirects(10));
		$this->assertInstanceOf(HttpClientInterface::class, $client->setMaxRedirects(10));
	}


	public function testSetTimeout(){
		$client = $this->getMockForAbstractClass(AbstractHttpClient::class);

		$this->assertInstanceOf(AbstractHttpClient::class, $client->setTimeout(25));
		$this->assertInstanceOf(HttpClientInterface::class, $client->setTimeout(25));
	}


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
