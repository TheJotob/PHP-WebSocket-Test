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
  <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" />
</head>
<body onload="go();">
  <div id="status"><a href=\"http://www.w3.org/TR/orientation-event/">http://www.w3.org/TR/orientation-event/</a> is supported.</div>
  <br/><br/>
  <div class="container-fluid">
    <div class="row">
      <div id="accelerationX" class="col-md-4 col-xs-12">0</div>
      <div id="accelerationY" class="col-md-4 col-xs-12">0</div>
      <div id="accelerationZ" class="col-md-4 col-xs-12">0</div>
    </div>
    <div class="row">
      <div class="col-md-8 col-xs-12 form-group">
        <input type="number" class="form-control" placeholder="Threshold" id="threshold">
      </div>
      <div class="col-md-4 col-xs-12 form-group">
        <button class="btn btn-success" onclick="updateThreshold()">Send</button>
      </div>
    </div>
  </div>
</body>
</html>
