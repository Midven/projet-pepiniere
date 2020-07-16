<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class InscriptionType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, $this->getConfiguration("Nom", "Votre nom"))
            ->add('prenom', TextType::class, $this->getConfiguration("Prénom", "Votre prénom"))
            ->add('pays', TextType::class, $this->getConfiguration("Pays", "Indiquez votre pays"))
            ->add('ville', TextType::class, $this->getConfiguration("Ville", "Indiquez votre ville"))
            ->add('rue', TextType::class, $this->getConfiguration("Rue", "Indiquez votre rue"))
            ->add('numerorue', IntegerType::class, $this->getConfiguration("Numéro", "Indiquez votre numéro de rue"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Indiquez un émail valide"))
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Choisissez votre mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
