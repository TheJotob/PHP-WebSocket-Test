/* Event listener for acceleration */
function motion(event){
    // Generate str with all the acceleration data
    var str = event.acceleration.x + ", "
            + event.acceleration.y + ", "
            + event.acceleration.z;

    // Send acceleration data to socket
    if(ws != null)
      ws.send(str);

    // Display acceleration data
    document.getElementById("accelerationX").innerHTML = "X: " + event.acceleration.x;
    document.getElementById("accelerationY").innerHTML = "Y: " + event.acceleration.y;
    document.getElementById("accelerationZ").innerHTML = "Z: " + event.acceleration.z;
}
