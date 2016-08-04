<?php

// Taskmanager.
/**
* CREATE, READ, UPDATE, DELETE based in url variables.
* Types.
* Create = PUT
* Read   = GET
* Update = POST
* Delete = DELETE
*/
class Crud
{

	// Start crud.
	public static function start() {

		// Check request is a crud request.
		if ( strpos($_SERVER['REQUEST_URI'], 'crud') === false )
			return;

		// Log query.
		Log::insert( array( 'user_id'=>User::current_user()->id, 'get_text'=>$_SERVER['REQUEST_URI'], 'input_json'=>json_encode(Input::get() )) );

		// Load classmap.
		$map = self::loadMap();

		// Process url.
		$url_segments = self::processUrl();

		// If segments not empty, find class and run method.
		self::findAndRunMethod( $map, $url_segments );

	}

	// Load classmap.
	public static function loadMap() {
		return require API_DIR . DS . 'autoload.php';
	}

	// Process url.
	public static function processUrl() {

		// Get ending of url and trim.
		$url_ending = explode( 'crud', $_SERVER['REQUEST_URI'] );
		$url_ending = str_replace( '?'.$_SERVER['QUERY_STRING'], '', $url_ending[1] );
		$url_ending = trim( $url_ending, DS );

		// Explode url_end.
		return explode( DS, $url_ending );

	}

	// Find and run method.
	public static function findAndRunMethod( $map, $segments ) {

		// Find class by name from the url.
		$Cls = '';
		foreach ($map as $key => $value) {
			if ( strtolower($key) === strtolower($segments[0]) ) {
				$Cls = $key;
			}
		}

		// Get rights for run methods.
		// Rights::check( $Cls, $segments );

		// Switch by request method.
		switch ( strtolower($_SERVER['REQUEST_METHOD']) ) {
			case 'get':
				self::getMethods( $Cls, $segments );
				break;
			case 'post':
				self::postMethods( $Cls, $segments );
				break;
			case 'put':
				self::putMethods( $Cls, $segments );
				break;
			case 'delete':
				self::deleteMethods( $Cls, $segments );
				break;
		}

	}

	// Methods for get queries.
	public static function getMethods( $Cls, $segments ) {

		// Get one record.
		if ( is_numeric($segments[1]) && count($segments) === 2 ) {
			$obj = $Cls::find( $segments[1] );
			if ( is_null($obj) ) {
				exit( json_encode(array('no_data'=>1)) );
			}
			exit( $obj->toJson() );
		}

		// Get all record, with optionally limit.
		if ( $segments[1] === 'all' && count($segments) === 2 ) {
			exit( $Cls::all()->toJson() );
		} else if ( $segments[1] === 'all' && count($segments) === 3 && is_numeric($segments[2]) ) {
			exit( $Cls::all()->take((int)$segments[2])->toJson() );
		}

		// Where.
		if ( $segments[1] === 'where' ) {
			$where = base64_decode( $segments[2] );
			$where = explode( ',', $where );
			$ret = $Cls::where( $where[0], $where[1], $where[2] )->get();
			exit( $ret->toJson() );
		}

		// Get data by class method.
		if ( $segments[1] !== 'all' ) {
			$ret = call_user_func_array( array($Cls, $segments[1]), $segments );
			if ( is_object($ret) ) {
				exit( $ret->toJson() );
			} else {
				if ( is_array($ret) ) {
					exit( json_encode($ret) );
				}
				exit( $ret );
			}
		}

	}

	// Handle PUT methods.
	public static function putMethods( $Cls, $segments ) {
		// exit ( json_encode(Input::get()));

		$obj = new $Cls;

		// If defined method.
		if ( isset($segments[1]) ) {
			$ret = call_user_func_array( array( $Cls, $segments[1] ), $segments );
			exit( $ret->toJson() );
		}

		// Standard insert.
		foreach (Input::get() as $key => $value) {
			$obj->{$key} = $value;
		}
		$obj->save();

		// Insert done.
		exit( json_encode( array('is_put' => 1, 'Input' => Input::get(), 'id' => $obj->id) ) );

	}

	// Handle DELETE methods.
	public static function deleteMethods( $Cls, $segments ) {
		// exit( json_encode( $segments ) );

		$obj = new $Cls;

		if( isset( $segments[1] ) ) {
			$obj = $Cls::find( $segments[1] );
			$obj->delete();
			// $Cls::delete( $segments[1] );
		}

		// Insert done.
		exit( json_encode( array('is_delete' => 1, 'Input' => Input::get(), 'id' => $segments[1]) ) );

	}

	// Handle POST methods.
	public static function postMethods( $Cls, $segments ) {

		// If defined method.
		if ( isset($segments[1]) ) {
			$ret = call_user_func_array( array( $Cls, $segments[1] ), $segments );
			exit( $ret->toJson() );
		}

		$obj = $Cls::find( Input::id() );
		if ( !isset($obj->id) ) {
			$obj = new $Cls;
		}
		foreach (Input::get() as $key => $value) {
			$obj->{$key} = $value;
		}
		$obj->save();

		// Insert done.
		exit( json_encode( array('is_post' => 1, 'Input' => Input::get(), 'id' => $obj->id) ) );

	}

}
