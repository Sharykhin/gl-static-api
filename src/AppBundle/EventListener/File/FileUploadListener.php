<?php

namespace AppBundle\EventListener\File;

use AppBundle\Event\FileUploadedEvent;

/**
 * Class FileUploadListener
 * @package AppBundle\EventListener\File
 */
class FileUploadListener
{
    /**
     * @param FileUploadedEvent $event
     */
    public function onFileUploaded(FileUploadedEvent $event) : void
    {
        // Do nothing...
    }
}
