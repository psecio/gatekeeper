<?php

namespace Psecio\Gatekeeper\Handler;

class Create extends \Psecio\Gatekeeper\Handler
{
	/**
	 * Execute the object/record creation handling
	 *
	 * @throws \Psecio\Gatekeeper\Exception\ModelNotFoundException If model type is not found
	 * @return mixed Either model object instance or false on failure
	 */
	public function execute()
	{
		$args = $this->getArguments();
		$name = $this->getName();

		$model = '\\Psecio\\Gatekeeper\\'.str_replace('create', '', $name).'Model';
        if (class_exists($model) === true) {
            $instance = new $model($this->getDb(), $args[0]);
            $instance = $this->getDb()->save($instance);
            return $instance;
        } else {
            throw new \Psecio\Gatekeeper\Exception\ModelNotFoundException(
            	'Model type '.$model.' could not be found'
            );
        }
        return false;
	}
}