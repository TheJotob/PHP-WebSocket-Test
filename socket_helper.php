<?php
class SocketHelper {
  public static $clients = array();
 /**
	* Decode message from client
	* @param String $text Text to unmask
  * @return String unmasked text
	*/
public static function unmask($text) {
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
public static function mask($text) {
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
public static function perform_handshaking($received_header,$client_conn, $host, $port) {
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
}
