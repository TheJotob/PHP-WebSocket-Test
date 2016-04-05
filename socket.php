<?php
require_once('config.php');	// including configuration
$host = Config::HOST; 			// reading socket host from config
$port = Config::PORT; 			// reading socket port from config
$null = NULL; 							// setting null var
$warning = mask("Warning");	// defining warning message

// Setup Socket to listen on defined host and port
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port);
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
		perform_handshaking($header, $socket_new, $host, $port);
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
	* Decode message from client
	* @param String $text Text to unmask
  * @return String unmasked text
	*/
function unmask($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	} elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	} else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

/**
	* Encode message for transfer to client
	* @param String $text Text to mask
  * @return String masked text
	*/
function mask($text) {
	$b1 = 0x80 | (0x1 & 0x0f);
	// $b1 = 0x81;
	$length = strlen($text);

	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

/**
	* Handshake new client
	* @param String $received_header Header received from client
	* @param resource $client_conn Connection to client
	* @param String $host Host address of socket
	* @param Integer $port Port of socket
  * @return float[] or false
	*/
function perform_handshaking($received_header,$client_conn, $host, $port) {
	$headers = array();
	$lines = preg_split("/\r\n/", $received_header);
	foreach($lines as $line) {
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/socket.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
}

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
