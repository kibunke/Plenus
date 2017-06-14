<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Individual
 * @ORM\Entity()
 */

class Individual extends Equipo
{
    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getPlanilla()->getMunicipio()->getRegionDeportiva()." - ".$this->getPlanilla()->getMunicipio()->getNombre()." - ".$this->getIntegrantes()->first()->getNombreCompleto();
    }

    /**
     * Get nombreCompletoRaw
     *
     * @return string
     */
    public function getNombreCompletoRaw()
    {
        return "<strong>".$this->getPlanilla()->getMunicipio()."</strong><br><small>".$this->getIntegrantes()->first()->getNombreCompleto()."</small>";
    }
}
