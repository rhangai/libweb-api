<?php
namespace libweb\api;

/**
 * Exception to be handled by the API
 */
class Exception extends \Exception implements \JsonSerializable {
	// Overrides the map function to wrap the handler
	public function __construct( string $message, int $code = 0, $data = null ) {
		parent::__construct( $message, $code );
		$this->data_ = $data;
	}
	/// Serialize the data
	public function jsonSerialize() {
		return array(
			"message" => $this->getMessage(),
			"code"    => $this->getCode(),
			"data"    => $this->data_,
		);
	}
	/// Data
	private $data_;
}