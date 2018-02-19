<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FileUploadedEvent
 * @package AppBundle\Event
 */
class FileUploadedEvent extends Event
{
    const NAME = 'file.uploaded';

    /** @var string $url */
    protected $url;

    /** @var string $bucket */
    protected $bucket;

    /**
     * FileUploadedEvent constructor.
     * @param string $url
     * @param string $bucket
     */
    public function __construct(
        string $url,
        string $bucket
    )
    {
        $this->url = $url;
        $this->bucket = $bucket;
    }

    /**
     * @return string
     */
    public function getFileUrl() : string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getBucket() : string
    {
        return $this->bucket;
    }
}
