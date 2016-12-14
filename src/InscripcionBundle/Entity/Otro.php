<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Otro
 * @ORM\Entity()
 */

class Otro extends Origen
{
    /**
     * Get icono
     *
     * @return string 
     */
    public function getIcono()
    {
        return "fa fa-dribbble";
    }
    
    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return "Otra institución";
    }
    
    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return "Otro";
    }    
}
