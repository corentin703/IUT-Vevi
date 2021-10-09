<?php


namespace Model\Finder;

use App\Src\App;
use Model\Gateway\PostGateway;
use function Sodium\add;

class PostFinder implements FinderInterface
{
    /**
     * @var \PDO
     */
    private $conn;

    /**
     * @var App
     */
    private $app;


    public function __construct(App $app)
    {
        $this->app = $app;
        $this->conn = $this->app->getService('database')->getConnection();
    }

    public function findAll()
    {
        $query = $this->conn->prepare('SELECT DISTINCT id, userId, text, imgLink, date FROM post ORDER BY date DESC');
        $query->execute();
        $postsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($postsDatas) === 0)
            return null;

        $posts = [];
        foreach ($postsDatas as $postData)
        {
            $postData = array_merge($postData ,$this->findLikesById($postData['id']));
            $post = new PostGateway($this->app);
            $post->hydrate($postData);
            $posts[] = $post;
        }


        return $posts;
    }

    public function findOneById($id)
    {
        $query = $this->conn->prepare('SELECT id, userId, text, imgLink, date FROM post WHERE id = :id');
        $query->execute([
            ':id' => $id
        ]);
        $element = $query->fetch(\PDO::FETCH_ASSOC);

        if (!is_countable($element) or count($element) === 0)
            return null;

        $element = array_merge($element ,$this->findLikesById($element['id']));
        $post = new PostGateway($this->app);
        $post->hydrate($element);

        return $post;
    }

    /**
     * @param $userId
     * Returns only posts from followed users
     */
    public function findForUserDisplaying($userId)
    {
        $query = $this->conn->prepare('SELECT post.id, post.userId, post.text, post.imgLink, post.date FROM post 
                                                LEFT JOIN userfollow ON userfollow.followedUserId = post.userId
                                                WHERE userfollow.userId = :userId
                                                ORDER BY date DESC');
        $query->execute([
            'userId' => $userId,
        ]);

        $postsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($postsDatas) === 0)
            return null;

        $posts = [];
        foreach ($postsDatas as $postData)
        {
            $postData = array_merge($postData ,$this->findLikesById($postData['id']));
            $post = new PostGateway($this->app);
            $post->hydrate($postData);
            $posts[] = $post;
        }

        return $posts;
    }



    private function findLikesById($id)
    {
        // Obtenir liste d'utilisateurs ayant aimé
        $query = $this->conn->prepare('SELECT likedpost.userId AS userWhoLiked FROM likedpost
                                                WHERE likedpost.postId = :id
                                                ORDER BY likedpost.postId ASC');
        $query->execute([
            ':id' => $id,
        ]);
        $usersWhoLiked = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($usersWhoLiked) === 0)
        {
            $likes['usersWhoLiked'] = null;
        }
        else
        {
            foreach ($usersWhoLiked as $userWhoLiked) {
                $likes['usersWhoLiked'][] = $userWhoLiked['userWhoLiked'];
            }
        }

        // Obtenir liste d'utilisateurs n'ayant pas aimé
        $query = $this->conn->prepare('SELECT dislikedpost.userId AS userWhoDisliked FROM dislikedpost
                                                WHERE dislikedpost.postId = :id
                                                ORDER BY dislikedpost.postId');
        $query->execute([
            ':id' => $id,
        ]);

        $usersWhoDisliked = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($usersWhoDisliked) === 0)
        {
            $likes['usersWhoDisliked'] = null;
        }
        else
        {
            foreach ($usersWhoDisliked as $userWhoDisliked) {
                $likes['usersWhoDisliked'][] = $userWhoDisliked['userWhoDisliked'];
            }
        }

        return $likes;
    }

    public function findLikeByIds($postId, $userId)
    {
        $like = [];

        $query = $this->conn->prepare('SELECT likedpost.userId FROM likedpost
                                                WHERE likedpost.postId = :postId AND likedpost.userId = :userId
                                                ORDER BY likedpost.postId ASC');
        $query->execute([
            ':postId' => $postId,
            ':userId' => $userId,
        ]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false)
            $like['like'] = false;
        else
            $like['like'] = true;



        unset($result);

        $query = $this->conn->prepare('SELECT dislikedpost.userId FROM dislikedpost
                                                WHERE dislikedpost.postId = :postId AND dislikedpost.userId = :userId
                                                ORDER BY dislikedpost.postId ASC');
        $query->execute([
            ':postId' => $postId,
            ':userId' => $userId,
        ]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false)
            $like['dislike'] = false;
        else
            $like['dislike'] = true;

        return json_encode($like);
    }

    public function findByUserId($userId)
    {
        $query = $this->conn->prepare('SELECT DISTINCT id, userId, text, imgLink, date 
                                                FROM post
                                                WHERE userId = :userId
                                                ORDER BY date DESC');
        $query->execute([
            'userId' => $userId,
        ]);
        $postsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($postsDatas) === 0)
            return null;

        $posts = [];
        foreach ($postsDatas as $postData)
        {
            $postData = array_merge($postData ,$this->findLikesById($postData['id']));
            $post = new PostGateway($this->app);
            $post->hydrate($postData);
            $posts[] = $post;
        }


        return $posts;
    }

}