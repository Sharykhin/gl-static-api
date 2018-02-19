<?php

namespace AppBundle\Service\Validation;

use AppBundle\Contract\Validation\FileValidatorInterface;
use AppBundle\Form\StaticFileType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FormFileValidation
 * @package AppBundle\Service\Validation
 */
class FormFileValidation implements FileValidatorInterface
{
    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;

    /***
     * FormFileValidation constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        FormFactoryInterface $formFactory
    )
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param null|UploadedFile $file
     * @return array
     */
    public function validate(?UploadedFile $file): array
    {
        $err = [];
        $form = $this->formFactory->create(StaticFileType::class)->submit(['file' => $file]);
        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $err[$error->getOrigin()->getPropertyPath()->getElement(0)] = $error->getMessage();
            }
        }
        return $err;
    }
}
