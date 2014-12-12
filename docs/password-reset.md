# Password Reset Handling

*Gatekeeper* also includes some password reset handling functionality. It doesn't try to send an email or output a web page
with the functionality. Instead, it provides methods to generate and validate a unique code. When the code is generated, it is
added into the user's record and stored for evaluation.

The code will expire in *one hour* from the time it was generated.

```php
<?php
$user = Gatekeeper::findUserById(1);
$code = $user->getResetPasswordCode();

echo 'Your password reset code is: '.$code."\n";

// Now lets verify it...
$code = $_GET['code'];
if ($user->checkResetPasswordCode($code) === true) {
    echo 'valid!';
}
?>
```

If the code is valid, it and the timeout are cleared from the user's record.