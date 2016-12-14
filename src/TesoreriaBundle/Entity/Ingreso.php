<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Ingreso
 * @ORM\Entity()
 */
class Ingreso extends Movimiento{
    /**
     * Get tipo
     * 
     * @return string Tipo de movimiento
     */
    public function getTipo(){
        return "Ingreso";
    }
}
