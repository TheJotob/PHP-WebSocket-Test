<?php
require_once('config.php');	// including configuration
require_once('socket_helper.php');
$null = NULL; 							// setting null var
$warning = mask("WARNING");	// defining warning message
$thresholds = array();

// Setup Socket to listen on defined host and port
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, Config::HOST, Config::PORT);
socket_listen($socket);
$clients = array($socket);

while (true) {
	// clone connection array for use in socket_select
	$changed = $clients;

	// get sockets that changed
	socket_select($changed, $null, $null, 0, 10);

	if (in_array($socket, $changed)) {
		// Accept new sockets and perform handshake
		$socket_new = socket_accept($socket);
		$clients[] = $socket_new;
		$header = socket_read($socket_new, 1024);
		perform_handshaking($header, $socket_new, Config::HOST, Config::PORT);
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}

	foreach ($changed as $changed_socket) {
		// Check for any incoming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
			$received_text = unmask($buf);
			$accelerationData = parseAccelerationData($received_text);

			if($accelerationData && thresholdExceeded($accelerationData))
				socket_write($changed_socket, $warning, strlen($warning));

			break 2;
		}

		// Cleanup disconnected clients
		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) {
			$found_socket = array_search($changed_socket, $clients);
			unset($clients[$found_socket]);
		}
	}
}
socket_close($socket);

/**
	* Parse the acceleration data.
	* @param String $str format: "xAcceleration, yAcceleration, zAcceleration"
  * @return float[] or false
	*/
function parseAccelerationData($str) {
	$accelerationData = explode(", ", $str);

	// check if the array has the right amount of values (should be 3 because of x, y, z)
	if(count($accelerationData) != 3)
		return false;

	// check if all values are containing floats
	for($i = 0; $i < 3; $i++) {
		if(is_float(floatval($accelerationData[$i])))
			$accelerationData[$i] = floatval($accelerationData[$i]);
		else return false;
	}

	return $accelerationData;
}

/**
	* Check if the threshold is exceeded
	* @param float[] $data
  * @return boolean
	*/
function thresholdExceeded($data) {
	for($i = 0; $i < count($data); $i++) {
		// check if the acceleration is higher or lower than the threshold
		if($data[$i] > Config::THRESHOLD || $data[$i] < (Config::THRESHOLD * -1)) {
			echo "Warning: " . $data[$i] . "\n";
			return true;
		}
	}
}
