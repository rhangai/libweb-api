<?php
namespace libweb\api\traits;

/**
 * Enables graphql on the current App
 */
trait AppGraphql {

	/**
	 * Add GraphQL handlers for the current uri
	 * 
	 * @param string $uri The path URI for the handler
	 * @param $options["schema"] The schema to run the graphql
	 * @param $options["context"] The context for the graphql
	 * @param $options["rootValue"] The value to pass to the root
	 */
	public function graphql( $uri, $options ) {
		$handler = function( $req, $res ) use ( $options ) {
			if ( is_callable( $options ) )
				$options = call_user_func( $options, $req, $res );
			$options = (array) $options;

			$schema = $options["schema"];
			if ( is_callable( $schema ) )
				$schema = call_user_func( $schema, $req, $res );

			$context = @$options["context"];
			if ( is_callable( $context ) )
				$context = call_user_func( $context, $req, $res );
	
	
			$query     = $req->getParam( "query" );
			$variables = $req->getParam( "variables" );
			$result = \GraphQL\GraphQL::executeQuery( 
				$schema, 
				$query, 
				@$options["rootValue"], 
				$context, 
				(array) $variables, 
				@$options[ "operationName" ], 
				@$options[ "fieldResolver" ],
				@$options[ "validationRules" ]
			);

			$flags = 0;
			if ( $this->getContainer()["settings"]["displayErrorDetails"] )
				$flags |= \GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE;
			$responseData = $result->toArray( $flags );
			$responseData["status"] = $result->errors ? "error" : "success";
			return $res->withJson( $responseData );
		};

		$this->get(  $uri, $handler );
		$this->post( $uri, $handler );
		return $this;
	}
}