<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Enviada
 * @ORM\Entity()
 */
class Enviada extends PlanillaEstado
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nombre = "Enviada";
        $this->createdAt = new \DateTime();
    }

    /**
     * __toString
     */    
    public function __toString()
    {
        return "Estado ".$this->getNombre();
    }
}
