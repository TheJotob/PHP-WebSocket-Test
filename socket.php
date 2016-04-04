<?php

class Socket {
  private $host, $port;

  public function Socket($host, $port) {
    $this->host = $host;
    $this->port = $port;
    $this->init();
    $this->show();
  }

  private function show() {
    error_log($this->host.":".$this->port, 4);
  }

  protected function init() {
    // Socket erstellen
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    // Socket an Adresse und Port binden
    socket_bind($sock, $this->host, $this->port);

    // An Port lauschen
    socket_listen($sock);

    $sockets = array($sock);
    $arClients = array();

    while (true) {
      error_log("Warte auf Verbindung...rn", 4);

      $sockets_change = $sockets;
      $ready = socket_select($sockets_change, $write = null, $expect = null, null);

      error_log("Verbindung angenommen.rn", 4);

      foreach($sockets_change as $s) {
        if ($s == $sock) {
          // Änderung am Serversocket
          $client = socket_accept($sock);
          socket_sendmsg($client, "TEST", MSG_OOB);
          array_push($sockets, $client);
          print_r($sockets);
        } else {
          // Eingehende Nachrichten der Clientsockets
          $bytes = @socket_recv($s, $buffer, 2048, 0);
        }
      }
    }
  }
}

// Error Reporting und Zeitlimit für Serverbetrieb setzen
error_reporting(E_ALL);
set_time_limit (0);

$socket = new Socket('192.168.0.201', 1414);
