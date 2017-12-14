<?php
/**
 * Created by PhpStorm.
 * User: neomm
 * Date: 12/6/2017
 * Time: 8:24 AM
 */

require_once 'SocketHandler.php';

try {
    $host = "0.0.0.0";
    $port = 8080;
    $null = NULL;
    $socketHandler = new SocketHandler();


//Create TCP/IP Stream socket
    if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
        echo "Socket creating failed: " . socket_strerror(socket_last_error($socket)) . "\n";
        die(1);
    } else {
        echo "Socket created.\n";
    }


    socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
//Bind an address to the socket
//Must be done before we establish a connection
    if (socket_bind($socket, $host, $port) === false) {
        echo "Bind failure: " . socket_strerror(socket_last_error($socket)) . "\n";
        die(1);
    } else {
        echo "Socket bound to $host:$port.\n";
    }
//Start listening to incoming traffic for our socket
    if (socket_listen($socket) === false) {
        echo "Listen failure: " . socket_strerror(socket_last_error($socket)) . "\n";
        die(1);
    } else {
        echo "The Socket is now listening to incoming traffic.\n";
    }
//Accept new connections to this socket
//socket_accept($socket);


//create & add listening socket to the list
    $clients = array($socket);

//start endless loop, so that our script doesn't stop
    while (true) {
        //manage multiple connections
        $changed = $clients;
        //returns the socket resources in $changed array
        socket_select($changed, $null, $null, 0, 10);

        //check for new socket
        if (in_array($socket, $changed)) {
            //Accept new connections to this socket
            if (($socketNew = socket_accept($socket)) === false) {
                echo "Socket acceptance failure: " . socket_strerror(socket_last_error($socket)) . "\n";
                die(1);
            } else {
                echo "The Socket is now accepting requests.\n";
            }
            //add socket to client array
            $clients[] = $socketNew;

            $header = socket_read($socketNew, 1024);

            echo "Preparing to handshake.\n";
            $socketHandler->handshake($header, $socketNew, $host, $port);
            echo "Hand shake has ended\n";

            socket_getpeername($socketNew, $ip); //get ip address of connected socket
            $response = $socketHandler->mask(json_encode(array('type' => 'systemMessage', 'message' => $ip . ' connected'))); //prepare json data
            $socketHandler->sendMessage($response); //notify all users about new connection

            //make room for new socket
            $foundSocket = array_search($socket, $changed);
            unset($changed[$foundSocket]);
        }

        //loop through all connected sockets
        foreach ($changed as $changedSocket) {

            //check for any incoming data
            while (socket_recv($changedSocket, $data, 1024, 0) >= 1) {
                $receivedText = $socketHandler->unmask($data); //unmask data
                $message = json_decode($receivedText); //json decode
                $userName = $message->name; //sender name
                $userMessage = $message->message; //message text
                $userColor = $message->color; //color

                //prepare data to be sent to client
                $responseText = $socketHandler->mask(json_encode(array('type' => 'userMessage', 'name' => $userName, 'message' => $userMessage, 'color' => $userColor)));
                $socketHandler->sendMessage($responseText); //send data
                break 2; //exit this loop
            }

            $data = @socket_read($changedSocket, 1024, PHP_NORMAL_READ);
            if ($data === false) { // check disconnected client
                // remove client for $clients array
                $foundSocket = array_search($changedSocket, $clients);
                socket_getpeername($changedSocket, $ip);
                unset($clients[$foundSocket]);

                //notify all users about disconnected connection
                $response = $socketHandler->mask(json_encode(array('type' => 'systemMessage', 'message' => $ip . ' disconnected')));
                $socketHandler->sendMessage($response);
            }
        }
    }
// close the listening socket
    socket_close($socket);
}
catch(Exception $e) {
    echo socket_strerror(socket_last_error($socket) . "\n");
    socket_close($socket);
    die(1);
}
