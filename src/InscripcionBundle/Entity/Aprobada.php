<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Aprobada
 * @ORM\Entity()
 */
class Aprobada extends PlanillaEstado
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setNombre("Aprobada");
        parent::__construct();
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
        return "Ap.";
    }
}
