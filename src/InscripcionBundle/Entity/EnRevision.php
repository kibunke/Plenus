<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\EnRevision
 * @ORM\Entity()
 */
class EnRevision extends PlanillaEstado
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setNombre("En Revisión");
        parent::__construct();
    }

    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        if($usuario->hasRole('ROLE_INSCRIPTOR'))
        {
            return array(new Cargada());
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
        return "Re.";
    }
}
