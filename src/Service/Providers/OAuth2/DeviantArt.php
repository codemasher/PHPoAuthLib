<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

class DeviantArt extends OAuth2Service{

	/**
	 * DeviantArt www url - used to build dialog urls
	 */
	const WWW_URL = 'https://www.deviantart.com/';

	/**
	 * Defined scopes
	 *
	 * If you don't think this is scary you should not be allowed on the web at all
	 *
	 * @link https://www.deviantart.com/developers/authentication
	 * @link https://www.deviantart.com/developers/http/v1/20150217
	 */
	const SCOPE_FEED       = 'feed';
	const SCOPE_BROWSE     = 'browse';
	const SCOPE_COMMENT    = 'comment.post';
	const SCOPE_STASH      = 'stash';
	const SCOPE_USER       = 'user';
	const SCOPE_USERMANAGE = 'user.manage';

	protected $API_BASE              = 'https://www.deviantart.com/api/v1/oauth2/';
	protected $authorizationEndpoint = 'https://www.deviantart.com/oauth2/authorize';
	protected $accessTokenEndpoint   = 'https://www.deviantart.com/oauth2/token';
	protected $accessTokenExpires    = true;

}
