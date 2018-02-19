<?php

namespace AppBundle\Controller;

use AppBundle\Contract\Auth\TokenAuthenticatedController;
use AppBundle\Contract\Upload\UploadFileInterface;
use AppBundle\Contract\Validation\FileValidatorInterface;
use AppBundle\Event\FileUploadedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UploadController
 * @package AppBundle\Controller
 */
class UploadController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/upload/{bucket}", name="post_upload_file")
     * @Method("POST")
     *
     * @param Request $request
     * @param FileValidatorInterface $validator
     * @param UploadFileInterface $fileUpload
     * @param EventDispatcherInterface $dispatcher
     * @param string $bucket
     * @return JsonResponse
     */
    public function upload(
        Request $request,
        FileValidatorInterface $validator,
        UploadFileInterface $fileUpload,
        EventDispatcherInterface $dispatcher,
        string $bucket
    ) : JsonResponse
    {
        $errors = $validator->validate($request->files->get('file'));
        if (!empty($errors)) {
            return $this->badRequest($errors);
        }

        $file = $request->files->get('file');
        $url = $fileUpload->uploadFile($file, $bucket);

        $event = new FileUploadedEvent($url, $bucket);
        $dispatcher->dispatch($event::NAME, $event);

        return $this->success([
            'url' => $url,
            'bucket' => $bucket,
            'fileName' => basename($url)
        ]);
    }
}
