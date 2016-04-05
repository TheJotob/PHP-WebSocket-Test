var ws = null;      // WebSocket Object
var lastAlert = 0;  // Time of last alert

/* Initialise Socket */
function init_socket() {
  try {
    ws = new WebSocket('ws://' + host + ':' + port + '/socket.php');

    ws.onmessage = function(msg) {
      alertUser();
      console.log(msg.data);
    };

    /* Configure socket */
    ws.onopen = function() {
      sendThreshold(6);
      console.log('Socket opened');
    };

    ws.onclose  = function(close) { console.log(close); };
    ws.onerror  = function(error) { console.log(error); };

  } catch(e) {
    alert("ERROR: " + e);
  }
}

/* Alert the user about a too high acceleration */
function alertUser() {
  if(Date.now() - lastAlert > 5000) {
    lastAlert = Date.now();
    alert("Something happened to your phone!!!");
  }
}

/* Send threshold to server */
function sendThreshold(threshold) {
  ws.send("T" + threshold);
}

function updateThreshold() {
  sendThreshold(document.getElementById('threshold').value);
}
