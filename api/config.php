<?php

// Database type and settings.
define( 'DB_DRIVER', 'mysql' );
define( 'DB_HOST', 'localhost' );
define( 'DB_NAME', 'myapp' );
define( 'DB_USER', 'root' );
define( 'DB_PASS', '' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATION', 'utf8_unicode_ci' );

// Encryption key.
define( 'ENCRYPTION_KEY', '!@#$%^&*+-.,<>' );

// Directory separator.
define( 'DS', DIRECTORY_SEPARATOR );

// Application directory.
define( 'APP_DIR', dirname(dirname(__FILE__)) );

// API directory. !!!NO CHANGE!!!
define( 'API_DIR', APP_DIR.DS.'api' );

// Api path.
define( 'PROTOCOL', 'http://' );
define( 'API_PATH', PROTOCOL.$_SERVER['HTTP_HOST'].'/myApp/api/' );

 ?>
