<?php
namespace App\Form\Validators;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ZipcodeValidatorFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event){
        $dataForm = $event->getData();
        $cpfValid = self::ZipCodeValidator($dataForm['zipcode']);
        if(!$cpfValid){
            $form = $event->getForm();
            $form->get('zipcode')->addError(new FormError("Insert Cep Valid"));
        }
    }

    private static function ZipCodeValidator(?string $cep): bool|int
    {
        $cep = trim($cep);
        return preg_match('/^[0-9]{5,5}([- ]?[0-9]{3,3})?$/', $cep);
    }
}