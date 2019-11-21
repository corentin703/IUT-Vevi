<?php

namespace App\Src\Request;

class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    /**
     * @var Array
     */
    private $parameters;

    /**
     * Request constructor.
     *
     * @param array $query Query string from the Request
     * @param array $request Request body from the Request (Post Method)
     */
    public function __construct(array $query = [], array $request = [])
    {
        $this->parameters = array_merge($query, $request);
    }

    /**
     * Create an instance from global variable
     * This method needs to stay static and have the name create from globals
     *
     * @return Request
     */
    public static function createFromGlobals()
    {
        return new self($_GET, $_POST);
    }

    /**
     * Return parameter name from get or post arguments
     *
     * @param string $name Name of the parameter to retrieve
     * @return mixed | null
     */
    public function getParameters(string $name)
    {
        if (isset($this->parameters[$name]))
            return htmlspecialchars($this->parameters[$name]); // Protection des failles XSS
        else
            return null;
    }

    /**
     * Return the Request method used
     * if no method avaliable return get by default
     *
     * @return string
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'] ?? self::GET;
    }

    /**
     * Return the Request URI
     * Also takes care of removing the query string to not interfeer with our routing system
     *
     * @return string
     */
    public function getUri()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        if ($pos = strpos($uri, '?'))
            $uri = substr($uri, 0, $pos);

        return $uri;
    }
}

