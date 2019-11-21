<?php

namespace Model\Actor;

use App\Src\App;
use Model\Gateway\RepostGateway;

class RepostActor
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function create($postId)
    {
        $originalPost = $this->app->getService('postFinder')->findOneById($postId);

        $repost = new RepostGateway($this->app);
        $repost->setUserId($this->app->getSessionParameters('user')['id']);
        $repost->setPostId($originalPost->getId());
        $repost->setAuthorId($originalPost->getUserId());
        $repost->setText($originalPost->getText());
        $repost->setImgLink($originalPost->getImgLink());

        $repost->insert();

        return $this->app->getService('redirect')('/home');
    }

    public function delete($id)
    {
        $repost = $this->app->getService('repostFinder')->findOneById($id);

        $checkRights = $this->VerrifyUserRights($repost);

        if ($checkRights)
        {
            $repost->delete();
        }
        else
            throw new \Error("You're not allowed to delete this post !", 1);
    }

    public function like($id)
    {
        $repost = $this->app->getService('repostFinder')->findOneById($id);
        $userId = $this->app->getSessionParameters('user')['id'];

        $usersWhoDisliked = $repost->getUsersWhoDisliked();
        if (!is_null($usersWhoDisliked)) // On teste sur le tableau n'est pas nul avant de le passer dans array_search
            $verrif = array_search($userId, $usersWhoDisliked);
        else
            $verrif = false;

        if ($verrif !== false)
            $this->dislike($id);


        $usersWhoLiked = $repost->getUsersWhoLiked();
        if (!is_null($usersWhoLiked))
            $verrif = array_search($userId, $usersWhoLiked);
        else
            $verrif = false;

        if ($verrif === false)
            $repost->like($userId);
        else
            $repost->unlike($userId);

    }


    public function dislike($id)
    {
        $repost = $this->app->getService('repostFinder')->findOneById($id);
        $userId = $this->app->getSessionParameters('user')['id'];

        $usersWhoLiked = $repost->getUsersWhoLiked();
        if (!is_null($usersWhoLiked))
            $verrif = array_search($userId, $usersWhoLiked);
        else
            $verrif = false;

        if ($verrif !== false)
            $this->like($id);


        $usersWhoDisliked = $repost->getUsersWhoDisliked();
        if (!is_null($usersWhoDisliked))
            $verrif = array_search($userId, $usersWhoDisliked);
        else
            $verrif = false;

        if ($verrif === false)
            $repost->dislike($userId);
        else
            $repost->undislike($userId);

    }

    /**
     * Vérrifie si l'utilisateur a le droit de supprimer le repost (si c'est le sien)
     *
     * @param RepostGateway $repost
     * @return bool
     */
    private function VerrifyUserRights(RepostGateway $repost) : bool
    {
        $user = $this->app->getService('userFinder')->findOneById($repost->getUserId())->toArray();
        $currentUser = $this->app->getSessionParameters('user');
        if ($user['id'] === $currentUser['id'] ||
            $user['username'] === $currentUser['username'] ||
            $user['email'] === $currentUser['email'])
            return true;
        else
            return false;
    }
}