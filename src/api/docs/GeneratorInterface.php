<?php
namespace libweb\api\docs;

/**
 * Interface for document generator
 */
interface GeneratorInterface {
	/**
	 * Generate the root page
	 */
	function generate( Page $root );
}