<?php

namespace LibWeb\api;

/**
 * Utility methods for API
 */
class Util {

	/**
	 * Write a json object
	 */
	public static function writeJSON( $obj ) {
		$isArray  = false;
		$isObject = false;

		// Check if Array
		if ( is_array( $obj ) ) {
			reset( $obj );
			$firstKey = key( $obj );
			end( $obj );
			$lastKey  = key( $obj );
			$size	  = count( $obj );
			if ( ( $firstKey === 0 ) && ( $lastKey === ( $size-1 ) ) )
				$isArray = true;
			else
				$isObject = true;
		// Check if Object
		} else if ( is_object( $obj ) ) {
			// Write the data to a string
			if ( method_exists( $obj, '__toString' ) ) {
				echo $obj;
				return;
			}
			
			if ( $obj instanceof \ArrayAccess )
				$isArray  = true;
			else
				$isObject = true;
		}

		// Write the array
		if ( $isArray ) {
			echo "[";
			$first = true;
			foreach( $obj as $val ) {
				if ( $first ) {
					$first = false;
				} else {
					echo ",";
				}
				self::writeJSON( $val );
			}
			echo "]";
		// Write the object
		} else if ( $isObject ) {
			echo "{";
			$first = true;
			foreach( $obj as $key => $val ) {
				if ( $first ) {
					$first = false;
				} else {
					echo ",";
				}
				echo '"', $key,'":';
				self::writeJSON( $val );
			}
			echo "}";
		// Write everything else
		} else {
			echo json_encode( $obj );
		}
	}
	/**
	 * Convert
	 */
	public static function uriToPathComponent( $component ) {
		$path = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $component ) ) );
		return mb_strtolower( $path[0] ).substr( $path, 1 );
	}
	/**
	 * Convert an uri to paths
	 */
	public static function uriToPath( $uri ) {
		$paths = array_values( array_filter( explode( "/", $uri ) ) );
		return array_map( __CLASS__.'::uriToPathComponent', $paths );
	}
	/**
	 * Normalize a namespace name
	 */
	public static function normalizeNamespace( $namespace ) {
		if ( $namespace ) {
			$namespaceLen = strlen($namespace);
			if ( $namespace[$namespaceLen - 1] === '\\' )
				$namespace = substr( $namespace, 0, $namespaceLen - 1 );
		}
		return $namespace;
	}
	/**
	 * Debug
	 */
	public static function debugFormatException( $exception, $skipPrevious = false ) {
		$info =  array(
			"code"		=> $exception->getCode(),
			"message"	=> $exception->getMessage(),
			"file"		=> $exception->getFile(),
			"line"		=> $exception->getLine(),
			"trace"		=> explode( "\n", $exception->getTraceAsString() ),
			"exception" => $exception->__toString(),
			'$obj'		=> $exception,
		);
		if ( !$skipPrevious  ) {
			$previousList = array();
			$current = $exception;
			while ( true ) {
				$current = $current->getPrevious();
				if ( !$current || !($current instanceof \Exception) )
					break;
				$previousList = self::debugFormatException( $current, true );
			}
			$info[ "previous" ] = $previousList;
		}
		return $info;
	}

}