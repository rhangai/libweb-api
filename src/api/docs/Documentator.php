<?php
namespace libweb\api\docs;

/**
 * Markdown generator
 */
class Documentator {

	public function __construct() {
		$this->root_ = new Page;
		$this->docParser_ = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
	}

	public function addMap( $methods, $route, $callable ) {
	}
	public function addClass( $base, $class ) {
	}
	public function addGenerator( $generator, $options = array() ) {
		$this->generators_[] = $generator;
	}

	/**
	 * Generate the documentation
	 */
	public function generate() {

	}

	public function pushDocGroup( $base, $callable ) {
		$this->groupPaths_[] = $base;

		$group = array( "path" => $base );
		if ( $callable instanceof \Closure ) {
			$reflection = new \ReflectionFunction( $callable );
			$block = $this->docParser_->create( $reflection->getDocComment() );

			$group["name"] = (string) $block->getSummary();
			$group["description"] = (string) $block->getDescription();
		}
		$this->groups_[] = (object) $group;
	}
	public function popDocGroup() {
		array_pop( $this->groups_ );
		array_pop( $this->groupPaths_ );
	}

	// Variables
	private $root_;
	private $docParser ;
	private $groups_ = array();
	private $groupPaths_ = array();
	private $generators_ = array();
}