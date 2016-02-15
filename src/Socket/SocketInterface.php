<?php

namespace Rvdv\Nntp\Socket;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
interface SocketInterface
{
    /**
     * @param bool $toggle
     *
     * @return self
     */
    public function setBlocking($blocking);

    /**
     * @param bool $enable
     * @param int  $cryptoType
     *
     * @return self
     */
    public function enableCrypto($enable, $cryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT);

    /**
     * @param string $address
     * @param int    $timeout
     *
     * @return self
     */
    public function connect($address, $timeout = null);

    /**
     * @return self
     */
    public function disconnect();

    /**
     * @return bool
     */
    public function eof();

    public function read($length);

    public function write($data);
}