<?php

namespace GiveWP;

use StellarWP\ContainerConnector\ContainerContract;
use function StellarWP\ContainerConnector\getPluginContainer;

require 'vendor/autoload.php';

class Container implements ContainerContract {
    public function make($abstract)
    {
    }

    public function bind($abstract, $concrete)
    {
    }

    public function singleton($abstract, $concrete)
    {
    }
}

function getContainer() {
    return getPluginContainer();
}

$container = new Container();

getPluginContainer($container, 'GiveWP');

$containerFromPlugin = getContainer();