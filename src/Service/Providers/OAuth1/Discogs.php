<?php
/**
 *
 * @filesource   Discogs.php
 * @created      09.07.2016
 * @package      OAuth\Service\Providers\OAuth1
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace OAuth\Service\Providers\OAuth1;

use OAuth\Service\OAuth1Service;

/**
 * Class Discogs
 */
class Discogs extends OAuth1Service{

	protected $API_BASE              = 'https://api.discogs.com/';
	protected $requestTokenEndpoint  = 'https://api.discogs.com/oauth/request_token';
	protected $authorizationEndpoint = 'https://www.discogs.com/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://api.discogs.com/oauth/access_token';

	protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = []){

		$parameters = array_merge(
			[
				'oauth_callback'         => $this->credentials->callbackURL,
				'oauth_consumer_key'     => $this->credentials->key,
				'oauth_nonce'            => bin2hex(random_bytes(32)),
				'oauth_signature'        => $this->credentials->secret.'&',
				'oauth_signature_method' => 'PLAINTEXT',
				'oauth_timestamp'        => (new \DateTime())->format('U'),
			], $extraParameters
		);

		return $this->buildAuthHeader($parameters);
	}

}
