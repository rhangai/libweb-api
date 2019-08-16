<?php

namespace libweb\api\docs;

/**
 * Interface for document generator.
 */
interface GeneratorInterface {
	/**
	 * Set options for generator.
	 */
	public function setOptions(array $options);

	/**
	 * Generate from the root page.
	 */
	public function generate(Page $root);
}
