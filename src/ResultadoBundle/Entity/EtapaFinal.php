<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\EtapaFinal
 *
 * @ORM\Entity()
 *
 */
class EtapaFinal extends Etapa
{
    /**
     * isFinal
     *
     * @return boolean 
     */
    public function isFinal()
    {
        return true;
    }
    
    /**
     * get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return "fa fa-trophy";
    }
    
    /**
     * get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return "final";
    }
    
    /**
     * get tipoValor
     *
     * @return integer
     */
    public function getTipoValor()
    {
        return 7;
    }
    
    public function getNombreInicial()
    {
        return 'Etapa Final';
    }
}
