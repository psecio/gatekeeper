<?php

namespace Psecio\Gatekeeper\Handler;
use Psecio\Gatekeeper\Gatekeeper as g;

class FindBy extends \Psecio\Gatekeeper\Handler
{
	/**
	 * Execute the "find by *" handling - smart enough to know
	 * 	if it's for one or multiple
	 *
	 * @return mixed Single model instance or collection on multiple
	 */
	public function execute()
	{
		$name = $this->getName();
		$args = $this->getArguments();

		return $this->handleFindBy($name, $args);
	}


	/**
     * Handle the "findBy" calls for data
     *
     * @param string $name Function name called
     * @param array $args Arguments
     * @throws \Exception\ModelNotFoundException If model type is not found
     * @throws \Exception If Data could not be found
     * @return object Model instance
     */
    public function handleFindBy($name, $args)
    {
        $action = 'find';
        $name = str_replace($action, '', $name);
        preg_match('/By(.+)/', $name, $matches);

        if (empty($matches) && strtolower(substr($name, -1)) === 's') {
            return $this->handleFindByMultiple($name, $args, $matches);
        } else {
            return $this->handleFindBySingle($name, $args, $matches);
        }

        return $instance;
    }

    /**
     * Handle the "find by" when a single record is requested
     *
     * @param string $name Name of function called
     * @param array $args Arguments list
     * @param array $matches Matches from regex
     * @return \Modler\Collection collection
     */
    public function handleFindBySingle($name, $args, $matches)
    {
        $property = lcfirst($matches[1]);
        $model = str_replace($matches[0], '', $name);
        $data = array($property => $args[0]);

        $modelNs = '\\Psecio\\Gatekeeper\\'.$model.'Model';
        if (!class_exists($modelNs)) {
            throw new \Psecio\Gatekepper\Exception\ModelNotFoundException('Model type '.$model.' could not be found');
        }
        $instance = new $modelNs($this->getDb());
        $instance = $this->getDb()->find($instance, $data);

        if ($instance->id === null) {
            return false;
        }

        return $instance;
    }

    /**
     * Handle the "find by" when multiple are requested
     *
     * @param string $name Name of function called
     * @param array $args Arguments list
     * @param array $matches Matches from regex
     * @return \Modler\Collection collection
     */
    public function handleFindByMultiple($name, $args, $matches)
    {
        $data = (isset($args[0])) ? $args[0] : array();
        $model = substr($name, 0, strlen($name) - 1);
        $collectionNs = '\\Psecio\\Gatekeeper\\'.$model.'Collection';
        if (!class_exists($collectionNs)) {
            throw new \Psecio\Gatekeeper\Exception\ModelNotFoundException('Collection type '.$model.' could not be found');
        }
        $model = g::modelFactory($model.'Model');
        $collection = new $collectionNs($this->getDb());
        $collection = $this->getDb()->find($model, $data, true);

        return $collection;
    }
}
