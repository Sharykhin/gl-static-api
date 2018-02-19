<?php

namespace AppBundle\Controller;

use AppBundle\Contract\Image\TransformInterface;
use AppBundle\Contract\Upload\DownloadFileInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Service\Image\ImageResizeDecorator;
use AppBundle\Service\Image\ImageRoundDecorator;
use AppBundle\Contract\Upload\UploadFileInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ImageController
 * @package AppBundle\Controller
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/images/{bucket}/{params}/{fileName}", name="get_image", defaults={"params"="origin"})
     * @Method("GET")
     * @Cache(expires="tomorrow", smaxage="3600", maxage="3600")
     *
     * @param UploadFileInterface $uploadFile
     * @param DownloadFileInterface $downloadFile
     * @param TransformInterface $transform
     * @param $bucket
     * @param $params
     * @param $fileName
     * @return BinaryFileResponse
     */
    public function getImage(
        UploadFileInterface $uploadFile,
        DownloadFileInterface $downloadFile,
        TransformInterface $transform,
        $bucket,
        $params,
        $fileName
    ) : BinaryFileResponse
    {
        list($fileBaseName, $ext) = explode('.', $fileName);

        if ($params === 'origin') {
            // TODO: think would it work in case we moved service to S3. It might be better to get the full, origin url?
            $file = $downloadFile->downloadFile("/{$bucket}/{$fileName}", $bucket);
            if ($file instanceof UploadedFile) {
                return new BinaryFileResponse($file->getRealPath());
            }
            throw new NotFoundHttpException($fileName);
        }

        $decorator = $uploadFile;

        //TODO: this is a bottleneck in the code since the service trims extra line for detecting origin name.
        $paramsName = '__params__';
        if (preg_match("/(?P<size1>[w|h]_\\d+),(?P<size2>[h|w]_\\d+)/", $params, $matches)) {
            list($width, $height) = strpos($matches['size1'], "h_") === 0 ? [$matches['size2'], $matches['size1']] : [$matches['size1'], $matches['size2']];
            $width = (int) str_replace('w_', '', $width);
            $height = (int) str_replace('h_', '', $height);
            $focus = 'center';

            if (preg_match("/(?P<focus>f_\\w+)/", $params, $matches)) {
                $focus = str_replace('f_', '', $matches['focus']);
                $paramsName .= "_" . $matches['focus'];
            }

            $decorator = new ImageResizeDecorator($decorator, $transform, $width, $height, $focus);
            $paramsName .= "_{$width}_{$height}";
        }

        if (preg_match("/r_circle/", $params, $matches)) {
            $decorator = (new ImageRoundDecorator($decorator, $transform));
            $paramsName .= '_r_circle';
        }

        $newFileName = $fileBaseName . $paramsName . '.' . $ext;

        if ($paramsName === '__params__') {
            throw new NotFoundHttpException($newFileName);
        }

        // TODO: all this looks overcomplicated.
        try {
            $file = $downloadFile->downloadFile("/{$bucket}/{$newFileName}", $bucket);
            return new BinaryFileResponse($file->getRealPath());
        } catch (FileException $exception) {
            try {
                $file = $downloadFile->downloadFile("/{$bucket}/{$fileName}", $bucket);
                $url = $decorator->uploadFile($file, $bucket, $newFileName);
                $file = $downloadFile->downloadFile($url, $bucket);
                return new BinaryFileResponse($file->getRealPath());
            } catch (FileException $exception) {
                throw new NotFoundHttpException($newFileName);
            }
        }
    }
}
