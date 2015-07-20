# Groups

Groups are represented as objects in the Gatekeeper system with the following properties:

- description
- name
- id
- created
- updated
- users (relational)

Gatekeeper also supports hierarchical groups (see below).

## Getting All Groups

You can use the `findGroupss` method on the `Gatekeeper` class to get a list (returns a `GroupCollection`) of the current groups:

```php
$groups = Gatekeeper::findGroups();

// You can then slice it up how you need, like getting the first three
$shortGroupList = $groups->slice(1, 3);
```

## Creating a Group

Making a new group is as easy as making a new user. One thing to note, the *group name* must be **unique**:

```php
<?php
$attrs = array(
    'name' => 'group1',
    'description' => 'Group #1'
);
Gatekeeper::createGroup($attrs);
?>
```

You can also create a group with an expiration timeout, allowing users in that group only a certain timeframe for their access. You use the `expires` value on the creation to set this with a Unix timestamp:

```php
<?php
$attrs = array(
    'name' => 'group1',
    'description' => 'Group #1',
    'expires' => strtotime('+1 day')
);
Gatekeeper::createGroup($attrs);
?>
```

You can then check to see if a group has expired with the `isExpired` method:

```php
<?php
if (Gatekeeper::findGroupById(1)-isExpired() === true) {
	echo 'Group expired!';
}

?>
```

## Getting Group Users

Much like you can easily get the groups the user belongs to, you can also get the members of a group. This will return a collection of user objects:

```php
<?php
$users = Gatekeeper::findGroupById(1)->users;

foreach ($users as $user) {
    echo 'Username: '.$user->username."\n";
}
?>
```

## Adding a user to a group

You can add a user to a group by giving the `addUser` method one of two kinds of inputs:

```php
<?php
// Either a user ID
Gatekeeper::findGroupById(1)->addUser(1);

// Or a user model, it will extract the ID
$user = new UserModel();
Gatekeeper::findGroupById(1)->addUser($user);
?>
```

## Removing a user from a group

You can remove a user from a group in much the same way, either by an ID or a User model instance with the `removeUser` method:

```
<?php
// Either a user ID
Gatekeeper::findGroupById(1)->removeUser(1);

// Or a user model, it will extract the ID
$user = new UserModel();
Gatekeeper::findGroupById(1)->removeUser($user);
?>
```

## Checking to see if a user is in a group

You can use the `inGroup` method to check and see if a user ID is in a group:

```php
<?php
$userId = 1;
if (Gatekeeper::findGroupById(1)->inGroup($userId) === true) {
	echo 'User is in the group!';
}
?>
```

## Adding a permission to a group

You can add permissions to groups too. These are related to the groups, not the users directly, so if you get the permissions for a user, these will not show in the list.

```php
<?php
$permId = 1;
Gatekeeper::findGroupById(1)->addPermission($permId);

// Or you can use a PermissionModel object
$permission = Gatekeeper::findPermissionById(1);
Gatekeeper::findGroupById(1)->addPermission($permission);
?>
```

## Removing a permission from a group

A permission can be removed from a group in the same way a user can, just with the `removePermission` method:

```
<?php
$permId = 1;
Gatekeeper::findGroupById(1)->removePermission($permId);

// Or you can use a PermissionModel object
$permission = Gatekeeper::findPermissionById(1);
Gatekeeper::findGroupById(1)->removePermission($permission);
?>
```

## Getting the permissions associated with the group

Since groups can have permissions attached, you can fetch those through the `permissions` property much in the same way you can for users:

```php
<?php
$permissions = Gatekeeper::findGroupById(1)->permissions;
foreach ($permissions as $permission) {
	echo 'Description: '.$permission->description."\n";
}
?>
```

## Hierarchical Groups

Groups can also be added as children of other groups to help make categorizing easier. They can either be added by ID or model instance:

```php
<?php
$group = Gatekeeper::findGroupById(1);
$group->addChild(2);
// or
$childGroup = Gatekeeper::findGroupById(2);
$group->addChild($childGroup);
?>
```

You can find the child groups using the `children` property from a group instance:

```
<?php
$children = Gatekeeper::findGroupById(1)->children;
?>
```

You can also remove child groups similarly:

```
<?php
$group = Gatekeeper::findGroupById(1);

// You can remove either by ID
$group->removeChild(2);
// or by model instance
$group2 = Gatekeeper::findGroupById(2);
$group->removeChild($group2);
?>
```
