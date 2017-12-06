<?php
/**
 * Created by PhpStorm.
 * User: neomm
 * Date: 12/6/2017
 * Time: 8:24 AM
 */

require_once 'SocketHandler.php';

//Create TCP/IP Stream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//Bind an address to the socket
//Must be done before we establish a connection
socket_bind($socket, 'localhost');
//Start listening to incoming traffic for our socket
socket_listen($socket);
//Accept new connections to this socket
//socket_accept($socket);

$socketHandler = new SocketHandler();

//create & add listning socket to the list
$clients = array($socket);
//start endless loop, so that our script doesn't stop
while (true) {
    //manage multipal connections
    $changed = $clients;
    //returns the socket resources in $changed array
    socket_select($changed, $null, $null, 0, 10);

    //check for new socket
    if (in_array($socket, $changed)) {
        $socket_new = socket_accept($socket); //accpet new socket
        $clients[] = $socket_new; //add socket to client array

        $header = socket_read($socket_new, 1024); //read data sent by the socket
        //add the color into the data
        $socketHandler->handshake($header, $socket_new, $host, $port); //perform websocket handshake

        socket_getpeername($socket_new, $ip); //get ip address of connected socket
        $response = $socketHandler->mask(json_encode(array('type'=>'system', 'message'=>$ip.' connected'))); //prepare json data
        $socketHandler->sendMessage($response); //notify all users about new connection

        //make room for new socket
        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
    }

    //loop through all connected sockets
    foreach ($changed as $changed_socket) {

        //check for any incomming data
        while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
        {
            $received_text = $socketHandler->unmask($buf); //unmask data
            $tst_msg = json_decode($received_text); //json decode
            $user_name = $tst_msg->name; //sender name
            $user_message = $tst_msg->message; //message text
            $user_color = $tst_msg->color; //color

            //prepare data to be sent to client
            $response_text = $socketHandler->mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));
            $socketHandler->endMessage($response_text); //send data
            break 2; //exist this loop
        }

        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
        if ($buf === false) { // check disconnected client
            // remove client for $clients array
            $found_socket = array_search($changed_socket, $clients);
            socket_getpeername($changed_socket, $ip);
            unset($clients[$found_socket]);

            //notify all users about disconnected connection
            $response = $socketHandler->mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
            $socketHandler->sendMessage($response);
        }
    }
}
// close the listening socket
socket_close($socket);
