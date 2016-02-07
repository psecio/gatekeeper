<?php

namespace Psecio\Gatekeeper\Handler;

class Count extends \Psecio\Gatekeeper\Handler
{
    /**
     * Execute the object/record count handling
     *
     * @throws \Psecio\Gatekeeper\Exception\ModelNotFoundException If model type is not found
     * @return int Count of entities
     */
    public function execute()
    {
        $args = $this->getArguments();
        $name = $this->getName();

        $model = '\\Psecio\\Gatekeeper\\' . str_replace('count', '',
                $name) . 'Model';
        if (class_exists($model) === true) {
            $instance = new $model($this->getDb());

            $count = (!$args) ? $this->getDb()->count($instance) : $this->getDb()->count($instance,
                $args[0]);
            return (int)$count['count'];
        } else {
            throw new \Psecio\Gatekeeper\Exception\ModelNotFoundException(
                'Model type ' . $model . ' could not be found'
            );
        }
    }
}