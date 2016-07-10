<?php
/**
 * Vimeo service.
 *
 * @author  Pedro Amorim <contact@pamorim.fr>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    https://developer.vimeo.com/
 * @link    https://developer.vimeo.com/api/authentication
 */

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * @link https://developer.vimeo.com/api/authentication#scope for scope definitions.
 */
class Vimeo extends OAuth2Service{

	// API version
	const VERSION = '3.2';
	// API Header Accept
	const HEADER_ACCEPT = 'application/vnd.vimeo.*+json;version=3.2';

	protected $API_BASE              = 'https://api.vimeo.com/';
	protected $authorizationEndpoint = 'https://api.vimeo.com/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://api.vimeo.com/oauth/access_token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_HEADER_BEARER;

	protected $accessTokenExpires = true;

	protected $extraOAuthHeaders = ['Accept' => self::HEADER_ACCEPT];
	protected $extraApiHeaders   = ['Accept' => self::HEADER_ACCEPT];

}
