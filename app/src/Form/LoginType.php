<?php

namespace App\Form;

use App\Form\Validators\EmailValidatorFieldListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
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
            ->add('password',PasswordType::class,[
                'required'   => true,
                'constraints' => [
                    new NotBlank(message: "Insert Password!!")
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
