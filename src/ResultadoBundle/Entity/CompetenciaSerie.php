<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ResultadoBundle\Form\PlazaSerieType;

/**
 * ResultadoBundle\Entity\CompetenciaSerie
 *
 * @ORM\Entity()
 *
 */
class CompetenciaSerie extends Competencia
{
    /**
     * @ORM\OneToMany(targetEntity="Serie", mappedBy="competencia", cascade={"persist"})
     */
    private $series;
    
    /** 
     * Get PlazaType
     *
     * @return \ResultadoBundle\Form\PlazaZonaType
     */
    public function getPlazaType()
    {
        return new PlazaSerieType();
    }
    
    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return "Competencia\Serie";
    }
    
    /**
     * Add serie
     *
     * @param \ResultadoBundle\Entity\Serie $serie
     * @return Competencia
     */
    public function addSerie(\ResultadoBundle\Entity\Serie $serie)
    {
        $this->series[] = $serie;
        $serie->setCompetencia($this);

        return $this;
    }

    /**
     * Remove serie
     *
     * @param \ResultadoBundle\Entity\Serie $serie
     */
    public function removeSerie(\ResultadoBundle\Entity\Serie $serie)
    {
        $this->series->removeElement($serie);
    }

    /** 
     * Get series
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeries()
    {
        return $this->series;
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        foreach ($this->getPlazas() as $item)
        {
            $percent += $item->getState();
        }
        if ($percent>0){
            $percent = $percent / count($this->getPlazas());
        }
        return round($percent,1);
    }
    
    /**
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload($plaza)
    {
        return $plaza->getSerie()->getId();
    }
    
    /**
     * Constructor
     */
    public function __construct($user = null, $nombre = "")
    {
        $this->series = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nombre = $nombre;
        parent::__construct($user);
    }   
}
