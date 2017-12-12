<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/4/2017
 * Time: 6:01 PM
 */

/*
 * Displays the html for the chat application.
 */
function displayChat($userName) {
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
                            <div id="userInfo">
                                <span>'.$userName.':</span>
                                <form method="post" action="Chat.php">
                                    <input type="submit" id="logOut" value="Log Out"/>
                                </form>
                            </div>
                            <div id="friendBar">
                    
                            </div>
                        </div>
                        <div id="chatContainer">
                            <div id="messageBox">
                    
                            </div>
                            <div id="panel">
                                <table id="messageTable">
                                    <tr>
                                        <td id="displayName">'.
                                            $userName.':
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

/*
 * Displays the html for the login form.
 */
function displayLoginForm() {
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
    <div id="chatContainer">
        <div id="loginMenu">
            <h2>Login Menu</h2>
            <form method="post" action="Chat.php">
                <table>
                    <tr>
                        <td>
                            <label for="userName">User Name: </label>
                        </td>
                        <td>
                            <input type="text" id="userName" name="userName" placeholder="Type your user name here" maxlength="30" required/>
                        </td>
                    </tr>
                    <tr>
                    <td>
                        <label for="color">User Name Color(Only if you are creating): </label>
                    </td>
                    <td>
                        <input type="color" id="color" name="color"/>
                    </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="password">Password: </label>
                        </td>
                        <td>
                            <input type="password" name="password" id="password" placeholder="Type your password here" maxlength="30" required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="submit" id="loginSubmit" value="Login"/>
                        </td>
                        <td>
                            <input type="submit" name="submit" id="createSubmit" value="Create"/>
                        </td>
                    </tr>
                </table>
            </form>
            </div>
        </div>
    </div>
</body>
</html>';
}

/*
 * Sends a javascript alert to the screen with the given message.
 */
function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}