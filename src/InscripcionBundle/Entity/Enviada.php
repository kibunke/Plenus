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
        $this->setNombre("Enviada");
        parent::__construct();
    }
    
    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        if($usuario->hasRole('ROLE_ORGANIZADOR'))
        {
            return array(new Presentada());
        }
        
        return parent::getProximosEstados($usuario);
    }
    
     /**
     * get Class
     */    
    public function getClass()
    {
        return "badge badge-info";
    }
    
    /**
     * get Class
     */    
    public function getAbr()
    {
        return "En.";
    }

    /**
     * get icon
     */    
    public function getIcon()
    {
        return "share";
    }
    
    /**
     * Get getRoute
     *
     * @return string
     */
    public function getRoute()
    {
        return 'planilla_toggle_enviada';
    }
    
    /**
     * get ClassButton
     */    
    public function getClassButton()
    {
        return "info";
    }

    /**
     * get Title
     */    
    public function getTitleBefore()
    {
        return "Enviar planilla";
    }
}
