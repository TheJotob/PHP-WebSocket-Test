<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <title>Sensors</title>
  <script type="text/javascript">
    var ws = null;
    var lastAlert = 0;

    /* Initialise Socket */
    function init_socket() {
      try {
        ws = new WebSocket('ws://<?= Config::HOST ?>:<?= Config::PORT ?>/socket.php');

        ws.onmessage = function(msg) {
          if(Date.now() - lastAlert > 5000) {
            lastAlert = Date.now();
            alert("Something happened to your phone!!!");
          }
          console.log(msg.data);
        };

        ws.onopen   = function()      { console.log('Socket opened'); };
        ws.onclose  = function(close) { console.log(close); };
        ws.onerror  = function(error) { console.log(error); };

      } catch(e) {
        alert("ERROR: " + e);
      }
    }

    // Event listener for acceleration
    function motion(event){
        // Generate str with all the acceleration data
        var str = event.acceleration.x + ", "
                + event.acceleration.y + ", "
                + event.acceleration.z;

        // Display acceleration data
        document.getElementById("accelerometer").innerHTML = "Accelerometer: " + str;

        // Send acceleration data to socket
        if(ws != null)
          ws.send(str);
    }

    /* Kick off function */
    function go() {
      if(window.DeviceMotionEvent) {
        window.addEventListener("devicemotion", motion, false);
      } else {
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
</body>
</html>
