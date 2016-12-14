<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Completado
 * @ORM\Entity()
 */
class Completado extends MovimientoEstado
{
     /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDescripcion("Completado");
        parent::__construct();
    }
    
    /**
     * Get Txt
     * 
     * @return string
     */
    public function getTxt(){
        return "Completado";
    }
    
    /**
     * Get icon
     *
     * @return string fa-icon
     */
    public function getIcon()
    {
        return "fa-smile-o text-success";
    }

    /**
     * estaCompletado
     *
     * @return boolean
     */
    public function estaCompletado()
    {
        return true;
    }
}
