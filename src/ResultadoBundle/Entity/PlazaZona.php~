<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\PlazaZona
 * @ORM\Entity()
 */ 
class  PlazaZona extends PlazaPartido
{    
    /**
     * @ORM\ManyToOne(targetEntity="Zona", inversedBy="plazas")
     * @ORM\JoinColumn(name="zona", referencedColumnName="id")
     */
    private $zona;

    /**
     * Set zona
     *
     * @param \ResultadoBundle\Entity\Zona $zona
     * @return Plaza
     */
    public function setZona(\ResultadoBundle\Entity\Zona $zona = null)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona
     *
     * @return \ResultadoBundle\Entity\Zona 
     */
    public function getZona()
    {
        return $this->zona;
    }
}
