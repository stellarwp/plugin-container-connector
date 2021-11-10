<?php

namespace StellarWP\ContainerConnector;

interface ContainerContract
{
    public function make($abstract);

    public function bind($abstract, $concrete);

    public function singleton($abstract, $concrete);
}