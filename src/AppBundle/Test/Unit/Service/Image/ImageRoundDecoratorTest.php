<?php

namespace AppBundle\Test\Unit\Service\Image;

use AppBundle\Contract\Image\TransformInterface;
use AppBundle\Contract\Upload\UploadFileInterface;
use AppBundle\Service\Image\ImageRoundDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageRoundDecoratorTest
 * @package AppBundle\Test\Unit\Service\Image
 */
class ImageRoundDecoratorTest extends TestCase
{
    public function testUploadFile()
    {
        $mockUploadFile = $this->createMock(UploadedFile::class);
        $mockTransformedFile = $this->createMock(UploadedFile::class);
        $bucket = 'test';
        $fileName = 'test.png';
        $returnUrl = 'http://some-url/test/test.png';

        $mockTransformService = $this->createMock(TransformInterface::class);
        $mockTransformService
            ->expects($this->once())
            ->method('roundImage')
            ->with($mockUploadFile, $fileName)
            ->willReturn($mockTransformedFile);

        $mockUploadFileService = $this->createMock(UploadFileInterface::class);
        $mockUploadFileService
            ->expects($this->once())
            ->method('uploadFile')
            ->with($mockTransformedFile, $bucket, $fileName)
            ->willReturn($returnUrl);

        $roundDecorator = new ImageRoundDecorator(
            $mockUploadFileService,
            $mockTransformService
        );

        $url = $roundDecorator->uploadFile($mockUploadFile, $bucket, $fileName);
        $this->assertEquals($returnUrl, $url);
    }

    public function testUploadFileWithoutFileName()
    {
        $file = tempnam(sys_get_temp_dir(), 'test_');
        $fileName = md5_file($file) . '.png';

        $mockUploadFile = $this->createMock(UploadedFile::class);
        $mockUploadFile
            ->expects($this->once())
            ->method('guessExtension')
            ->with()
            ->willReturn('png');

        $mockUploadFile
            ->expects($this->once())
            ->method('__toString')
            ->with()
            ->willReturn($file);

        $mockTransformedFile = $this->createMock(UploadedFile::class);
        $bucket = 'test';
        $returnUrl = 'http://some-url/test/test.png';

        $mockTransformService = $this->createMock(TransformInterface::class);
        $mockTransformService
            ->expects($this->once())
            ->method('roundImage')
            ->with($mockUploadFile, $fileName)
            ->willReturn($mockTransformedFile);

        $mockUploadFileService = $this->createMock(UploadFileInterface::class);
        $mockUploadFileService
            ->expects($this->once())
            ->method('uploadFile')
            ->with($mockTransformedFile, $bucket, $fileName)
            ->willReturn($returnUrl);

        $roundDecorator = new ImageRoundDecorator(
            $mockUploadFileService,
            $mockTransformService
        );

        $url = $roundDecorator->uploadFile($mockUploadFile, $bucket, null);
        $this->assertEquals($returnUrl, $url);
    }
}
