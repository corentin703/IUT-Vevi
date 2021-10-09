<?php

namespace App\Src\ServiceContainer;

class ServiceContainer
{
    /**
     * Contains all services of the php App
     * @var array
     */
    private $container = array();

    /**
     * Get a service in the container
     *
     * @param string $serviceName Namo of the service to create in the container
     * @return mixed
     */
    public function get(string $serviceName)
    {
        return $this->container[$serviceName];
    }

    /**
     * Create a service in the container
     *
     * @param string $serviceName Name of the service to retrieve
     * @param mixed $assigned Value associated to the service (can be any type)
     */
    public function set(string $serviceName, $assigned)
    {
        $this->container[$serviceName] = $assigned;
    }

    /**
     * Unset a service in the container
     *
     * @param string $serviceName Name of the service to be unset in the container
     */
    public function unset(string $serviceName)
    {
        unset($this->container[$serviceName]);
    }

}