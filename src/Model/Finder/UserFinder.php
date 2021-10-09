<?php


namespace Model\Finder;

use App\Src\App;
use Couchbase\BooleanFieldSearchQuery;
use Model\Gateway\UserGateway;

class UserFinder implements FinderInterface
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
        $query = $this->conn->prepare('SELECT DISTINCT id, username, firstName, lastName, email FROM user ORDER BY id');
        $query->execute();
        $elements = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($elements) === 0)
            return null;

        $users = [];
        foreach ($elements as $element)
        {
            // Récupération des IDs des utilisateurs suivis
            $element = array_merge($element, $this->findAllFollowedById($element['id']));
            $user = new UserGateway($this->app);
            $user->hydrate($element);

            $users[] = $user;
        }

        return $users;
    }

    public function findOneById($id)
    {
        $query = $this->conn->prepare('SELECT id, username, firstName, lastName, email FROM user WHERE id = :id');
        $query->execute([':id' => $id]);
        $element = $query->fetch(\PDO::FETCH_ASSOC);

        if (!is_countable($element) or count($element) === 0)
            return null;

        // Récupération des IDs des utilisateurs suivis
        $element = array_merge($element, $this->findAllFollowedById($element['id']));

        $user = new UserGateway($this->app);
        $user->hydrate($element);

        return $user;
    }

    public function findOneByName($name)
    {
        $query = $this->conn->prepare('SELECT id, username, password, firstName, lastName, email FROM user WHERE username = :name OR user.email = :email');
        $query->execute([
            ':name' => $name,
            ':email' => $name,
            ]);

        $element = $query->fetch(\PDO::FETCH_ASSOC);

        if (!is_countable($element) or count($element) === 0)
            return null;

        // Récupération des IDs des utilisateurs suivis
        $element = array_merge($element, $this->findAllFollowedById($element['id']));

        $user = new UserGateway($this->app);
        $user->hydrate($element);

        return $user;
    }

    public function search($searchString)
    {
        $query = $this->conn->prepare('SELECT DISTINCT id, username, firstName, lastName, email 
                                                FROM user 
                                                WHERE username LIKE :searchString
                                                ORDER BY username');
        $query->execute([
            ':searchString' => '%' . $searchString . '%',
        ]);
        $elements = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($elements) === 0)
            return null;

        $users = [];
        foreach ($elements as $element)
        {
            // Récupération des IDs des utilisateurs suivis
            $element = array_merge($element, $this->findAllFollowedById($element['id']));
            $user = new UserGateway($this->app);
            $user->hydrate($element);

            $users[] = $user;
        }

        return $users;
    }

    public function verrifyLogIn(String $loginStr, String $password) : Bool
    {
        $query = $this->conn->prepare('SELECT user.username, user.email, user.password 
                                                FROM user 
                                                WHERE (user.username = :username OR user.email = :email) AND user.password = :password');
        $query->execute([
            ':username' => $loginStr,
            ':email' => $loginStr,
            ':password' => $password,
        ]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result)
        {
            sleep(3); // Ralentir les attaques par force brute
            return false;
        }
        else
            return true;

    }

    public function findFollowById($userToCheckId, $userId)
    {
        $query = $this->conn->prepare('SELECT userfollow.followedUserId FROM userfollow
                                                WHERE userfollow.userId = :userId AND userfollow.followedUserId = :userToCheckId
                                                ORDER BY userfollow.followedUserId ASC');
        $query->execute([
            ':userId' => $userId,
            ':userToCheckId' => $userToCheckId,
        ]);

        $userFollowed = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (count($userFollowed) === 0)
            return json_encode(false);
        else
            return json_encode(true);
    }

    public function isConnected() : bool
    {
        if ($this->app->getSessionParameters('user') != null)
            return true;
        else
            return false;
    }

    private function findAllFollowedById($userId)
    {
        $query = $this->conn->prepare('SELECT userfollow.followedUserId AS followedUser FROM userfollow
                                                WHERE userfollow.userId = :userId');
        $query->execute([
            'userId' => $userId,
        ]);

        $resultArray = $query->fetchAll(\PDO::FETCH_ASSOC);

        if ($resultArray)
        {
            $userFollowed = ['followedUser'];

            foreach ($resultArray AS $result)
                $userFollowed['followedUser'][] = $result['followedUser'];

            return $userFollowed;
        }
        else
            return ['followedUser' => null];
    }

}