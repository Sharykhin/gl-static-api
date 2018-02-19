<?php

namespace AppBundle\Test\Unit\Service\Validation;

use AppBundle\Form\StaticFileType;
use AppBundle\Service\Validation\FormFileValidation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class FormFileValidationTest
 * @package AppBundle\Test\Unit\Service\Validation
 */
class FormFileValidationTest extends TestCase
{
    public function testFailValidate()
    {
        $mockUploadedFile = $this->createMock(UploadedFile::class);
        $mockForm = $this->createMock(FormInterface::class);

        $mockPropertyPath = $this->createMock(PropertyPathInterface::class);
        $mockPropertyPath->expects($this->once())->method('getElement')->with(0)->willReturn('file');

        $mockFormError = $this->createMock(FormError::class);
        $mockFormError->expects($this->once())->method('getMessage')->willReturn('file is too big');
        $mockFormError->expects($this->once())->method('getOrigin')->willReturn($mockForm);

        $mockForm->expects($this->once())->method('getPropertyPath')->willReturn($mockPropertyPath);
        $mockForm->expects($this->once())->method('submit')->with(['file' => $mockUploadedFile])->willReturnSelf();
        $mockForm->expects($this->once())->method('isValid')->willReturn(false);

        $items = new \ArrayIterator([$mockFormError]);
        $mockForm->expects($this->once())->method('getErrors')->with(true)->willReturn($items);

        $mockFormFactory = $this->createMock(FormFactoryInterface::class);
        $mockFormFactory->expects($this->once())->method('create')->with(StaticFileType::class)->willReturn($mockForm);

        $validator = new FormFileValidation($mockFormFactory);
        $errors = $validator->validate($mockUploadedFile);
        $this->assertEquals(json_encode($errors), json_encode(['file' => 'file is too big']));
    }

    public function testSuccessValidate()
    {
        $mockUploadedFile = $this->createMock(UploadedFile::class);

        $mockForm = $this->createMock(FormInterface::class);
        $mockForm->expects($this->once())->method('submit')->with(['file' => $mockUploadedFile])->willReturnSelf();
        $mockForm->expects($this->once())->method('isValid')->willReturn(true);

        $mockForm->expects($this->never())->method('getErrors');

        $mockFormFactory = $this->createMock(FormFactoryInterface::class);
        $mockFormFactory->expects($this->once())->method('create')->with(StaticFileType::class)->willReturn($mockForm);

        $validator = new FormFileValidation($mockFormFactory);
        $errors = $validator->validate($mockUploadedFile);
        $this->assertTrue(empty($errors));
    }
}
