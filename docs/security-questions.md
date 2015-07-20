# Security Questions

Gatekeeper includes the concept of security questions to act as a secondary mechanism for authenticating the user. Instead of trying to provide a set of questions with the installation, the tool only provides the functionality to create and verify
the answers.

The answers for the questions are stored as `bcrypt` strings instead of in plain-text to prevent simple exposure if the database is compromised. It currently uses the [password hashing](http://php.net/manual/en/ref.password.php) handling in PHP for hash creation and verification. It evaluates the hashes directly and, as such, the answer is *case sensitive* and must match the answer exactly.

Additionally, Gatekeeper also prevents the user from providing an answer that's the same as their current password.

## Adding a question

To add a security question for a user, you'll need to first find the user then call the `addSecurityQuestion` method on that user object:

```php
<?php
$user = Gatekeeper::findUserById(1);
$result = $user->addSecurityQuestion(array(
	'question' => 'What...is your favorite color?',
	'answer' => 'Blue...no, yellow!'
));

if ($result === true) {
	echo 'Question added successfully';
}

?>
```

## Getting a user's questions and answers

You can get the list of questions for a user by using the `securityQuestions` property:

```php
<?php
$user = Gatekeeper::findUserById(1);

// Returns a collection object of the user's questions
$questions = $user->securityQuestions;
?>
```

## Validating the answer given

You can use the `verifyAnswer` method on the `SecurityQuestionModel` object to verify the answer to the given question. For example, we can pull the questions and check to be sure the answer to the first one is correct:

```php
<?php
$questions = Gatekeeper::findUserById(1)->securityQuestions;
$answer = "this is my answer that's correct";

if ($questions[0]->verifyAnswer($answer) === true) {
	echo 'The answer was correct!';
}
?>
```
