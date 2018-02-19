<?php

namespace AppBundle\Service\Auth\JWT;

use stdClass;
use \Firebase\JWT\JWT;
use AppBundle\Contract\Auth\JWTInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FireBaseJWT
 * @package AppBundle\Service\Auth\JWT
 */
class FireBaseJWT implements JWTInterface
{
    /** @var ContainerInterface $container */
    protected $container;

    /**
     * FireBaseJWT constructor.
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    /**
     * @param string $jwt
     * @param string $key
     * @param array $allowedAlgs
     * @return stdClass
     */
    public function decode(string $jwt, string $key, array $allowedAlgs = []): stdClass
    {
        //TODO: later we need to read public key here. No needs to get the key since we would use RSA!
        //TODO: it's not testable. Use factory
        return JWT::decode($jwt, $key, $allowedAlgs);
    }
}
