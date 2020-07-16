<?php

namespace App\Form;

use App\Entity\Panier;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AjoutPanierType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('quantite', IntegerType::class, $this->getConfiguration("Choissisez la quantité", "Vous pouvez commander jusqu'à 5 unités de cet article", [
            'attr' => [
                'min' => 1,
                'max' => 5,
                'step' => 1
            ]
        ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
        ]);
    }
}
