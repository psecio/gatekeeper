<?php

namespace Psecio\Gatekeeper\User\Collection;

interface ProviderInterface
{
	public function findByGroupId($groupId);
}