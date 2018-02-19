<?php

namespace AppBundle\Contract\Auth;

use stdClass;

/**
 * Interface JWTInterface
 * @package AppBundle\Contract\Auth
 */
interface JWTInterface
{
    /**
     * @param string $jwt
     * @param string $key
     * @param array $allowedAlgs
     * @return stdClass
     */
    public function decode(string $jwt, string $key, array $allowedAlgs = []) : stdClass;
}
