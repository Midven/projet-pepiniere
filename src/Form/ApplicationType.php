<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType{

    /**
     * ApplicationType hérite de AbstractType
     * ApplicationType est la pour faire en sorte de ne pas dupliquer du code des formulaires quand celui ci
     * doit être réutilisé plusieurs fois, ca permet de changer ce qu'il y a en commun dans les différents
     * formtype en une seule fois via ce fichier
     */


    /**
     * Permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    protected function getConfiguration($label, $placeholder, $options = []){
        // array merge va fusionner le premier tableau avec le tableau des options
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }
}
