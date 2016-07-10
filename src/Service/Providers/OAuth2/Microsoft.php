<?php

namespace OAuth\Service\Providers\OAuth2;

use OAuth\Service\OAuth2Service;

class Microsoft extends OAuth2Service{

	/**
	 * MS uses some magical not officialy supported scope to get even moar info like full emailaddresses.
	 * They agree that giving 3rd party apps access to 3rd party emailaddresses is a pretty lame thing to do so in all
	 * their wisdom they added this scope because fuck you that's why.
	 *
	 * https://github.com/Lusitanian/PHPoAuthLib/issues/214
	 * http://social.msdn.microsoft.com/Forums/live/en-US/c6dcb9ab-aed4-400a-99fb-5650c393a95d/how-retrieve-users-
	 *                                  contacts-email-address?forum=messengerconnect
	 *
	 * Considering this scope is not officially supported: use with care
	 */
	const SCOPE_CONTACTS_EMAILS = 'wl.contacts_emails';

	protected $API_BASE              = 'https://apis.live.net/v5.0/';
	protected $authorizationEndpoint = 'https://login.live.com/oauth20_authorize.srf';
	protected $accessTokenEndpoint   = 'https://login.live.com/oauth20_token.srf';
	protected $authorizationMethod   = self::AUTHORIZATION_METHOD_QUERY_STRING;
	protected $accessTokenExpires    = true;

}
