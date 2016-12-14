<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaEliminacionDirectaTantosCartas
 *
 * @ORM\Entity()
 *
 */
class CompetenciaEliminacionDirectaTantosCartas extends CompetenciaEliminacionDirecta
{
    /*
     * CONSTRUCT
     */
    public function __construct($user = NULL) {
       parent::__construct($user);
    }
    
    /** 
     * Get newPartido
     *
     * @return \ResultadoBundle\Entity\Partido
     */
    public function newPartido($user = null, $nombre = "")
    {
        return new PartidoTantosCartas($user,$nombre);
    }  
}
