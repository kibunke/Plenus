<?php

namespace ResultadoBundle\Entity;

use ResultadoBundle\Form\PlazaType;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaOrden
 *
 * @ORM\Entity()
 *
 */
class CompetenciaOrden extends Competencia
{
    /*
     * CONSTRUCT
     */
    public function __construct($user = NULL) {
       parent::__construct($user);
    }
    
    /** 
     * Get PlazaType
     *
     * @return \ResultadoBundle\Form\PlazaType
     */
    public function getPlazaType()
    {
        return new PlazaType();
    }
    
    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return "Competencia\Orden";
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
}
