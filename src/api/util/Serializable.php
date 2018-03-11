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
	/// Perform the serialization
	public function jsonSerialize() {
		return self::serialize( $this->item_, $this->type_ );
	}
	/// Serialize some common types
	public static function serialize( $item, $type ) {
		if ( $item instanceof \JsonSerializable )
			return self::serialize( $item->jsonSerialize(), $type );
		else if ( $item instanceof \Propel\Runtime\Util\PropelModelPager ) {
			return array( 
				"page_current"  => $item->getPage(),
				"page_total"    => $item->getLastPage(),
				"index_first"   => $item->getFirstIndex(),
				"index_last"    => $item->getLastIndex(),
				"total" => $item->getNbResults(),
				"items" => new SerializableIterator( $item, $type ),
			);
		} else if ( $item instanceof \Propel\Runtime\Collection\Collection )
			return array( "items" => new SerializableIterator( $item, $type ) );
		else if ( $item instanceof \Propel\Runtime\ActiveRecord\ActiveRecordInterface )
			return $item->toArray( $type );
		else if ( is_array( $item ) )
			return new SerializableIterator( new \ArrayIterator( $item ), $type ) ;
		else if ( is_object( $item ) ) {
			return new SerializableIterator( $item, $type );
		}
		return $item;
	}

	private $item_;
	private $type_;
};