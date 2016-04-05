<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <title>Sensors</title>
  <script type="text/javascript">
  var host = '<?= Config::HOST ?>';
  var port = '<?= Config::PORT ?>';
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
  <script type="text/javascript" src="/assets/js/socket.js"></script>
  <script type="text/javascript" src="/assets/js/motion.js"></script>
</head>
<body onload="go();">
  <div id="status"><a href=\"http://www.w3.org/TR/orientation-event/">http://www.w3.org/TR/orientation-event/</a> is supported.</div>
  <br/><br/>
  <div id="accelerometer"></div>
</body>
</html>
