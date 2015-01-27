# Restrictions

You can place restrictions on the authentication of your users via Gatekeeper. They can be added with the `restrict` method on the
main Gatekeeper class. For example, if we want to add IP-based restrictions:

```php
<?php
Gatekeeper::restrict('ip', array(
    'DENY' => '127.*'
));
```

This restriction is then added to the set that is evaluated on authentication. If any of the checks fail, the authentication is
stopped and a `\Psecio\Gatekeeper\Exception\RestrictionFailedException` is thrown.

## Restriction Evaluation

Restrictions are currently only evaluated on user login (with the `authenticate` method).

## IP Restriction

You can allow or deny users based on their `REMOTE_ADDR` value when they try to access the application. Here's a simple set up to
deny users from localhost (127.0.0.1):

```
<?php
Gatekeeper::restrict('ip', array(
    'DENY' => '127.*'
));
?>
```

In this example, we're setting a `DENY` check for anything in the `127.*` range (so, localhost). The `*` (asterisk) operates as a
wildcard character and can be used to replace any number set in the IPv4 format. So, you can use it like:

- 127.*
- 192.168.1.*
- 192.*.1.100

You can also set up more complex rules with the `ALLOW` check too:

```
<?php
Gatekeeper::restrict('ip', array(
    'DENY' => '127.*',
    'ALLOW' => '145.12.14.*'
));
?>
```

In this example we're both denying anything from localhost and only allowing things matching the `145.12.14.*` pattern.

**NOTE:** The `ALLOW` and `DENY` restrictions will be evaluated if they exist. So, you can either: just use `DENY`, just use `ALLOw` or combine them into something more complex. If you have a pattern that matches the current IP in both, it will fail closed with a `DENY`.