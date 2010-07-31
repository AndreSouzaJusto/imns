<?php
/*
 * Queremos saber de tudo!
 */
error_reporting( E_ALL | E_STRICT );
ini_set( 'display_errors' , 'On' );

/*
 * Adiciona a pasta application ao include_path
 */
$include_path = ini_get( 'include_path' ) . PATH_SEPARATOR . realpath( '../application' );
ini_set( 'include_path' , implode( PATH_SEPARATOR , array_unique( explode( PATH_SEPARATOR , $include_path ) ) ) );

/*
 * Registra a funчуo para carregar classes automaticamente
 */
function loader( $class ) {
	require sprintf( '%s.php' , implode( DIRECTORY_SEPARATOR , explode( '\\' , $class ) ) );
}
spl_autoload_register( 'loader' , true );