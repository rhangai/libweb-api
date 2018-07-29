<?php
namespace libweb\api\traits;

/**
 * A basic app runner class with a few methods already set so you can only include it
 */
trait AppRunner {

	/**
	 * Perform route setup
	 */
	public function setupRoutes() {}
	/**
	 * Create the app
	 */
	public static function create( $config = array() ) {
		$app = new static([
			"settings" => [
				"displayErrorDetails" => !!@$config[ "debug" ],
				"logErrors" => !!@$config[ "logErrors" ],
			],
		]);
		if ( @$config["cors"] )
			$app->cors();
		$app->setupRoutes();
		return $app;
	}
	/**
	 * A main function to run on the main script
	 */
	public static function main( $argv ) {
		$app = static::create( self::config( $argv ) );
		$app->run();
	}
	/**
	 * Set the error handler
	 */
	public function errorHandler( $request, $response, $exception ) {
		if ( $this->getContainer()["settings"]["logErrors"] )
			error_log( $exception );
		$response = $response->withStatus( 500 );
		return $this->formatResponse( $request, $response, null, $exception, true );
	}
	/**
	 * Get configuration to run the app
	 */
	public static function config( $argv ) {
		return [];
	}
};