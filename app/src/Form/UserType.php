<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Validators\CpfValidatorFieldListener;
use App\Form\Validators\EmailValidatorFieldListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new CpfValidatorFieldListener())
            ->addEventSubscriber(new EmailValidatorFieldListener())
            ->add('name',TextType::class,[
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('cell_phone',TextType::class,[
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('cpf',TextType::class,[
                'required'   => true,
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('email',EmailType::class,[
                'required'   => true,
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Different passwords.',
                'constraints' => [
                    new Length(['min' => 8, 'max' => 20]),
                    new NotBlank(['message' => 'Insert password for User'])
                ]
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )

            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [$this, 'onSubmitSetData']
            )
        ;
    }

    public function onSubmitSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        if($form->isValid()){
            /** @var User $user */
            $user = $event->getData();
            if(!$user->getId()){
                $user = $event->getData();
                $user->setPassword($this->passwordEncoder->hashPassword($user,$user->getPassword()));
            }
        }
    }

    public function onPreSetData(FormEvent $event): void
    {
        $this->checkEdit($event);

    }

    private function checkEdit($event){
        /** @var User $user */
        $user = $event->getData();
        $form = $event->getForm();
        if($user->getId()){
            $form->remove('password');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
