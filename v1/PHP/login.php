<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/3/2017
 * Time: 1:35 AM
 */
require_once 'DatabaseConnection.php';
require_once '../chat.php';

$db = DatabaseConnection::getInstance();
$userName = "";
$password = "";
$color = "";
$submitType = "";

//Make sure everything is working as intended
if(array_key_exists('userName', $_POST) && array_key_exists('password', $_POST) && array_key_exists('color', $_POST) && array_key_exists('submit', $_POST)) {
    if(!empty($_POST['userName']) && !empty($_POST['password']) && !empty($_POST['color']) && !empty($_POST['submit'])) {
        //Sanitize all of the users input
        $userName = filter_var($_POST['userName'], FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        $color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
        $submitType = filter_var($_POST['submit'], FILTER_SANITIZE_STRING);

    }
    else {
        echo "Invalid data";
        die();
    }
}
else {
    echo "Invalid data";
    die();
}

if($submitType === 'Login') {
    login($db, $userName, $password);
}
else if($submitType === 'Create') {
    create($db, $userName, $password, $color);
}
else {
    echo "You have sent an invalid submission type.";
    http_response_code(400);
}

function login($db, $userName, $password) {
    //Make sure the lengths are correct
    if(strlen($userName) <= 30 && strlen($password) <= 30 && strlen($color) <= 7) {
            try {
                $stmtHandle = $db->prepare("SELECT userName, color FROM User WHERE userName = :userName AND password = :password");
                $stmtHandle->bindValue('userName', $userName);
                $stmtHandle->bindValue('password', $password);
                $stmtHandle->execute();
                $user = $stmtHandle->fetch();

                if(!empty($user) && count($user) == 1) {
                    //Valid login!
                    echo '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="description" content="This is a chat application using HTML5 WebSockets" />
                            <meta name="keywords" content="chat" />
                            <meta name="author" content="Matthew Jensen" />
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Title</title>
                        
                            <link href="CSS/style.css" type="text/css" rel="stylesheet">
                        
                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
                            <script src="JS/SocketHandler.js" type="text/javascript"></script>
                        </head>
                        <body>
                            <div id="sideBar">
                                <div id="friendBar">
                        
                                </div>
                            </div>
                            <div id="chatContainer">
                                <div id="messageBox">
                        
                                </div>
                                <div id="panel">
                                    <table id="messageTable">
                                        <tr>
                                            <td id="displayName">
                                                UserName:
                                            </td>
                                            <td>
                                                <input type="text" name="message" id="message" placeholder="Type your message here" maxlength="80" />
                                            </td>
                                            <td>
                                                <button id="btnSend">Send</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </body>
                        </html>';
                }
                else {
                    echo "Invalid login";
                    http_response_code(401);
                }
            }
            catch (PDOException $e) {
                http_response_code(500);
                echo $e;
                die();
            }
        }
        else {
            echo "One or more fields are an invalid length";
            http_response_code(400);
            die();
        }
}

function create($db, $userName, $password, $color) {
        //Create the user in the database
    if(strlen($userName) <= 30 && strlen($password) <= 30 && strlen($color) <= 7) {
        try {
            $stmtHandle = $db->prepare("INSERT INTO User (userName, password, color) VALUES(:userName, :password, :color)");
            $stmtHandle->bindValue('userName', $userName);
            $stmtHandle->bindValue('password', $password);
            $stmtHandle->bindValue('color', $color);
            $stmtHandle->execute();
            //Successfully created the user
            echo '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="description" content="This is a chat application using HTML5 WebSockets" />
                        <meta name="keywords" content="chat" />
                        <meta name="author" content="Matthew Jensen" />
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Title</title>
                    
                        <link href="CSS/style.css" type="text/css" rel="stylesheet">
                    
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
                        <script src="JS/SocketHandler.js" type="text/javascript"></script>
                    </head>
                    <body>
                        <div id="sideBar">
                            <div id="friendBar">
                    
                            </div>
                        </div>
                        <div id="chatContainer">
                            <div id="messageBox">
                    
                            </div>
                            <div id="panel">
                                <table id="messageTable">
                                    <tr>
                                        <td id="displayName">
                                            UserName:
                                        </td>
                                        <td>
                                            <input type="text" name="message" id="message" placeholder="Type your message here" maxlength="80" />
                                        </td>
                                        <td>
                                            <button id="btnSend">Send</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </body>
                    </html>';
            http_response_code(201);
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e;
            die();
        }
    }
    else {
        echo "One or more fields are an invalid length";
        http_response_code(400);
        die();
    }
}