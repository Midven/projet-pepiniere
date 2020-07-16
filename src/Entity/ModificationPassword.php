<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModificationPasswordRepository;

/**
 * ModificationPassword n'est plus une entité mais une simple classe php
 * car on a retiré toute les annotations ORM
 */
class ModificationPassword
{
    
    private $ancienpassword;

    /**
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit faire au moins 8 caractères")
     */
    private $nouveaupassword;

    /**
     * @Assert\EqualTo(propertyPath="nouveaupassword", message="Vous n'avez pas correctement confirmé votre nouveau mot de passe.")
     */
    private $confirmationpassword;


    public function getAncienpassword(): ?string
    {
        return $this->ancienpassword;
    }

    public function setAncienpassword(string $ancienpassword): self
    {
        $this->ancienpassword = $ancienpassword;

        return $this;
    }

    public function getNouveaupassword(): ?string
    {
        return $this->nouveaupassword;
    }

    public function setNouveaupassword(string $nouveaupassword): self
    {
        $this->nouveaupassword = $nouveaupassword;

        return $this;
    }

    public function getConfirmationpassword(): ?string
    {
        return $this->confirmationpassword;
    }

    public function setConfirmationpassword(string $confirmationpassword): self
    {
        $this->confirmationpassword = $confirmationpassword;

        return $this;
    }
}
