# Permissions

The system supports the concept of *permissions*, a common part of a role-based access control system. In the Gatekeeper
system the permissions have these properties:

- id
- name
- description
- created date
- updated date

## Creating a permission

When creating a permission, you need to specify a name and description value. The `name` must be unique:

```php
<?php
$perm = array(
	'name' => 'perm1',
	'description' => 'Permission #1'
);
if (Gatekeeper::createPermission($perm) === true) {
	echo 'Permission created successfully!';
}
?>
```