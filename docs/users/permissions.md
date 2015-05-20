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