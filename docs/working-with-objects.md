# Working with Objects

Each item in the system is represented by an object (a `model` type). When you perform an operation like a `find` or `create`, an instance of the corresponding model is created. There's some common things that you can do on models all across the system. Rather than duplicate that information across multiple pages of the documentation, I'm going to put it here in one place.

## Finding

You can use the magic "find by" handling to locate records of the various types. Most commonly, this would be used to locate a record by its unique ID number (all records have this). Here's some examples:

```php
<?php
// Finding a User
$user = Gatekeeper::findUserById(1);

// Or a Group, maybe a Permission
$group = Gatekeeper::findGroupById(1);
$permission = Gatekeeper::findPermissionById(1);
?>
```

Each of these will return an object you can pull properties from. For example, a `User` object has properties like `username`, `email` and `firstName`. These can be accessed directly just like any other PHP property:

```php
<?php
$user = Gatekeeper::findUserById(1);

echo 'My name is '.$user->firstName.', username is '.$user->username.' and email is '.$user->email;
?>
```

If no records are found given the criteria you provided, one of the "not found" `Exception` options will be thrown.

**NOTE:** You can find the list of properties on the pages for each of the different types (like `Users` and `Groups`).

## Deleting

You can also use common functionality to delete records from the system. This uses a format similar to the "find by" methods but instead uses a "delete by".

```php
<?php
if (Gatekeeper::deleteUserById(1) === true) {
    echo 'User deleted!';
}

// Or by username:
Gatekeeper::deleteUserByUsername('ccornutt');
?>
```

The delete calls can also use any property on the object, but there is one thing to watch out for. If you provide information that matches more than one record in the system, the operation will fail. For example, if there were five users with a first name of "Chris", the call doesn't know which one you want to remove, so it returns false.

In this case, you'll need to run a `find` operation and locate the record you want. When you have the model instance you want, you can just call `delete` directly on it:

```php
<?php
$user = Gatekeeper::findUserByUsername('ccornutt');
$user->delete();
?>
```

## Cloning

You can clone certain kinds of objects in the Gatekeeper system, duplicating the type of object and its relations.

#### Users

You can clone a user with the `Gatekeeper::cloneUser` method. This will create a new user with the data provided and link this new user to the same permissions and groups as the user you're cloning.

```php
<?php
$user1 = Gatekeeper::findUserByUsername('ccornutt');

$result = Gatekeeper::cloneUser($user1, [
	'username' => 'ccornutt1',
	'password' => 'super-secret',
  	'email' => 'ccornut2@mydomain.com',
  	'firstName' => 'Chris',
  	'lastName' => 'Cornutt2'
]);

if ($result === true) {
	echo 'User cloned successfully!';
}
?>
```

In this case user `cornutt1` will have the same permissions and groups as `ccornutt`. The result of the `cloneUser` function call is the success/fail status of the creation.

