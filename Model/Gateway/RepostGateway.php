<?php

namespace Model\Gateway;

use App\Src\App;

class RepostGateway
{
    /**
     * @var \PDO
     */
    private $conn;

    private $id;

    private $userId;
    private $authorId;
    private $postId;

    private $text;
    private $imgLink;

    private $usersWhoLiked;
    private $usersWhoDisliked;

    private $date;


    public function __construct(App $app)
    {
        $this->conn = $app->getService('database')->getConnection();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId): void
    {
        $this->postId = $postId;
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
    * @param mixed $authorId
    */
    public function setAuthorId($authorId): void
    {
        $this->authorId = $authorId;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $text : Timestamp mySQL
     */
    public function setDate($date): void
    {
        $this->date = strtotime($date);
    }

    /**
     * @return mixed
     */
    public function getImgLink()
    {
        return $this->imgLink;
    }

    /**
     * @param mixed $imgLink
     */
    public function setImgLink($imgLink = ""): void
    {
        $this->imgLink = $imgLink;
    }

    /**
     * @return mixed
     */
    public function getUsersWhoLiked()
    {
        return $this->usersWhoLiked;
    }

    /**
     * @param mixed $usersWhoLiked
     */
    public function setUsersWhoLiked($usersWhoLiked): void
    {
        $this->usersWhoLiked = $usersWhoLiked;
    }

    /**
     * @return int
     */
    public function getLikes() : int
    {
        if (is_countable($this->usersWhoLiked))
            return count($this->usersWhoLiked);
        else
            return 0;
    }

    /**
     * @return mixed
     */
    public function getUsersWhoDisliked()
    {
        return $this->usersWhoDisliked;
    }

    /**
     * @param mixed $usersWhoDisliked
     */
    public function setUsersWhoDisliked($usersWhoDisliked): void
    {
        $this->usersWhoDisliked = $usersWhoDisliked;
    }

    /**
     * @return int
     */
    public function getDislikes() : int
    {
        if (is_countable($this->usersWhoDisliked))
            return count($this->usersWhoDisliked);
        else
            return 0;
    }



    public function insert() : void
    {
        var_dump($this);
        $this->date = time();
        $query = $this->conn->prepare('INSERT INTO repost (userId, postId, date) 
                                                VALUES (:userId, :postId, :date)');
        $executed = $query->execute([
            ':userId' => $this->userId,
            ':postId' => $this->postId,
            ':date' => date("Y-m-d H:i:s", $this->date),
        ]);

        if (!$executed)
            throw new \Error('Insert Failed');
        else
            $this->id = $this->conn->lastInsertId();
    }

    public function update() : void
    {
        $this->date = time();
        if (!$this->id)
            throw new \Error('Instance does not exist in base');

        $query = $this->conn->prepare('UPDATE repost
                                                SET userId = :userId,
                                                postId = :postId,
                                                date = :date
                                                WHERE id = :id');
        $executed = $query->execute([
            ':id' => $this->id,
            ':userId' => $this->userId,
            ':postId' => $this->postId,
            ':date' => date("Y-m-d H:i:s", $this->date),
        ]);

        if (!$executed)
            throw new \Error('Update failed');
    }

    public function delete() : void
    {
        $query = $this->conn->prepare('DELETE FROM repost
                                                WHERE id = :id AND userId = :userId');
        $executed = $query->execute([
            ':id' => $this->id,
            ':userId' => $this->userId,
        ]);

        if (!$executed)
            throw new \Error('Delete failed');
    }

    public function like($userId) : void
    {
        $query = $this->conn->prepare('INSERT INTO likedpost (userId, repostId) 
                                                VALUES (:userId, :repostId)');
        $executed = $query->execute([
            ':userId' => $userId,
            ':repostId' => $this->id,
        ]);

        if (!$executed)
            throw new \Error('Like failed');
        else
            $this->usersWhoLiked[] = $userId;
    }

    public function unlike($userId) : void
    {
        $query = $this->conn->prepare('DELETE FROM likedpost 
                                                WHERE userId = :userId AND repostId = :repostId');
        $executed = $query->execute([
            ':userId' => $userId,
            ':repostId' => $this->id,
        ]);

        if (!$executed)
            throw new \Error('Unlike failed');
        else
            unset($this->usersWhoLiked[array_search($userId, $this->usersWhoLiked)]);
    }

    public function dislike($userId) : void
    {
        $query = $this->conn->prepare('INSERT INTO dislikedpost (userId, repostId) 
                                                VALUES (:userId, :repostId)');
        $executed = $query->execute([
            ':userId' => $userId,
            ':repostId' => $this->id,
        ]);

        if (!$executed)
            throw new \Error('Dislike failed');
        else
            $this->usersWhoDisliked[] = $userId;
    }

    public function undislike($userId) : void
    {
        $query = $this->conn->prepare('DELETE FROM dislikedpost 
                                                WHERE userId = :userId AND repostId = :repostId');
        $executed = $query->execute([
            ':userId' => $userId,
            ':repostId' => $this->id,
        ]);

        if (!$executed)
            throw new \Error('Undislike failed');
        else
            unset($this->usersWhoDisliked[array_search($userId, $this->usersWhoDisliked)]);
    }


    public function hydrate(array $elements) : void
    {
        $this->id = $elements['id'];

        $this->userId = $elements['userId'];
        $this->authorId = $elements['authorId'];
        $this->postId = $elements['postId'];
        $this->text = $elements['text'];
        $this->imgLink = $elements['imgLink'];

        $this->usersWhoLiked = $elements['usersWhoLiked'];
        $this->usersWhoDisliked = $elements['usersWhoDisliked'];

        $this->setDate($elements['date']);
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id,

            'userId' => $this->userId,
            'authorId' => $this->authorId,
            'postId' => $this->postId,

            'text' => $this->text,
            'imgLink' => $this->imgLink,

            'likes' => $this->getLikes(),
            'dislikes' => $this->getDislikes(),

            'usersWhoLiked' => $this->usersWhoLiked,
            'usersWhoDisliked' => $this->usersWhoDisliked,

            'date' => $this->date,
        ];
    }
}
