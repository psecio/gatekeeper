<?php

namespace Psecio\Gatekeeper\Restrict;

class Ip extends \Psecio\Gatekeeper\Restriction
{
    /**
     * Execute the evaluation for the restriction
     *
     * @return boolean Success/fail of evaluation
     */
    public function evaluate()
    {
        if (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR'])) {
            throw new \Psecio\Gatekeeper\Exception\DataNotFoundException('Cannot get remote address');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $config = $this->getConfig();

        if ($this->check($config, 'DENY', $ip) === true) {
            return false;
        }
        if ($this->check($config, 'ALLOW', $ip) === false) {
            return false;
        }
        return true;
    }

    /**
     * Check to see if the value matches against the configuration type
     *
     * @param array $config Configuration options
     * @param string $type Configuration type (ALLOW or DENY)
     * @param string $value  Value to compare against
     * @return boolean Found/not found by matching
     */
    public function check(array $config, $type, $value)
    {
        if (!isset($config[$type])) {
            return false;
        }
        $found = false;
        $config = (!is_array($config[$type])) ? array($config[$type]) : $config[$type];

        foreach ($config as $pattern) {
            $result = $this->validateIpContains($value, $pattern);
            if ($result === true && $found === false) {
                $found = true;
            }
        }
        return $found;
    }

    /**
     * Evaluate to see if the pattern given matches the IP address value
     *
     * @param string $ipAddress IPv4 address
     * @param string $pattern Pattern to match against
     * @return boolean Contains/does not contain
     */
    public function validateIpContains($ipAddress, $pattern)
    {
        // Replace wildcards (*) with regex matches and escape dots
        $pattern = str_replace(array('.', '*'), array('\.', '.+'), $pattern);
        return (preg_match('#'.$pattern.'#', $ipAddress) == true);
    }
}