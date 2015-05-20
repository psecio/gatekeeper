# Users

We'll start with the **User** handling. Gatekeeper makes it simple to manage users and perform the usual CRUD (create, read update, delete) operations on their data.

Users are represented as objects in the code with the following properties:

- username
- password
- email
- firstName
- lastName
- status
- id
- resetCode
- resetCodeTimeout
- groups
- created
- updated
- groups (relational)
- permissions (relational)
- loginAttempts (relational)

You can access this data on a populated user object as you would any other object properties:

```php
<?php
echo 'Full name: '.$user->firstName.' '.$user->lastName."\n";
?>
```