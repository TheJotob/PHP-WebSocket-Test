<?php
require_once('socket.php');

// Error Reporting und Zeitlimit für Serverbetrieb setzen
error_reporting(E_ALL);
set_time_limit (0);

$socket = new Socket('192.168.0.201', '1515');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<title>Sensors</title>
<script type="text/javascript">
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
}
</script>
</head>
<body onload="go();">
<div id="status"><a href=\"http://www.w3.org/TR/orientation-event/">http://www.w3.org/TR/orientation-event/</a> is supported.</div>
<br/><br/>
<div id="accelerometer"></div>
<br/><br/>
<div id="magnetometer"></div>
</body>
</html>
