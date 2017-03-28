# PHP SimpleCache

The PHP SimpleCache Class is an easy way to cache 3rd party API calls.

## Install

Install via [composer](https://getcomposer.org):

```javascript
{
    "require": {
        "gilbitron/php-simplecache": "~1.4"
    }
}
```

Run `composer install` then use as normal:

```php
require 'vendor/autoload.php';
$cache = new Gilbitron\Util\SimpleCache();
```

## Usage

A very basic usage example:

```php
$cache = new Gilbitron\Util\SimpleCache();
$latest_tweet = $cache->get_data('tweet', 'http://search.twitter.com/search.atom?q=from:gilbitron&rpp=1');
echo $latest_tweet;
```

A more advanced example:

```php
$cache = new Gilbitron\Util\SimpleCache();
$cache->cache_path = 'cache/';
$cache->cache_time = 3600;

if($data = $cache->get_cache('label')){
	$data = json_decode($data);
} else {
	$data = $cache->do_curl('http://some.api.com/file.json');
	$cache->set_cache('label', $data);
	$data = json_decode($data);
}

print_r($data);
```

## Credits

PHP SimpleCache was created by [Gilbert Pellegrom](http://gilbert.pellegrom.me) from [Dev7studios](http://dev7studios.co). Released under the MIT license.
