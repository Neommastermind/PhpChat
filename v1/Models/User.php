<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/4/2017
 * Time: 4:21 PM
 */


require_once dirname(__FILE__).'/../PHP/DatabaseConnection.php';
require_once  dirname(__FILE__).'/../PHP/ChatFunctions.php';

class User
{
    private $userName;
    private $password;
    private $color;
    private $ip;
    private $socket;

    /*
     * Populates the user with data and sanitizes the input.
     */
    public function __construct($userName, $password, $ip, $color = "") {
        if(!empty($userName) && !empty($password)) {
            $this->setUserName($userName);
            $this->setPassword($password);
            $this->setColor($color);
            $this->setIp($ip);
        }
        else {
            alert("You need to enter a user name and password!");
            displayLoginForm();
            exit(0);
        }
    }

    public function getSocket() {
        return $this->socket;
    }

    public function setSocket($socket) {
        $this->socket = $socket;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setIp($ip) {
        if(strlen($ip) <= 45 && strlen($ip) > 0) {
            $this->ip = filter_var($ip, FILTER_VALIDATE_IP);
        }
        else {
            alert("You have an invalid IP address");
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
     * returns a user from the database based on their user name.
     */
    public static function loadUser($userName) {
        try {
            $db = DatabaseConnection::getInstance();
            $stmtHandle = $db->prepare("SELECT userName, password, color, ip FROM User WHERE userName = :userName");
            $stmtHandle->bindValue('userName', $userName);
            $stmtHandle->execute();
            $stmtHandle->setFetchMode(PDO::FETCH_ASSOC);
            $data = $stmtHandle->fetch();

            $user = null;

            if(!empty($data['userName']) && !empty($data['password']) && !empty($data['color'] && !empty($data['ip']))) {
                $user = new User($data['userName'], $data['password'], $data['ip'], $data['color']);
            }

           return $user;
        }
        catch (PDOException $e) {
            http_response_code(500);
            throw $e;
        }
    }

    /*
     * returns a user from the database based on their ip address.
     */
    public static function loadUserByIp($ip) {
        try {
            $db = DatabaseConnection::getInstance();
            $stmtHandle = $db->prepare("SELECT userName, color, ip FROM User WHERE ip = :ip");
            $stmtHandle->bindValue('ip', $ip);
            $stmtHandle->execute();
            $stmtHandle->setFetchMode(PDO::FETCH_ASSOC);
            $data = $stmtHandle->fetch();

            $user = null;

            if(!empty($data['userName']) && !empty($data['color'] && !empty($data['ip']))) {
                $user = new User($data['userName'], "Irrelevant", $data['ip'], $data['color']);
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
            //$dbUser = User::loadUserByIp($user->getIp());
            //if(empty($dbUser)) {
                $stmtHandle = $db->prepare("INSERT INTO User (userName, password, color, ip) VALUES(:userName, :password, :color, :ip)");
                $stmtHandle->bindValue('userName', $user->getUserName());
                $stmtHandle->bindValue('password', password_hash($user->getPassword(), PASSWORD_DEFAULT));
                $stmtHandle->bindValue('color', $user->getColor());
                $stmtHandle->bindValue('ip', $user->getIp());
                return $stmtHandle->execute();
            //}
            //return false;
        }
        catch (PDOException $e) {
            http_response_code(500);
            throw $e;
        }
    }

    /*
     * Creates a user in the database and returns the results.
     */
    public static function updateIp(User $user): bool {
        try {
            $db = DatabaseConnection::getInstance();
            //$dbUser = User::loadUserByIp($user->getIp());
            //if(empty($dbUser)) {
                $stmtHandle = $db->prepare("UPDATE User SET ip = :ip WHERE userName = :userName");
                $stmtHandle->bindValue('ip', $user->getIp());
                $stmtHandle->bindValue('userName', $user->getUserName());
                return $stmtHandle->execute();
            //}
            //else {
                //alert("A user is already using this IP address");
                //return false;
           // }
        }
        catch (PDOException $e) {
            http_response_code(500);
            throw $e;
        }
    }
}