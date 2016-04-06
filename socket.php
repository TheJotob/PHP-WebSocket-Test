<?php
require_once('config.php');	// including configuration
require_once('socket_helper.php');
require_once('motion_helper.php');
$null = NULL; 							// setting null var
$warning = SocketHelper::mask("WARNING");	// defining warning message

// Setup Socket to listen on defined host and port
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, Config::HOST, Config::PORT);
socket_listen($socket);
SocketHelper::$clients = array($socket);

while (true) {
	// clone connection array for use in socket_select
	$changed = SocketHelper::$clients;

	// get sockets that changed
	socket_select($changed, $null, $null, 0, 10);

	if (in_array($socket, $changed)) {
		// Accept new sockets and perform handshake
		$socket_new = socket_accept($socket);
		SocketHelper::$clients[] = $socket_new;
		$header = socket_read($socket_new, 1024);
		SocketHelper::perform_handshaking($header, $socket_new, Config::HOST, Config::PORT);
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}

	foreach ($changed as $changed_socket) {
		// Check for any incoming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
			$received_text = SocketHelper::unmask($buf);
			if(substr($received_text, 0, 1) == 'T') {
				MotionHelper::setThreshold($changed_socket, intval(substr($received_text, 1)));
				break 2;
			}

			$accelerationData = MotionHelper::parseAccelerationData($received_text);

			if($accelerationData && MotionHelper::thresholdExceeded($changed_socket, $accelerationData))
				socket_write($changed_socket, $warning, strlen($warning));

			break 2;
		}

		// Cleanup disconnected clients
		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) {
			$found_socket = array_search($changed_socket, SocketHelper::$clients);
			unset(SocketHelper::$clients[$found_socket]);
		}
	}
}
socket_close($socket);
