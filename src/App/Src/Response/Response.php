<?php

namespace App\Src\Response;

class Response
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $headers;

    /**
     * Response constructor.
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $content, int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = array_merge(['Content-Type' => 'text/html'], $headers); // In the context of a website, we will always send 'text/html' header
    }

    /**
     * Get the status code of the Response
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Get content of the Response
     * @return string.
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * Send header to the browser
     * Always send headers before content, if you don't do this, you risk the browser to not understand what you are sending
     */
    public function sendHeader() : void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value)
        {
            header(sprintf('%s: %s', $name, $value));
        }
    }

    /**
     * Send the content of the Response to the browser
     */
    public function send()
    {
        $this->sendHeader(); // Insure headers are sent before sending content

        echo $this->content;
    }


}