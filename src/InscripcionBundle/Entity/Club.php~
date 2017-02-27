<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Club
 * @ORM\Entity()
 */

class Club extends Institucion
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
        return "Club";
    }
    
    /**
     * Get Discr
     *
     * @return string 
     */
    public function getDiscr()
    {
        return "inscripcionInstitucionalClub";
    }
    
    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return "club";
    }    

    /**
     * Set responsableNombre
     *
     * @param string $responsableNombre
     *
     * @return Club
     */
    public function setResponsableNombre($responsableNombre)
    {
        $this->responsableNombre = $responsableNombre;

        return $this;
    }

    /**
     * Get responsableNombre
     *
     * @return string
     */
    public function getResponsableNombre()
    {
        return $this->responsableNombre;
    }

    /**
     * Set responsableApellido
     *
     * @param string $responsableApellido
     *
     * @return Club
     */
    public function setResponsableApellido($responsableApellido)
    {
        $this->responsableApellido = $responsableApellido;

        return $this;
    }

    /**
     * Get responsableApellido
     *
     * @return string
     */
    public function getResponsableApellido()
    {
        return $this->responsableApellido;
    }
}
