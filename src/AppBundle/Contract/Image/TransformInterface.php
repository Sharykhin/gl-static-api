<?php

namespace AppBundle\Contract\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface TransformInterface
 * @package AppBundle\Contract\Image
 */
interface TransformInterface
{
    /**
     * @param UploadedFile $file
     * @param string $fileName
     * @param $width
     * @param $height
     * @param bool $crop
     * @return UploadedFile
     */
    public function resizeAndCrop(UploadedFile $file, string $fileName, $width, $height, $crop = false) : UploadedFile;

    /**
     * @param UploadedFile $file
     * @param string $fileName
     * @param $new_w
     * @param $new_h
     * @param string $focus
     * @return UploadedFile
     */
    public function crop(UploadedFile $file, string $fileName, $new_w, $new_h, $focus = 'center') : UploadedFile;

    /**
     * @param UploadedFile $file
     * @param string $filename
     * @return UploadedFile
     */
    public function roundImage(UploadedFile $file, string $filename) : UploadedFile;
}
