<?php
/**
 * Contains EveOnline class.
 * PHP version 5.4
 *
 * @copyright 2014 Michael Cummings
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * Class EveOnline
 */
class EveOnline extends OAuth2Service{

	protected $API_BASE              = 'https://login.eveonline.com';
	protected $authorizationEndpoint = 'https://login.eveonline.com/oauth/authorize';
	protected $accessTokenEndpoint   = 'https://login.eveonline.com/oauth/token';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_HEADER_BEARER;
	protected $accessTokenExpires    = true;

}
