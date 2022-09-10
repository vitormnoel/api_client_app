<?php


namespace App\Helper;


use Symfony\Component\Form\FormInterface;

class FormResources
{
    public static function getErrors(FormInterface $form):array
    {
        $errors = [];
        foreach ($form->getErrors(true,true) as $error){
            $propertyName = $error->getOrigin()->getName();
            $errors[$propertyName] = $error->getMessage();
        }
        return $errors;
    }

    public static function validEmail(?string $email):bool
    {
       return filter_var($email, FILTER_VALIDATE_EMAIL);
    }




}