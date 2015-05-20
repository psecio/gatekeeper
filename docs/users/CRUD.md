## Create a New User

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

## Create with Permissions

Addiitonally, you can also link the user to permissions at create time:

```php
<?php
$credentials = array(
    'username' => 'ccornutt',
    'password' => 'test1',
    'email' => 'ccornutt@phpdeveloper.org',
    'first_name' => 'Chris',
    'last_name' => 'Cornutt'
);
// Use can use permission names
$credentials['permissions'] = array('perm1', 'perm2');
// or use IDs
$credentials['permissions'] = array(1, 2);

Gatekeeper::register($credentials);
?>
```

**NOTE:** The permissions by the name/id you use must exist *before* the user, otherwise the link is not created.

## Creating with Groups

You can also create groups the same way:

```php
<?php
$credentials = array(
    'username' => 'ccornutt',
    'password' => 'test1',
    'email' => 'ccornutt@phpdeveloper.org',
    'first_name' => 'Chris',
    'last_name' => 'Cornutt'
);
// Use can use permission names
$credentials['groups'] = array('group1', 'group2');
// or use IDs
$credentials['groups'] = array(1, 2);

Gatekeeper::register($credentials);
?>
```

## Removing users

Deleteing user records can be done with the `deleteUserById` method:

```php
<?php
if (Gatekeeper::deleteUserById(1) === true) {
    echo "User removed successfully!";
}

// Or, if you already have the User model object
Gatekeeper::deleteUser($user);
?>
```