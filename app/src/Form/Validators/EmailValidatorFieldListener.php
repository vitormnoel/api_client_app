<?php
namespace App\Form\Validators;

use App\Helper\FormResources;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EmailValidatorFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event){
        $dataForm = $event->getData();
        $email = is_array($dataForm) ? $dataForm['email'] : $dataForm?->getEmail();
        if(!FormResources::validEmail($email)){
            $form = $event->getForm();
            $form->get('email')->addError(new FormError("Insert Email Valid"));
        }
    }
}