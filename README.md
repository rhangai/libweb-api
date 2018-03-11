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