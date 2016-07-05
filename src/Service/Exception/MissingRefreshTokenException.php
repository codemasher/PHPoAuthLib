<?php

/**
 * @author David Desberg <david@daviddesberg.com>
 * Released under the MIT license.
 */

namespace OAuth\Service\Exception;

use OAuth\OauthException;

/**
 * Exception thrown when service is requested to refresh the access token but no refresh token can be found.
 */
class MissingRefreshTokenException extends OauthException{

}
