<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Reservado
 * @ORM\Entity()
 */
class Reservado extends MovimientoEstado
{
     /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDescripcion("Reservado");
        parent::__construct();
    }
    
    /**
     * Get Txt
     * 
     * @return string
     */
    public function getTxt(){
        return "Reservado";
    }
    
    /**
     * Get icon
     *
     * @return string fa-icon
     */
    public function getIcon()
    {
        return "fa-meh-o text-warning";
    }
    
    /**
     * estaReservado
     *
     * @return boolean
     */
    public function estaReservado()
    {
        return true;
    }    
}
