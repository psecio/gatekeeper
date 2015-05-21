<?php

namespace Psecio\Gatekeeper\User\Collection;

interface GroupProviderInterface
{
	public function findByUserId($userId);
	public function create($model, array $data);
}

