<?php


namespace Controllers;


use App\Src\App;
use App\Src\Request\Request;

class RepostController extends ControllerBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function createRepostDBHandler(Request $request)
    {
        $postId = $request->getParameters('postId');

        $this->app->getService('repostActor')->create($postId);

        return $this->app->getService('redirect')($this->app->getLastUri());
    }


    public function likeRepostDBHandler(Request $request)
    {
        $repostId = $request->getParameters('repostId');
        $this->app->getService('repostActor')->like($repostId);

        return $this->app->getService('redirect')($this->app->getLastUri());


    }

    public function dislikeRepostDBHandler(Request $request)
    {
        $repostId = $request->getParameters('repostId');
        $this->app->getService('repostActor')->dislike($repostId);

        return $this->app->getService('redirect')($this->app->getLastUri());
    }

    public function deleteRepostDBHandler(Request $request)
    {
        $repostId = $request->getParameters('repostId');

        $this->app->getService('repostActor')->delete($repostId);

        return $this->app->getService('redirect')($this->app->getLastUri());
    }
}