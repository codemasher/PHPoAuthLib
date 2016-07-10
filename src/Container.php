<?php
/**
 * Class Container
 *
 * @filesource   Container.php
 * @created      09.07.2016
 * @package      OAuth
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace OAuth;

/**
 * a generic container with getter and setter
 */
abstract class Container{

	/**
	 * Boa constructor.
	 *
	 * @param array $properties
	 */
	public function __construct(array $properties = []){
		foreach($properties as $key => $value){
			$this->__set($key, $value);
		}
	}

	/**
	 * David Getter
	 *
	 * @param $property
	 *
	 * @return mixed|bool
	 */
	public function __get($property){
		if(property_exists($this, $property)){
			return $this->{$property};
		}

		return false;
	}

	/**
	 * Jet-setter
	 *
	 * @param $property
	 * @param $value
	 *
	 * @return bool
	 */
	public function __set($property, $value){
		if(property_exists($this, $property)){
			$this->{$property} = $value;
		}
	}

}
