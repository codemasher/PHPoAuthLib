<?php

namespace OAuth\Service\Exception;

use OAuth\OauthException;

/**
 * Thrown when an unsupported hash mechanism is requested in signature class.
 */
class UnsupportedHashAlgorithmException extends OauthException{

}
