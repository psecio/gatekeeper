Gatekeeper: An Authentication & Authorization Library
==========

The **Gatekeeper** library is a simple drop-in library that can be used to manage users, permissions and groups for
your application.

### Installation

There's a few things you'll need to do before using the system. First off, it uses [Phinx](https://phinx.org) for working
with the database tables (creation and all). Second it uses the handy [phpdotenv](https://github.com/vlucas/phpdotenv) library
to handle the loading of environment values. This helps you understand the installation and usage a bit better.

1. Create a database named `gatekeeper` and create a user for it (this is configured later)

```
create database gatekeeper;
grant all on gatekeeper.* to 'gk-user'@'localhost' identified by 'some-password-here';
flush privileges;
```

1. Copy the `.env.dist` file from the *Gatekeeper* vendor directory to the (non-DOCUMENT_ROOT) place of your choosing and rename it to `.env`.
2. Copy the `phinx.dist.xml` file to the place of your choosing (again, non-DOCUMENT_ROOT) and rename it to `phinx.yml`.

```
cp vendor/psecio/gatekeeper/.env.dist /tmp/someplace/.env
cp vendor/psecio/gatekeeper/phinx.dist.yml /tmp/someplace/phinx.yml
```

3. Open both of these files and update the connection information to match the database you created earlier.
4. Now we run the migrations:

```
vendor/bin/phinx migrate -c /tmp/somplace/phinx.yml
```

If you get errors here, be sure the connection information is correct.

5. You're ready to go!

With all of this created, you're ready to use the *Gatekeeper* system. Here's an example of using it in your code:

```php
<?php

require_once 'vendor/autoload.php';

use \Psecio\Gatekeeper\Gatekeeper;
Gatekeeper::init();

$credentials = array(
    'username' => 'ccornutt',
    'password' => 'test1',
    'email' => 'me@me.com',
    'first_name' => 'Chris',
    'last_name' => 'Cornutt'
);

// Let's register a user
if (Gatekeeper::register($credentials) === true) {
    echo 'User create successful!';
} else {
    echo 'There was a problem creating the user!';
}

// Now we can authenticate them
$credentials = array(
    'username' => 'ccornutt',
    'password' => 'test1'
);

if (Gatekeeper::authenticate($credentials) === true) {
    echo 'Auth success!';
} else {
    echo 'Auth fail!';
}

// We can perform password reset handling too
$userId = 1;
$code = Gatekeeper::findUserById($userId)->getResetPasswordCode();

$userCode = 'user-inputted-code-goes-here';

if (Gatekeeper::findUserById($userId)->checkResetPasswordCode($userCode) === true) {
    echo 'Code is valid!';
}

// Now for Groups.....

// Creating a group, returns the group instance
$attrs = array('name' => 'Group #1');
$group = Gatekeeper::createGroup($attrs);

// Find a group by ID
$group = Gatekeeper::findById(1);

// Add a user to the group
$userId = 1;
$groupId = 1;
Gatekeeper::findById($groupId)->addUser($userId);

```

@author Chris Cornutt <ccornutt@phpdeveloper.org>
@license MIT
