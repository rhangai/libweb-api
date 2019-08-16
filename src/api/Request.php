<?php

namespace libweb\api;

use LibWeb\Validator as v;

/**
 * Request class.
 */
class Request extends \Slim\Http\Request {
	/**
	 * Get a validated parameter.
	 */
	public function getValidatedParam($name, $validator) {
		return v::validate($this->getParam($name), $validator);
	}

	/**
	 * Get the validated parameters from the request using a validator.
	 */
	public function getValidatedParams($validator) {
		return v::validate($this->getParams(), $validator);
	}

	/**
	 * Get the validated parameters from the request with uploaded files using a validator.
	 */
	public function getValidatedParamsWithUpload($validator) {
		$params = array_merge($this->getParams(), $this->getUploadedFiles());
		return v::validate($params, $validator);
	}

	/**
	 * Get the attribute directly using a shortcut.
	 */
	public function __get($name) {
		if (!$this->attributes->has($name)) {
			throw new \InvalidArgumentException("Attribute $name does not exist");
		}
		$attr = $this->attributes->get($name);
		if ($attr instanceof \Closure) {
			$attr = call_user_func($attr, $this);
			$this->attributes->set($name, $attr);
		}
		return $attr;
	}
}
