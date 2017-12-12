$(document).ready(function(){
    //create a new WebSocket object.
    var wsUri = "ws://icarus.cs.weber.edu/~mj34023/PhpChat/v1/PHP/WebSocket.php";
    var websocket = new WebSocket(wsUri);
    var userName = "";

    websocket.onopen = function(event) { // connection is open
        $('#messageBox').append("<div class=\"systemMessage\">Connected!</div>"); //notify user
        userName = $('#displayName').text(); //get user name
    };

    $('#logout').click(function() {
        websocket.close();
    });

    $('#btnSend').click(function(){ //use clicks message send button

        waitForSocketConnection(websocket, function () {
            var message = $('#message').val(); //get message text
            userName = $('#displayName').text(); //get user name

            if(userName == ""){
                alert("You do not have a name, please log out.");
                websocket.close();
                return;
            }
            if(message == ""){
                alert("You must enter a message first!");
                websocket.close();
                return;
            }

            //prepare json data
            var msg = {
                message: message,
                name: userName,
                color : "#000000"
            };
            //convert and send data to server
            websocket.send(JSON.stringify(msg));
        });
    });

    //#### Message received from server?
    websocket.onmessage = function(event) {
        var msg = JSON.parse(event.data); //PHP sends Json data
        var type = msg.type; //message type
        var message = msg.message; //message text
        var userName = msg.name; //user name
        var color = msg.color; //color

        if (type === 'userMessage') {
            $('#messageBox').append("<div class='userMessage'><span class=\"userName\" style=\"color:" + color + "\">" + userName + "</span> : <span class=\"displayMessage\">" + message + "</span></div>");
        }
        if (type === 'systemMessage') {
            $('#messageBox').append("<div class=\"systemMessage\">" + message + "</div>");
        }

        $('#message').val(''); //reset text
    };

    websocket.onerror   = function(event){$('#messageBox').append("<div class=\"systemError\">Error Occurred - "+event.data+"</div>");};
    websocket.onclose   = function(event){$('#messageBox').append("<div class=\"systemMessage\">Connection Closed</div>");};

    function waitForSocketConnection(socket, callback){
        setTimeout(
            function(){
                if (socket.readyState === socket.OPEN) {
                    if(callback !== undefined){
                        callback();
                    }
                    return;
                } else {
                    waitForSocketConnection(socket,callback);
                }
            }, 5);
    };
});