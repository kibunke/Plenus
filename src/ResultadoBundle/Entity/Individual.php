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
        return '<div style="color:#555"><strong>'.$this->getIntegrantes()->first()->getNombreCompleto().'</strong></div>';
    }
}
