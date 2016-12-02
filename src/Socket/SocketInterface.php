<?php

/*
 * This file is part of the NNTP library.
 *
 * (c) Robin van der Vleuten <robin@webstronauts.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rvdv\Nntp\Socket;

/**
 * @author Robin van der Vleuten <robin@webstronauts.co>
 */
interface SocketInterface
{
    /**
     * @param bool $enable
     * @param int  $cryptoType
     *
     * @return self
     */
    public function enableCrypto($enable, $cryptoType = STREAM_CRYPTO_METHOD_TLS_CLIENT);

    /**
     * @param string $address
     *
     * @return self
     */
    public function connect($address);

    /**
     * @return self
     */
    public function disconnect();

    /**
     * @return bool
     */
    public function eof();

    /**
     * @param int $length
     *
     * @return string
     */
    public function gets($length = null);

    /**
     * @param int $length
     *
     * @return string
     */
    public function read($length);

    /**
     * @param $data
     *
     * @return int
     */
    public function write($data);
}
