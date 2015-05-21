<?php

namespace Psecio\Gatekeeper\User\Model;

interface ProviderInterface
{
	public function findByUsername($username);
	public function findByUserId($userId);
	public function addPermission($perm);
	public function revokePermission($perm);
	public function addGroup($group);
	public function revokeGroup($group);
	public function activate();
	public function deactivate();
	public function getResetPasswordCode($length = 80);
	public function checkResetPasswordCode($resetCode);
	public function clearPasswordResetCode();
	public function inGroup($groupId);
	public function hasPermission($permId);
	public function isBanned();
	public function findAttemptsByUser($userId = null);
	public function grant(array $config);
	public function grantPermissions(array $permissions);
	public function grantGroups(array $groups);
	public function addSecurityQuestion(array $data);
}