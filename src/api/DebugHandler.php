<?php
namespace LibWeb\api;

use LibWeb\api\Util;
use LibWeb\Debug;


class DebugHandler {

	public function dispatchRequest( $req, $res ) {
		$uri = '/'.implode( "/", array_filter( explode( "/", $req->relativeUri() ) ) );
		if ( $uri === '/_debug/debug.js' ) {
			$res->raw(function() use( $req ) { Debug::dumpJs( $req->base() ); });
		} else if ( $uri === '/_debug/debug.css' ) {
			$res->raw(function() use( $req ) { Debug::dumpCss( $req->base() ); });
		} else if ( $uri === '/_debug/handler.json' ) {
			$res->raw(function() { Debug::dumpHandler(); });
		} else if ( self::_strStartsWith( $uri, "/_debug/fontawesome-webfont" ) ) {
			$res->raw(function() use( $uri ) { Debug::dumpFontAwesome( $uri ); });
		} else {
			$res->code( 404 );
			$res->raw( "Not Found" );
		}
	}

	private static function _strStartsWith( $str, $other ) {
		return (substr( $str, 0, strlen( $other ) ) === $other );
	}
	public function __call( $name, $args ) {
		return $this->dispatchRequest( $args[0], $args[1] );
	}
}
