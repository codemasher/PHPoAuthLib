<?php

namespace OAuthTest\Unit\Service;


use OAuth\Credentials;
use OAuth\Http\Uri;
use OAuth\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase{

	public function testConstructCorrectInterface(){
		$signature = new Signature($this->getMock(Credentials::class));

		$this->assertInstanceOf(Signature::class, $signature);
	}


	public function testSetTokenSecret(){
		$signature = new Signature($this->getMock(Credentials::class));

		$this->assertNull($signature->setTokenSecret('foo'));
	}

	/**







	 */
	public function testGetSignatureBareUri(){
		$credentials = new Credentials(['key' => 'foo','secret' => 'bar']);

		$signature = new Signature($credentials);

		$signature->setTokenSecret('foo');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue(''))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue(''))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/foo'))
		;

		$this->assertSame('uoCpiII/Lg/cPiF0XrU2pj4eGFQ=', $signature->getSignature($uri, ['pee' => 'haa']));
	}

	/**







	 */
	public function testGetSignatureWithQueryString(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())
		            ->method('getConsumerSecret')
		            ->will($this->returnValue('foo'))
		;

		$signature = new Signature($credentials);

		$signature->setHashingAlgorithm('HMAC-SHA1');
		$signature->setTokenSecret('foo');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue('param1=value1'))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue(''))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/foo'))
		;

		$this->assertSame('LxtD+WjJBRppIUvEI79iQ7I0hSo=', $signature->getSignature($uri, ['pee' => 'haa']));
	}

	/**







	 */
	public function testGetSignatureWithAuthority(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())
		            ->method('getConsumerSecret')
		            ->will($this->returnValue('foo'))
		;

		$signature = new Signature($credentials);

		$signature->setHashingAlgorithm('HMAC-SHA1');
		$signature->setTokenSecret('foo');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue('param1=value1'))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue('peehaa:pass'))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/foo'))
		;

		$this->assertSame('MHvkRndIntLrxiPkjkiCNsMEqv4=', $signature->getSignature($uri, ['pee' => 'haa']));
	}

	/**







	 */
	public function testGetSignatureWithBarePathNonExplicitTrailingHostSlash(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())
		            ->method('getConsumerSecret')
		            ->will($this->returnValue('foo'))
		;

		$signature = new Signature($credentials);

		$signature->setHashingAlgorithm('HMAC-SHA1');
		$signature->setTokenSecret('foo');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue('param1=value1'))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue('peehaa:pass'))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/'))
		;
		$uri->expects($this->any())
		    ->method('hasExplicitTrailingHostSlash')
		    ->will($this->returnValue(false))
		;

		$this->assertSame('iFELDoiI5Oj9ixB3kHzoPvBpq0w=', $signature->getSignature($uri, ['pee' => 'haa']));
	}

	/**







	 */
	public function testGetSignatureWithBarePathWithExplicitTrailingHostSlash(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())
		            ->method('getConsumerSecret')
		            ->will($this->returnValue('foo'))
		;

		$signature = new Signature($credentials);

		$signature->setHashingAlgorithm('HMAC-SHA1');
		$signature->setTokenSecret('foo');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue('param1=value1'))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue('peehaa:pass'))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/'))
		;
		$uri->expects($this->any())
		    ->method('hasExplicitTrailingHostSlash')
		    ->will($this->returnValue(true))
		;

		$this->assertSame('IEhUsArSTLvbQ3QYr0zzn+Rxpjg=', $signature->getSignature($uri, ['pee' => 'haa']));
	}

	/**







	 */
	public function testGetSignatureNoTokenSecretSet(){
		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())
		            ->method('getConsumerSecret')
		            ->will($this->returnValue('foo'))
		;

		$signature = new Signature($credentials);

		$signature->setHashingAlgorithm('HMAC-SHA1');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue('param1=value1'))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue('peehaa:pass'))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/'))
		;
		$uri->expects($this->any())
		    ->method('hasExplicitTrailingHostSlash')
		    ->will($this->returnValue(true))
		;

		$this->assertSame('YMHF7FYmLq7wzGnnHWYtd1VoBBE=', $signature->getSignature($uri, ['pee' => 'haa']));
	}

	/**







	 */
	public function testGetSignatureThrowsExceptionOnUnsupportedAlgo(){
		$this->setExpectedException(OAuthException::class);

		$credentials = $this->getMock(CredentialsInterface::class);
		$credentials->expects($this->any())
		            ->method('getConsumerSecret')
		            ->will($this->returnValue('foo'))
		;

		$signature = new Signature($credentials);

		$signature->setHashingAlgorithm('UnsupportedAlgo');

		$uri = $this->getMock(Uri::class);
		$uri->expects($this->any())
		    ->method('getQuery')
		    ->will($this->returnValue('param1=value1'))
		;
		$uri->expects($this->any())
		    ->method('getScheme')
		    ->will($this->returnValue('http'))
		;
		$uri->expects($this->any())
		    ->method('getRawAuthority')
		    ->will($this->returnValue('peehaa:pass'))
		;
		$uri->expects($this->any())
		    ->method('getPath')
		    ->will($this->returnValue('/'))
		;
		$uri->expects($this->any())
		    ->method('hasExplicitTrailingHostSlash')
		    ->will($this->returnValue(true))
		;

		$signature->getSignature($uri, ['pee' => 'haa']);
	}
}
