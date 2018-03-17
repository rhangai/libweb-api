<?php
namespace libweb\api\docs;

/**
 * A page class
 */
class Page {

	public function __construct( $name, $parent ) {
		$this->name_   = $name;
		$this->parent_ = $parent;
	}

	public function createChild( $name ) {
		$child = new Page( $name, $this );
		$this->children_[] = $child;
		return $child;
	}
	public function getParent() {
		return $this->parent_;
	}
	public function getChildren() {
		return $this->children_;
	}



	public function setTitle( $title ) {
		$this->title_ = $title;
	}
	public function setDescription( $description ) {
		$this->description_ = $description;
	}
	public function addSection( $name, $content ) {
		$section = array(
			"name" => $name,
			"content" => $content,
		);
		$this->sectionList_[] = (object) $section;
	}

	// Variables
	private $name_;
	private $parent_;
	private $children_ = array();

	// Page variables
	private $title_;
	private $description_;
	private $sectionList_ = array();
}