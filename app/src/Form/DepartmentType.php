<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DepartmentType extends AbstractType
{
    private NativePasswordHasher $passwordEncoder;

    public function __construct(NativePasswordHasher $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Different passwords.',
                'constraints' => [
                    new Length(['min' => 8, 'max' => 20]),
                    new NotBlank(['message' => 'Insert password for Department'])
                ]
            ])
            ->add('description',TextType::class)
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [$this, 'onSubmit']
            )
        ;
    }

    public function onPreSetData(FormEvent $event): void
    {
        /** @var Department $department */
        $department = $event->getData();
        $form = $event->getForm();
        if($department->getId()){
            $form->remove('password');
        }
    }

    public function onSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        if($form->isValid()){
            /** @var Department $department */
            $department = $event->getData();
            if(!$department->getId()){
                $department->setPassword($this->passwordEncoder->hash($department->getPassword()));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
            'csrf_protection' => false,
        ]);
    }
}
