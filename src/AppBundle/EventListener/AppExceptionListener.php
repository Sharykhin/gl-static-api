<?php

namespace AppBundle\EventListener;

use AppBundle\Exception\TokenInvalidException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class AppExceptionListener
 * @package AppBundle\EventListener
 */
class AppExceptionListener
{

    /** @var ContainerInterface $container */
    protected $container;

    /**
     * AppExceptionListener constructor.
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) : void
    {
        $exception = $event->getException();
        $debug = filter_var($event->getRequest()->query->get('_debug'), FILTER_VALIDATE_BOOLEAN);

        if ($debug === true && $this->container->get('kernel')->getEnvironment() !== 'prod') {
            return;
        }

        if ($exception instanceof FileException) {
            //TODO: This is the second place where we specify response format
            $data = [
                'success' => false,
                'data' => null,
                'errors' => $exception->getMessage(),
                'meta' => null
            ];

            $response = new JsonResponse($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            $event->setResponse($response);
        }

        if ($exception instanceof TokenInvalidException) {
            $data = [
                'success' => false,
                'data' => null,
                'errors' => $exception->getMessage(),
                'meta' => null
            ];

            $response = new JsonResponse($data, JsonResponse::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
    }
}
