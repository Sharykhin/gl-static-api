<?php

namespace AppBundle\Service\Image;

use AppBundle\Contract\Image\TransformInterface;
use Imagick;
use ImagickDraw;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImagickImageTransform
 * @package AppBundle\Service\Image
 */
class ImagickImageTransform implements TransformInterface
{
    /**
     * @param UploadedFile $file
     * @param string $fileName
     * @param $width
     * @param $height
     * @param bool $crop
     * @return UploadedFile
     */
    public function resizeAndCrop(UploadedFile $file, string $fileName, $width, $height, $crop = false) : UploadedFile
    {
        // TODO: this method is not testable just because we use nw for Imagick and for UploadedFile
        $image = new Imagick($file->getRealPath());
        $ratio = $width / $height;

        // Original image dimensions.
        $old_width = $image->getImageWidth();
        $old_height = $image->getImageHeight();
        $old_ratio = $old_width / $old_height;

        // Determine new image dimensions to scale to.
        // Also determine cropping coordinates.
        if ($ratio > $old_ratio) {
            $new_width = $width;
            $new_height = $width / $old_width * $old_height;
            $crop_x = 0;
            $crop_y = intval(($new_height - $height) / 2);
        }
        else {
            $new_width = $height / $old_height * $old_width;
            $new_height = $height;
            $crop_x = intval(($new_width - $width) / 2);
            $crop_y = 0;
        }

        // Scale image to fit minimal of provided dimensions.
        $image->resizeImage($new_width, $new_height, imagick::FILTER_LANCZOS, 0.9, true);
        if ($crop) {
            // Now crop image to exactly fit provided dimensions.
            $image->cropImage($new_width, $new_height, $crop_x, $crop_y);
        }

        $image->writeImage(sys_get_temp_dir() . '/' . $fileName);
        $image->destroy();
        return new UploadedFile(sys_get_temp_dir() . '/' . $fileName, $fileName, $file->getClientMimeType(), null, null, true);
    }

    /**
     * @param UploadedFile $file
     * @param string $fileName
     * @param $new_w
     * @param $new_h
     * @param string $focus
     * @return UploadedFile
     */
    public function crop(UploadedFile $file, string $fileName, $new_w, $new_h, $focus = 'center') : UploadedFile
    {
        // TODO: this method is not testable just because we use nw for Imagick and for UploadedFile
        $image = new Imagick($file->getRealPath());
        $w = $image->getImageWidth();
        $h = $image->getImageHeight();

        if ($w > $h) {
            $resize_w = $w * $new_h / $h;
            $resize_h = $new_h;
        }
        else {
            $resize_w = $new_w;
            $resize_h = $h * $new_w / $w;
        }
        $image->resizeImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0.9);

        switch ($focus) {
            case 'northwest':
                $image->cropImage($new_w, $new_h, 0, 0);
                break;

            case 'center':
                $image->cropImage($new_w, $new_h, ($resize_w - $new_w) / 2, ($resize_h - $new_h) / 2);
                break;

            case 'northeast':
                $image->cropImage($new_w, $new_h, $resize_w - $new_w, 0);
                break;

            case 'southwest':
                $image->cropImage($new_w, $new_h, 0, $resize_h - $new_h);
                break;

            case 'southeast':
                $image->cropImage($new_w, $new_h, $resize_w - $new_w, $resize_h - $new_h);
                break;
        }

        $image->writeImage(sys_get_temp_dir() . '/' . $fileName);
        $image->destroy();
        return new UploadedFile(sys_get_temp_dir() . '/' . $fileName, $fileName, $file->getClientMimeType(), null, null, true);
    }

    /**
     * @param UploadedFile $file
     * @param string $filename
     * @return UploadedFile
     */
    public function roundImage(UploadedFile $file, string $filename) : UploadedFile
    {
        // TODO: this method is not testable just because we use nw for Imagick and for UploadedFile
        $image = new Imagick($file->getRealPath());
        list($width, $height) = [$image->getImageWidth(), $image->getImageHeight()];

        if ($width !== $height) {

            if ($width > $height) {
                $width = $height;
            } else {
                $height = $width;
            }

            $image->cropImage($width, $height, 0, 0);
        }

        list($width, $height) = [$image->getImageWidth(), $image->getImageHeight()];

        $circle = new Imagick();
        $circle->newImage($width, $height, '#fff');
        $circle->setImageMatte(true);

        $draw = new ImagickDraw();
        $draw->setFillColor('#000');
        $draw->circle(round($width / 2), round($height / 2), round($width / 2), $width);
        $circle->drawImage($draw);
        $image->compositeImage($circle, Imagick::COMPOSITE_SCREEN, 0, 0);

        $image->writeImage(sys_get_temp_dir() . '/' . $filename);
        $image->destroy();
        return new UploadedFile(sys_get_temp_dir() . '/' . $filename, $filename, $file->getClientMimeType(), null, null, true);
    }
}
