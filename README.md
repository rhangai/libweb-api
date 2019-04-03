# libweb\api

Generate REST APIs with ease using slim framework
```php
<?php
$app = new \libweb\api\App;
$app->get( "/", function() { return "hello world"; });
$app->run();
```

Will output
```json
{
	"status": "success",
	"data":   "hello world",
}
```


Methods
==========

- `mapClass( $base, $class )`

	Ex:
	```php
	$app->mapClass( "/test", "\\test\\api\\Test" );
	```

	Will be mapped to
	```php
	$obj = new \test\api\Test( $app );

	// "example.com/test/data"
	$obj->GET_data()

	// "example.com/test/info-name"
	$obj->GET_infoName()

	// "example.com/test/sub/dir/data"
	$obj->GET_sub_dir_data()

	// "example.com/test/sub-info/dir-name/data-user"
	$obj->GET_subInfo_dirName_dataUser()
	```


- `mapPath( $base, $dir, $classTemplate )`

	Every path will be mapped to a file
	Ex:
	```php
	$app->mapPath( "/test", "/project/test/", "\\myproject\\api\\test{path}{class}API" );
	```

	When entering to "example.com/test/user/books/data"
	Will be mapped to 
	```php
	require_once "/project/test/user/Books.php";
	$obj = new \myproject\api\test\user\BooksAPI( $app );
	$obj->GET_data()
	```

### Request

Inherits from [\Slim\Http\Request](http://www.slimframework.com/docs/v3/objects/request.html)

- `getValidatedParam( $name, $rule )`
- `getValidatedParams( $rules )`
   ```php
   $data = $req->getValidatedParams([
        "name" => v::s(),
        "list" => v::arrayOf([
            "id" => v::i(),
            "level?" => v::set(["N","R","W"]),
        ]),
    ]);
    ```
- `getValidatedParamsWithUpload( $rules )`

### Response

Inherits from [\Slim\Http\Response](http://www.slimframework.com/docs/v3/objects/response.html)

- `withFile( $file, $contentType = null )`
- `withString( $buffer, $contentType = null )`
- `withDownload( $file, $filename = null, $contentType = null, $mode = "attachment" )`
- `withDownloadString( $buffer, $filename, $contentType = null, $mode = "attachment" )`
- `withCookie( $key, $value, $options = array() )`
- `withCookies( $cookies, $options = array() )`
- `withJson( $data, $status = null )`
- `withResponse( $data )`