<?php
namespace LibWeb;

use LibWeb\api\Response;
use LibWeb\api\Request;
use LibWeb\api\Util;
use LibWeb\api\ExceptionNotFound;


class API {
	/// Construct the API
	public function __construct( $namespace = null, $dir = null ) {
		$this->rootNamespace_ = $namespace;
		$this->rootDir_       = $dir;
	}
	/***
	 * Add some files to be ignores
	 */
	public function addIgnore( $ignoreFiles ) {
		if ( !$ignoreFiles )
			return;
		if ( is_array( $ignoreFiles ) ) {
			foreach ( $ignoreFiles as $file )
				$this->addIgnore( $file );
			return;
		}

		$this->ignoreFiles_[] = realpath( $ignoreFiles );
	}
	/***
	 * Add some files to be ignores
	 */
	public function addCustomHandler( $name, $class ) {
		$this->customHandlers_[ $name ] = $class;
	}
	/**
	 * Dispatch the API
	 */
	public function dispatch( $base = null, $uri = null, $method = null ) {
		$req = Request::createFromGlobals( $base, $uri, $method );
		return $this->dispatchRequest( $req );
	}
	/**
	 * Dispatch the request
	 */
	public function dispatchRequest( $req, $send = true ) {
		$method = $req->method();
		$result = $this->processRequest( $req, $res );
		if ( $result === false ) {
			$result = $this->handleNotFound( $req, $res );
			if ( $result != null )
				$res->data( $result );
		}
		if ( $send ) {
			$headersOnly = ($method === 'OPTIONS');
			$this->sendResponse( $req, $res, $headersOnly );
		}
		return $res;
	}
	/**
	 * Dispatch the request
	 */
	public function processRequest( $req, &$res = null ) {
		if ( !$res )
			$res = $req->createResponse();
		return $this->dispatchInternal( $req, $res );
	}
	/**
	 * Dispatch a list of handlers
	 * @return true If any handler was dispatched
	 */
	private function dispatchHandlers( $req, $res, $handlers ) {
		foreach( $handlers as $handler ) {
			try {
				$ret = call_user_func( $handler, $req, $res );
			} catch( \Exception $e ) {
				$ret = $this->handleException( $e, $req, $res );
			} catch( \Error $e ) {
				error_log( $e );
				Debug::exception( $e );
				$res->code( 500 );
				$res->data( Config::get( "debug" ) ? null : Util::debugFormatException( $e ) );
				return true;
			}
			if ( $ret != null ) {
				$res->data( $ret );
				return true;
			} else if ( $res->getData() )
				return true;
		}
		return false;
	}
	/**
	 * Internally dispatches the API
	 */
	private function dispatchInternal( $req, $res ) {
		$method = $req->method();
		
		// Handle the options
		if ( $method === 'OPTIONS' ) {
			$this->handleOptions( $req, $res );
			return true;
		}

		// Try to dispatch the middlewares
		$finish = $this->dispatchHandlers( $req, $res, $this->middlewares_ );
		if ( $finish )
			return true;

		// The request path
		$path = Util::uriToPath( $req->relativeUri() );
		$handlers = $this->getHandlersForPath( $path, $method );
		if ( !$handlers )
			return false;
		return $this->dispatchHandlers( $req, $res, $handlers );
	}
	/// Get the handler
	private function getHandlersForPath( $path, $httpMethod ) {
		$pathLen = count( $path );
		$apiPath = implode( "/", array_slice( $path, 0, $pathLen - 1 ) );
		if ( isset( $this->customHandlers_[ $apiPath ] ) ) {
			$classname = $this->customHandlers_[ $apiPath ];
		} else {
			$namespaceName = $this->rootNamespace_."\\".implode( "\\", array_slice( $path, 0, $pathLen - 2 ) );
			$classname     = $namespaceName . '\\'. ucwords( $path[ $pathLen - 2 ] ).'API';
		}
		if ( !class_exists( $classname ) )
			return false;

		$obj = new $classname;
		$mainHandler = $this->getHandlerForObject( $obj, $path[$pathLen-1], $httpMethod );
		if ( !$mainHandler )
			return false;
		
		$handlers = array();
		$handlers[] = $mainHandler;
		return $handlers;
		
	}
	/// Get the handler
	protected function getHandlerForObject( $obj, $apiMethod, $httpMethod ) {
		if ( $httpMethod === 'GET' ) {
			$handler = array( $obj, 'GET_'.$apiMethod );
			if ( is_callable( $handler ) )
				return $handler;
			$handler = array( $obj, 'REQUEST_'.$apiMethod );
			if ( is_callable( $handler ) )
				return $handler;
			return false;
		} else if ( $httpMethod === 'POST' ) {
			$handler = array( $obj, 'POST_'.$apiMethod );
			if ( is_callable( $handler ) )
				return $handler;
			$handler = array( $obj, 'REQUEST_'.$apiMethod );
			if ( is_callable( $handler ) )
				return $handler;
			return false;
		}
	}

	/**
	 * Format a response to send
	 */
	public function formatResponse( $status, $data, $errorType, $req, $res ) {
		if ( $data instanceof \Exception ) {
			$error = array();
			if ( $errorType !== null )
				$error["type"] = $errorType;
			if ( is_callable( array( $data, "serializeAPI" ) ) ) {
				$errorData = $data->serializeAPI();
				if ( $errorData !== null )
					$error["data"] = $errorData;
			}
			if ( Config::get( "debug" ) )
				$error['$debug'] = $this->debugFormatException( $data );
			return $error ?: null;
		}
		
		if ( is_object( $data) && is_callable( array( $data, "serializeAPI" ) ) )
			$data = $data->serializeAPI();
		return $data;
	}
	/**
	 * Send the response (calls format)
	 */
	public function sendResponse( $req, $res, $headersOnly = false ) {
		$responseCode = $res->getCode() ?: 200;
		$headers	  = $res->getHeaders();
		$data		  = $res->getData();
		$raw		  = $res->getRaw();
		
		// Status of the response
		$status = $responseCode === 200 ? "success" : "error";

		// 
		Debug::collect();

		// Send headers
		http_response_code( $responseCode );
		if ( !$raw && !isset( $headers["content-type"] ) )
			header( "content-type: application/json" );
		foreach ( $headers as $key => $value ) {
			header( $key.": ".$value );
		}
		if ( $headersOnly )
			return;

		// Send data
		if ( $data instanceof \Closure )
			call_user_func( $data );
		else if ( $raw )
			echo $data;
		else {
			$errorType = null;
			if ( $data instanceof APIException )
				$errorType = $data->getType();
			$obj = $this->formatResponse( $status, $data, $errorType, $req, $res );
			if ( Config::get( "debug" ) && ( $data instanceof \Exception ) && !is_callable( array( $data, "serializeAPI" ) ) ) {
				if ( isset( $data->xdebug_message ) ) {
					header( "content-type: text/html" );
					echo "<table>".$data->xdebug_message."</table>";
					return;
				}
			}
			$this->writeResponse( $obj );
		}
	}
	/// Write the response
	public function writeResponse( $obj ) {
		Util::writeJSON( $obj );
	}
	/// Internal not found handler (May be overwritten)
	public function handleNotFound( $req, $res ) {
		$res->code( 404 );
		$res->header( "content-type", "text/text" );
		$res->raw( "Cannot ".$req->method()." ".$req->uri() );
	}
	/// Options Handler
	public function handleOptions( $req, $res ) {
	}
	/// Exception handler (May be overwritten)
	public function handleException( $e, $req, $res ) {
		if ( $e instanceof \Exception ) {
			error_log( $e );
			Debug::exception( $e );
		}
		
		if ( $e instanceof \LibWeb\APIException ) {
			$res->code( $e->getCode() );
			$res->data( $e );
		} else {
			$res->code( 500 );
			$res->data( $e );
		}
	}

	// Variable
	private $rootNamespace_;
	private $rootDir_;
	private $ignoreFiles_ = array();
	private $middlewares_ = array();
	private $customHandlers_ = array();
};
