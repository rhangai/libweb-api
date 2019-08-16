<?php

namespace libweb\api\docs;

use Webmozart\PathUtil\Path;

/**
 * Markdown generator.
 */
class Documentator {
	const GENERATORS = array(
		'markdown' => '\\libweb\\api\\docs\\generator\\Markdown',
	);
	const METHODS = array('GET', 'POST', 'PUT', 'DELETE');

	public function __construct() {
		$this->root_ = new Page(null, true, null);
		$this->currentPage_ = $this->root_;
	}

	public function addMap($methods, $route, $callable) {
		$reflection = null;
		if ($callable instanceof \Closure) {
			$reflection = new \ReflectionFunction($callable);
		} elseif (is_string($callable)) {
			@list($class, $method) = explode(':', $callable, 2);
			if ($method == null) {
				if (function_exists($class)) {
					$reflection = new \ReflectionFunction($class);
				}
			} elseif (class_exists($class)) {
				$reflectionClass = new \ReflectionClass($class);
				if ($reflectionClass->hasMethod($method)) {
					$reflection = $reflectionClass->getMethod($method);
				}
			}
		}

		$fullpath = Path::join(implode('/', $this->groupPaths_), $route);
		$this->addInternalMethod($this->currentPage_, $methods, $fullpath, $route, $reflection);
	}

	public function addClass($base, $class) {
		$fullpath = Path::join(implode('/', $this->groupPaths_), $base);
		$reflectionClass = new \ReflectionClass($class);

		$page = $this->currentPage_->createChild(strtolower(basename($fullpath)));

		$classBlock = self::parseBlock($reflectionClass);
		if ($classBlock) {
			$page->setTitle($classBlock->getSummary());
			$page->setDescription($classBlock->getDescription());
		} else {
			$page->setTitle($page->getName());
		}

		foreach ($reflectionClass->getMethods() as $method) {
			$methodName = $method->getName();
			if (!preg_match('/^([A-Z]+)_(.+)$/', $methodName, $matches)) {
				continue;
			}

			$methodMethods = $matches[1];
			$methodPath = $matches[2];
			if ($methodMethods === 'REQUEST') {
				$methodMethods = self::METHODS;
			} elseif (in_array($methodMethods, self::METHODS)) {
				$methodMethods = array($methodMethods);
			} else {
				continue;
			}

			$methodPath = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $methodPath));
			$methodPath = str_replace('_', '/', $methodPath);

			$methodFullPath = Path::join($fullpath, $methodPath);
			$this->addInternalMethod($page, $methodMethods, $methodFullPath, $methodPath, $method);
		}
	}

	protected function addInternalMethod($page, $methods, $fullpath, $path, $reflection) {
		$item = $page->addMethod($methods, $fullpath, $path, $reflection);
		$this->methods_[] = $item;
	}

	public function addGenerator($generator, $options = array()) {
		if (is_string($generator)) {
			$generatorClass = @self::GENERATORS[$generator];
			if (!$generatorClass) {
				if (!class_exists($generator)) {
					throw new \InvalidArgumentException("Invalid generator '$generator'. Must be a valid class or one of: " . implode(', ', array_keys(self::GENERATORS)));
				}
				$generatorClass = $generator;
			}

			$generator = new $generatorClass();
		}

		if (!$generator instanceof GeneratorInterface) {
			throw new \InvalidArgumentException('Generator is not an instance of \\libweb\\api\\docs\\GeneratorInterface. Given: ' . get_class($generator));
		}
		if (!is_array($options)) {
			throw new \InvalidArgumentException('Invalid options. Must be an array.');
		}
		$generator->setOptions($options);
		$this->generators_[] = $generator;
	}

	public function generate() {
		foreach ($this->generators_ as $generator) {
			$generator->generate($this->root_);
		}
	}

	public function pushDocGroup($base, $callable) {
		$page = $this->currentPage_->createChild(strtolower(basename($base)), true);
		$this->currentPage_ = $page;

		$group = array(
			'path' => $base,
			'page' => $page,
		);
		if ($callable instanceof \Closure) {
			$block = self::parseBlock(new \ReflectionFunction($callable));
			if ($block) {
				$page->setTitle((string) $block->getSummary());
				$page->setDescription((string) $block->getDescription());
			}
		}

		$this->groupPaths_[] = $base;
		$this->groups_[] = (object) $group;
	}

	public function popDocGroup() {
		$this->currentPage_ = $this->currentPage_->getParent();
		array_pop($this->groups_);
		array_pop($this->groupPaths_);
	}

	public static function parseBlock($doc) {
		static $docParser = null;
		if ($docParser === null) {
			$docParser = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
		}
		if (!$doc) {
			return null;
		} elseif (is_string($doc)) {
			return $docParser->create($doc);
		} elseif (method_exists($doc, 'getDocComment')) {
			return self::parseBlock($doc->getDocComment());
		} else {
			throw new \InvalidArgumentException('Invalid block');
		}
	}

	// Variables
	private $root_;
	private $currentPage_;
	private $groups_ = array();
	private $groupPaths_ = array();
	private $generators_ = array();
	private $methods_ = array();
}
