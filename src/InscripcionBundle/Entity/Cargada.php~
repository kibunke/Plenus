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
        return "Ca.";
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
    
    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        if($usuario->hasRole('ROLE_INSCRIPTOR'))
        {
            return array(new Enviada());
        }
        
        if($usuario->hasRole('ROLE_ORGANIZADOR'))
        {
            return array(new Presentada());
        }
        
        if($usuario->hasRole('ROLE_COORDINADOR'))
        {
            return array(new Aprobada());
        }

        return parent::getProximosEstados($usuario);
    }
}
