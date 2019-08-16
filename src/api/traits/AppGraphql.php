<?php

namespace libweb\api\traits;

/**
 * Enables graphql on the current App.
 */
trait AppGraphql {
	/**
	 * Add GraphQL handlers for the current uri2.
	 *
	 * @param string $uri     The path URI for the handler
	 * @param mixed  $options Mixed Options
	 *                        When an array
	 *                        ["schema"] => The schema to run the graphql
	 *                        ["context"] => The context for the graphql
	 *                        ["rootValue"] => See \GraphQL\GraphQL::executeQuery
	 *                        ["operationName"] => See \GraphQL\GraphQL::executeQuery
	 *                        ["fieldResolver"] => See \GraphQL\GraphQL::executeQuery
	 *                        ["validationRules"] => See \GraphQL\GraphQL::executeQuery
	 *                        When \GraphQL\Type\Schema the "schema" will be set to this option
	 *                        When \GraphQL\Type\Definition\Type will create a Schema(["query" => ...]) and use it as schema
	 *                        When callable, may return any of the above
	 */
	public function graphql($uri, $options) {
		// Get the options according to the request
		$getOptions = function ($req, $res) use ($options) {
			if (is_callable($options)) {
				$options = call_user_func($options, $req, $res);
			}

			if (is_object($options)) {
				if ($options instanceof \GraphQL\Type\Schema) {
					$options = ['schema' => $options];
				} elseif ($options instanceof \GraphQL\Type\Definition\Type) {
					$options = ['schema' => new \GraphQL\Type\Schema(['query' => $options])];
				} else {
					throw new \LogicException('Invalid type for options on graphql method. Must be an array, a type or an schema.');
				}
			} elseif (!is_array($options)) {
				throw new \LogicException('Invalid type for options on graphql method. Must be an array, a type or an schema.');
			}
			return $options;
		};

		// Default Graphql Handler
		$handler = function ($req, $res) use ($getOptions) {
			$options = $getOptions($req, $res);

			$schema = $options['schema'];
			if (is_callable($schema)) {
				$schema = call_user_func($schema, $req, $res);
			}

			$context = @$options['context'];
			if (is_callable($context)) {
				$context = call_user_func($context, $req, $res);
			}
			if (!$context && ($context !== false)) {
				$context = new \libweb\api\graphql\Context($req, $res);
			}

			$query = $req->getParam('query');
			$variables = $req->getParam('variables');
			$result = \GraphQL\GraphQL::executeQuery(
				$schema,
				$query,
				@$options['rootValue'],
				$context,
				(array) $variables,
				@$options['operationName'],
				@$options['fieldResolver'],
				@$options['validationRules']
			);

			$flags = 0;
			if ($this->getContainer()['settings']['displayErrorDetails']) {
				$flags |= \GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE;
			}
			$responseData = $result->toArray($flags);
			$responseData['status'] = $result->errors ? 'error' : 'success';
			return $res->withJson($responseData);
		};

		// Get handler
		$this->post($uri, $handler);
		$this->get($uri, function ($req, $res) use ($getOptions, $handler) {
			$options = $getOptions($req, $res);
			// Use playground if requested
			$isDebug = (bool) @$this->getContainer()->get('settings')['debug'];
			if ((@$options['playground'] !== false) && $isDebug) {
				$accept = $req->getHeaderLine('accept');
				if (strpos($accept, 'text/html') !== false) {
					// Send the playground
					$content = file_get_contents(dirname(__DIR__) . '/graphql/Playground.html.template');
					$playgroundOptions = $options['playground'];
					if (is_string($playgroundOptions)) {
						$playgroundOptions = ['endpoint' => $playgroundOptions];
					} elseif (!is_object($playgroundOptions)) {
						$playgroundOptions = ['endpoint' => (string) $req->getUri()];
					}
					$content = str_replace('{{options}}', json_encode($playgroundOptions), $content);
					return $res->withString($content, 'text/html');
				}
			}
			return $handler($req, $res);
		});
		return $this;
	}
}
