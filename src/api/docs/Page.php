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
	public function getMethodList() {
		return $this->methodList_;
	}
	public function addMethod( $methods, $fullpath, $path, $reflection ) {
		$method = (object) array(
			"methods" => $methods,
			"fullpath" => $fullpath,
			"path" => $path,
			"params" => null,
			"summary" => null,
			"description" => null,
			"body" => null,
		);
		$this->parseMethod( $method, $reflection );
		$this->methodList_[] = $method;
		return $method;
	}

	protected function parseMethod( $method, $reflection ) {
		$block = Documentator::parseBlock( $reflection );
		if ( $block ) {
			$method->summary = $block->getSummary();
			$method->description = $block->getDescription();
		}
		$method->body = self::getMethodBody( $reflection );
	}

	protected static function getMethodBody( $reflection ) {
		$filename = $reflection->getFileName();
		$start_line = $reflection->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
		$end_line = $reflection->getEndLine();
		$length = $end_line - $start_line;
		$source = file($filename);
		$body = implode("", array_slice($source, $start_line, $length));
		$body = str_replace( "\t", "  ", $body );
		return $body;
	}

	// Variables
	private $name_;
	private $parent_;
	private $isDirectory_;
	private $children_ = array();

	// Page variables
	private $title_;
	private $description_;
	private $methodList_ = array();
}