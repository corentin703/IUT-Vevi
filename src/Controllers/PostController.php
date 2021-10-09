<?php

namespace Controllers;


use App\Src\App;
use App\Src\Request\Request;
use Model\Gateway\PostGateway;

class PostController extends ControllerBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function createPostDBHandler(Request $request)
    {
        $postInfos = [
            'userId' => $this->app->getSessionParameters('user')['id'],
            'content' =>  $request->getParameters('content'),
            'imageLink' => "N/A", // $request->getParameters('imageLink'),
        ];

        $this->app->getService('postActor')->create($postInfos);

        return $this->app->getService('redirect')($this->app->getLastUri());
    }

    public function updatePostDBHandler(Request $request)
    {
        $postInfos = [
            'id' => $request->getParameters('postId'),
            'content' => $request->getParameters('content'),
            'imageLink' => "N/A", // $request->getParameters('imageLink'),
        ];

        try
        {
            $this->app->getService('postActor')->update($postInfos);
            return $this->app->getService('redirect')($this->app->getLastUri());
        }
        catch (\Error $e)
        {
            if ($e->getCode() == 1)
                return $this->app->getService('render')('404', ['reason' => "Accès refusé", 'details' => "Ce post n'est pas le vôtre !."]);
            else
                return $this->app->getService('render')('404', ['reason' => "Erreur", 'details' => "Une erreur s'est produite."]);
        }

    }

    public function deletePostDBHandler(Request $request)
    {
        $postId = $request->getParameters('postId');

        try
        {
            $this->app->getService('postActor')->delete($postId);
            return $this->app->getService('redirect')($this->app->getLastUri());
        }
        catch (\Error $e)
        {
            if ($e->getCode() == 1)
                return $this->app->getService('render')('404', ['reason' => "Accès refusé", 'details' => "Ce post n'est pas le vôtre !."]);
            else
                return $this->app->getService('render')('404', ['reason' => "Erreur", 'details' => "Une erreur s'est produite."]);
        }

    }

    public function repostDBHandler(Request $request)
    {

    }

    public function likePostDBHandler(Request $request)
    {
        $postId = $request->getParameters('postId');
        $this->app->getService('postActor')->like($postId);

        return $this->app->getService('redirect')($this->app->getLastUri());


    }

    public function dislikePostDBHandler(Request $request)
    {
        $postId = $request->getParameters('postId');
        $this->app->getService('postActor')->dislike($postId);

        return $this->app->getService('redirect')($this->app->getLastUri());
    }

}

