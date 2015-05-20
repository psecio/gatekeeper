## Creating a Group

Making a new group is as easy as making a new user. One thing to note, the *group name* must be **unique**:

```php
<?php
$attrs = array(
    'name' => 'group1',
    'description' => 'Group #1'
);
Gatekeeper::createGroup($attrs);
?>
```