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