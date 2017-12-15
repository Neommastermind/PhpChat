<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/4/2017
 * Time: 4:21 PM
 */

require_once 'PHP/DatabaseConnection.php';
require_once 'PHP/ChatFunctions.php';

class User
{
    private $userName;
    private $password;
    private $color;
    private $socket;

    /*
     * Populates the user with data and sanitizes the input.
     */
    public function __construct($userName, $password, $color = "") {
        if(!empty($userName) && !empty($password)) {
            $this->setUserName($userName);
            $this->setPassword($password);
            $this->setColor($color);
        }
        else {
            alert("You need to enter a user name and password!");
            displayLoginForm();
            exit(0);
        }
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        if(strlen($userName) <= 30 && strlen($userName) > 0) {
            $this->userName = filter_var($userName, FILTER_SANITIZE_STRING);
        }
        else {
            alert("Invalid User Name length.");
            displayLoginForm();
            exit(0);
        }
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
    public function setPassword($password)
    {
        if(strlen($password) <= 255 && strlen($password) > 0) {
            $this->password = filter_var($password, FILTER_SANITIZE_STRING);
        }
        else {
            alert("Invalid Password length.");
            displayLoginForm();
            exit(0);
        }
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        if(strlen($color) <= 7) {
            $this->color = filter_var($color, FILTER_SANITIZE_STRING);
        }
        else {
            alert("Invalid Color length.");
            displayLoginForm();
            exit(0);
        }
    }

    /*
     * returns a user from the database.
     */
    public static function loadUser($userName) {
        try {
            $db = DatabaseConnection::getInstance();
            $stmtHandle = $db->prepare("SELECT userName, password, color FROM User WHERE userName = :userName");
            $stmtHandle->bindValue('userName', $userName);
            $stmtHandle->execute();
            $stmtHandle->setFetchMode(PDO::FETCH_ASSOC);
            $data = $stmtHandle->fetch();

            $user = null;

            if(!empty($data['userName']) && !empty($data['password']) && !empty($data['color'])) {
                $user = new User($data['userName'], $data['password'], $data['color']);
            }

           return $user;
        }
        catch (PDOException $e) {
            http_response_code(500);
            throw $e;
        }
    }

    /*
     * Creates a user in the database and returns the results.
     */
    public static function createUser(User $user): bool {
        try {
            $db = DatabaseConnection::getInstance();
            $stmtHandle = $db->prepare("INSERT INTO User (userName, password, color) VALUES(:userName, :password, :color)");
            $stmtHandle->bindValue('userName', $user->getUserName());
            $stmtHandle->bindValue('password', password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $stmtHandle->bindValue('color', $user->getColor());
            return $stmtHandle->execute();
        }
        catch (PDOException $e) {
            http_response_code(500);
            throw $e;
        }
    }
}