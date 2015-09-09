# Installation & Configuration

## Installation

The best way to install Gatekeeper is through Composer (though I suppose you could clone the repo if you want to do it the hard way). Use the following command to add it to your current project:

```
composer require psecio/gatekeeper
```

## Dependencies

*Gatekeeper* makes use of several other PHP dependencies to help reduce code duplication:

- [Modler](http://github.com/enygma/modler)
- [Phinx](http://github.com/robmorgan/phinx)
- [password_compat](http://github.com/ircmaxell/password-compat)
- [phpdotenv](http://github.com/vlucas/phpdotenv)


## Setup Quick Start

There's a "quick start" method for getting Gatekeeper up and running in two steps:

1. Create your Gatekeeper database and user:

```
create database gatekeeper;
CREATE USER 'gk-user'@'localhost' IDENTIFIED BY 'some-password-here';
grant all on gatekeeper.* to 'gk-user'@'localhost';
flush privileges;
```

2. Execute the `vendor/bin/setup.sh` file. This script will ask several questions about your database setup, write the needed files and run the migrations for you.

That's it - you're all done!


## You're ready to go!

You can now start using the *Gatekeeper* functionality in your application. You only need to call the `init` function to set
up the connection and get the instance configured:

```php
<?php
require_once 'vendor/autoload.php';
use \Psecio\Gatekeeper\Gatekeeper;

Gatekeeper::init();
?>
```

## Configuration options

You can pass in options to the `init` call if you don't necesarily want to use the `.env` configuration file handling. There's a few options:

```php
<?php
// You can specify your own .env path
Gatekeeper::init('/path/to/directory');

// You can also force the use of your own database configuration
$config = array(
    'type' => 'mysql',
    'username' => 'gatekeeper-user',
    'password' => 'gatekeeper-pass',
    'name' => 'gatekeeper',
    'host' => 'gatekeeper-db.localhost'
);
Gatekeeper::init(null, $config);
?>
```

## Throttling

By default Gatekeeper will have throttling enabled. This means that, on incorrect login by a user, the information will be logged. When they hit a threshold (defaults to 5) in a certain amount of time (default of 1 minute) they'll be marked as blocked and will not be allowed to log in.

You can disable this feature in one of two ways:

```php
<?php
// Either through the init call
Gatekeeper::init(null, array('throttle' => false));

// Or through a method call
Gatekeeper::disableThrottle();

// And to reenable
Gatekeeper::enableThrottle();
?>
```

You can also check the status of the throttling:

```php
<?php
if (Gatekeeper::throttleStatus() === true) {
    echo 'Throttling is enabled!';
}
?>
```

