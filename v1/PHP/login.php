<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/3/2017
 * Time: 1:35 AM
 */
require_once 'DatabaseConnection.php';

$db = DatabaseConnection::getInstance();

//Make sure everything is working as intended
if(array_key_exists('userName', $_POST) && array_key_exists('password', $_POST) && array_key_exists('color', $_POST) && array_key_exists('submit', $_POST)) {
    if(!empty($_POST['userName']) && !empty($_POST['password']) && !empty($_POST['color']) && !empty($_POST['submit'])) {
        //Sanitize all of the users input
        $userName = filter_var($_POST['userName'], FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        $color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
        $submitType = filter_var($_POST['submit'], FILTER_SANITIZE_STRING);

        //Make sure the lengths are correct
        if(strlen($userName) <= 30 && strlen($password) <= 30 && strlen($color) <= 7) {

            if($submitType === 'Login') {
                //Check to see if the login is valid
                try {
                    $stmtHandle = $db->prepare("SELECT userName, color FROM User WHERE userName = :userName AND password = :password");
                    $stmtHandle->bindValue('userName', $userName);
                    $stmtHandle->bindValue('password', $password);
                    $stmtHandle->execute();
                    $user = $stmtHandle->fetch();

                    if(!empty($user) && count($user) == 1) {
                        //Valid login!
                    }
                    else {

                    }
                }
                catch (PDOException $e) {
                    http_response_code(500);
                    echo $e;
                    die();
                }
            }
            elseif ($submitType === 'Create') {
                //Create the user in the database
                try {
                    $stmtHandle = $db->prepare("INSERT INTO User (userName, password, color) VALUES(:userName, :password, :color)");
                    $stmtHandle->bindValue('userName', $userName);
                    $stmtHandle->bindValue('password', $password);
                    $stmtHandle->bindValue('color', $color);
                    $stmtHandle->execute();
                    //Successfully created the user
                    http_response_code(201);
                }
                catch (PDOException $e) {
                    http_response_code(500);
                    echo $e;
                    die();
                }
            }
            else {
                http_response_code(400);
                die();
            }

        }
        else {
            http_response_code(400);
            die();
        }
    }
}