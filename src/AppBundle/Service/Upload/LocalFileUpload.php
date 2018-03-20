<?php

namespace AppBundle\Service\Upload;

use AppBundle\Contract\Upload\ManageFileUploadInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LocalFileUpload
 * @package AppBundle\Service\Upload
 */
class LocalFileUpload implements ManageFileUploadInterface
{
//    TODO: don't you think it's better to use some kind of method that returns root directory?
    const UPLOAD_DIR = __DIR__ . '/../../../../web/uploads';

    /** @var ContainerInterface $container */
    protected $container;

    /**
     * LocalFileUpload constructor.
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    /**
     * @param UploadedFile $file
     * @param string $bucket
     * @return string
     */
    public function uploadFile(UploadedFile $file, string $bucket, string $fileName = null) : string
    {

        if (is_null($fileName)) {
            $fileName = md5_file($file) . '.' . $file->guessExtension();
        }

        $dir = preg_replace("/__params__.*/", "", substr($fileName, 0, strpos($fileName, '.')));

        $file->move(static::UPLOAD_DIR . '/' . $bucket . '/' . $dir. '/', $fileName);
        // TODO: we need to figure out how to get rid of uploads dir from url. Think about nginx proxy_pas
        return "{$this->container->getParameter('web_host')}/images/{$bucket}/origin/{$fileName}";
    }

    /**
     * @param string $uri
     * @param string $bucket
     * @return UploadedFile
     */
    public function downloadFile(string $uri, string $bucket) : UploadedFile
    {
        $parseUri = explode('/', $uri);
        $fileName = array_pop($parseUri);
        $dir = preg_replace("/__params__.*/", "", substr($fileName, 0, strpos($fileName, '.')));
        $filePath = realpath(static::UPLOAD_DIR) . '/' . $bucket . '/' . $dir . '/' . $fileName;
        if (!file_exists($filePath)) {
            throw new FileException(sprintf("File %s does not exist in %s.", $fileName, $filePath));
        }
        return new UploadedFile(
            static::UPLOAD_DIR . '/' . $bucket . '/' . $dir . '/' . $fileName,
            $fileName,
            null,
            null,
            null,
            true
        );
    }
}
