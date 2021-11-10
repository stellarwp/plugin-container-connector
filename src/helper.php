<?php

namespace StellarWP\ContainerConnector;

function getPluginContainer($container = null, $namespace = null)
{
    static $connector = null;

    if ($connector === null) {
        $connector = new ContainerConnector();
    }

    if ($container === null || $namespace === null) {
        return $connector->getContainer();
    }

    $connector->registerContainer($container, $namespace);
    return null;
}
