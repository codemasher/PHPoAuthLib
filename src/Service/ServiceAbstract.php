<?php

namespace OAuth\Service;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Uri;
use OAuth\OauthException;
use OAuth\Storage\TokenStorageInterface;

/**
 * Abstract OAuth service, version-agnostic
 */
abstract class ServiceAbstract implements ServiceInterface{

	/** @var \OAuth\_killme\CredentialsInterface */
	protected $credentials;

	/** @var ClientInterface */
	protected $httpClient;

	/** @var TokenStorageInterface */
	protected $storage;

	/**
	 * @param CredentialsInterface  $credentials
	 * @param ClientInterface       $httpClient
	 * @param TokenStorageInterface $storage
	 */
	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage
	){
		$this->credentials = $credentials;
		$this->httpClient  = $httpClient;
		$this->storage     = $storage;
	}

	/**
	 * @param \OAuth\Http\Uri|string $path
	 * @param \OAuth\Http\Uri        $baseApiUri
	 *
	 * @return \OAuth\Http\Uri
	 *
	 * @throws \OAuth\OauthException
	 */
	protected function determineRequestUriFromPath($path, Uri $baseApiUri = null){
		if($path instanceof Uri){
			$uri = $path;
		}
		elseif(stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0){
			$uri = new Uri($path);
		}
		else{
			if(null === $baseApiUri){
				throw new OauthException(
					'An absolute URI must be passed to ServiceInterface::request as no baseApiUri is set.'
				);
			}

			$uri = clone $baseApiUri;
			if(false !== strpos($path, '?')){
				$parts = explode('?', $path, 2);
				$path  = $parts[0];
				$query = $parts[1];
				$uri->setQuery($query);
			}

			if($path[0] === '/'){
				$path = substr($path, 1);
			}

			$uri->setPath($uri->getPath().$path);
		}

		return $uri;
	}

	/**
	 * Accessor to the storage adapter to be able to retrieve tokens
	 *
	 * @return TokenStorageInterface
	 */
	public function getStorage(){
		return $this->storage;
	}

	/**
	 * @return string
	 */
	public function service(){
		// get class name without backslashes
		$classname = get_class($this);

		return preg_replace('/^.*\\\\/', '', $classname);
	}
}
