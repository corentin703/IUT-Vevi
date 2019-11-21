<?php

namespace Controllers;

use App\Src\App;
use App\Src\Request\Request;

class HomeController extends ControllerBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }


    /**
     * Affiche la page d'accueil de l'utilisateur
     *
     * @param Request $request
     * @return mixed
     */
    public function homeHandler(Request $request)
    {
        if ($this->app->getService('userFinder')->isConnected()) {
            $currentUserId = $this->app->getSessionParameters('user')['id'];
            $postsObjects = $this->app->getService('postFinder')->findForUserDisplaying($currentUserId);
            $repostsObjects = $this->app->getService('repostFinder')->findForUserDisplaying($currentUserId);

            $allPosts = $this->postArrayPrepare($postsObjects, $repostsObjects);

            // Mise en tableau des objets de type UserGateway
            $allUsersObjects = $this->app->getService('userFinder')->findAll();
            $allUsers = [];
            foreach ($allUsersObjects as $userObject) {
                $user = $userObject->toArray();
                $user['isFollowed'] = $this->app->getService('userFinder')->findFollowById($user['id'], $this->app->getSessionParameters('user')['id']);
                $allUsers[] = $user;
            }

            return $this->app->getService('render')('Wall', ['currentUser' => $this->app->getSessionParameters('user'),
                'posts' => $allPosts,
                'allUsers' => $allUsers,
            ]);
        }
        else
            return $this->app->getService('redirect')('/');
    }

    /**
     * Affiche la page correspondant à un profil d'utilisateur, avec ses tweets et retweets uniquement
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function userHandler(Request $request, string $name)
    {
        if ($this->app->getService('userFinder')->isConnected()) {
            $user = $this->app->getService('userFinder')->findOneByName($name);

            if ($user === null)
                return $this->app->getService('render')('Wall', ['currentUser' => $this->app->getSessionParameters('user'),
                                                                            'userNotFound' => true,
                                                                            'search' => true,]);

            $postsObjects = $this->app->getService('postFinder')->findByUserId($user->getId());
            $repostsObjects = $this->app->getService('repostFinder')->findByUserId($user->getId());

            $allPosts = $this->postArrayPrepare($postsObjects, $repostsObjects);

            return $this->app->getService('render')('Wall', ['currentUser' => $this->app->getSessionParameters('user'),
                'posts' => $allPosts,
                'user' => $user->toArray(),
            ]);
        }
        else
            return $this->app->getService('redirect')('/');
    }

    /**
     * Interraction avec la barre de recherche
     *
     * @param Request $request
     * @return mixed
     */
    public function userSearchHandler(Request $request)
    {
        $searchString = $request->getParameters('searchString');

        if ($this->app->getService('userFinder')->isConnected()) {
            $usersFoundObjects = $this->app->getService('userFinder')->search($searchString);

            // Si aucune personne n'a été trouvé ou si la seule trouvée est l'utilisateur courrant
            if ($usersFoundObjects === null || (count($usersFoundObjects) === 1 && $usersFoundObjects[0]->getId() === $this->app->getSessionParameters('user')['id']))
                return $this->app->getService('render')('Wall', ['currentUser' => $this->app->getSessionParameters('user'),
                                                                            'userNotFound' => true,
                                                                            'search' => true,]);

            $allFound = [];
            foreach ($usersFoundObjects as $userFoundObject) {
                $user = $userFoundObject->toArray();
                $user['isFollowed'] = $this->app->getService('userFinder')->findFollowById($user['id'], $this->app->getSessionParameters('user')['id']);
                $allFound[] = $user;
            }

            return $this->app->getService('render')('Wall', ['currentUser' => $this->app->getSessionParameters('user'),
                                                                        'allUsers' => $allFound,
                                                                        'search' => true,
            ]);
        }
        else
            return $this->app->getService('redirect')('/');
    }


    /**
     * Vise à fusionner les posts et les reposts
     * Retourne un tableau qui est prêt à envoyer à la vue (avec les tweets triés chronologiquement)
     *
     * @param $postsObjects
     * @param $repostsObjects
     * @return array
     */
    private function postArrayPrepare($postsObjects, $repostsObjects)
    {
        $allPosts = [];

        if ($postsObjects !== null)
        {
            foreach ($postsObjects as $postObject)
            {
                $post = $postObject->toArray();
                $post['user'] = $this->app->getService('userFinder')->findOneById($post['userId'])->toArray(); // Stockage des informations de l'auteur du post

                // Stocke si l'utilisateur a réagit à ce post et comment (j'aime ou je n'aime pas)
                $post['userLikeDatas'] = $this->app->getService('postFinder')->findLikeByIds($postObject->getId(), $this->app->getSessionParameters('user')['id']);
                $allPosts[] = $post;
            }
        }

        if ($repostsObjects !== null)
        {
            foreach ($repostsObjects as $repostObject)
            {
                $repost = $repostObject->toArray();
                $repost['user'] = $this->app->getService('userFinder')->findOneById($repost['userId'])->toArray(); // Stockage des informations de l'auteur du post

                // Stockage des informations de l'auteur original pour les re-posts
                $repost['author'] = $this->app->getService('userFinder')->findOneById($repost['authorId'])->toArray(); // Stockage des informations de l'auteur du post

                // Stocke si l'utilisateur a réagit à ce post et comment (j'aime ou je n'aime pas)
                $repost['userLikeDatas'] = $this->app->getService('repostFinder')->findLikeByIds($repostObject->getId(), $this->app->getSessionParameters('user')['id']);
                $allPosts[] = $repost;
            }
        }

        if ($allPosts !== null)
        {
            $allPosts = $this->sortByDate($allPosts); // Mise à l'ordre chronologique
            $allPosts = array_reverse($allPosts); // On veut afficher les posts du plus récent au plus ancien
        }

        return $allPosts;
    }

    /**
     * Trie le tableau associatif des posts selon sa colonne date
     *
     * @param array $allPosts
     * @return array
     */
    private function sortByDate(array $allPosts)
    {
        usort($allPosts, function($a, $b)
        {
        $a = $a['date'];
        $b = $b['date'];

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
        });

        return $allPosts;
    }

}