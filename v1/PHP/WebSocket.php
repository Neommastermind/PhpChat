<?php
/**
 * Created by PhpStorm.
 * User: neomm
 * Date: 12/6/2017
 * Time: 8:24 AM
 */

$host = "localhost";
$port = null;

require_once 'SocketHandler.php';

//Create TCP/IP Stream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//Bind an address to the socket
//Must be done before we establish a connection
socket_bind($socket, $host, $port);
//Start listening to incoming traffic for our socket
socket_listen($socket);
//Accept new connections to this socket
//socket_accept($socket);

$socketHandler = new SocketHandler();

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
        $socketNew = socket_accept($socket);
        //add socket to client array
        $clients[] = $socketNew;

        $header = socket_read($socketNew, 1024);
        //add the color into the data
        $socketHandler->handshake($header, $socketNew, $host, $port);

        socket_getpeername($socketNew, $ip); //get ip address of connected socket
        $response = $socketHandler->mask(json_encode(array('type'=>'systemMessage', 'message'=>$ip.' connected'))); //prepare json data
        $socketHandler->sendMessage($response); //notify all users about new connection

        //make room for new socket
        $foundSocket = array_search($socket, $changed);
        unset($changed[$foundSocket]);
    }

    //loop through all connected sockets
    foreach ($changed as $changedSocket) {

        //check for any incoming data
        while(socket_recv($changedSocket, $buf, 1024, 0) >= 1)
        {
            $receivedText = $socketHandler->unmask($buf); //unmask data
            $message = json_decode($receivedText); //json decode
            $userName = $message->name; //sender name
            $userMessage = $message->message; //message text
            $userColor = $message->color; //color

            //prepare data to be sent to client
            $responseText = $socketHandler->mask(json_encode(array('type'=>'userMessage', 'name'=>$userName, 'message'=>$userMessage, 'color'=>$userColor)));
            $socketHandler->sendMessage($responseText); //send data
            break 2; //exit this loop
        }

        $buf = @socket_read($changedSocket, 1024, PHP_NORMAL_READ);
        if ($buf === false) { // check disconnected client
            // remove client for $clients array
            $foundSocket = array_search($changedSocket, $clients);
            socket_getpeername($changedSocket, $ip);
            unset($clients[$foundSocket]);

            //notify all users about disconnected connection
            $response = $socketHandler->mask(json_encode(array('type'=>'systemMessage', 'message'=>$ip.' disconnected')));
            $socketHandler->sendMessage($response);
        }
    }
}
// close the listening socket
socket_close($socket);
