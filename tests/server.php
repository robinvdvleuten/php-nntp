#!/usr/bin/env php
<?php

if (!$server = stream_socket_server('tcp://0.0.0.0:5000', $errno, $errorMessage)) {
    throw new UnexpectedValueException("Could not bind to socket: $errorMessage");
}

while (true) {
    $connection = @stream_socket_accept($server);

    if ($connection) {
        fwrite($connection, "200 server ready - posting allowed\r\n");
        fclose($connection);
    }
}
