<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Municipio
 * @ORM\Entity()
 */

class Municipio extends Origen
{
    /**
     * Get icono
     *
     * @return string 
     */
    public function getIcono()
    {
        return "fa fa-building-o";
    }
    
    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return "Municipal";
    }
    
    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return "Municipio";
    }        
}
