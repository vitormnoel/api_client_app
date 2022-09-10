<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Flow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FlowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('description',TextType::class)
            ->add('department',EntityType::class,[
                'class' => Department::class,
                'constraints' => [
                    new NotBlank(message: 'Need to insert a department')
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Flow::class,
            'csrf_protection' => false,
        ]);
    }
}
