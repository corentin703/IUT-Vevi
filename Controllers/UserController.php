<?php
/**
 * Created by PhpStorm.
 * User: Corentin
 * Date: 29/03/2019
 * Time: 10:08
 */

namespace Controllers;

use App\Src\App;
use App\Src\Request\Request;
use Model\Finder\UserFinder;

class UserController extends ControllerBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function registerHandler(Request $request)
    {
        return $this->app->getService('render')('Register');
    }

    public function registerDBHandler(Request $request)
    {
        $userInfos = [
            'firstName' => $request->getParameters('firstName'),
            'lastName' => $request->getParameters('lastName'),
            'username' => $request->getParameters('username'),
            'password' => $request->getParameters('password'),
            'passwordConfirm' => $request->getParameters('passwordConfirm'),
            'email' => $request->getParameters('email'),
        ];

        try
        {
            $result = $this->app->getService('userActor')->create($userInfos);
            return $this->app->getService('render')('Login', ['registered' => true]);
        }
        catch (\Error $e)
        {
            $error = "";
            switch ($e->getCode())
            {
                case 1 : $error = "insertError"; break;
                case 2 : $error = "passwordsDMatch"; break;
                case 3 : $error = "userAlreadyExist"; break;
            }

            return $this->app->getService('render')('Register', [$error => true, 'userInfos' => $userInfos]); // Affiche erreur en fonction du problème
        }
    }


    public function userSettingsHandler(Request $request)
    {
        if ($this->app->getService('userFinder')->isConnected())
        {
            return $this->app->getService('render')('UserSettings', ['user' => $this->app->getSessionParameters('user')]);
        }
        else
            return $this->app->getService('redirect')('/');
    }

    public function updateUserDBHandler(Request $request)
    {
        try
        {
            $userInfos = [
                'firstName' => $request->getParameters('firstName'),
                'lastName' => $request->getParameters('lastName'),
                'username' => $request->getParameters('username'),
                'password' => $request->getParameters('password'),
                'passwordConfirm' => $request->getParameters('passwordConfirm'),
                'email' => $request->getParameters('email')
            ];

            $this->app->getService('userActor')->update($userInfos);

            return $this->app->getService('render')('UserSettings', ['user' => $this->app->getSessionParameters('user'), 'success' => true]);
        }
        catch (\Error $e)
        {
            if ($e->getCode() === 1)
                return $this->app->getService('render')('UserSettings', ['user' => $this->app->getSessionParameters('user'), 'passwordsMismatch' => true]);
            else
                return $this->app->getService('render')('UserSettings', ['user' => $this->app->getSessionParameters('user'), 'updateError' => true]);
        }
    }

    public function followUserDBHandler(Request $request)
    {
        try
        {
            $userId = $request->getParameters('userId');
            $this->app->getService('userActor')->follow($userId);

            return $this->app->getService('redirect')('/home');

        }
        catch (\Error $e)
        {
            return $this->app->getService('render')('404', ['reason' => "Erreur", 'details' => "Vous ne pouvez pas vous suivre vous même"]);
        }
    }

    public function deleteUserHandler(Request $request)
    {
        try
        {
            $confirm = $request->getParameters('confirm');

            if ($confirm === "true")
            {
                $this->app->getService('userActor')->delete();
                return $this->app->getService('redirect')('/');
            }

            else throw new \Error("Delete account not confirmed", 1);
        }
        catch (\Error $e)
        {
            switch ($e->getCode())
            {
                case 1: return $this->app->getService('render')('UserSettings', ['user' => $this->app->getSessionParameters('user'), 'unConfirmed' => true]);
                        break;
                default: return $this->app->getService('render')('UserSettings', ['user' => $this->app->getSessionParameters('user'), 'updateError' => true]);
                        break;
            }
        }
    }

}