<?php
namespace libweb\api\util;

/**
 * Serialize a propel collection
 * 
 * It iterates through every item on the collection and transforms it
 */
class SerializablePropelCollection extends \IteratorIterator {
	/// COnstruct the collection iteration
	public function __construct( $collection, $type = Serializable::DEFAULT_PROPEL_TYPE ) {
		parent::__construct( $collection );
		$this->type_ = $type;
	}
	/// Transform the propel object into a serializable object
	public function current() {
		$item = parent::current();
		if ( !$item )
			return $item;
		if ( $item instanceof \JsonSerializable )
			return $item->jsonSerialize();
		return $item->toArray( $this->type_ );
	}
	/// Type of the propel
	private $type_;
};

