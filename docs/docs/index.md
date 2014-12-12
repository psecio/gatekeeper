## **Gatekeeper:** An Authentication & Authorization Library

### Introduction

The **Gatekeeper** library is a simple drop-in library that can be used to manage users, permissions and groups for
your application. The goal is to make securing your application as simple as possible why still providing a solid and
secure foundation to base your user system around.

*Gatekeeper* is best classified as a Role-Base Access Control (RBAC) system with users, groups and permissions. It is
framework-agnostic and is set up to use its own database for the user handling.

### Dependencies

*Gatekeeper* makes use of several other PHP dependencies to help reduce code duplication:

- [Modler](http://github.com/enygma/modler)
- [Phinx](http://github.com/robmorgan/phinx)
- [password_compat](http://github.com/ircmaxell/password-compat)
- [phpdotenv](http://github.com/rvlucas/phpdotenv)

### Setup

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

### Authentication

One of the main features of the library is validating a `username` and `password` combination against a current user record. Is it achieved with the `authenticate` method:

```php
<?php
$credentials = array(
    'username' => 'ccornutt',
    'password' => 'valid-password'
);
if (Gatekeeper::authenticate($credentials) == true) {
    echo 'valid!';
}
?>
```

### Users

We'll start with the *User* handling. *Gatekeeper* makes it simple to manage users and perform the usual CRUD (create, read
update, delete) operations on their data.

Users are represented as objects in the code with the following properties:

- username
- password
- email
- firstName
- lastName
- status
- id
- resetCode
- resetCodeTimeout
- groups
- created
- updated

You can access this data on a populated user object as you would any other object properties:

```php
<?php
echo 'Full name: '.$user->firstName.' '.$user->lastName."\n";
?>
```

#### Creating Users

To create a user, you only need to provide the user details to the `register` method:

```php
<?php
$credentials = array(
    'username' => 'ccornutt',
    'password' => 'test1',
    'email' => 'ccornutt@phpdeveloper.org',
    'first_name' => 'Chris',
    'last_name' => 'Cornutt'
);
Gatekeeper::register($credentials);
?>
```

The return value from the `register` call is a *boolean* indicating the pass/fail status of the registration.

#### Finding Users

You can use the `findByUserId` method to locate a user by their ID number:

```php
<?php
$userId = 1;
$user = Gatekeeper::findUserById($userId);

// Or, to get a property directly
$username = Gatekeeper::findUserById($userId)->username;
?>
```

The return value is an instance of the `UserModel` with the properties populated with the user data (if it was found). A `UserNotFoundException` will be thrown if the user is not found.

#### Activating/Deactivating Users

You can mark a user as active or inactive in the system easily. Inactive users will not be able to log in using the `authenticate` method. Changing the user status is easy:

```php
<?php
// Change the user status to active
Gatekeeper::findUserById(1)->activate();

// Change the user status to inactive
Gatekeeper::findUserById($userId)->deactivate();
?>
```

#### Get User Groups

You can use the `groups` relational property to find the groups the user is a member of. It will return an iterable collection
you can use like any other array of data:

```php
<?php
$groups = Gatekeeper::findUserById($userId)->groups;
foreach($groups as $group) {
    echo 'Group name: '.$group->name."\n";
}
?>
```

### Groups

#### Creating a Group

Making a new group is as easy as making a new user. One thing to note, the *group name* must be **unique*:

```php
<?php
$attrs = array(
    'name' => 'Group #1'
);
Gatekeeper::createGroup($attrs);
?>
```

#### Finding Groups

And, like users, you can find groups by their IDs:

```php
<?php
$group = Gatekeeper::findGroupById(1);
?>
```

If the group is not found, a `GroupNotFoundException` will be thrown.

#### Getting Group Users

Much like you can easily get the groups the user belongs to, you can also get the members of a group. This will return a collection of user objects:

```php
<?php
$users = Gatekeeper::findGroupById(1)->users;

foreach ($users as $user) {
    echo 'Username: '.$user->username."\n";
}
?>
```

### Password Reset Handling

*Gatekeeper* also includes some password reset handling functionality. It doesn't try to send an email or output a web page
with the functionality. Instead, it provides methods to generate and validate a unique code. When the code is generated, it is
added into the user's record and stored for evaluation.

The code will expire in *one hour* from the time it was generated.

```php
<?php
$user = Gatekeeper::findUserById(1);
$code = $user->getResetPasswordCode();

echo 'Your password reset code is: '.$code."\n";

// Now lets verify it...
$code = $_GET['code'];
if ($user->checkResetPasswordCode($code) === true) {
    echo 'valid!';
}
?>
```

If the code is valid, it and the timeout are cleared from the user's record.

### Contact

If you have any questions or suggestions about this library, please let me know by adding an issue [in the Gatekeeper Issues list](https://github.com/psecio/gatekeeper/issues) on GitHub.

Thanks! I hope you find *Gatekeeper* useful!

Chris Cornutt <ccornutt@phpdeveloper.org>
