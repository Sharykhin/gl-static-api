<?php

namespace AppBundle\Test\Unit\Service\Image;

use AppBundle\Contract\Image\TransformInterface;
use AppBundle\Contract\Upload\UploadFileInterface;
use AppBundle\Service\Image\ImageResizeDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageResizeDecoratorTest
 * @package AppBundle\Test\Unit\Service\Image
 */
class ImageResizeDecoratorTest extends TestCase
{
    public function testUploadFile()
    {
        $mockFileUpload = $this->createMock(UploadedFile::class);
        $mockResizedFileUpload = $this->createMock(UploadedFile::class);

        $bucket = 'test';
        $filename = 'test.png';
        $width = 200;
        $height = 200;
        $focus = 'center';
        $urlToReturn = 'http://some-url/bucket/test.png';

        $mockUploadFileService = $this->createMock(UploadFileInterface::class);
        $mockTransformService = $this->createMock(TransformInterface::class);
        $mockTransformService
            ->expects($this->once())
            ->method('crop')
            ->with($mockFileUpload, $filename, $width, $height, $focus)
            ->willReturn($mockResizedFileUpload);

        $mockUploadFileService
            ->expects($this->once())
            ->method('uploadFile')
            ->with($mockResizedFileUpload, $bucket, $filename)
            ->willReturn($urlToReturn);


        $imageResizeDecorator = new ImageResizeDecorator(
            $mockUploadFileService,
            $mockTransformService,
            $width,
            $height,
            $focus
        );

        $url = $imageResizeDecorator->uploadFile($mockFileUpload, $bucket, $filename);
        $this->assertEquals($urlToReturn, $url);
    }

    public function testUploadFileWithoutFileName()
    {
        $file = tempnam(sys_get_temp_dir(), 'test_');
        $fileName = md5_file($file) . '.png';

        $mockFileUpload = $this->createMock(UploadedFile::class);
        $mockFileUpload
            ->expects($this->once())
            ->method('guessExtension')
            ->with()
            ->willReturn('png');

        $mockFileUpload
            ->expects($this->once())
            ->method('__toString')
            ->with()
            ->willReturn($file);

        $mockResizedFileUpload = $this->createMock(UploadedFile::class);

        $bucket = 'test';
        $width = 200;
        $height = 200;
        $focus = 'center';
        $urlToReturn = 'http://some-url/bucket/test.png';

        $mockUploadFileService = $this->createMock(UploadFileInterface::class);
        $mockTransformService = $this->createMock(TransformInterface::class);
        $mockTransformService
            ->expects($this->once())
            ->method('crop')
            ->with($mockFileUpload, $fileName, $width, $height, $focus)
            ->willReturn($mockResizedFileUpload);

        $mockUploadFileService
            ->expects($this->once())
            ->method('uploadFile')
            ->with($mockResizedFileUpload, $bucket, $fileName)
            ->willReturn($urlToReturn);


        $imageResizeDecorator = new ImageResizeDecorator(
            $mockUploadFileService,
            $mockTransformService,
            $width,
            $height,
            $focus
        );

        $url = $imageResizeDecorator->uploadFile($mockFileUpload, $bucket, null);
        $this->assertEquals($urlToReturn, $url);
    }
}
