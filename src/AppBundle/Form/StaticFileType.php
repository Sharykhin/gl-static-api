<?php

namespace AppBundle\Form;

use Mockery\Matcher\Not;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class StaticFileType
 * @package AppBundle\Form
 */
class StaticFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('file', FileType::class, [
                'required' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '2048k'
                    ]),
                    new NotBlank()
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}
