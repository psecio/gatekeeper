## Activating/Deactivating Users

You can mark a user as active or inactive in the system easily. Inactive users will not be able to log in using the `authenticate` method. Changing the user status is easy:

```php
<?php
// Change the user status to active
Gatekeeper::findUserById(1)->activate();

// Change the user status to inactive
Gatekeeper::findUserById($userId)->deactivate();
?>
```