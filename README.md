# noriko
** incomplete doc **

A super basic httpbin-like, one file lumen app that spits back whatever you send. Usually used for HTTP Client library testing.

## Usage
You should think of this more as a template/skeleton instead of a library. This repo is meant to be cloned into the test directory of whatever project you have.

## Setup
* Clone this repo into your test directory of your project
* Put `laravel/lumen-framework` in your **require-dev**.
    - This was written with lumen ^5.6 but you should be good with any 5.x if you're using an older version of lumen (because conflicts)
* Edit the following
    - `Server.php` - Replace namespace with whatever your test suite is using.
    - `public/lumen.php` - Appropriate path to your project's `vendor/autoload.php`
* Boot the server with `Server::boot()`. You would call this from whatever fixture method you have.

So for something like PHPUnit, you would place this call in `setUpBeforeClass()`. You don't need to worry about tearing down the process as a `register_shutdown_function()` is registered that automatically cleans up when the test has ended.

You should also add the directory to your exclude list (in `phpunit.xml`).

```php
public static function setUpBeforeClass()
{
    Server::boot(getenv('TEST_LUMEN_PORT'));
}
```

## Endpoints

### /{HTTP Verb}
e.g. /get, /post

```json
{
    "method": "GET",
    "headers": {},
    "query_strings": [],
    "form_params": [],
    "json_payload": []
}
```
