# Providers

## Laravel

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