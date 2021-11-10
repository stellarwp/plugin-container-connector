<?php

namespace StellarWP\ContainerConnector;

use InvalidArgumentException;

class ContainerConnector
{
    private $containers = [];

    /**
     * @return ContainerContract
     */
    public function getContainer()
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach($traces as $trace) {
            // Use either the class (if available) or caller to extract the namespace
            $caller = isset($trace['class']) ? $trace['class'] : $trace['function'];
            $namespace = substr($caller, 0, strrpos($caller, '\\'));

            if ( isset($this->containers[$namespace])) {
                return $this->containers[$namespace];
            }
        }

        return null;
    }

    /**
     * @param ContainerContract $container
     * @param string $namespace
     */
    public function registerContainer(ContainerContract $container, $namespace)
    {
        if ( isset($this->containers[$namespace])) {
            throw new InvalidArgumentException("$namespace has already been registered");
        }

        $this->containers[$namespace] = $container;
    }
}
