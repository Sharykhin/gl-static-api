<?php

namespace AppBundle\Contract\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface UploadFileInterface
 * @package AppBundle\Contract\Upload
 */
interface DownloadFileInterface
{
    /**
     * @param string $uri
     * @param string $bucket
     * @return UploadedFile
     */
    public function downloadFile(string $uri, string $bucket) : UploadedFile;
}
