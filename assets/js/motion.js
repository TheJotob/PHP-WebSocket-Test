/* Event listener for acceleration */
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
