<?php

namespace Controllers;

use App\Src\App;
use App\Src\Request\Request;

class LoginController extends ControllerBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function loginHandler(Request $request)
    {
        if (!$this->app->getService('userFinder')->isConnected())
            return $this->app->getService('render')('Login');
        else
        {
            $this->app->destroySession();
            $this->app->getService('redirect')('/'); // Redirige vers la racine
        }
    }

    public function loginDBHandler(Request $request)
    {
        $userInfos = [
            'username' => $request->getParameters('username'),
            'password' => $request->getParameters('password'),
        ];

        $check = $this->app->getService('userFinder')->VerrifyLogIn($userInfos['username'], md5($userInfos['password']));

        if ($check == true)
        {
            $this->app->setSessionParameters('user', $this->app->getService('userFinder')->findOneByName($userInfos['username'])->toArray());
            return $this->app->getService('redirect')('/home');
        }
        else
            return $this->app->getService('render')('Login', array_merge(['errorLI' => true], $userInfos));
    }
}