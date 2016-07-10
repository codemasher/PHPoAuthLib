<?php

namespace OAuth\Storage;

use OAuth\OAuthException;
use OAuth\Token;

/*
 * Stores a token in-memory only (destroyed at end of script execution).
 */

class Memory implements TokenStorageInterface{

	/**
	 * @var object|\OAuth\Token
	 */
	protected $tokens;

	/**
	 * @var array
	 */
	protected $states;

	public function __construct(){
		$this->tokens = [];
		$this->states = [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function retrieveAccessToken($service){
		if($this->hasAccessToken($service)){
			return $this->tokens[$service];
		}

		throw new OAuthException('Token not stored');
	}

	/**
	 * {@inheritDoc}
	 */
	public function storeAccessToken($service, Token $token){
		$this->tokens[$service] = $token;

		// allow chaining
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAccessToken($service){
		return isset($this->tokens[$service]) && $this->tokens[$service] instanceof Token;
	}

	/**
	 * {@inheritDoc}
	 */
	public function clearToken($service){
		if(array_key_exists($service, $this->tokens)){
			unset($this->tokens[$service]);
		}

		// allow chaining
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function clearAllTokens(){
		$this->tokens = [];

		// allow chaining
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function retrieveAuthorizationState($service){
		if($this->hasAuthorizationState($service)){
			return $this->states[$service];
		}

		throw new OAuthException('State not stored');
	}

	/**
	 * {@inheritDoc}
	 */
	public function storeAuthorizationState($service, $state){
		$this->states[$service] = $state;

		// allow chaining
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAuthorizationState($service){
		return isset($this->states[$service]) && null !== $this->states[$service];
	}

	/**
	 * {@inheritDoc}
	 */
	public function clearAuthorizationState($service){
		if(array_key_exists($service, $this->states)){
			unset($this->states[$service]);
		}

		// allow chaining
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function clearAllAuthorizationStates(){
		$this->states = [];

		// allow chaining
		return $this;
	}

}
