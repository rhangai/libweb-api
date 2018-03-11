<?php
namespace libweb\api\util;

/**
 * Added serializable interface to any item
 */
class Serializable implements \JsonSerializable {

	const DEFAULT_PROPEL_TYPE = "fieldName";

	/**
	 * @param $item The item to be serialized
	 * @param $propelType The propel type for serialization
	 */
	public function __construct( $item, $propelType = self::DEFAULT_PROPEL_TYPE ) {
		$this->item_ = $item;
		$this->type_ = $propelType;
	}

	/// Serialize some common types
	public function jsonSerialize() {
		if ( $this->item_ instanceof \JsonSerializable )
			return $this->item_->jsonSerialize();
		else if ( $this->item_ instanceof \Propel\Runtime\Util\PropelModelPager ) {
			return array( 
				"page_current"  => $this->item_->getPage(),
				"page_total"    => $this->item_->getLastPage(),
				"index_first"   => $this->item_->getFirstIndex(),
				"index_last"    => $this->item_->getLastIndex(),
				"total" => $this->item_->getNbResults(),
				"items" => new SerializablePropelCollection( $this->item_, $this->type_ ),
			);
		} else if ( $this->item_ instanceof \Propel\Runtime\Collection\Collection )
			return array( "items" => new SerializablePropelCollection( $this->item_, $this->type_ ) );
		else if ( $this->item_ instanceof \Propel\Runtime\ActiveRecord\ActiveRecordInterface )
			return $this->item_->toArray( $this->type_ );
		return $this->item_;
	}

	private $item_;
	private $type_;
};