<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Cargada
 * @ORM\Entity()
 */
class Cargada extends PlanillaEstado
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setNombre("Cargada");
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * __toString
     */    
    public function __toString()
    {
        return "Estado ".$this->getNombre();
    }
    
    /**
     * get Class
     */    
    public function getClass()
    {
        return "badge badge-teal";
    }
    
    /**
     * get Class
     */    
    public function getAbr()
    {
        return "Ca";
    }
    
    /**
     * Get isRemovable
     *
     * @return boolean
     */
    public function isRemovable()
    {
        return true;
    }     
}
