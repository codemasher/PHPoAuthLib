<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Chris Heng <bigblah@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuth\Unit\_killme;

use OAuth\_killme\CredentialsInterface;
use OAuth\_killme\ServiceFactory;
use OAuth\Http\ClientInterface;
use OAuth\OauthException;
use OAuth\Service\OAuth1ServiceInterface;
use OAuth\Service\OAuth2ServiceInterface;
use OAuth\Service\Providers\OAuth1\Twitter;
use OAuth\Service\Providers\OAuth2\Facebook;
use OAuth\Storage\TokenStorageInterface;
use OAuthTest\Mocks\FakeOAuth1Service;
use OAuthTest\Mocks\FakeOAuth2Service;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @covers OAuth\ServiceFactory::setHttpClient
	 */
	public function testSetHttpClient(){
		$factory = new ServiceFactory();

		$this->assertInstanceOf(
			ServiceFactory::class,
			$factory->setHttpClient($this->getMock(ClientInterface::class))
		);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 */
	public function testRegisterServiceThrowsExceptionNonExistentClass(){
		$this->setExpectedException(OauthException::class);

		$factory = new ServiceFactory();
		$factory->registerService('foo', 'bar');
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 */
	public function testRegisterServiceThrowsExceptionWithClassIncorrectImplementation(){
		$this->setExpectedException(OauthException::class);

		$factory = new ServiceFactory();
		$factory->registerService('foo', ServiceFactory::class);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 */
	public function testRegisterServiceSuccessOAuth1(){
		$factory = new ServiceFactory();

		$this->assertInstanceOf(
			ServiceFactory::class,
			$factory->registerService('foo', FakeOAuth1Service::class)
		);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 */
	public function testRegisterServiceSuccessOAuth2(){
		$factory = new ServiceFactory();

		$this->assertInstanceOf(
			ServiceFactory::class,
			$factory->registerService('foo', FakeOAuth2Service::class)
		);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV1Service
	 */
	public function testCreateServiceOAuth1NonRegistered(){
		$factory = new ServiceFactory();

		$service = $factory->createService(
			'twitter',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Twitter::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV1Service
	 */
	public function testCreateServiceOAuth1Registered(){
		$factory = new ServiceFactory();

		$factory->registerService('foo', FakeOAuth1Service::class);

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth1Service::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV1Service
	 */
	public function testCreateServiceOAuth1RegisteredAndNonRegisteredSameName(){
		$factory = new ServiceFactory();

		$factory->registerService('twitter', FakeOAuth1Service::class);

		$service = $factory->createService(
			'twitter',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth1ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth1Service::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV2Service
	 * @covers OAuth\ServiceFactory::resolveScopes
	 */
	public function testCreateServiceOAuth2NonRegistered(){
		$factory = new ServiceFactory();

		$service = $factory->createService(
			'facebook',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(Facebook::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV2Service
	 * @covers OAuth\ServiceFactory::resolveScopes
	 */
	public function testCreateServiceOAuth2Registered(){
		$factory = new ServiceFactory();

		$factory->registerService('foo', FakeOAuth2Service::class);

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV2Service
	 * @covers OAuth\ServiceFactory::resolveScopes
	 */
	public function testCreateServiceOAuth2RegisteredAndNonRegisteredSameName(){
		$factory = new ServiceFactory();

		$factory->registerService('facebook', FakeOAuth2Service::class);

		$service = $factory->createService(
			'facebook',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV1Service
	 */
	public function testCreateServiceThrowsExceptionOnPassingScopesToV1Service(){
		$this->setExpectedException(OauthException::class);

		$factory = new ServiceFactory();

		$factory->registerService('foo', FakeOAuth1Service::class);

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class),
			['bar']
		);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 */
	public function testCreateServiceNonExistentService(){
		$factory = new ServiceFactory();

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertNull($service);
	}

	/**
	 * @covers OAuth\ServiceFactory::registerService
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV2Service
	 * @covers OAuth\ServiceFactory::resolveScopes
	 */
	public function testCreateServicePrefersOauth2(){
		$factory = new ServiceFactory();

		$factory->registerService('foo', FakeOAuth1Service::class);
		$factory->registerService('foo', FakeOAuth2Service::class);

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class)
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV2Service
	 * @covers OAuth\ServiceFactory::resolveScopes
	 */
	public function testCreateServiceOAuth2RegisteredWithClassConstantsAsScope(){
		$factory = new ServiceFactory();

		$factory->registerService('foo', FakeOAuth2Service::class);

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class),
			['FOO']
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth2Service::class, $service);
	}

	/**
	 * @covers OAuth\ServiceFactory::createService
	 * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
	 * @covers OAuth\ServiceFactory::buildV2Service
	 * @covers OAuth\ServiceFactory::resolveScopes
	 */
	public function testCreateServiceOAuth2RegisteredWithCustomScope(){
		$factory = new ServiceFactory();

		$factory->registerService('foo', FakeOAuth2Service::class);

		$service = $factory->createService(
			'foo',
			$this->getMock(CredentialsInterface::class),
			$this->getMock(TokenStorageInterface::class),
			['custom']
		);

		$this->assertInstanceOf(OAuth2ServiceInterface::class, $service);
		$this->assertInstanceOf(FakeOAuth2Service::class, $service);
	}
}
