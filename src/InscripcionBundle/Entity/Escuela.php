<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Escuela
 * @ORM\Entity()
 */

class Escuela extends Origen
{
    /**
     * Get icono
     *
     * @return string 
     */
    public function getIcono()
    {
        return "fa fa-book";
    }
    
    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return "Escuela";
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return "Escuela";
    }    
}
