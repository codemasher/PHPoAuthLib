<?php
/**
 *
 * @filesource   CredentialsTest.php
 * @created      08.07.2016
 * @package      OAuthTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace OAuthTest;

use OAuth\Credentials;

class CredentialsTest extends \PHPUnit_Framework_TestCase{

	public function testGetSet(){
		$credentials = new Credentials;

		$this->assertInstanceOf(Credentials::class, $credentials);

		$credentials->key = 'foobar';
		$credentials->foo = 'bar';

		$this->assertEquals('foobar', $credentials->key);
		$this->assertEquals(false, $credentials->foo);
	}

	public function testGetSetWithArgs(){
		$credentials = new Credentials([
			'key' => 'foo',
			'secret' => 'bar',
			'callbackURL' => 'https://foo.bar',
			'foo' => 'whatever', // OOPS!
		]);

		$this->assertInstanceOf(Credentials::class, $credentials);

		$this->assertEquals('foo', $credentials->key);
		$this->assertEquals('bar', $credentials->secret);
		$this->assertEquals('https://foo.bar', $credentials->callbackURL);
		$this->assertEquals(false, $credentials->foo);
	}

}
