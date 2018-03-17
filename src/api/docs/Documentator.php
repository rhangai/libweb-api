<?php
namespace libweb\api\docs;

use Webmozart\PathUtil\Path;


/**
 * Markdown generator
 */
class Documentator {

	const METHODS = array( "GET", "POST", "PUT", "DELETE" );

	public function __construct() {
		$this->root_ = new Page( null, null );
		$this->currentPage_ = $this->root_;
		$this->docParser_ = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
	}

	public function addMap( $methods, $route, $callable ) {
	}
	public function addClass( $base, $class ) {
		$fullpath = Path::canonicalize( Path::join( implode( "/", $this->groupPaths_ ), $base ) );
		$reflectionClass = new \ReflectionClass( $class );

		$pageName = basename( $fullpath );
		$classBlock = $this->parseBlock( $reflectionClass );
		if ( $classBlock ) {
			$pageName = $classBlock->getSummary();
		}

		$page = $this->currentPage_->createChild( $pageName );

		if ( $classBlock )
			$page->setTitle( $classBlock->getDescription() );
			
		foreach ( $reflectionClass->getMethods() as $method ) {
			$methodName = $method->getName();
			if ( !preg_match( "/^([A-Z]+)_(.+)$/", $methodName, $matches ) )
				continue;
			if ( !in_array( $matches[1], self::METHODS ) )
				continue;

			$methodPath = $matches[2];
			$methodPath = strtolower( preg_replace( "/([a-z])([A-Z])/", '$1-$2', $methodPath ) );
			$methodPath = str_replace( "_", '/', $methodPath );
			
			$methodFullPath = Path::join( $fullpath, $methodPath );
			$page->addSection( $methodPath, $method->getDocComment() );
		}
	}
	
	public function addGenerator( $generator, $options = array() ) {
		$this->generators_[] = $generator;
	}
	public function generate() {
		foreach ( $this->generators_ as $generator )
			$generator->generate( $this->root_ );
	}


	public function pushDocGroup( $base, $callable ) {
		$group = array( 
			"path" => $base,
		);
		if ( $callable instanceof \Closure ) {
			$block = $this->parseBlock( new \ReflectionFunction( $callable ) );
			if ( $block ) {
				$group["name"] = (string) $block->getSummary();
				$group["description"] = (string) $block->getDescription();
			}
		}

		$page = $this->currentPage_->createChild( $group["name"] ?: $group["path"] );
		$this->currentPage_ = $page;
		$group["page"] = $page;
		
		$this->groupPaths_[] = $base;
		$this->groups_[] = (object) $group;
	}
	public function popDocGroup() {
		$this->currentPage_ = $this->currentPage_->getParent();
		array_pop( $this->groups_ );
		array_pop( $this->groupPaths_ );
	}
	public function parseBlock( $doc ) {
		if ( !$doc )
			return null;
		else if ( is_string( $doc ) ) 
			return $this->docParser_->create( $doc );
		else if ( method_exists( $doc, 'getDocComment' ) )
			return $this->parseBlock( $doc->getDocComment() );
		else
			throw new \InvalidArgumentException( "Invalid block" );
	}

	// Variables
	private $root_;
	private $currentPage_;
	private $docParser ;
	private $groups_ = array();
	private $groupPaths_ = array();
	private $generators_ = array();
}