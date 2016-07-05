<?php

namespace OAuthTest\Unit\Http;

use InvalidArgumentException;
use OAuth\Http\Uri;

class UriTest extends \PHPUnit_Framework_TestCase{

	/**

	 */
	public function testConstructCorrectInterfaceWithoutUri(){
		$uri = new Uri();

		$this->assertInstanceOf(Uri::class, $uri);
	}

	/**


	 */
	public function testConstructThrowsExceptionOnInvalidUri(){
		$this->setExpectedException(InvalidArgumentException::class);

		// http://lxr.php.net/xref/PHP_5_4/ext/standard/tests/url/urls.inc#92
		$uri = new Uri('http://@:/');
	}

	/**


	 */
	public function testConstructThrowsExceptionOnUriWithoutScheme(){
		$this->setExpectedException(InvalidArgumentException::class);

		$uri = new Uri('www.pieterhordijk.com');
	}

	/**



	 */
	public function testGetScheme(){
		$uri = new Uri('http://example.com');

		$this->assertSame('http', $uri->getScheme());
	}

	/**





	 */
	public function testGetUserInfo(){
		$uri = new Uri('http://peehaa@example.com');

		$this->assertSame('peehaa', $uri->getUserInfo());
	}

	/**





	 */
	public function testGetUserInfoWithPass(){
		$uri = new Uri('http://peehaa:pass@example.com');

		$this->assertSame('peehaa:********', $uri->getUserInfo());
	}

	/**





	 */
	public function testGetRawUserInfo(){
		$uri = new Uri('http://peehaa@example.com');

		$this->assertSame('peehaa', $uri->getRawUserInfo());
	}

	/**





	 */
	public function testGetRawUserInfoWithPass(){
		$uri = new Uri('http://peehaa:pass@example.com');

		$this->assertSame('peehaa:pass', $uri->getRawUserInfo());
	}

	/**



	 */
	public function testGetHost(){
		$uri = new Uri('http://example.com');

		$this->assertSame('example.com', $uri->getHost());
	}

	/**



	 */
	public function testGetPortImplicitHttp(){
		$uri = new Uri('http://example.com');

		$this->assertSame(80, $uri->getPort());
	}

	/**



	 */
	public function testGetPortImplicitHttps(){
		$uri = new Uri('https://example.com');

		$this->assertSame(443, $uri->getPort());
	}

	/**



	 */
	public function testGetPortExplicit(){
		$uri = new Uri('http://example.com:21');

		$this->assertSame(21, $uri->getPort());
	}

	/**



	 */
	public function testGetPathNotSupplied(){
		$uri = new Uri('http://example.com');

		$this->assertSame('/', $uri->getPath());
	}

	/**



	 */
	public function testGetPathSlash(){
		$uri = new Uri('http://example.com/');

		$this->assertSame('/', $uri->getPath());
	}

	/**



	 */
	public function testGetPath(){
		$uri = new Uri('http://example.com/foo');

		$this->assertSame('/foo', $uri->getPath());
	}

	/**



	 */
	public function testGetQueryWithParams(){
		$uri = new Uri('http://example.com?param1=first&param2=second');

		$this->assertSame('param1=first&param2=second', $uri->getQuery());
	}

	/**



	 */
	public function testGetQueryWithoutParams(){
		$uri = new Uri('http://example.com');

		$this->assertSame('', $uri->getQuery());
	}

	/**



	 */
	public function testGetFragmentExists(){
		$uri = new Uri('http://example.com#foo');

		$this->assertSame('foo', $uri->getFragment());
	}

	/**



	 */
	public function testGetFragmentNotExists(){
		$uri = new Uri('http://example.com');

		$this->assertSame('', $uri->getFragment());
	}

	/**



	 */
	public function testGetAuthorityWithoutUserInfo(){
		$uri = new Uri('http://example.com');

		$this->assertSame('example.com', $uri->getAuthority());
	}

	/**



	 */
	public function testGetAuthorityWithoutUserInfoWithExplicitPort(){
		$uri = new Uri('http://example.com:21');

		$this->assertSame('example.com:21', $uri->getAuthority());
	}

	/**





	 */
	public function testGetAuthorityWithUsernameWithExplicitPort(){
		$uri = new Uri('http://peehaa@example.com:21');

		$this->assertSame('peehaa@example.com:21', $uri->getAuthority());
	}

	/**





	 */
	public function testGetAuthorityWithUsernameAndPassWithExplicitPort(){
		$uri = new Uri('http://peehaa:pass@example.com:21');

		$this->assertSame('peehaa:********@example.com:21', $uri->getAuthority());
	}

	/**





	 */
	public function testGetAuthorityWithUsernameAndPassWithoutExplicitPort(){
		$uri = new Uri('http://peehaa:pass@example.com');

		$this->assertSame('peehaa:********@example.com', $uri->getAuthority());
	}

	/**



	 */
	public function testGetRawAuthorityWithoutUserInfo(){
		$uri = new Uri('http://example.com');

		$this->assertSame('example.com', $uri->getRawAuthority());
	}

	/**



	 */
	public function testGetRawAuthorityWithoutUserInfoWithExplicitPort(){
		$uri = new Uri('http://example.com:21');

		$this->assertSame('example.com:21', $uri->getRawAuthority());
	}

	/**





	 */
	public function testGetRawAuthorityWithUsernameWithExplicitPort(){
		$uri = new Uri('http://peehaa@example.com:21');

		$this->assertSame('peehaa@example.com:21', $uri->getRawAuthority());
	}

	/**





	 */
	public function testGetRawAuthorityWithUsernameAndPassWithExplicitPort(){
		$uri = new Uri('http://peehaa:pass@example.com:21');

		$this->assertSame('peehaa:pass@example.com:21', $uri->getRawAuthority());
	}

	/**





	 */
	public function testGetRawAuthorityWithUsernameAndPassWithoutExplicitPort(){
		$uri = new Uri('http://peehaa:pass@example.com');

		$this->assertSame('peehaa:pass@example.com', $uri->getRawAuthority());
	}

	/**



	 */
	public function testGetAbsoluteUriBare(){
		$uri = new Uri('http://example.com');

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**






	 */
	public function testGetAbsoluteUriWithAuthority(){
		$uri = new Uri('http://peehaa:pass@example.com');

		$this->assertSame('http://peehaa:pass@example.com', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testGetAbsoluteUriWithPath(){
		$uri = new Uri('http://example.com/foo');

		$this->assertSame('http://example.com/foo', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testGetAbsoluteUriWithoutPath(){
		$uri = new Uri('http://example.com');

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testGetAbsoluteUriWithoutPathExplicitTrailingSlash(){
		$uri = new Uri('http://example.com/');

		$this->assertSame('http://example.com/', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testGetAbsoluteUriWithQuery(){
		$uri = new Uri('http://example.com?param1=value1');

		$this->assertSame('http://example.com?param1=value1', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testGetAbsoluteUriWithFragment(){
		$uri = new Uri('http://example.com#foo');

		$this->assertSame('http://example.com#foo', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testGetRelativeUriWithoutPath(){
		$uri = new Uri('http://example.com');

		$this->assertSame('', $uri->getRelativeUri());
	}

	/**



	 */
	public function testGetRelativeUriWithPath(){
		$uri = new Uri('http://example.com/foo');

		$this->assertSame('/foo', $uri->getRelativeUri());
	}

	/**



	 */
	public function testGetRelativeUriWithExplicitTrailingSlash(){
		$uri = new Uri('http://example.com/');

		$this->assertSame('/', $uri->getRelativeUri());
	}

	/**



	 */
	public function testToStringBare(){
		$uri = new Uri('http://example.com');

		$this->assertSame('http://example.com', (string)$uri);
	}

	/**






	 */
	public function testToStringWithAuthority(){
		$uri = new Uri('http://peehaa:pass@example.com');

		$this->assertSame('http://peehaa:********@example.com', (string)$uri);
	}

	/**



	 */
	public function testToStringWithPath(){
		$uri = new Uri('http://example.com/foo');

		$this->assertSame('http://example.com/foo', (string)$uri);
	}

	/**



	 */
	public function testToStringWithoutPath(){
		$uri = new Uri('http://example.com');

		$this->assertSame('http://example.com', (string)$uri);
	}

	/**



	 */
	public function testToStringWithoutPathExplicitTrailingSlash(){
		$uri = new Uri('http://example.com/');

		$this->assertSame('http://example.com/', (string)$uri);
	}

	/**



	 */
	public function testToStringWithQuery(){
		$uri = new Uri('http://example.com?param1=value1');

		$this->assertSame('http://example.com?param1=value1', (string)$uri);
	}

	/**



	 */
	public function testToStringWithFragment(){
		$uri = new Uri('http://example.com#foo');

		$this->assertSame('http://example.com#foo', (string)$uri);
	}

	/**




	 */
	public function testSetPathEmpty(){
		$uri = new Uri('http://example.com');
		$uri->setPath('');

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPathWithPath(){
		$uri = new Uri('http://example.com');
		$uri->setPath('/foo');

		$this->assertSame('http://example.com/foo', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPathWithOnlySlash(){
		$uri = new Uri('http://example.com');
		$uri->setPath('/');

		$this->assertSame('http://example.com/', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetQueryEmpty(){
		$uri = new Uri('http://example.com');
		$uri->setQuery('');

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetQueryFilled(){
		$uri = new Uri('http://example.com');
		$uri->setQuery('param1=value1&param2=value2');

		$this->assertSame('http://example.com?param1=value1&param2=value2', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testAddToQueryAppend(){
		$uri = new Uri('http://example.com?param1=value1');
		$uri->addToQuery('param2', 'value2');

		$this->assertSame('http://example.com?param1=value1&param2=value2', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testAddToQueryCreate(){
		$uri = new Uri('http://example.com');
		$uri->addToQuery('param1', 'value1');

		$this->assertSame('http://example.com?param1=value1', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetFragmentEmpty(){
		$uri = new Uri('http://example.com');
		$uri->setFragment('');

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetFragmentWithData(){
		$uri = new Uri('http://example.com');
		$uri->setFragment('foo');

		$this->assertSame('http://example.com#foo', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetSchemeWithEmpty(){
		$uri = new Uri('http://example.com');
		$uri->setScheme('');

		$this->assertSame('://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetSchemeWithData(){
		$uri = new Uri('http://example.com');
		$uri->setScheme('foo');

		$this->assertSame('foo://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetUserInfoEmpty(){
		$uri = new Uri('http://example.com');
		$uri->setUserInfo('');

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**





	 */
	public function testSetUserInfoWithData(){
		$uri = new Uri('http://example.com');
		$uri->setUserInfo('foo:bar');

		$this->assertSame('http://foo:bar@example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPortCustom(){
		$uri = new Uri('http://example.com');
		$uri->setPort('21');

		$this->assertSame('http://example.com:21', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPortHttpImplicit(){
		$uri = new Uri('http://example.com');
		$uri->setPort(80);

		$this->assertSame('http://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPortHttpsImplicit(){
		$uri = new Uri('https://example.com');
		$uri->setPort(443);

		$this->assertSame('https://example.com', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPortHttpExplicit(){
		$uri = new Uri('http://example.com');
		$uri->setPort(443);

		$this->assertSame('http://example.com:443', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetPortHttpsExplicit(){
		$uri = new Uri('https://example.com');
		$uri->setPort(80);

		$this->assertSame('https://example.com:80', $uri->getAbsoluteUri());
	}

	/**




	 */
	public function testSetHost(){
		$uri = new Uri('http://example.com');
		$uri->setHost('pieterhordijk.com');

		$this->assertSame('http://pieterhordijk.com', $uri->getAbsoluteUri());
	}

	/**



	 */
	public function testHasExplicitTrailingHostSlashTrue(){
		$uri = new Uri('http://example.com/');

		$this->assertTrue($uri->hasExplicitTrailingHostSlash());
	}

	/**



	 */
	public function testHasExplicitTrailingHostSlashFalse(){
		$uri = new Uri('http://example.com/foo');

		$this->assertFalse($uri->hasExplicitTrailingHostSlash());
	}

	/**



	 */
	public function testHasExplicitPortSpecifiedTrue(){
		$uri = new Uri('http://example.com:8080');

		$this->assertTrue($uri->hasExplicitPortSpecified());
	}

	/**



	 */
	public function testHasExplicitPortSpecifiedFalse(){
		$uri = new Uri('http://example.com');

		$this->assertFalse($uri->hasExplicitPortSpecified());
	}
}
