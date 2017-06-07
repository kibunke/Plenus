<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\EtapaClasificacion
 *
 * @ORM\Entity()
 *
 */
class EtapaClasificacion extends Etapa
{    
    /**
     * get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return "fa fa-table";
    }

    /**
     * get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return "clasificación";
    }
    
    /**
     * get tipoValor
     *
     * @return integer
     */
    public function getTipoValor()
    {
        return 4;
    }
    
    public function getNombreInicial()
    {
        return 'Etapa de Clasificación';
    }
}
