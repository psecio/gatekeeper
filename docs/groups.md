# Groups

Creating a Group
----------------

Making a new group is as easy as making a new user. One thing to note, the *group name* must be **unique**:

```php
<?php
$attrs = array(
    'name' => 'Group #1'
);
Gatekeeper::createGroup($attrs);
?>
```

## Finding Groups

And, like users, you can find groups by their IDs:

```php
<?php
$group = Gatekeeper::findGroupById(1);
?>
```

If the group is not found, a `GroupNotFoundException` will be thrown.

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