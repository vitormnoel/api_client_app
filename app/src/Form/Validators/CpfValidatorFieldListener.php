<?php
namespace App\Form\Validators;


use App\Helper\CpfValidator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CpfValidatorFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event){
        $dataForm = $event->getData();
        $cpf = $dataForm->getCpf();
        if(!is_null($cpf)){
            $cpfValid = CpfValidator::validate($cpf);
            if(!$cpfValid){
                $form = $event->getForm();
                $form->get('cpf')->addError(new FormError("Insert Cpf Valid"));
            }
        }
    }
}