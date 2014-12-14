# Users

We'll start with the **User** handling. Gatekeeper makes it simple to manage users and perform the usual CRUD (create, read update, delete) operations on their data.

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
- groups (relational)
- permissions (relational)

You can access this data on a populated user object as you would any other object properties:

```php
<?php
echo 'Full name: '.$user->firstName.' '.$user->lastName."\n";
?>
```

## Creating Users

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

## Finding Users

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

## Activating/Deactivating Users

You can mark a user as active or inactive in the system easily. Inactive users will not be able to log in using the `authenticate` method. Changing the user status is easy:

```php
<?php
// Change the user status to active
Gatekeeper::findUserById(1)->activate();

// Change the user status to inactive
Gatekeeper::findUserById($userId)->deactivate();
?>
```

## Get User Groups

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

## See if a user is in a group

You can check to see if a user is in a group with the `inGroup` method:

```php
<?php
$groupId = 1;

if (Gatekeeper::findUserById(1)->inGroup($groupId) === true) {
	echo 'The user is in the group!';
}

?>
```

## Get a list of user permissions

You can use the `permissions` property to get the full set of user permissions. These are the permissions **directly assigned** to the user, not to any groups they may be a part of:

```php
<?php
$permissions = Gatekeeper::findUserById(1)->permissions;
foreach ($permissions as $perm) {
	echo $perm->description."\n";
}
?>
```

## Giving a user a permission

```php
<?php
$userId = 1;
$permissionId = 1;
if (Gatekeeper::findUserById($userId)->addPermission($permissionId) === true) {
	echo 'Permission added!';
}
?>
```