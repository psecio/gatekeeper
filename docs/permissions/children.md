## Adding Child Permissions

Much like groups, permissions also support the concept of children. Adding a permission as a child to a parent is easy and can be done in one of two ways:

```
<?php
$permission = Gatekeeper::findPermissionById(1);

// You can either add via ID
$permission->addChild(1);
// or with another model instance
$permission2 = Gatekeeper::findPermissionById(2);
$permission->addChild($permission);
?>
```

### Removing Child Permissions

You can also remove child permissions in a similar way:

```
<?php
$permission1 = Gatekeeper::findPermissionById(1);

// You can either remove via ID
$permission1->removeChild(2);
// Or by model instance
$permission2 = Gatekeeper::findPermissionById(1);
$permission1->removeChild($permission2);
?>
```

## Finding child permissions

If you want to find the permissions that are children of the current instance, you can use the `children` property:

```
<?php
$permission1 = Gatekeeper::findPermissionById(1);

$childPermissions = $permission1->children;
?>
```

This will return an array of permission objects representing the children.