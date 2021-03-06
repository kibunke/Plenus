<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\PlazaPartido
 * @ORM\Entity()
 */
class PlazaPartido extends Plaza
{
    /**
     * @ORM\OneToMany(targetEntity="Partido", mappedBy="plaza1", cascade={"all"})
     */
    private $partidosLocal;

    /**
     * @ORM\OneToMany(targetEntity="Partido", mappedBy="plaza2", cascade={"all"})
     */
    private $partidosVisitante;
    
    /**
     * Add partidoLocal
     *
     * @param \ResultadoBundle\Entity\Partido $partido
     * @return Competencia
     */
    public function addPartidoLocal(\ResultadoBundle\Entity\Partido $partido)
    {
        $this->partidosLocal[] = $partido;
        $partido->setPlaza1($this);

        return $this;
    }

    /**
     * Remove partidoLocal
     *
     * @param \ResultadoBundle\Entity\Partido $partido
     */
    public function removePartidoLocal(\ResultadoBundle\Entity\Partido $partido)
    {
        $this->partidosLocal->removeElement($partido);
    }

    /** 
     * Get partidosLocal
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidosLocal()
    {
        return $this->partidosLocal;
    }
    
    /**
     * Add partidoVisitante
     *
     * @param \ResultadoBundle\Entity\Partido $partido
     * @return Competencia
     */
    public function addPartidoVisitante(\ResultadoBundle\Entity\Partido $partido)
    {
        $this->partidosVisitante[] = $partido;
        $partido->setPlaza2($this);

        return $this;
    }

    /**
     * Remove partidoVisitante
     *
     * @param \ResultadoBundle\Entity\Partido $partido
     */
    public function removePartidoVisitant(\ResultadoBundle\Entity\Partido $partido)
    {
        $this->partidosVisitante->removeElement($partido);
    }

    /** 
     * Get partidosVisitante
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidosVisitante()
    {
        return $this->partidosVisitante;
    }
    
    /** 
     * Get partidosVisitante
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidos()
    {
        return new ArrayCollection(
                                array_merge(
                                            $this->partidosLocal->toArray(),
                                            $this->partidosVisitante->toArray()
                                )
        );
    }    

    /**
     * Add partidosLocal
     *
     * @param \ResultadoBundle\Entity\Partido $partidosLocal
     *
     * @return PlazaPartido
     */
    public function addPartidosLocal(\ResultadoBundle\Entity\Partido $partidosLocal)
    {
        $this->partidosLocal[] = $partidosLocal;

        return $this;
    }

    /**
     * Remove partidosLocal
     *
     * @param \ResultadoBundle\Entity\Partido $partidosLocal
     */
    public function removePartidosLocal(\ResultadoBundle\Entity\Partido $partidosLocal)
    {
        $this->partidosLocal->removeElement($partidosLocal);
    }

    /**
     * Add partidosVisitante
     *
     * @param \ResultadoBundle\Entity\Partido $partidosVisitante
     *
     * @return PlazaPartido
     */
    public function addPartidosVisitante(\ResultadoBundle\Entity\Partido $partidosVisitante)
    {
        $this->partidosVisitante[] = $partidosVisitante;

        return $this;
    }

    /**
     * Remove partidosVisitante
     *
     * @param \ResultadoBundle\Entity\Partido $partidosVisitante
     */
    public function removePartidosVisitante(\ResultadoBundle\Entity\Partido $partidosVisitante)
    {
        $this->partidosVisitante->removeElement($partidosVisitante);
    }
}
