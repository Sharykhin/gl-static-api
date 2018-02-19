<?php

namespace AppBundle\Service\Image;

use AppBundle\Contract\Image\TransformInterface;
use AppBundle\Contract\Upload\UploadFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageRoundDecorator
 * @package AppBundle\Service\Image
 */
class ImageRoundDecorator implements UploadFileInterface
{
    /** @var UploadFileInterface $uploadFile */
    protected $uploadFile;

    /** @var TransformInterface $transform */
    protected $transform;

    /**
     * ImageRoundDecorator constructor.
     * @param UploadFileInterface $uploadFile
     * @param TransformInterface $transform
     */
    public function __construct(
        UploadFileInterface $uploadFile,
        TransformInterface $transform
    )
    {
        $this->uploadFile = $uploadFile;
        $this->transform = $transform;
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
        $roundedFile = $this->transform->roundImage($file, $filename);
        return $this->uploadFile->uploadFile($roundedFile, $bucket, $filename);
    }
}
