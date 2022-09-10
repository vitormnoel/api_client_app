<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Flow;
use App\Entity\Question;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reply',TextType::class,[
                'mapped' => false,
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('question',EntityType::class,[
                'class' => Question::class,
                'invalid_message' => 'invalid question',
                'constraints' => [
                    new NotBlank(message: 'Need to insert a Question')
                ],
            ])
            ->add('session',EntityType::class,[
                'class' => Session::class,
                'invalid_message' => 'invalid session',
                'constraints' => [
                    new NotBlank(message: 'Need to insert a Session')
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'csrf_protection' => false,
        ]);
    }
}
