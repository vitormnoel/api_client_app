<?php

namespace App\Form;

use App\Attribute\QuestionAttribute;
use App\Entity\Flow;
use App\Entity\QuestionAverage;
use App\Entity\QuestionBoolean;
use App\Entity\QuestionOption;
use App\Interface\QuestionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enunciation',TextType::class,[
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 8, 'max' => 300]),
                ],
            ])
            ->add('flow',EntityType::class,[
                'class' => Flow::class,
                'constraints' => [
                    new NotBlank(message: 'Need to insert a flow')
                ],
            ])
            ->add('type_answer',ChoiceType::class,[
                'mapped' => false,
                'required' => true,
                'choices' => array_flip(QuestionAttribute::MAP),
                'invalid_message' => 'Select an OPTION: '.implode(', ',array_keys(QuestionAttribute::MAP)),
                'constraints' => [
                    new NotBlank(message: 'Select an OPTION: '.implode(', ',array_keys(QuestionAttribute::MAP)))
                ],
            ])
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            )
        ;
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();
        if(!array_key_exists('type_answer',$data)){
            return;
        }
        if(!array_key_exists($data['type_answer'],QuestionAttribute::MAP)){
            return;
        }
        match (QuestionAttribute::MAP[$data['type_answer']]){
          QuestionBoolean::class => $this->addBooleanProperties($form),
          QuestionAverage::class => $this->addAverageProperties($form),
          QuestionOption::class => $this->addOptionProperties($form),
        };
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionInterface::class,
            'csrf_protection' => false,
        ]);
    }

    private function addBooleanProperties(FormInterface $form){

    }

    private function addAverageProperties(FormInterface $form)
    {
        $form->add('init_range',NumberType::class,[
            'required' => true,
            'constraints' => [
                new NotBlank(message: 'Need to insert a init range')
            ],
        ]);
        $form->add('end_range',NumberType::class,[
            'required' => true,
            'constraints' => [
                new NotBlank(message: 'Need to insert a end range')
            ],
        ]);
    }

    private function addOptionProperties(FormInterface $form)
    {
        $form->add('options',CollectionType::class,[
            'allow_add' => true,
            'allow_delete' => true,
        ]);
        $form->add('interaction',CheckboxType::class);
    }
}
