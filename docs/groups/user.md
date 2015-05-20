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