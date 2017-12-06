$(document).ready(function(){
    //create a new WebSocket object.
    var wsUri = "ws://icarus.cs.weber.edu/~mj34023/PhpChat/v1/PHP/WebSocket.php";
    websocket = new WebSocket(wsUri);
    var userName = "";

    websocket.onopen = function(ev) { // connection is open
        $('#messageBox').append("<div class=\"systemMessage\">Connected!</div>"); //notify user
        userName = $('#displayName').val(); //get user name
    }

    $('#logout').click(function() {
        websocket.close();
    });

    $('#btnSend').click(function(){ //use clicks message send button
        var message = $('#message').val(); //get message text

        if(userName === ""){ //empty name?
            alert("You do not have a name, please log out.");
            websocket.close();
            return;
        }
        if(message === ""){ //emtpy message?
            alert("You must enter a message first!");
            return;
        }

        //prepare json data
        var msg = {
            message: message,
            name: userName,
            color : '<?php echo $colours[$user_colour]'
        };
        //convert and send data to server
        websocket.send(JSON.stringify(msg));
    });

    //#### Message received from server?
    websocket.onmessage = function(ev) {
        var msg = JSON.parse(ev.data); //PHP sends Json data
        var type = msg.type; //message type
        var umsg = msg.message; //message text
        var uname = msg.name; //user name
        var ucolor = msg.color; //color

        if(type == 'usermsg')
        {
            $('#message_box').append("<div><span class="user_name" style="color:#"+ucolor+"">"+uname+"</span> : <span class="user_message">"+umsg+"</span></div>");
        }
        if(type == 'system')
        {
            $('#message_box').append("<div class=\"systemMessage\">"+umsg+"</div>");
        }

        $('#message').val(''); //reset text
    };

    websocket.onerror   = function(ev){$('#message_box').append("<div class=\"systemError\">Error Occurred - "+ev.data+"</div>");};
    websocket.onclose   = function(ev){$('#message_box').append("<div class=\"systemMessage\">Connection Closed</div>");};
});