<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ModificationPasswordType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ancienpassword', PasswordType::class, $this->getConfiguration("Ancien mot de passe", "Donnez votre mot de passe actuel"))
            ->add('nouveaupassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Donnez votre nouveau mot de passe"))
            ->add('confirmationpassword', PasswordType::class, $this->getConfiguration("Confirmation nouveau mot de passe", "Confirmez votre nouveau mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
