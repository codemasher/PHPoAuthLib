<?php

namespace OAuth\Token\Exception;

use OAuth\OauthException;

/**
 * Exception thrown when an expired token is attempted to be used.
 */
class ExpiredTokenException extends OauthException{

}
