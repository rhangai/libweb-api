<?php
namespace libweb\api\docs;

/**
 * A page class
 */
class Page {

	public function __construct( $name, $isDirectory, $parent ) {
		$this->name_   = $name;
		$this->isDirectory_ = !!$isDirectory;
		$this->parent_ = $parent;
	}

	public function getName() {
		return $this->name_;
	}
	public function isDirectory() {
		return $this->isDirectory_;
	}
	public function createChild( $name, $isDirectory = false ) {
		if ( !$this->isDirectory() )
			throw new \LogicException( "A page cannot have childs" );
		$child = new Page( $name, $isDirectory, $this );
		$this->children_[] = $child;
		return $child;
	}
	public function getParent() {
		return $this->parent_;
	}
	public function getChildren() {
		return $this->children_;
	}



	public function getTitle() {
		return $this->title_;
	}
	public function setTitle( $title ) {
		$this->title_ = $title;
	}
	public function getDescription() {
		return $this->description_;
	}
	public function setDescription( $description ) {
		$this->description_ = $description;
	}
	public function getSectionList() {
		return $this->sectionList_;
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
	private $isDirectory_;
	private $children_ = array();

	// Page variables
	private $title_;
	private $description_;
	private $sectionList_ = array();
}