<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/4/2017
 * Time: 5:26 PM
 */

require_once 'Models/User.php';
require_once 'PHP/ChatFunctions.php';

$user;
$submitType = "";

//Make sure everything is working as intended
if(array_key_exists('userName', $_POST) && array_key_exists('password', $_POST) && array_key_exists('color', $_POST) && array_key_exists('submit', $_POST)) {
    if(!empty($_POST['submit'])) {
        //Create the user object. The object sanitizes the data and does checking.
        $user = new User($_POST['userName'], $_POST['password'], $_POST['color']);
        $submitType = filter_var($_POST['submit'], FILTER_SANITIZE_STRING);

    }
    else {
        http_response_code(400);
        alert("Invalid data");
        displayLoginForm();
        exit(0);
    }
}
else {
    //You have not entered submitted the correct data or you have just opened file
    //So you have to log in.
    displayLoginForm();
    exit(0);
}

if($submitType === 'Login') {
    login($user);
}
else if($submitType === 'Create') {
    create($user);
}
else {
    http_response_code(400);
    alert("You have sent an invalid submission type.");
    displayLoginForm();
    exit(0);
}

/*
 * Validates a users log in and redirects them appropriately.
 */
function login(User $user) {
    try {
        $databaseUser = User::loadUser($user->getUserName());

        if (!empty($databaseUser) && password_verify($user->getPassword(), $databaseUser->getPassword()) === true) {
            //Valid login!
            displayChat($user->getUserName());
            exit(0);
        } else {
            http_response_code(401);
            alert("Invalid login information!");
            displayLoginForm();
            exit(0);
        }
    }
    catch(PDOException $e) {
        http_response_code(500);
        alert("An error has occurred while trying to log in. $e");
        displayLoginForm();
        exit(0);
    }
}

/*
 * Creates a user in the database and redirects them appropriately.
 */
function create(User $user) {
    try {
        //Boolean status
        $status = User::createUser($user);

        if ($status) {
            //Valid Creation, log in!
            http_response_code(201);
            displayChat($user->getUserName());
            exit(0);
        } else {
            http_response_code(401);
            alert("Invalid Creation information!");
            displayLoginForm();
            exit(0);
        }
    }
    catch(PDOException $e) {
        http_response_code(500);
        alert("An error has occurred while trying to log in. $e");
        displayLoginForm();
        exit(0);
    }
}