<?php
namespace libweb\api\graphql;

/**
 * Basic context class
 */
class Context {

	// Construct the context
	public function __construct( $req, $res ) {
		$this->req_ = $req;
		$this->res_ = $res;
	}

	// Get the request
	public function getRequest() { 
		return $this->req_;
	}

	// Get the response
	public function getResponse() { 
		return $this->req_;
	}

	private $req_;
	private $res_;
}