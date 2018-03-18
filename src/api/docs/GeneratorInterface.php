<?php
namespace libweb\api\docs;

/**
 * Interface for document generator
 */
interface GeneratorInterface {
	/**
	 * Set options for generator
	 */
	function setOptions( array $options );
	/**
	 * Generate from the root page
	 */
	function generate( Page $root );
}