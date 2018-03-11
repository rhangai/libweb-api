<?php
namespace libweb\api;

use libweb\api\util\Serializable;
use \Violet\StreamingJsonEncoder\BufferJsonEncoder;
use \Violet\StreamingJsonEncoder\JsonStream;

class App extends \Slim\App {

	// Overrides the map function to wrap the handler
	public function map( array $methods, $pattern, $callable ) {
		return parent::map( $methods, $pattern, $this->wrapHandler( $callable ) );
	}
	/**
	 * Format the response for the application
	 */
	public function formatResponse( $request, $response, $params, $data, $error = false ) {
		/// Does nothing when already a response
		if ( $data instanceof \Psr\Http\Message\ResponseInterface )
			return $data;

		if ( $error ) {
			if ( !$data instanceof \JsonSerializable )
				throw $data;
			$responseData = array( "status" => "error", "error" => new Serializable( $data ) );
		} else {
			$responseData = array( "status" => "success", "data" => new Serializable( $data ) );
		}
		$encoder = new BufferJsonEncoder( $responseData );
		$stream  = new JsonStream( $encoder );
		return $response
			->withHeader('Content-Type', 'application/json;charset=utf-8')
			->withBody( $stream );
	}
	/**
	 * Wrap the handler for the map
	 */
	public function wrapHandler( $callable ) {
		$app = $this;
		$container = $this->getContainer();
		return function( $request, $response, $params ) use ( $callable, $app, $container ) {
			$resolver = $container->get('callableResolver');
			$args = [ $request, $response, $params ];
			
			$callable = $resolver->resolve( $callable );
			
			try { 
				$data = call_user_func_array( $callable, $args );
				$error = false;
			} catch ( \Exception $e ) {
				$data = $e;
				$error = true;
			}
			return $app->formatResponse( $request, $response, $params, $data, $error );
		};
	}
}