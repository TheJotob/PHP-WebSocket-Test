<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<title>Sensors</title>
<script type="text/javascript">
function init_socket() {
  try {
    socket = new WebSocket('ws://192.168.0.201:1414/socket.php');

    socket.onopen = function() {
      console.log('Socket opened');
      socket.send('Hi there!!!');
    };

    socket.onclose = function(close) {
      console.log('Socket closed' + close.code);
    };

    socket.onmessage = function(msg) {
      console.log('Neue Nachricht: ' + msg.data);
    };

    socket.onerror = function(error) {
      console.log('error: ' + error.data);
    }

  } catch(e) {
    alert("ERROR: " + e);
  }
}

function motion(event){
  document.getElementById("accelerometer").innerHTML = "Accelerometer: "
    + event.accelerationIncludingGravity.x + ", "
    + event.accelerationIncludingGravity.y + ", "
    + event.accelerationIncludingGravity.z;
}
function orientation(event){
  document.getElementById("magnetometer").innerHTML = "Magnetometer: "
    + event.alpha + ", "
    + event.beta + ", "
    + event.gamma;
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
  if(window.DeviceOrientationEvent){
    window.addEventListener("deviceorientation", orientation, false);
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
<div id="magnetometer"></div>
<div id="socket_status"></div>
</body>
</html>
