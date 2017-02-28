<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Observada
 * @ORM\Entity()
 */
class Observada extends PlanillaEstado
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setNombre("Observada");
        parent::__construct();
    }

    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        if($usuario->hasRole('ROLE_ORGANIZADOR'))
        {
            return array(new EnRevision());
        }
        
        return parent::getProximosEstados($usuario);
    }
    
    /**
     * get Class
     */    
    public function getClass()
    {
        return "badge badge-warning";
    }
    
    /**
     * get Class
     */    
    public function getAbr()
    {
        return "Ob.";
    }
    
    /**
     * get icon
     */    
    public function getIcon()
    {
        return "reply";
    }
    
    /**
     * Get getRoute
     *
     * @return string
     */
    public function getRoute()
    {
        return 'planilla_toggle_observada';
    }
    
    /**
     * get ClassButton
     */    
    public function getClassButton()
    {
        return "warning";
    }

    /**
     * get Title
     */    
    public function getTitleBefore()
    {
        return "Observar planilla";
    }    
}
