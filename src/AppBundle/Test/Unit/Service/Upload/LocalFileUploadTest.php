<?php

namespace AppBundle\Test\Unit\Service\Upload;

use AppBundle\Service\Upload\LocalFileUpload;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LocalFileUploadTest
 * @package AppBundle\Test\Unit\Service\Upload
 */
class LocalFileUploadTest extends TestCase
{
    public function testUploadFile()
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->once())->method('getParameter')->with('web_host')->willReturn('http://localhost');

        $bucket = 'rideshare';
        $file = tempnam(sys_get_temp_dir(), 'upl');
        $fileName = md5_file($file) . '.png';
        $dir = substr($fileName, 0, strpos($fileName, '.'));
        $to = LocalFileUpload::UPLOAD_DIR . '/' . $bucket . '/' . $dir . '/';

        $mockFileUpload = $this->createMock(UploadedFile::class);
        $mockFileUpload->expects($this->once())->method('__toString')->willReturn($file);
        $mockFileUpload->expects($this->once())->method('guessExtension')->willReturn('png');
        $mockFileUpload->expects($this->once())->method('move')->with($to, $fileName);

        $uploadService = new LocalFileUpload($mockContainer);
        $url = $uploadService->uploadFile($mockFileUpload, $bucket);

        $this->assertEquals($url, 'http://localhost/images/rideshare/origin/' . $fileName);
        unlink($file);
    }

    public function testWithCustomFileName()
    {
        $fileName = 'file.png';
        $dir = substr($fileName, 0, strpos($fileName, '.'));
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->once())->method('getParameter')->with('web_host')->willReturn('http://localhost');

        $bucket = 'rideshare';
        $file = tempnam(sys_get_temp_dir(), 'upl');
        $to = LocalFileUpload::UPLOAD_DIR . '/' . $bucket . '/' . $dir . '/';

        $mockFileUpload = $this->createMock(UploadedFile::class);
        $mockFileUpload->expects($this->never())->method('__toString');
        $mockFileUpload->expects($this->never())->method('guessExtension');
        $mockFileUpload->expects($this->once())->method('move')->with($to, $fileName);

        $uploadService = new LocalFileUpload($mockContainer);
        $url = $uploadService->uploadFile($mockFileUpload, $bucket, $fileName);

        $this->assertEquals($url, 'http://localhost/images/rideshare/origin/' . $fileName);
        unlink($file);
    }

    /**
     * @expectedException \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function testUploadFileWithException()
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->never())->method('getParameter')->with('web_host');

        $bucket = 'rideshare';
        $file = tempnam(sys_get_temp_dir(), 'upl');
        $fileName = md5_file($file) . '.png';
        $dir = substr($fileName, 0, strpos($fileName, '.'));
        $to = LocalFileUpload::UPLOAD_DIR . '/' . $bucket . '/' . $dir . '/';
       

        $mockFileUpload = $this->createMock(UploadedFile::class);
        $mockFileUpload->expects($this->once())->method('__toString')->willReturn($file);
        $mockFileUpload->expects($this->once())->method('guessExtension')->willReturn('png');
        $mockFileUpload->expects($this->once())->method('move')
            ->with($to, $fileName)
            ->willThrowException(new \Symfony\Component\HttpFoundation\File\Exception\FileException('An error was occurred.'));

        $uploadService = new LocalFileUpload($mockContainer);
        $uploadService->uploadFile($mockFileUpload, $bucket);
        unlink($file);
    }

    public function testSuccessDownload()
    {
        $bucket = 'test';
        $fileName = 'test.png';
        $dir = substr($fileName, 0, strpos($fileName, '.'));
        $url = "http://someurl/{$bucket}/{$fileName}";
        $tmpPath = __DIR__ . '/../../../../../../web/tmp/' . $bucket . '/' . $dir;

        if (!is_dir($tmpPath)) {
            mkdir($tmpPath , 0777, true);
        }

        file_put_contents($tmpPath . '/'. $fileName, '1234');

        $mockContainer = $this->createMock(ContainerInterface::class);

        $service = (new class($mockContainer) extends LocalFileUpload {
            const UPLOAD_DIR = __DIR__ . '/../../../../../../web/tmp/';
        });

        /** @var UploadedFile $file */
        $file = $service->downloadFile($url, $bucket);
        $this->assertTrue($file instanceof UploadedFile);
        $this->assertEquals($fileName, $file->getFilename());
        $this->assertEquals(4, $file->getSize());

        unlink($tmpPath . '/'. $fileName);
        rmdir($tmpPath);
    }

    /**
     * @expectedException \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function testFailedDownload()
    {
        $bucket = 'test';
        $fileName = 'test.png';
        $url = "http://someurl/{$bucket}/{$fileName}";

        $mockContainer = $this->createMock(ContainerInterface::class);
        $uploadService = new LocalFileUpload($mockContainer);
        $uploadService->downloadFile($url, $bucket);
    }
}
