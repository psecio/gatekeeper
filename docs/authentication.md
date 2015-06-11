# Authentication

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

## Remember Me

In most applications there's a concept of session lasting longer than just one login. It's common to see apps allowing a "Remember Me" kind of handling and Gatekeeper includes this functionality in a simple, easy to use way. There's two functions in the main `Gatekeeper` class that take care of the hard work for you:

```php
<?php
// To set it up and create the tokens based on a user
$user = Gatekeeper::findUserByUsername($credentials['username']);

if (Gatekeeper::rememberMe($user) === true) {
    echo 'this user is now remembered for 14 days!';
}

// Then to check when the user comes back in
$user = Gatekeeper::checkRememberMe();
if ($user !== false) {
    echo "Hey, I remember you, ".$user->username;
}

?>
```

Using the `checkRememberMe` method, you can automatically verify the existence of the necessary cookie values and return the user they match. The default timeout for the "remember me" cookies is **14 days**. This can be changed by passing in an `interval` configuration option when the `rememberMe` function is called:

```
<?php
$user = Gatekeeper::findUserByUsername($credentials['username']);

$config = array(
    'interval' => '+4 weeks'
);
if (Gatekeeper::rememberMe($user, $config) === true) {
    echo 'this user is now remembered for 14 days!';
}

?>
```

The `interval` format here is any supported by the [PHP DateTime handling](http://php.net/manual/en/datetime.formats.php) in the constructor.

> **NOTE:** As the "remember me" handling uses cookies to store the token, use of this feature should happen before any other content is output to the page. This is a limitation with how cookies must be set (as headers).

#### Remember Me & Authentication

In addition to the more manual handling of the "remember me" functionality above, you can also have the `authenicate` method kick off the process when the user successfully authenticates with a second optional parameter:

```
<?php
$credentials = array(
    'username' => 'ccornutt',
    'password' => 'valid-password'
);
if (Gatekeeper::authenticate($credentials, true) == true) {
    echo 'valid!';
}
?>
```

The only difference here is that second parameter, the `true`, that is a switch to turn on the "remember" handling. By default this is disabled, so if you want to use this automatically, you'll need to enable it here. With that enabled, you can then use the `checkRememberMe` method mentioned above to get the user that matches the token.
