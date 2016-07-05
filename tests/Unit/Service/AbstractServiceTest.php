<?php

namespace OAuthTest\Unit\Service;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Uri;
use OAuth\OauthException;
use OAuth\Service\ServiceAbstract;
use OAuth\Service\ServiceInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuthTest\Mocks\MockServiceAbstract;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 */
	public function testConstructCorrectInterface(){
		$service = $this->getMockForAbstractClass(
			ServiceAbstract::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
			]
		);

		$this->assertInstanceOf(ServiceInterface::class, $service);
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::getStorage
	 */
	public function testGetStorage(){
		$service = $this->getMockForAbstractClass(
			ServiceAbstract::class,
			[
				$this->getMock(CredentialsInterface::class),
				$this->getMock(ClientInterface::class),
				$this->getMock(TokenStorageInterface::class),
			]
		);

		$this->assertInstanceOf(TokenStorageInterface::class, $service->getStorage());
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::service
	 */
	public function testService(){
		$service = new MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertSame('MockServiceAbstract', $service->service());
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testDetermineRequestUriFromPathUsingUriObject(){
		$service = new MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(
			Uri::class,
			$service->testDetermineRequestUriFromPath($this->getMock(Uri::class))
		);
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testDetermineRequestUriFromPathUsingHttpPath(){
		$service = new \OAuthTest\Mocks\MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$uri = $service->testDetermineRequestUriFromPath('http://example.com');

		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testDetermineRequestUriFromPathUsingHttpsPath(){
		$service = new \OAuthTest\Mocks\MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$uri = $service->testDetermineRequestUriFromPath('https://example.com');

		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertSame('https://example.com', $uri->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testDetermineRequestUriFromPathThrowsExceptionOnInvalidUri(){
		$this->setExpectedException(OauthException::class);

		$service = new MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$uri = $service->testDetermineRequestUriFromPath('example.com');
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testDetermineRequestUriFromPathWithQueryString(){
		$service = new MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$uri = $service->testDetermineRequestUriFromPath(
			'path?param1=value1',
			new Uri('https://example.com')
		);

		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertSame('https://example.com/path?param1=value1', $uri->getAbsoluteUri());
	}

	/**
	 * @covers OAuth\Common\Service\AbstractService::__construct
	 * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testDetermineRequestUriFromPathWithLeadingSlashInPath(){
		$service = new MockServiceAbstract(
			$this->getMock(CredentialsInterface::class),
			$this->getMock(ClientInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$uri = $service->testDetermineRequestUriFromPath(
			'/path',
			new Uri('https://example.com')
		);

		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertSame('https://example.com/path', $uri->getAbsoluteUri());
	}
}
