# Providers

## Laravel 5

All the files you'll need to use Gatekeeper as an authentication provider with Laravel 5 are included. These instructions assume you've already followed the Gatekeeper installation instructions and things there are working. Here's how to set it up:

1. You'll then need to add the Auth provider to be loaded. Update your `app\config\app.php` and add this to your `providers` list:

```php
\Psecio\Gatekeeper\Provider\Laravel5\AuthServiceProvider::class
```

2. Update your `app\config\auth.php` settings to change the "driver" setting to "gatekeeper":

```php
'driver' => 'gatekeeper'
```

3. Add the Gatekeeper configuration to your `.env` file for the Laravel application:

```php
GATEKEEPER_USER=gk42
GATEKEEPER_PASS=gk42
GATEKEEPER_HOST=127.0.0.1
GATEKEEPER_DATABASE=gatekeeper
```

**This information is just an example**, so be sure you fill in your actual information here.

That's it - you should be all set to use the standard Laravel authentication handling and it will use Gatekeeper behind the scenes.


## Laravel 4

**NOTE:** The current Laravel support is for 4.x based versions.

A Laravel authentication provider is included with the Gatekeeper package in `Psecio\Gatekeeper\Provider\Laravel`.
It's easy to add into your Laravel application and seamlessly works with the framework's `Auth` handling.

**Step 1:** Add the database configuration into your `app/config/database.php` file:

```
'gatekeeper' => array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'gatekeeper',
    'username' => 'your-username',
    'password' => 'your-password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
)
```

**Step 2:** In the `app/start/global.php` file, add the following to inject the provider and make it available:

```php
<?php

Auth::extend('gatekeeper', function($app) {
    return new \Psecio\Gatekeeper\Provider\Laravel();
});

?>
```

**Step 3:** Finally, in your `app/config/auth.php` file, change the `driver` value to "gatekeeper":

```php
'driver' => 'gatekeeper'
```