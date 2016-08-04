<?php

/**
* Factory, create and store classes.
*/
class Factory {

	private static $classes = array();

	public static function __callStatic($name, $arguments) {
        return self::getStaticName( $name, $arguments );
    }

    public static function getStaticName($name, $arguments = null) {
        if (!isset(self::$classes[$name])) {
        	self::$classes[$name] = new $name;
        }
        return self::$classes[$name];
    }

}
