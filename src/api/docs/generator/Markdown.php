<?php
namespace libweb\api\docs\generator;


use libweb\api\docs\GeneratorInterface;
use Webmozart\PathUtil\Path;

/**
 * 
 */
class Markdown implements GeneratorInterface {

	public function setOptions( array $options ) {
		if ( !is_string( @$options["output"] ) )
			throw new \InvalidArgumentException( "Missing generator option: 'output' => The output directory");
		$this->outputDir_ = $options["output"];
	}

	public function generate( $page ) {
		$this->generatePage( array( $this->outputDir_ ), $page, true );
	}

	protected function generatePage( $path, $page, $root = false ) {
		if ( $page->isDirectory() ) {
			if ( $page->getName() !== null )
				$path[] = $page->getName();
			$dirpath = Path::join( $path );
			echo "Creating dir: ".$dirpath, "\n";
			@mkdir( $dirpath, 0775, true );
			$filename = "_index.md";
		} else {
			$dirpath = Path::join( $path );
			$filename = $page->getName().".md";
		}

		$fullpath = Path::join( $dirpath, $filename );

		$file = new \SplFileObject( $fullpath, "w+" );
		$file->fwrite( $page->getTitle() );
		$file->fwrite( "\n=====================\n\n" );
		$file->fwrite( $page->getDescription() );

		foreach ( $page->getSectionList() as $section ) {
			$file->fwrite( "\n\n### `".$section->name."` ###\n\n" );
			$file->fwrite( $section->content );
		}

		if ( $page->isDirectory() ) {
			foreach ( $page->getChildren() as $childPage )
				$this->generatePage( $path, $childPage );
		}
	}

	private $outputDir_;
}