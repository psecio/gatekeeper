<?php

namespace Psecio\Gatekeeper\Restrict;

class Throttle extends \Psecio\Gatekeeper\Restriction
{
    public $model;

    /**
     * Execute the evaluation for the restriction
     *
     * @return boolean Success/fail of evaluation
     */
    public function evaluate()
    {
        $config = $this->getConfig();
        $throttle = \Psecio\Gatekeeper\Gatekeeper::getUserThrottle($config['userId']);
        $throttle->updateAttempts();
        $this->model = $throttle;

        // See if they're blocked
        if ($throttle->status === \Psecio\Gatekeeper\ThrottleModel::STATUS_BLOCKED) {
            $result = $throttle->checkTimeout();
            if ($result === false) {
                return false;
            }
        } else {
            $result = $throttle->checkAttempts();
            if ($result === false) {
                return false;
            }
        }

	return true;
    }
}