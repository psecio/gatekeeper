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
- loginAttempts (relational)

You can access this data on a populated user object as you would any other object properties:

```php
<?php
echo 'Full name: '.$user->firstName.' '.$user->lastName."\n";
?>
```

## Getting All Users

You can use the `findUsers` method on the `Gatekeeper` class to get a list (returns a `UserCollection`) of the current users:

```php
$users = Gatekeeper::findUsers();

// You can then slice it up how you need, like getting the first three
$shortUserList = $users->slice(1, 3);
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
Additionally, you can also link the user to permissions at create time:

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

Deleting user records can be done with the `deleteUserById` method:

```php
<?php
if (Gatekeeper::deleteUserById(1) === true) {
    echo "User removed successfully!";
}

// Or, if you already have the User model object
Gatekeeper::deleteUser($user);
?>
```

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

## Adding a user to a group

You can add a user to a group by using the group ID:

```php
<?php
$groupId = 1;
if (Gatekeeper::findUserById($userId)->addGroup($groupId) === true) {
    echo "User added successfully!";
}
?>
```

You can also grant the group to a user with an expiration time, giving them permissions until a certain time. You set the expiration as a second value on the `addGroup` method by passing in a Unix timestamp:

```php
<?php
if (Gatekeeper::findUserById(1)->addGroup(1, strtotime('+1 day')) === true) {
    echo "User added successfully!";
}
```

## Revoking access to a group

You can also remove a user from a group by revoking their access:

```php
<?php
$groupId = 1;
if (Gatekeeper::findUserById($userId)->revokeGroup($groupId) === true) {
    echo "User removed from group successfully!";
}
?>
```

## Checking to see if a user has a permission

You can check the user's immediate permissions (not the ones on groups they belong to) with the `hasPermission` method:

```php
<?php
$permissionId = 1;
if (Gatekeeper::findUserById($userId)->hasPermission($permissionId) === true) {
    echo "They've got it!";
}
?>
```

You'll need to have the `id` value for the permission you want to check and provide that as the parameter in the call.

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

You can assign a permission **directly** to a user (not through a group) with the `addPermission` method:

```php
<?php
$userId = 1;
$permissionId = 1;
if (Gatekeeper::findUserById($userId)->addPermission($permissionId) === true) {
	echo 'Permission added!';
}
?>
```

You can also provide an optional second parameter with an expiration time if you only want to allow the user the permission for a limited about of time. This parameter should be in the form of a Unix timestamp:

```php
<?php
Gatekeeper::findUserById(1)->addPermission($permissionId, strtotime('+1 day'));
?>
```

When fetching a user's permission list (like with `$user->permissions`) it will only return the non-expired or permanent permissions.

## Revoking a permission

You can remove a permission from a user by revoking it:

```
<?php
$userId = 1;
$permissionId = 1;
if (Gatekeeper::findUserById($userId)->revokePermission($permissionId) === true) {
    echo 'Permission revoked!';
}
?>
```

## Using "grant"

There's also a method on the User object that can be used to grant a user access to multiple permissions and groups all at the same time: `grant`. Here's an example:

```php
<?php
Gatekeeper::findUserById(1)->grant(array(
    'permissions' => array(1, 3),
    'groups' => array(1)
));
?>
```

You can either specify a `permissions` and `groups` values as an array of IDs or you can feed in objects...or a mix of both:

```php
<?php
$perm1 = Gatekeeper::findPermissionById(1);
$group1 = Gatekeeper::findGroupById(1);

Gatekeeper::findUserById(1)->grant(array(
    'permissions' => array($perm1, 3),
    'groups' => array($group1)
));
?>
```

Additionally, much like manually adding groups and permissions for a user, you can also set an expiration time:

```php
<?php
$perm1 = Gatekeeper::findPermissionById(1);
$group1 = Gatekeeper::findGroupById(1);
$expireTime = strtotime('+1 day');

Gatekeeper::findUserById(1)->grant(array(
    'permissions' => array($perm1, 3),
    'groups' => array($group1)
), $expireTime);
?>
```

## Check if a user is currently banned (throttling)

If the user login has had too many failed attempts, they'll be marked as "banned" in the system. You can find a user's ban status with the `isBanned` check:

```php
<?php
if (Gatekeeper::findUserById(1)->isBanned() === true) {
    echo "User is banned!";
}
?>
```

## Get full user throttle information

You can also get the full throttling information for a user using the `throttle` property:

```php
<?php
$throttle = Gatekeeper::findUserById(1)->throttle;

// This gives you properties like:
$throttle->attempts;
$throttle->status;
$throttle->lastAttempt;
$throttle->statusChange;
?>
```

## Get the number of login attempts

You can also get information about the number of times a login has been attempted for a user (valid or invalid) with the `loginAttempts` property:

```php
<?php
$attempts = Gatekeeper::findUserById(1)->loginAttempts;
echo "The user has tried to log in ".$attempts." times.";
?>
```
