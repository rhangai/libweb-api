<?php
namespace LibWeb\api;

use LibWeb\api\Util;
use LibWeb\Debug;


class DebugHandler {

	public function dispatchRequest( $req, $res ) {
		$uri = '/'.implode( "/", array_filter( explode( "/", $req->relativeUri() ) ) );
		if ( $uri === '/_debug/debug.js' ) {
			Debug::dumpJs( $req->base() );
		} else if ( $uri === '/_debug/debug.css' ) {
			Debug::dumpCss( $req->base() );
		} else if ( $uri === '/_debug/handler.json' ) {
			Debug::dumpHandler();
		} else if ( self::_strStartsWith( $uri, "/_debug/fontawesome-webfont" ) ) {
			Debug::dumpFontAwesome( $uri );
		}
		exit;
	}

	private static function _strStartsWith( $str, $other ) {
		return (substr( $str, 0, strlen( $other ) ) === $other );
	}
	public function __call( $name, $args ) {
		return $this->dispatchRequest( $args[0], $args[1] );
	}
}
