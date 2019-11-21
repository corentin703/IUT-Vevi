<?php

namespace Model\Actor;

use App\Src\App;
use Model\Gateway\UserGateway;

class UserActor
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function create(array $datas)
    {
        if ($datas['password'] === $datas['passwordConfirm']) // Test des mots de passes
        {
            $isExist = $this->app->getService('userFinder')->findOneByName($datas['username']); // Est-ce que l'utilisateur existe déjà ?
            if ($isExist === null)
            {
                $user = new UserGateway($this->app);
                $user->setUsername($datas['username']);
                $user->setPassword(md5($datas['password'])); // Stocke seulement le md5 du mot de passe
                $user->setFirstName($datas['firstName']);
                $user->setLastName($datas['lastName']);
                $user->setEmail($datas['email']);

                $user->insert();

                return true;
            }
            else
                throw new \Error("User already exist", 3);
        }
        else
            throw new \Error("Passwords don't match", 2);
    }

    public function update(array $datas)
    {
        $user = $this->app->getService('userFinder')->findOneById($this->app->getSessionParameters('user')['id']);

        if ($datas['firstName'] !== "")  // Prénom
            $user->setFirstName($datas['firstName']);

        if ($datas['lastName'] !== "") // Nom de famille
            $user->setLastName($datas['lastName']);

        if ($datas['email'] !== "") // Email
            $user->setEmail($datas['email']);

        if ($datas['username'] !== "") // Nom d'utilisateur
            $user->setUsername($datas['username']);


        if ($datas['password']  === "") // Mot de passe
        {
            $user->update();
            $this->app->setSessionParameters('user', $this->app->getService('userFinder')->findOneById($user->getId())->toArray());
        }
        else
        {
            if ($datas['password'] === $datas['passwordConfirm'])
            {
                $user->setPassword(md5($datas['password']));
                $user->update(true);
                $this->app->setSessionParameters('user', $this->app->getService('userFinder')->findOneById($user->getId())->toArray());
            }
            else
                throw new \Error("Password mismatch", 1);
        }
    }

    public function delete()
    {
        $user = $this->app->getService('userFinder')->findOneById($this->app->getSessionParameters('user')['id']);

        // Récupère les posts de l'utilisateur pour suppression
        $userPosts = $this->app->getService('postFinder')->findByUserId($user->getId());
        $userPosts[] = $this->app->getService('repostFinder')->findByUserId($user->getId());

        foreach ($userPosts as $post)
        {
            if (!is_null($post))
                $post->delete();
        }

        $user->delete();
    }

    public function follow($userToFollowId)
    {
        $currentUserId = $this->app->getSessionParameters('user')['id'];

        if ($currentUserId === $userToFollowId) // L'utiliateur ne peut pas se suivre lui même
            throw new \Error("User can't follow himself", 1);

        $user = $this->app->getService('userFinder')->findOneById($currentUserId);

        $followedUser = $user->getFollowedUser();
        if (!is_null($followedUser))
            $isAlreadyFollowed = array_search($userToFollowId, $followedUser);
        else
            $isAlreadyFollowed = false;


        if ($isAlreadyFollowed === false)
            $user->follow($userToFollowId);
        else
            $user->unfollow($userToFollowId);


        $this->app->setSessionParameters('user', $user->toArray());
    }
}