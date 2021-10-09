<?php


namespace Model\Finder;


use App\Src\App;
use Model\Gateway\RepostGateway;

class RepostFinder implements FinderInterface
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
        $query = $this->conn->prepare('SELECT DISTINCT repost.id AS id, repost.userId AS userId, post.userId AS authorId, post.id AS postId, post.text AS text, post.imgLink AS imgLink, repost.date AS date
                                                FROM repost
                                                LEFT OUTER JOIN post ON repost.postId = post.id');
        $query->execute();
        $repostsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_countable($repostsDatas) or count($repostsDatas) === 0)
            return null;

        $reposts = [];
        foreach ($repostsDatas as $repostData)
        {
            $repostData = array_merge($repostData ,$this->findLikesById($repostData['id']));
            $repost = new RepostGateway($this->app);
            $repost->hydrate($repostData);
            $reposts[] = $repost;
        }

        return $reposts;
    }

    public function findOneById($id)
    {
        $query = $this->conn->prepare('SELECT repost.id AS id, repost.userId AS userId, post.userId AS authorId, post.id AS postId, post.text AS text, post.imgLink AS imgLink, repost.date AS date
                                                FROM repost
                                                LEFT OUTER JOIN post ON repost.postId = post.id
                                                WHERE repost.id = :id'
    );
        $query->execute([
            ':id' => $id,
        ]);

        $repostDatas = $query->fetch(\PDO::FETCH_ASSOC);

        if (!is_countable($repostDatas) or count($repostDatas) === 0)
            return null;

        $repostDatas = array_merge($repostDatas, $this->findLikesById($repostDatas['id']));
        $repost = new RepostGateway($this->app);
        $repost->hydrate($repostDatas);

        return $repost;
    }

    /**
     * @param $userId
     * Returns only posts from followed users
     */
    public function findForUserDisplaying($userId)
    {
        $query = $this->conn->prepare('SELECT repost.id AS id, repost.userId AS userId, post.userId AS authorId, post.id AS postId, post.text AS text, post.imgLink AS imgLink, repost.date AS date FROM repost
                                                LEFT JOIN post ON post.id = repost.postId
                                                LEFT JOIN userfollow ON userfollow.followedUserId = repost.userId
                                                WHERE userfollow.userId = :userId
                                                ORDER BY date DESC');
        $query->execute([
            'userId' => $userId,
        ]);

        $repostsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($repostsDatas) === 0)
            return null;

        $reposts = [];
        foreach ($repostsDatas as $repostData)
        {
            $repostData = array_merge($repostData ,$this->findLikesById($repostData['id']));
            $repost = new RepostGateway($this->app);
            $repost->hydrate($repostData);
            $reposts[] = $repost;
        }

        return $reposts;
    }

    public function findLikeByIds($repostId, $userId)
    {
        $like = [];

        $query = $this->conn->prepare('SELECT likedpost.userId FROM likedpost
                                                WHERE likedpost.repostId = :repostId AND likedpost.userId = :userId
                                                ORDER BY likedpost.repostId ASC');
        $query->execute([
            ':repostId' => $repostId,
            ':userId' => $userId,
        ]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false)
            $like['like'] = false;
        else
            $like['like'] = true;



        unset($result);

        $query = $this->conn->prepare('SELECT dislikedpost.userId FROM dislikedpost
                                                WHERE dislikedpost.repostId = :repostId AND dislikedpost.userId = :userId
                                                ORDER BY dislikedpost.repostId ASC');
        $query->execute([
            ':repostId' => $repostId,
            ':userId' => $userId,
        ]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false)
            $like['dislike'] = false;
        else
            $like['dislike'] = true;

        return json_encode($like);
    }

    private function findLikesById($id)
    {
        // Obtenir liste d'utilisateurs ayant aimé
        $query = $this->conn->prepare('SELECT likedpost.userId AS userWhoLiked FROM likedpost
                                                WHERE likedpost.repostId = :id
                                                ORDER BY likedpost.repostId ASC');
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
                                                WHERE dislikedpost.repostId = :id
                                                ORDER BY dislikedpost.repostId');
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

    public function findByOriginalPostId($originalPostId)
    {
        $query = $this->conn->prepare('SELECT DISTINCT repost.id AS id, repost.userId AS userId, post.userId AS authorId, post.id AS postId, post.text AS text, post.imgLink AS imgLink, repost.date AS date
                                                FROM repost
                                                LEFT OUTER JOIN post ON repost.postId = post.id
                                                WHERE repost.postId = :originalPostId');
        $query->execute([
            ':originalPostId' => $originalPostId,
        ]);
        $repostsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_countable($repostsDatas) or count($repostsDatas) === 0)
            return null;

        $reposts = [];
        foreach ($repostsDatas as $repostData)
        {
            $repostData = array_merge($repostData ,$this->findLikesById($repostData['id']));
            $repost = new RepostGateway($this->app);
            $repost->hydrate($repostData);
            $reposts[] = $repost;
        }

        return $reposts;
    }

    public function findByUserId($userId)
    {
        $query = $this->conn->prepare('SELECT DISTINCT repost.id AS id, repost.userId AS userId, post.userId AS authorId, post.id AS postId, post.text AS text, post.imgLink AS imgLink, repost.date AS date
                                                FROM repost
                                                LEFT OUTER JOIN post ON repost.postId = post.id
                                                WHERE repost.userId = :userId');
        $query->execute([
            ':userId' => $userId,
        ]);
        $repostsDatas = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_countable($repostsDatas) or count($repostsDatas) === 0)
            return null;

        $reposts = [];
        foreach ($repostsDatas as $repostData)
        {
            $repostData = array_merge($repostData ,$this->findLikesById($repostData['id']));
            $repost = new RepostGateway($this->app);
            $repost->hydrate($repostData);
            $reposts[] = $repost;
        }

        return $reposts;
    }


}