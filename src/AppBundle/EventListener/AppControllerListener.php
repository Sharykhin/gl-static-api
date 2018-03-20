<?php

namespace AppBundle\EventListener;

use AppBundle\Contract\Auth\JWTInterface;
use AppBundle\Contract\Auth\TokenAuthenticatedController;
use AppBundle\Exception\TokenInvalidException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Translation\TranslatorInterface;
use Exception;

/**
 * Class AppControllerListener
 * @package AppBundle\EventListener
 */
class AppControllerListener
{
    /** @var ContainerInterface $container */
    protected $container;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var JWTInterface $JWT */
    protected $JWT;

    /**
     * AppControllerListener constructor.
     * @param ContainerInterface $container
     * @param TranslatorInterface $translator
     * @param JWTInterface $JWT
     */
    public function __construct(
        ContainerInterface $container,
        TranslatorInterface $translator,
        JWTInterface $JWT
    )
    {
        $this->container = $container;
        $this->translator = $translator;
        $this->JWT = $JWT;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event) : void
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokenAuthenticatedController) {
            if ($this->container->get('kernel')->getEnvironment() !== 'prod' &&
                filter_var($event->getRequest()->query->get('_debug'), FILTER_VALIDATE_BOOLEAN) == true) {
                return;
            }

            $token = trim($event->getRequest()->headers->get('Authorization'));
            $clients = $this->container->getParameter('clients');

            if (strpos($token, 'GL ') === 0) {
                list($accessKey, $signature) = explode(':', trim(str_replace('GL', '', $token)));
                $index = array_search($accessKey, array_column($clients, 'access_key'));
                if ($index === false || !isset($clients[$index])) {
                    goto exc;
                }
                $client = $clients[$index];
                //Find by access key bucket and secret key.
                $sign = hash_hmac('sha1', $client['bucket'], $client['secret_key'], true);
                $signatureToCheck = base64_encode($sign);
                if ($signatureToCheck === $signature) {
                    return;
                }
            } else if (strpos($token, 'Bearer ') === 0) {
                $jwtToken = trim(str_replace('Bearer', '', $token));
                $publicKey = file_get_contents($this->container->getParameter('kernel.root_dir') . '/var/public.pem');
                try {
                    $this->JWT->decode($jwtToken, $publicKey, ['RS256']);
                } catch (Exception $exception) {
                    goto exc;
                }
                return;
            }
            //TODO it was used intentionally just to check why everybody hates it and in this case it is quite convenient
            exc:
            throw new TokenInvalidException($this->translator->trans('token_is_invalid'));
        }
    }
}
