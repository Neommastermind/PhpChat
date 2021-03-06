$(document).ready(function(){
    //create a new WebSocket object.
    var wsUri = "ws://icarus.cs.weber.edu:8080";
    var websocket = new WebSocket(wsUri);
    var userName = "";

    websocket.onopen = function(event) { // connection is open
        $('#messageBox').append("<div class=\"systemMessage\">You have Connected!</div>"); //notify user
        userName = $('#displayName').text(); //get user name
    };

    $('#logout').click(function() {
        websocket.close();
    });

    $('#btnSend').click(function(){ //use clicks message send button
        if(websocket.readyState === WebSocket.CONNECTING) {
            $('#messageBox').append("<div class=\"systemMessage\">We are still connecting you to the server, please wait.</div>");
        }
        else if(websocket.readyState === WebSocket.CLOSED) {
            $('#messageBox').append("<div class=\"systemMessage\">The connection is closed, please wait while we re-open that for you.</div>");
            websocket = new WebSocket(wsUri);
        }
        else {
            var message = $('#message').val(); //get message text
            userName = $('#displayName').text(); //get user name
            var userColor = $('#displayName').css("color"); //get user color

            if (userName == "") {
                alert("You do not have a name, please log out.");
                websocket.close();
                return;
            }
            if (message == "") {
                alert("You must enter a message first!");
                websocket.close();
                return;
            }

            //prepare json data
            var msg = {
                message: message,
                name: userName,
                color: userColor
            };
            //convert and send data to server
            websocket.send(JSON.stringify(msg));
        }
    });

    //#### Message received from server?
    websocket.onmessage = function(event) {
        var msg = JSON.parse(event.data); //PHP sends Json data
        var type = msg.type; //message type
        var message = msg.message; //message text

        if (type === 'userMessage') {
            var userName = msg.name; //user name
            var color = msg.color; //color
            $('#messageBox').append("<div class=\"userMessage\"><span class=\"userName\" style=\"color:" + color + "\">" + userName + "</span><span class=\"displayMessage\">" + message + "</span></div>");
        }
        if (type === 'systemMessage') {
            $('#messageBox').append("<div class=\"systemMessage\">" + message + "</div>");
        }

        $('#message').val(''); //reset text
    };

    websocket.onerror   = function(event){$('#messageBox').append("<div class=\"systemError\">Error Occurred - "+event.data+"</div>");};
    websocket.onclose   = function(event){$('#messageBox').append("<div class=\"systemMessage\">Connection Closed</div>");};
});
