<?php

namespace OAuth\Storage;

use OAuth\Storage\Exception\AuthorizationStateNotFoundException;
use OAuth\Storage\Exception\TokenNotFoundException;
use OAuth\Token\TokenInterface;

/*
 * Stores a token in-memory only (destroyed at end of script execution).
 */

class Memory implements TokenStorageInterface{

	/**
	 * @var object|TokenInterface
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

		throw new TokenNotFoundException('Token not stored');
	}

	/**
	 * {@inheritDoc}
	 */
	public function storeAccessToken($service, TokenInterface $token){
		$this->tokens[$service] = $token;

		// allow chaining
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAccessToken($service){
		return isset($this->tokens[$service]) && $this->tokens[$service] instanceof TokenInterface;
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

		throw new AuthorizationStateNotFoundException('State not stored');
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
