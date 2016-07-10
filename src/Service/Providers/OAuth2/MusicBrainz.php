<?php
/**
 *
 * @filesource   MusicBrainz.php
 * @created      10.07.2016
 * @package      OAuth\Service\Providers\OAuth2
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

/**
 * todo
 *
 * @link https://musicbrainz.org/doc/Development/OAuth2
 */
class MusicBrainz extends OAuth2Service{

	protected $API_BASE              = 'https://musicbrainz.org/ws/2/';
	protected $authorizationEndpoint = 'https://musicbrainz.org/oauth2/authorize';
	protected $accessTokenEndpoint   = 'https://musicbrainz.org/oauth2/token';
	protected $accessTokenExpires    = true;
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_HEADER_BEARER;

}
