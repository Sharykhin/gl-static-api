<?php

namespace AppBundle\Contract\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FileValidatorInterface
 * @package AppBundle\Contract\Validation
 */
interface FileValidatorInterface
{
    /**
     * @param null|UploadedFile $file
     * @return array
     */
    public function validate(?UploadedFile $file) : array;
}
