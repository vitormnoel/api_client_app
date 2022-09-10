<?php

namespace App\Form;

use App\Form\Validators\EmailValidatorFieldListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new EmailValidatorFieldListener())
            ->add('email',EmailType::class,[
                'required'   => true,
                'constraints' => [
                    new NotBlank(message: "Insert Email Address!!")
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            // Configure your form options here
        ]);
    }
}
