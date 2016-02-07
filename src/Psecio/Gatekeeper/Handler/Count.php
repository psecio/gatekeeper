<?php

namespace Psecio\Gatekeeper\Handler;

class Count extends \Psecio\Gatekeeper\Handler
{
	/**
	 * Execute the object/record count handling
	 *
	 * @throws \Psecio\Gatekeeper\Exception\ModelNotFoundException If model type is not found
	 * @return mixed Either model object instance or false on failure
	 */
	public function execute()
	{
		$args = $this->getArguments();
		$name = $this->getName();

		$model = '\\Psecio\\Gatekeeper\\'.str_replace('count', '', $name).'Model';
        if (class_exists($model) === true) {
            $instance = new $model($this->getDb(), $args);
            return $this->getDb()->count($instance);
        } else {
            throw new \Psecio\Gatekeeper\Exception\ModelNotFoundException(
            	'Model type '.$model.' could not be found'
            );
        }
	}
}