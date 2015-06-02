# Policies

Policies are one of the more complex parts of the Gatekeeper system. While most evaluations only need to check a user for groups or permissions, some things are a bit more complicated. That's where *policies* come in. A **policy** is a more complex evaluation that happens based on given objects and an *expression* to determine a pass or fail status.

Let's see an example of how we could translate an existing check for a permission into a policy. First off, here's how to check the permission:

```php
<?php

$user = Gatekeeper::findUserByUsername('ccornutt');

if ($user->hasPermission('permission1')) {
	echo 'They have the permission! Score!';
}

?>
```

This is a pretty simple example, but I wanted to start with the basics to show the use of expressions. So, lets translate this into a policy that can be reused across the whole system easily. The key to the policies lies in the use of the [Symfony Expression Language](http://symfony.com/doc/current/components/expression_language/syntax.html) syntax, a well-documented standards in use for configuring the Symfony framework.

Here's how to translate the same evaluation into a policy:

```php
<?php
// First let's make the policy
Gatekeeper::createPolicy(array(
	'name' => 'perm1-test',
	'expression' => '"permission1" in user.permissions.getName()',
	'description' => 'See if a user has "permission1"'
));

// Now, we need to evaluate the user against the policy
if (Gatekeeper::evaluatePolicy('perm1-test', $user) === true) {
	echo 'They have the permission! Rock on!';
}

?>
```

Once we've created this policy then we only need to use the `evaluatePolicy` check with the name and data to perform the check. This will return a boolean `true` or `false` based on the evaluation. This makes it easy to reuse the same logic all over your application. In this example Gatekeeper knows to translate the `UserModel` object into the `user` the expression is looking for. You could could also define multiple objects for reference in the expression by passing in an array of objects, either with indexes or without. For example:

```php
<?php

$user = Gatekeeper::findUserByUsername('ccornutt');
$group = Gatekeeper::findGroupByName('test1');

Gatekeeper::createPolicy(array(
	'name' => 'perm-group-test',
	'expression' => '"permission1" in user.permissions.getName() and group.name = "test1"',
	'description' => 'See if a user has "permission1" and the group is named "group1"'
));

$data = [ $user, $group ];
if (Gatekeeper::evaluatePolicy('perm-group-test', $data) === true) {
	echo "They're good - move along...";
}

?>
```

In this example, Gatekeeper is once again resolving the `UserModel` and `GroupModel` objects to `user` and `group` respectively (based on the class names). You can also define what you want the objects to be named for reference in the expression by providing indexes:

```php
<?php

$data = [
	'user' => $user,
	'group' => $group
];

?>
```

Additionally, if you ever have the need to evaluate a policy directly, you can do so with the `evaluate` method on the `PolicyModel` object:

```php
<?php
$policy = Gatekeeper::findPolicyByName('perm1-test');

if ($policy->evaluate($user) === true) {
	echo "Awesome, they're good to go!";
}

?>
```

## Using Closures as Policies

You can also use the same structure of you'd like to define policies as closures, letting you do a bit more programmatic evaluation of the data provided. The same rules apply above to how they're evaluated, you just define them differently:

```php
<?php

Gatekeeper::createPolicy([
	'name' => 'eval-closure',
	'description' => 'Using a closure to validate policy',
	'expression' => function($data) {
		return ($data->username === 'ccornutt');
	};
]);

if (Gatekeeper::evaluatePolicy('eval-closure', $user) === true) {
	echo "Sweet success!";
}
?>
```

Here we've define the check for a `username` match as a closure and run an evaluation on it.

> **NOTE:** Any checks that happen to have the same name as a policy that lives in the database will be overridden by a closure-based check (which may be advantageous depending on your needs).

