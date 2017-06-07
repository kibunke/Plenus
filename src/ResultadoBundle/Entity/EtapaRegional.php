<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\EtapaRegional
 *
 * @ORM\Entity()
 *
 */
class EtapaRegional extends Etapa
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
        return "regional";
    }

    /**
     * get tipoValor
     *
     * @return integer
     */
    public function getTipoValor()
    {
        return 444;
    }
    
    public function getNombreInicial()
    {
        return 'Etapa Regional';
    }
}
