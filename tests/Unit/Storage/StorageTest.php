<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Hannes Van De Vreken <vandevreken.hannes@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\OAuthException;
use OAuth\Token;

abstract class StorageTest extends \PHPUnit_Framework_TestCase{

	protected $storage;

	/**
	 * Check that the token gets properly stored.
	 */
	public function testStorage(){
		// arrange
		$service_1 = 'Facebook';
		$service_2 = 'Foursquare';

		$token_1 = new Token('access_1', 'refresh_1', Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);
		$token_2 = new Token('access_2', 'refresh_2', Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);

		// act
		$this->storage->storeAccessToken($service_1, $token_1);
		$this->storage->storeAccessToken($service_2, $token_2);

		// assert
		$extraParams = $this->storage->retrieveAccessToken($service_1)->extraParams;
		$this->assertEquals('param', $extraParams['extra']);
		$this->assertEquals($token_1, $this->storage->retrieveAccessToken($service_1));
		$this->assertEquals($token_2, $this->storage->retrieveAccessToken($service_2));
	}

	/**
	 * Test hasAccessToken.
	 */
	public function testHasAccessToken(){
		// arrange
		$service = 'Facebook';
		$this->storage->clearToken($service);

		// act
		// assert
		$this->assertFalse($this->storage->hasAccessToken($service));
	}

	/**
	 * Check that the token gets properly deleted.
	 */
	public function testStorageClears(){
		// arrange
		$service = 'Facebook';
		$token   = new Token('access', 'refresh', Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);

		// act
		$this->storage->storeAccessToken($service, $token);
		$this->storage->clearToken($service);

		// assert
		$this->setExpectedException(OAuthException::class);
		$this->storage->retrieveAccessToken($service);
	}
}
