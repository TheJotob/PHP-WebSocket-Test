<?php
require_once('config.php');
$host = Config::HOST; //host
$port = Config::PORT; //port
$null = NULL; //null var
$warning = mask("Warning");

//Create TCP/IP sream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//reuseable port
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//bind socket to specified host
socket_bind($socket, $host, $port);

//listen to port
socket_listen($socket);

//create & add listning socket to the list
$clients = array($socket);

//start endless loop, so that our script doesn't stop
while (true) {
	//manage multipal connections
	$changed = $clients;
	//returns the socket resources in $changed array
	socket_select($changed, $null, $null, 0, 10);

	//check for new socket
	if (in_array($socket, $changed)) {
		$socket_new = socket_accept($socket); //accpet new socket
		$clients[] = $socket_new; //add socket to client array

		$header = socket_read($socket_new, 1024); //read data sent by the socket
		perform_handshaking($header, $socket_new, $host, $port); //perform websocket handshake

		//make room for new socket
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}

	//loop through all connected sockets
	foreach ($changed as $changed_socket) {

		//check for any incomming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
			$received_text = unmask($buf); //unmask data

			$accelerationData = parseAccelerationData($received_text);
			if(thresholdExceeded($accelerationData))
				socket_write($changed_socket, $warning, strlen($warning));

			break 2; //exit this loop
		}

		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) { // check disconnected client
			// remove client for $clients array
			$found_socket = array_search($changed_socket, $clients);
			socket_getpeername($changed_socket, $ip);
			unset($clients[$found_socket]);
		}
	}
}
// close the listening socket
socket_close($socket);

//Unmask incoming framed message
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

//Encode message for transfer to client.
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

//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port) {
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
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

function parseAccelerationData($str) {
	$accelerationData = explode(", ", $str);

	if(count($accelerationData) != 3)
		return;

	for($i = 0; $i < 3; $i++) {
		if(is_float(floatval($accelerationData[$i])))
			$accelerationData[$i] = floatval($accelerationData[$i]);
		else return;
	}

	//echo implode("|", $accelerationData) . "\n";
	return $accelerationData;
}

function thresholdExceeded($data) {
	for($i = 0; $i < 3; $i++) {
		if($data[$i] > Config::THRESHOLD || $data[$i] < (Config::THRESHOLD * -1)) {
			echo "Warning: " . $data[$i];
			return true;
		}
	}
}
