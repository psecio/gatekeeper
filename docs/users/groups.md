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
    echo "User added successfullly!";
}
?>
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