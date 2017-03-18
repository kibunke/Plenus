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
     * Add equipoCompetidore
     *
     * @param \ResultadoBundle\Entity\EquiposCompetidores $equipoCompetidore
     *
     * @return Individual
     */
    public function addEquipoCompetidore(\ResultadoBundle\Entity\EquiposCompetidores $equipoCompetidore)
    {
        $this->equipoCompetidores[] = $equipoCompetidore;

        return $this;
    }

    /**
     * Remove equipoCompetidore
     *
     * @param \ResultadoBundle\Entity\EquiposCompetidores $equipoCompetidore
     */
    public function removeEquipoCompetidore(\ResultadoBundle\Entity\EquiposCompetidores $equipoCompetidore)
    {
        $this->equipoCompetidores->removeElement($equipoCompetidore);
    }

    /**
     * Get equipoCompetidores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEquipoCompetidores()
    {
        return $this->equipoCompetidores;
    }
}
