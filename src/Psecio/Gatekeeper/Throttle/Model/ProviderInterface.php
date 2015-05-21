<?php

namespace Psecio\Gatekeeper\Throttle\Model;

interface ProviderInterface
{
	public function findByUserId($userId);
	public function updateAttempts();
	public function allow();
	public function checkTimeout($timeout = null);
	public function checkAttempts();
}