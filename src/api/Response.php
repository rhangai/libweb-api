<?php
namespace libweb\api;

use Violet\StreamingJsonEncoder\BufferJsonEncoder;
use Violet\StreamingJsonEncoder\JsonStream;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;

/**
 * Response class
 */
class Response extends \Slim\Http\Response {

	/**
	 * Get the application
	 */
	public function getApp() { return $this->_app; }
	/**
	 * Set the application on the response
	 */
	public function withApp( $app ) {
		$clone = clone $this;
		$clone->_app = $app;
		return $clone;
	}
	/**
	 * Set the response as it would usually be if data was returned
	 */
	public function withResponse( $data, $error = false ) {
		return $this->_app->formatResponse( $this->_app->getContainer()->request, $this, [], $data, $error );
	}
	/**
	 * Send a json
	 */
	public function withJson( $data, $status = null, $encodingOptions = 0 ) {
		$encoder = new BufferJsonEncoder( $data );
		$stream  = new JsonStream( $encoder );

		// Response
		$response = $this;
		$response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
		if ( $status !== null )
			$response = $response->withStatus( $status );
		return $response->withBody( $stream );
	}
	/**
	 * Set the content disposition header
	 */
	public function withContentDisposition( $filename, $mode = "attachment" ) {
		return $this
			->withHeader( "Content-Disposition", $mode.'; filename="'.rawurlencode($filename).'"' );
	}
	/**
	 * Create a response with a file
	 * @param $file The filepath or file descriptor
	 * @return $this
	 */
	public function withFile( $file, $contentType = null ) {
		$body = null;
		if ( is_resource( $file ) ) {
			$body = new \Slim\Http\Body( $file );
		} else if ( is_string( $file ) ) {
			$body = new \Slim\Http\Body( fopen( $file, "rb" ) );
			if ( ( $contentType === null ) && function_exists( 'mime_content_type' ) )
				$contentType = mime_content_type( $file );
		} else {
			throw new \InvalidArgumentException( "Download parameter must be a stream or a path to a file" );
		}
			
		// Make the response
		$response = $this;
		if ( ( $contentType !== null ) && ( $contentType !== false ) )
			$response = $response->withHeader( "Content-Type", $contentType );
		return $response->withBody( $body );
	}
	/**
	 * Create a response with a string
	 * @param $buffer The buffer
	 * @return $this
	 */
	public function withString( $buffer, $contentType = null ) {
		$stream = fopen( "php://temp", "rw+" );
		fwrite( $stream, $buffer );
		return $this->withFile( $stream, $contentType );
	}
	/**
	 * Create a response with a download
	 * @param $file The stream
	 */
	public function withDownload( $file, $filename = null, $contentType = null, $mode = "attachment" ) {
		if ( is_string( $file ) && $filename === null )
			$filename = basename( $file );
		if ( $filename === null )
			$filename = "download";
		return $this
			->withContentDisposition( $filename, $mode )
			->withFile( $file, $contentType );
	}
	/**
	 * Create a response with a download string
	 * @param $buffer The buffer for the download
	 */
	public function withDownloadString( $buffer, $filename, $contentType = null, $mode = "attachment" ) {
		$stream = fopen( "php://temp", "rw+" );
		fwrite( $stream, $buffer );
		return $this->withDownload( $stream, $filename, $contentType, $mode );
	}
	/**
	 * Set a cookie as a response
	 * @param $key The key of the cookie
	 * @param $value The value for the cookie
	 * @param array $options The array of options for the cookie
	 * @param string $options["domain"]
	 * @param boolean $options["secure"]
	 * @param boolean $options["httponly"]
	 */
	public function withCookie( $key, $value, $options = array() ) {
		$cookie = SetCookie::create( $key )
			->withValue( $value )
			->withPath( isset( $options["path"] ) ? $options["path"] : "/" );
		if ( isset( $options["domain" ] ) )
			$cookie = $cookie->withDomain( $options["domain"] );
		if ( isset( $options["secure" ] ) )
			$cookie = $cookie->withSecure( $options["secure" ] );
		if ( isset( $options["httponly" ] ) )
			$cookie = $cookie->withHttpOnly( $options["httponly" ] );
		return FigResponseCookies::set( $this, $cookie );
	}
	/**
	 * Set multiple cookies as response
	 * @param $cookies Array of cookies to be set
	 * @param $options Options for every cookie
	 */
	public function withCookies( $cookies, $options = array() ) {
		$response = $this;
		foreach ( $cookies as $key => $value )
			$response = $response->withCookie( $key, $value, $options );
		return $response;
	}

	/// The application
	private $_app = null;
}