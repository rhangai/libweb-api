<?php
namespace libweb\api;

use \Violet\StreamingJsonEncoder\BufferJsonEncoder;
use \Violet\StreamingJsonEncoder\JsonStream;

/**
 * Response class
 */
class Response extends \Slim\Http\Response {

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
	 * Create a response with a download
	 * @param $file The stream
	 */
	public function withDownload( $file, $filename = null, $contentType = null, $mode = "attachment" ) {
		$body = null;
		if ( is_resource( $file ) ) {
			$body = new \Slim\Http\Body( $file );
		} else if ( is_string( $file ) ) {
			$body = new \Slim\Http\Body( fopen( $file, "rb" ) );
			if ( $filename === null )
				$filename = basename( $file );
			if ( ( $contentType === null ) && function_exists( 'mime_content_type' ) )
				$contentType = mime_content_type( $file );
		} else {
			throw new \InvalidArgumentException( "Download parameter must be a stream or a path to a file" );
		}
		
		// Create a dummy filename
		if ( $filename === null )
			$filename = "download";
			
		// Make the response
		$response = $this;
		$response = $response->withContentDisposition( $filename, $mode );
		if ( ( $contentType !== null ) && ( $contentType !== false ) )
			$response = $response->withHeader( "Content-Type", $contentType );
		return $response->withBody( $body );
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
}