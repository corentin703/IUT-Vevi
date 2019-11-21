<?php

namespace Model\Gateway;

use App\Src\App;

class UserGateway implements GatewayInterface
{
    /**
     * @var \PDO
     */
    private $conn;


    private $id;

    private $username;
    private $password;
    private $firstName;
    private $lastName;
    private $email;
    private $followedUser;


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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFollowedUser()
    {
        if (is_array($this->followedUser))
            return $this->followedUser;
        else
            return null;
    }

    /**
     * @param mixed $followedUser
     */
    public function setFollowedUser($followedUser): void
    {
        $this->followedUser = $followedUser;
    }

    /**
     * Insert an user
     */
    public function insert() : void
    {
        $query = $this->conn->prepare('INSERT INTO user (firstName, lastName, username, password, email) 
                                                VALUES (:firstName, :lastName, :username, :password, :email)');
        $executed = $query->execute([':firstName' => $this->firstName,
            ':lastName' => $this->lastName,
            ':username' => $this->username,
            ':password' => $this->password,
            ':email' => $this->email]);

        if (!$executed)
            throw new \Error('Insert Failed', 1);
        else
            $this->id = $this->conn->lastInsertId();
    }

    public function update(bool $withPassword = false) : void
    {
        if (!$this->id)
            throw new \Error('Instance does not exist in base');

        if ($withPassword)
        {
            $query = $this->conn->prepare('UPDATE user 
                                                SET firstName = :firstName,
                                                lastName = :lastName,
                                                username = :username,
                                                password = :password,
                                                email = :email
                                                WHERE id = :id');
            $executed = $query->execute([
                ':id' => $this->id,
                ':firstName' => $this->firstName,
                ':lastName' => $this->lastName,
                ':username' => $this->username,
                ':password' => $this->password,
                ':email' => $this->email
            ]);
        }
        else
        {
            $query = $this->conn->prepare('UPDATE user 
                                                SET firstName = :firstName,
                                                lastName = :lastName,
                                                username = :username,
                                                email = :email
                                                WHERE id = :id');
            $executed = $query->execute([
                ':id' => $this->id,
                ':firstName' => $this->firstName,
                ':lastName' => $this->lastName,
                ':username' => $this->username,
                ':email' => $this->email
            ]);
        }


        if (!$executed)
            throw new \Error('Update failed');
    }

    public function delete() : void
    {
        $query = $this->conn->prepare('DELETE FROM user
                                                WHERE id = :id AND username = :username AND email = :email');
        $executed = $query->execute([
            ':id' => $this->id,
            ':username' => $this->username,
            ':email' => $this->email,
        ]);

        if (!$executed)
            throw new \Error('Delete failed');
    }

    public function follow($userToFollowId)
    {
        $query = $this->conn->prepare('INSERT INTO userfollow (userId, followedUserId)
                                                VALUES (:userId, :userToFollowId)');
        $executed = $query->execute([
            ':userId' => $this->id,
            ':userToFollowId' => $userToFollowId,
        ]);

        if (!$executed)
            throw new \Error('Follow failed');
        else
            $this->followedUser[] = $userToFollowId;
    }

    public function unfollow($userToUnfollowId)
    {
        $query = $this->conn->prepare('DELETE FROM userfollow
                                                WHERE userId = :userId AND followedUserId = :userToFollowId');
        $executed = $query->execute([
            ':userId' => $this->id,
            ':userToFollowId' => $userToUnfollowId,
        ]);

        if (!$executed)
            throw new \Error('Follow failed');
        else
            unset($this->followedUser[array_search($userToUnfollowId, $this->followedUser)]);
    }

    public function hydrate(array $elements, bool $withPassword = false)
    {
        $this->id = $elements['id'];
        $this->username = $elements['username'];

        if ($withPassword)
            $this->password = $elements['password'];

        $this->firstName = $elements['firstName'];
        $this->lastName = $elements['lastName'];
        $this->email = $elements['email'];
        $this->followedUser = $elements['followedUser'];
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'followedUser' => $this->followedUser,
        ];
    }

}