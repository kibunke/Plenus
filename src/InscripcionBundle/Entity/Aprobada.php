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
        return "badge badge-success";
    }

    /**
     * get Class
     */
    public function getAbr()
    {
        return "Ap.";
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
        return 'planilla_toggle_aprobada';
    }

    /**
     * get ClassButton
     */
    public function getClassButton()
    {
        return "success";
    }

    /**
     * get Title
     */
    public function getTitleBefore()
    {
        return "Aprobar planilla";
    }

    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        if($usuario->hasRole('ROLE_ADMIN'))
        {
            return array(new Observada());
        }

        return parent::getProximosEstados($usuario);
    }

    public function isAprobada(){
        return true;
    }
}
