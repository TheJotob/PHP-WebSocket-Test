<?php
require_once('config.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<title>Sensors</title>
<script type="text/javascript">
var ws;
function init_socket() {
  try {
    ws = new WebSocket('ws://<?= Config::HOST ?>:<?= Config::PORT ?>/socket.php');
    console.log(ws.readyState);

    ws.onmessage = function(msg) {
      console.log(msg.data);
    };

    ws.onopen = function() {
      console.log('Socket opened');
      try {
        ws.send("HI");
      } catch (e) {
        console.log(e);
      }

      console.log(ws);
    };

    ws.onclose = function(close) {
      console.log(close);
    };

    ws.onerror = function(error) {
      console.log(error);
    }

  } catch(e) {
    alert("ERROR: " + e);
  }
}

function send_message() {
  ws.send("TEST");
  console.log("Send 'TEST'");
}

function motion(event){
  document.getElementById("accelerometer").innerHTML = "Accelerometer: "
    + event.accelerationIncludingGravity.x + ", "
    + event.accelerationIncludingGravity.y + ", "
    + event.accelerationIncludingGravity.z;
}

function go(){
  if(window.DeviceMotionEvent){
    window.addEventListener("devicemotion", motion, false);
  }else{
    var status = document.getElementById("status");
    status.innerHTML = status.innerHTML.replace(
      "is supported", "is not supported"
    );
  }

  init_socket();
}
</script>
</head>
<body onload="go();">
<div id="status"><a href=\"http://www.w3.org/TR/orientation-event/">http://www.w3.org/TR/orientation-event/</a> is supported.</div>
<br/><br/>
<div id="accelerometer"></div>
<br/><br/>
<div id="socket_status"></div>
<button id="test_send" onclick="send_message()">TEST</div>
</body>
</html>
