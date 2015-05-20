## Check if a user is currently banned (throttling)

If the user login has had too many failed attempts, they'll be marked as "banned" in the system. You can find a user's ban status with the `isBanned` check:

```php
<?php
if (Gatekeeper::findUserById(1)->isBanned() === true) {
    echo "User is banned!";
}
?>
```

## Get full user throttle information

You can also get the full throttling information for a user using the `throttle` property:

```php
<?php
$throttle = Gatekeeper::findUserById(1)->throttle;

// This gives you properties like:
$throttle->attempts;
$throttle->status;
$throttle->lastAttempt;
$throttle->statusChange;
?>
```

## Get the number of login attempts

You can also get information about the number of times a login has been attempted for a user (valid or invalid) with the `loginAttempts` property:

```php
<?php
$attempts = Gatekeeper::findUserById(1)->loginAttempts;
echo "The user has tried to log in ".$attempts." times.";
?>
```