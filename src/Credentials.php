<?php
/**
 *
 * @filesource   Credentials.php
 * @created      08.07.2016
 * @package      OAuth
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace OAuth;

/**
 * Class Credentials
 *
 * @property string $key
 * @property string $secret
 * @property string $callbackURL
 */
class Credentials extends Container{

	/**
	 * @var string
	 */
	protected $key = '';

	/**
	 * @var string
	 */
	protected $secret = '';

	/**
	 * @var string
	 */
	protected $callbackURL = '';

}
