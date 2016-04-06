var ws = null;      // WebSocket Object
var lastAlert = 0;  // Time of last alert

/* Initialise Socket */
function init_socket() {
  try {
    ws = new WebSocket('ws://' + host + ':' + port + '/socket.php');

    ws.onmessage = function(msg) { alertUser(); };
    ws.onerror  = function(error) { alert("Something went wrong please reload the page!") };

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

/* Read the new threshold value and send it to server */
function updateThreshold() {
  sendThreshold(document.getElementById('threshold').value);
}
