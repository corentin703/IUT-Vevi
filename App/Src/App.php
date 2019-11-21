<?php
/**
 * Created by PhpStorm.
 * User: Corentin
 * Date: 02/04/2019
 * Time: 09:15
 */

namespace App\Src;

use App\Src\Request\Request;
use App\Src\Response\Response;
use App\Src\ServiceContainer\ServiceContainer;
use App\Src\Route\Route;

class App
{
    /**
     * @var array
     */
    private $routes = array();

    /**
     * @var int statusCode
     */
    private $statusCode;


    public function __construct(ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Retrieve a service from the service container
     *
     * @param string $serviceName Name of the service to retrieve
     * @return mixed
     */
    public function getService(string $serviceName)
    {
        return $this->serviceContainer->get($serviceName);
    }

    /**
     * Set a service in the service container
     *
     * @param string $serviceName Name of the service to set
     * @param mixed $assigned value of the service to set
     */
    public function setService(string $serviceName, $assigned)
    {
        $this->serviceContainer->set($serviceName, $assigned);
    }

    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

    /**
     * Creates a Route for HTTP verb Get
     *
     * @param string $pattern
     * @param callable $callable
     * @return $this
     */
    public function get(string $pattern, callable $callable)
    {
        $this->registerRoute(Request::GET, $pattern, $callable);

        return $this;
    }

    /**
     * @param string $pattern
     * @param callable $callable
     * @return $this
     */
    public function post(string $pattern, callable $callable)
    {
        $this->registerRoute(Request::POST, $pattern, $callable);

        return $this;
    }

    /**
     * @param string $pattern
     * @param callable $callable
     * @return $this
     */
    public function put(string $pattern, callable $callable)
    {
        $this->registerRoute(Request::PUT, $pattern, $callable);

        return $this;
    }

    /**
     * @param string $pattern
     * @param callable $callable
     * @return $this
     */
    public function delete(string $pattern, callable $callable)
    {
        $this->registerRoute(Request::DELETE, $pattern, $callable);

        return $this;
    }


    /**
     * Launch the php App
     *
     * @param Request|null $request
     * @throws \Exception
     */
    public function run(Request $request = null)
    {
        session_start(); // Démarre la session utilisateur

        if ($request === null)
            $request = Request::createFromGlobals();

        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $route)
        {
            if ($route->match($method, $uri))
            {
                return $this->process($route, $request);
            }
        }

        throw new \Error('No routes avaliable for the uri : ' . $uri);
    }

    /**
     * Process Route
     *
     * @param Route $route
     * @throws HttpException
     */
    private function process(Route $route, Request $request)
    {
        try
        {
            $arguments = $route->getArguments();
            array_unshift($arguments, $request);
            $content = call_user_func_array($route->getCallable(), $arguments);
            
            if ($content instanceof Response)
            {
                $content->send();
                return;
            }

            $response = new Response($content, $this->statusCode ?? 200);
            $response->send();
        }
        catch (\HttpException $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            throw new \Error(('There was an error during the processing of your Request'));
        }
    }

    /**
     * Register a Route in the routes array
     *
     * @param string $method
     * @param string $pattern
     * @param callable $callable
     */

    private function registerRoute(string $method, string $pattern, callable $callable)
    {
        $this->routes[] = new Route($method,$pattern, $callable);
    }

    /**
     * Create new session
     */
    public function createSession() : void
    {
        session_start(); // Create a new session
    }

    /**
     * Destroy current session
     */
    public function destroySession() : void
    {
        session_destroy(); // Destruction  de la sesion courante
    }

    /**
     * Return parameter name from current session array
     *
     * @param string $name
     * @return mixed|null
     */
    public function getSessionParameters(string $name)
    {
        if (isset($_SESSION[$name]))
            return $_SESSION[$name];
        else
            return null;
    }

    /**
     * Set session's parameter
     *
     * @param string $name
     * @param $parameter
     */
    public function setSessionParameters(string $name, $parameter) : void
    {
        $_SESSION[$name] = $parameter;
    }

    /**
     * Return the last uri which has been visited
     *
     * @return mixed
     */
    public function getLastUri()
    {
        return $_SERVER['HTTP_REFERER'];
    }
}
