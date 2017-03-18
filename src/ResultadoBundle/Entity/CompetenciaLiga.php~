<?php

namespace ResultadoBundle\Entity;

use ResultadoBundle\Form\PlazaZonaType;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaLiga
 *
 * @ORM\Entity()
 *
 */
class CompetenciaLiga extends Competencia
{    
    /**
     * @ORM\OneToMany(targetEntity="Zona", mappedBy="liga", cascade={"persist"})
     */
    private $zonas;
    
    /** 
     * Get PlazaType
     *
     * @return \ResultadoBundle\Form\PlazaZonaType
     */
    public function getPlazaType()
    {
        return new PlazaZonaType();
    }
    
    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return "Competencia\Liga";
    }
    
    /**
     * Add zona
     *
     * @param \ResultadoBundle\Entity\Zona $zona
     * @return Competencia
     */
    public function addZona(\ResultadoBundle\Entity\Zona $zona)
    {
        $this->zonas[] = $zona;
        $zona->setLiga($this);

        return $this;
    }

    /**
     * Remove plaza
     *
     * @param \ResultadoBundle\Entity\Zona $zona
     */
    public function removeZona(\ResultadoBundle\Entity\Zona $zona)
    {
        $this->zonas->removeElement($zona);
    }

    /** 
     * Get zonas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getZonas()
    {
        return $this->zonas;
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        $zonas = $this->getZonas();
        foreach ($zonas as $zona){
            $percent += $zona->getState();
        }
        if ($percent)
            $percent = $percent/count($zonas);
        
        return round($percent,1);
    }
    
    /**
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload($plaza)
    {
        return $plaza->getZona()->getId();
    }
    
    /**
     * Constructor
     */
    public function __construct($user = null, $nombre = "")
    {
        $this->zonas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nombre = $nombre;
        parent::__construct($user);
    }
    
    /**
     * Get partidos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidos()
    {
        $partidos = [];
        foreach($this->getZonas() as $zona){
            $partidos = array_merge($zona->getPartidos()->toArray(),$partidos);
        }
        return $partidos;
    }
}
