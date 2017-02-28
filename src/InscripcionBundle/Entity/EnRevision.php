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

    /**
     * Get isRemovable
     *
     * @return boolean
     */
    public function isRemovable()
    {
        return true;
    }
    
    /**
     * Get isEditable
     *
     * @return boolean
     */
    public function isEditable()
    {
        return true;
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
        return "badge badge-default";
    }
    
    /**
     * get Class
     */    
    public function getAbr()
    {
        return "Re.";
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
        return 'planilla_toggle_enrevision';
    }
    
    /**
     * get ClassButton
     */    
    public function getClassButton()
    {
        return "danger";
    }

    /**
     * get Title
     */    
    public function getTitleBefore()
    {
        return "Revisar planilla";
    }    
}
