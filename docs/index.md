# **Gatekeeper:** An Authentication & Authorization Library

## Introduction

The **Gatekeeper** library is a simple drop-in library that can be used to manage users, permissions and groups for
your application. The goal is to make securing your application as simple as possible why still providing a solid and
secure foundation to base your user system around.

*Gatekeeper* is best classified as a Role-Base Access Control (RBAC) system with users, groups and permissions. It is
framework-agnostic and is set up to use its own database for the user handling.

## Dependencies

*Gatekeeper* makes use of several other PHP dependencies to help reduce code duplication:

- [Modler](http://github.com/enygma/modler)
- [Phinx](http://github.com/robmorgan/phinx)
- [password_compat](http://github.com/ircmaxell/password-compat)
- [phpdotenv](http://github.com/rvlucas/phpdotenv)

## Setup

There's a few things you'll need to do before using the system. First off, it uses [Phinx](https://phinx.org) for working
with the database tables (creation and all). Second it uses the handy [phpdotenv](https://github.com/vlucas/phpdotenv) library
to handle the loading of environment values. This helps you understand the installation and usage a bit better.

1. Create a database named `gatekeeper` and create a user for it (this is configured later)

```
create database gatekeeper;
grant all on gatekeeper.* to 'gk-user'@'localhost' identified by 'some-password-here';
flush privileges;
```

1. Copy the `.env.dist` file from the *Gatekeeper* vendor directory to the (non-`DOCUMENT_ROOT`) place of your choosing and rename it to `.env`.
2. Copy the `phinx.dist.xml` file to the place of your choosing (again, non-`DOCUMENT_ROOT`) and rename it to `phinx.yml`.

```
cp vendor/psecio/gatekeeper/.env.dist /tmp/someplace/.env
cp vendor/psecio/gatekeeper/phinx.dist.yml /tmp/someplace/phinx.yml
```

3. Open both of these files and update the **connection information** to match the database you created earlier.
4. Now we run the migrations:

```
vendor/bin/phinx migrate -c /tmp/somplace/phinx.yml
```

Where the `/tmp/someplace/phinx.yml` is the path to where you put your `phinx.yml` file (again, not in the document root **please!**).

If you get errors here, be sure the connection information is correct.

5. You're ready to go!

You can now start using the *Gatekeeper* functionality in your application. You only need to call the `init` function to set
up the connection and get the instance configured:

```php
<?php
require_once 'vendor/autoload.php';
Gatekeeper::init();
?>
```

## Contact

If you have any questions or suggestions about this library, please let me know by adding an issue [in the Gatekeeper Issues list](https://github.com/psecio/gatekeeper/issues) on GitHub.

Thanks! I hope you find *Gatekeeper* useful!

Chris Cornutt <ccornutt@phpdeveloper.org>
