<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Presentada
 * @ORM\Entity()
 */
class Presentada extends PlanillaEstado
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setNombre("Presentada");
        parent::__construct();
    }
    
    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        if($usuario->hasRole('ROLE_COORDINADOR'))
        {
            return array(new Observada(), new Aprobada());
        }
        
        return parent::getProximosEstados($usuario);
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
        return "Pr.";
    }
}
