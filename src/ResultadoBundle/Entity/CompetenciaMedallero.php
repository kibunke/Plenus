<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ResultadoBundle\Form\PlazaMedalleroType;

/**
 * ResultadoBundle\Entity\CompetenciaMedallero
 *
 * @ORM\Entity()
 *
 */
class CompetenciaMedallero extends Competencia
{ 
    /**
     * Construct
     */
    public function __construct($user = NULL) {
        $this->setNombre("Medallero");
        $this->addPlazas(new PlazaMedallero($user,"Medalla de Oro",1));
        $this->addPlazas(new PlazaMedallero($user,"Medalla de Plata",2));
        $this->addPlazas(new PlazaMedallero($user,"Medalla de Bronce",3));
        parent::__construct($user);
    }
    
    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return "Competencia\Medallero";
    }

    /** 
     * Get PlazaType
     *
     * @return \ResultadoBundle\Form\PlazaZonaType
     */
    public function getPlazaType()
    {
        return new PlazaMedalleroType();
    }
    
    /**
     * Get folder
     *
     * @return string
     */
    public function getMedalla($plaza)
    {
        switch ($plaza->getOrden()){
            case 1: return "bundles/resultado/images/medallas/oro_thumb.png";
            case 2: return "bundles/resultado/images/medallas/plata_thumb.png";
            case 3: return "bundles/resultado/images/medallas/bronce_thumb.png";
            default: return null;
        }
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        $plazas =$this->getPlazas();
        $plazaAsignada = 0;
        foreach ($plazas as $plaza){
            if ($plaza->getEquipo())
                $plazaAsignada++;
        }
        if ($plazaAsignada)
            $percent = ($plazaAsignada*100)/count($plazas);
        //return $percent;
        return round($percent,1);
    }

    /**
     * Get performance
     *
     * @return string
     */
    public function getPerformance($plaza)
    {
        switch ($plaza->getOrden()){
            case 1: return "Medalla de Oro";
            case 2: return "Medalla de Plata";
            case 3: return "Medalla de Bronce";
            default: return null;
        }
    }    
}
