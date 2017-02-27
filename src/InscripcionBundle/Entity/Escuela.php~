<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Escuela
 * @ORM\Entity()
 */

class Escuela extends Institucion
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
     * Get Discr
     *
     * @return string 
     */
    public function getDiscr()
    {
        return "inscripcionInstitucionalEscuela";
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

    /**
     * Set responsableNombre
     *
     * @param string $responsableNombre
     *
     * @return Escuela
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
     * @return Escuela
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
