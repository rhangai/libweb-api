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
		$this->title_     = @$options["title"] ?: "API";
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
			if ( $root ) {
				$page = clone $page;
				$page->setTitle( $this->title_ );
			}
		} else {
			$dirpath = Path::join( $path );
			$filename = $page->getName().".md";
		}

		$fullpath = Path::join( $dirpath, $filename );

		$file = new \SplFileObject( $fullpath, "w+" );
		$file->fwrite( $page->getTitle() );
		$file->fwrite( "\n=====================\n\n" );
		$file->fwrite( $page->getDescription() );
		$file->fwrite( "\n\n" );
			
		foreach ( $page->getMethodList() as $method ) {
			$file->fwrite( "### `".$method->path."` ###\n" );
			$file->fwrite( $method->summary );
			$file->fwrite( "\n\n" );

			// Write method information
			$file->fwrite( "- **Methods**: ".implode( ", ", $method->methods )."\n" );
			$file->fwrite( "- **Endpoint**: `".$method->fullpath."`\n" );
			//$file->fwrite( "- **Params**: \n" );

			// Write description
			$file->fwrite( "\n\n" );
			$file->fwrite( $method->description );
			$file->fwrite( "\n\n" );

			// Code body
			if ( $method->body ) {
				$file->fwrite( "```php\n" );
				$file->fwrite( $method->body );
				$file->fwrite( "```" );
				$file->fwrite( "\n\n" );
			}
		}

		if ( $page->isDirectory() ) {
			foreach ( $page->getChildren() as $childPage )
				$this->generatePage( $path, $childPage );
		}
	}

	private $title_;
	private $outputDir_;
}