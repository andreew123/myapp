<?php

class Input {

	public static function has( $key ) {
		return empty( self::get( $key ) ) ? false : true;
	}

	// Collect any input datas.
	public static function getInputs( $format = 'object' ) {

		// Input datas.
		if ( isset($_POST['Input']) ) {
			return $_POST['Input'];
		}

		// Format is object.



		if ( $format === 'object' ) {

			$data = new stdClass;
			$post = file_get_contents( 'php://input' );
			// Check if the string is Json.
			if ( strlen($post) > 0 && self::isJson($post) ) {
				$data = json_decode( $post );
			}
			if ( count($_GET) > 0 ) {
				foreach ($_GET as $key => $value) {
					$data->{$key} = $value;
				}
			}
			if ( count($_POST) > 0 ) {
				foreach ($_POST as $key => $value) {
					$data->{$key} = $value;
				}
			}

		// Format is array.
		} else {

			$data = array();
			$post = file_get_contents( 'php://input' );
			// Check if the string is Json.
			if ( strlen($post) > 0 && self::isJson($post) ) {
				$json = json_decode( $post );
				foreach ($json as $key => $value) {
					$data[$key] = $value;
				}
			}
			if ( count($_GET) > 0 ) {
				foreach ($_GET as $key => $value) {
					$data[$key] = $value;
				}
			}
			if ( count($_POST) > 0 ) {
				foreach ($_POST as $key => $value) {
					$data[$key] = $value;
				}
			}
		}
		$_POST['Input'] = $data;

		return $data;
	}

	// Get value.
	public static function get( $name = null, $format = 'object' ) {
		if ( is_null($name) ) {

			// If not get any name, returns with all inputs.
			return self::getInputs( $format );

		} else {

			$inp = self::getInputs( $format );

			// Format is object.
			if ( $format === 'object' ) {
				if ( isset($inp->{$name}) ) {
					return $inp->{$name};
				} else {
					return null;
				}

			// Format is array.
			} else {
				if ( isset($inp[$name]) ) {
					if ( gettype($inp[$name]) === 'object' ) {
						$inp[$name] = self::toArray( $inp[$name] );
					}
					return $inp[$name];
				} else {
					return null;
				}
			}

		}
		return null;
	}

	// Convert to array.
	public static function toArray( $data ) {
		$new_array = array();
		foreach ($data as $key => $value) {
			$new_array[$key] = $value;
		}
		return $new_array;
	}

	// Get all value.
	public static function getAll( $format = 'object' ) {
		return self::getInputs( $format );
	}

	// Set value.
	public static function setValue( $name, $value ) {
		if ( !isset($_POST['Input']) ) {
			self::getInputs();
		}
		$_POST['Input']->{$name} = $value;
	}

	// Remove item.
	public static function remove( $name ) {
		$_POST['Input']->{$name} = null;
	}

	// Check string is json.
	public static function isJson( $string ) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	// Call static.
	public static function __callStatic( $name, $arguments ) {
        return self::get( $name );
    }

}


?>
