<?php

namespace AppBundle\Contract\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface UploadFileInterface
 * @package AppBundle\Contract\Upload
 */
interface UploadFileInterface
{
    /**
     * @param UploadedFile $file
     * @param string $bucket
     * @param string $filename
     * @return string
     */
    public function uploadFile(UploadedFile $file, string $bucket, string $filename = null) : string;
}
