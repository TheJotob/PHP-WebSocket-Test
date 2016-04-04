<!--
Learning to access sensors in JavaScript.
The MIT License (MIT)
Copyright (c) 2016
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->
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
