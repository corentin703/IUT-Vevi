<?php
/**
 * Created by PhpStorm.
 * User: Corentin
 * Date: 02/04/2019
 * Time: 10:24
 */

namespace App;

use App\Src\App;
use App\Src\Request\Request;
use App\Src\Response\Response;
use Controllers\HomeController;
use Controllers\LoginController;
use Controllers\PostController;
use Controllers\RepostController;
use Controllers\UserController;

class Routing
{
    private $app;

    /**
     * Routing constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function setup()
    {
        $login = new LoginController($this->app);
        $user = new UserController($this->app);
        $home = new HomeController($this->app);
        $post = new PostController($this->app);
        $repost = new RepostController($this->app);

        $this->app->get('/', [$login, 'loginHandler']);

        $this->app->post('/login', [$login, 'loginDBHandler']);

        $this->app->get('/register', [$user, 'registerHandler']);

        $this->app->post('/tryRegister', [$user, 'registerDBHandler']);

        $this->app->get('/home', [$home, 'homeHandler']);

        $this->app->post('/home/post', [$post, 'createPostDBHandler']);

        $this->app->post('/home/modifyPost', [$post, 'updatePostDBHandler']);

        $this->app->post('/home/deletePost', [$post, 'deletePostDBHandler']);

        $this->app->post('/home/likePost', [$post, 'likePostDBHandler']);

        $this->app->post('/home/dislikePost', [$post, 'dislikePostDBHandler']);

        $this->app->post('/home/repost', [$repost, 'createRepostDBHandler']);

        $this->app->post('/home/deleteRepost', [$repost, 'deleteRepostDBHandler']);

        $this->app->post('/home/likeRepost', [$repost, 'likeRepostDBHandler']);

        $this->app->post('/home/dislikeRepost', [$repost, 'dislikeRepostDBHandler']);

        $this->app->get('/user/wall/(\w+)', [$home, 'userHandler']);

        $this->app->get('/user/search', [$home, 'userSearchHandler']);

        $this->app->get('/user/settings', [$user, 'userSettingsHandler']);

        $this->app->post('/user/settings/update', [$user, 'updateUserDBHandler']);

        $this->app->post('/user/follow', [$user, 'followUserDBHandler']);

        $this->app->post('/user/delete', [$user, 'deleteUserHandler']);

    }
}