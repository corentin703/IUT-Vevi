<?php


namespace Model\Actor;


use App\Src\App;
use Model\Gateway\PostGateway;
use Model\Gateway\RepostGateway;
use Model\Gateway\UserGateway;

class PostActor
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function create(array $datas)
    {
        $post = new PostGateway($this->app);
        $post->setUserId($datas['userId']);
        $post->setText($datas['content']);
        $post->setImgLink($datas['imageLink']);

        $post->insert();

    }

    public function update(array $datas)
    {
        $post = $this->app->getService('postFinder')->findOneById($datas['id']);

        $checkRights = $this->VerrifyUserRights($post);

        if ($checkRights)
        {
            $post->setText($datas['content']);
            $post->update();
        }
        else
            throw new \Error("You're not allowed to delete this post !", 1);
    }

    public function delete($id)
    {
        $post = $this->app->getService('postFinder')->findOneById($id);

        $checkRights = $this->VerrifyUserRights($post);

        if ($checkRights)
        {
            $reposts = $this->app->getService('repostFinder')->findByOriginalPostId($id);

            if ($reposts !== null)
                foreach ($reposts as $repost)
                    $repost->delete();


            $post->delete();

        }
        else
            throw new \Error("You're not allowed to delete this post !", 1);
    }

    public function like($postId)
    {
        $post = $this->app->getService('postFinder')->findOneById($postId);
        $userId = $this->app->getSessionParameters('user')['id'];


        $usersWhoDisliked = $post->getUsersWhoDisliked();
        if (!is_null($usersWhoDisliked)) // On teste sur le tableau n'est pas nul avant de le passer dans array_search
            $verrif = array_search($userId, $usersWhoDisliked); // On vérifie que l'utilisateur n'a pas déjà aimé, cas dans lequel on supprime son j'aime
        else
            $verrif = false;

        if ($verrif !== false)
            $this->dislike($postId);


        $usersWhoLiked = $post->getUsersWhoLiked();
        if (!is_null($usersWhoLiked))
            $verrif = array_search($userId, $usersWhoLiked);
        else
            $verrif = false;

        if ($verrif === false)
            $post->like($userId);
        else
            $post->unlike($userId);

    }

    public function dislike($postId)
    {
        $post = $this->app->getService('postFinder')->findOneById($postId);
        $userId = $this->app->getSessionParameters('user')['id'];

        $usersWhoLiked = $post->getUsersWhoLiked();
        if (!is_null($usersWhoLiked))
            $verrif = array_search($userId, $usersWhoLiked);
        else
            $verrif = false;

        if ($verrif !== false)
            $this->like($postId);


        $usersWhoDisliked = $post->getUsersWhoDisliked();
        if (!is_null($usersWhoDisliked))
            $verrif = array_search($userId, $usersWhoDisliked);
        else
            $verrif = false;

        if ($verrif === false)
            $post->dislike($userId);
        else
            $post->undislike($userId);

    }

    /**
     * Vérrifie si l'utilisateur a le droit de modifier ou supprimer le post (si c'est le sien)
     *
     * @param PostGateway $post
     * @return bool
     */
    private function VerrifyUserRights(PostGateway $post) : bool
    {
        $user = $this->app->getService('userFinder')->findOneById($post->getUserId())->toArray();
        $currentUser = $this->app->getSessionParameters('user');
        if ($user['id'] === $currentUser['id'] ||
            $user['username'] === $currentUser['username'] ||
            $user['email'] === $currentUser['email'])
            return true;
        else
            return false;
    }
}