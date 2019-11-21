<?php

namespace Controllers;

use App\Src\App;

abstract class ControllerBase
{
    /**
     * @var App
     */
    protected $app;

    public function __construct(App $app) {
        $this->app = $app;
    }

}