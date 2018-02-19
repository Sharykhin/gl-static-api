<?php

namespace AppBundle\Service\Image;

use AppBundle\Contract\Image\TransformInterface;
use AppBundle\Contract\Upload\UploadFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageResizeDecorator
 * @package AppBundle\Service\Image
 */
class ImageResizeDecorator implements UploadFileInterface
{
    /** @var UploadFileInterface $uploadFile */
    protected $uploadFile;

    /** @var TransformInterface $transform */
    protected $transform;

    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    /** @var string $focus */
    protected $focus;

    /**
     * ImageResizeDecorator constructor.
     * @param UploadFileInterface $uploadFile
     * @param TransformInterface $transform
     * @param int $width
     * @param int $height
     * @param string $focus
     */
    public function __construct(
        UploadFileInterface $uploadFile,
        TransformInterface $transform,
        int $width,
        int $height,
        string $focus
    )
    {
        $this->uploadFile = $uploadFile;
        $this->transform = $transform;
        $this->width = $width;
        $this->height = $height;
        $this->focus = $focus;
    }

    /**
     * @param UploadedFile $file
     * @param string $bucket
     * @param string|null $filename
     * @return string
     */
    public function uploadFile(UploadedFile $file, string $bucket, string $filename = null) : string
    {
        if (is_null($filename)) {
            $filename = md5_file($file) . '.' . $file->guessExtension();
        }
        $resizedFile = $this->transform->crop($file, $filename, $this->width, $this->height, $this->focus);
        return $this->uploadFile->uploadFile($resizedFile, $bucket, $filename);
    }
}
