<?php

namespace OAuth\Service\Providers\OAuth1;

use OAuth\_killme\CredentialsInterface;
use OAuth\Http\ClientInterface;
use OAuth\Http\Exception\TokenResponseException;
use OAuth\Http\Uri;
use OAuth\Service\OAuth1Service;
use OAuth\Service\SignatureInterface;
use OAuth\Storage\TokenStorageInterface;
use OAuth\Token\OAuth1Token;

class Tumblr extends OAuth1Service{

	public function __construct(
		CredentialsInterface $credentials,
		ClientInterface $httpClient,
		TokenStorageInterface $storage,
		SignatureInterface $signature,
		Uri $baseApiUri = null
	){
		parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);

		if(null === $baseApiUri){
			$this->baseApiUri = new Uri('https://api.tumblr.com/v2/');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequestTokenEndpoint(){
		return new Uri('https://www.tumblr.com/oauth/request_token');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthorizationEndpoint(){
		return new Uri('https://www.tumblr.com/oauth/authorize');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAccessTokenEndpoint(){
		return new Uri('https://www.tumblr.com/oauth/access_token');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function parseRequestTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if(null === $data || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] !== 'true'){
			throw new TokenResponseException('Error in retrieving token.');
		}

		return $this->parseAccessTokenResponse($responseBody);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function parseAccessTokenResponse($responseBody){
		parse_str($responseBody, $data);

		if(null === $data || !is_array($data)){
			throw new TokenResponseException('Unable to parse response.');
		}
		elseif(isset($data['error'])){
			throw new TokenResponseException('Error in retrieving token: "'.$data['error'].'"');
		}

		$token = new OAuth1Token();

		$token->setRequestToken($data['oauth_token']);
		$token->setRequestTokenSecret($data['oauth_token_secret']);
		$token->setAccessToken($data['oauth_token']);
		$token->setAccessTokenSecret($data['oauth_token_secret']);

		$token->setEndOfLife(OAuth1Token::EOL_NEVER_EXPIRES);
		unset($data['oauth_token'], $data['oauth_token_secret']);
		$token->setExtraParams($data);

		return $token;
	}
}
