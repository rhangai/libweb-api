<?php

namespace libweb\api\util;

/**
 * Added serializable interface to any item.
 */
class Serializable implements \JsonSerializable {
	/**
	 * @param $item The item to be serialized
	 * @param $propelType The propel type for serialization
	 */
	public function __construct($item) {
		$this->item_ = $item;
	}

	/// Perform the serialization
	public function jsonSerialize() {
		return self::serialize($this->item_);
	}

	/// Serialize some common types
	public static function serialize($item) {
		if ($item instanceof \JsonSerializable) {
			return $item;
		} elseif (is_array($item)) {
			return $item;
		} elseif (is_object($item)) {
			if (!($item instanceof \stdClass)) {
				throw new \Exception('Object cannot be serialized');
			}
			return $item;
		}
		return $item;
	}

	private $item_;
	private $type_;
}
