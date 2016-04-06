<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Sensors</title>
  <script type="text/javascript">
  var host = '<?= Config::HOST ?>';
  var port = '<?= Config::PORT ?>';
  /* Kick off function */
  function go() {
    if(window.DeviceMotionEvent)
      window.addEventListener("devicemotion", motion, false);
    else
      alert("Your device is not supporting DeviceMotionEvent :(")

    init_socket();
  }
  </script>
  <script type="text/javascript" src="/assets/js/socket.js"></script>
  <script type="text/javascript" src="/assets/js/motion.js"></script>
  <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" />
</head>
<body onload="go();">
  <br/><br/>
  <div class="container">
    <div class="list-group">
      <div id="accelerationX" class="list-group-item"></div>
      <div id="accelerationY" class="list-group-item"></div>
      <div id="accelerationZ" class="list-group-item"></div>
    </div>
    <div class="row">
      <div class="col-md-8 col-xs-12 form-group">
        <input type="number" class="form-control" placeholder="Threshold" id="threshold">
      </div>
      <div class="col-md-4 col-xs-12 form-group">
        <button class="btn btn-success form-control" onclick="updateThreshold()">Set Threshold</button>
      </div>
    </div>
  </div>
</body>
</html>
