<?php
namespace libweb\api;

use libweb\api\util\Serializable;
use \Violet\StreamingJsonEncoder\BufferJsonEncoder;
use \Violet\StreamingJsonEncoder\JsonStream;
use Webmozart\PathUtil\Path;

class App extends \Slim\App {

	// Overrides the map function to wrap the handler
	public function map( array $methods, $pattern, $callable ) {
		return parent::map( $methods, $pattern, $this->wrapHandler( $callable ) );
	}
	/**
	 * Map a class to an API path
	 * @param $base The base path to load
	 * @param $dir The directory to resolve
	 * @param $classTemplate The template string for the class to resolve
	 * @return {\Slim\Route} The root route for the base path
	 * 
	 * Every path will be mapped to a file
	 * Ex:
	 *     $app->mapClass( "/test", "\\test\\api\\Test" );
	 * 
	 * Will be mapped to
	 *     $obj = new \test\api\Test( $app );
	 * 
	 *     // "example.com/test/data"
	 *     $obj->GET_data()
	 * 
	 *     // "example.com/test/info-name"
	 *     $obj->GET_infoName()
	 * 
	 *     // "example.com/test/sub/dir/data"
	 *     $obj->GET_sub_dir_data()
	 * 
	 *     // "example.com/test/sub-info/dir-name/data-user"
	 *     $obj->GET_subInfo_dirName_dataUser()
	 */
	public function mapClass( $base, $class ) {
		if ( !class_exists( $class ) )
			throw new \InvalidArgumentException( "Cannot find class '$class'." );
		$app = $this;
		$pattern = Path::join( "/", $base, "{method:.*}" );
		return $this->any( $pattern, function( $request, $response, $params ) use ( $app, $class ) {
			return $app->_dispatchApiClass( $request, $response, $params, $class, $params["method"] );
		});
	}
	/**
	 * Map a directory to a path on the API
	 * @param $base The base path to load
	 * @param $dir The directory to resolve (If null, no new files will be included )
	 * @param $classTemplate The template string for the class to resolve
	 * @return {\Slim\Route} The root route for the base path
	 * 
	 * Every path will be mapped to a file
	 * Ex:
	 *     $app->mapPath( "/test", "/project/test/", "\\myproject\\api\\test{path}{class}API" );
	 * 
	 * When entering to "example.com/test/user/books/data"
	 * Will be mapped to 
	 *    require_once "/project/test/user/Books.php"; // If $dir is null, no file will be required
	 *    $obj = new \myproject\api\test\user\BooksAPI( $app );
	 *    $obj->GET_data()
	 */
	public function mapPath( $base, $dir, $classTemplate = "{path}{class}API" ) {
		$app = $this;
		$pattern = Path::join( "/", $base, "{path:.*}" );
		return $this->any( $pattern, function( $request, $response, $params ) use ( $app, $dir, $classTemplate ) {
			// Normalize the parts for the path
			$path  = Path::canonicalize( $params["path"] );
			$parts = explode( "/", $path );
			if ( count( $parts ) <= 1 )
				throw new \Slim\Exception\NotFoundException( $request, $response );

			$methodbase = array_pop( $parts );
			$classbase  = ucfirst( array_pop( $parts ) );

			$pathdir    = implode( "/", $parts );

			// Try to find the file
			if ( $dir !== null ) {
				$classdir   = Path::join( $dir, $pathdir );
				$classpath  = Path::join( $classdir, $classbase.".php" );
				if ( !file_exists( $classpath ) )
					throw new \Slim\Exception\NotFoundException($request, $response);
				require_once $classpath;
			}
			
			// Try to find the class
			$replace = array(
				"{path}" => str_replace( "/", "\\", Path::join( "/", $pathdir, "/" ) ),
				"{class}" => $classbase,
			);
			$class = str_replace( array_keys( $replace ), array_values( $replace ), $classTemplate );
			if ( !class_exists( $class ) )
				throw new \Slim\Exception\NotFoundException($request, $response);

			// Dispatch the object
			return $app->_dispatchApiClass( $request, $response, $params, $class, $methodbase );
		});
	}
	/**
	 * Dispatch an API class using the method
	 */
	public function _dispatchApiClass( $request, $response, $params, $class, $methodbase ) {
		if ( !class_exists( $class ) )
			throw new \Slim\Exception\NotFoundException($request, $response);
		$obj = new $class( $this );
		$args = [ $request, $response, $params ];
		
		$methodbase = str_replace( " ", "", lcfirst( ucwords( str_replace( "-", " ", $methodbase ) ) ) );
		$methodbase = str_replace( "/", "_", $methodbase );
		
		$requestMethods = array( $request->getMethod(), "REQUEST" );
		foreach ( $requestMethods as $requestMethodName ) {
			$method = $requestMethodName . "_" . $methodbase;
			if ( method_exists( $obj, $method ) )
				return call_user_func_array([ $obj, $method ], $args );
		}
		throw new \Slim\Exception\NotFoundException( $request, $response );
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
	 * Wrap a new callable for the map
	 */
	public function wrapHandler( $callable ) {
		$app = $this;
		return function( $request, $response, $params ) use ( $callable, $app ) {
			$resolver = $app->getContainer()->get('callableResolver');
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