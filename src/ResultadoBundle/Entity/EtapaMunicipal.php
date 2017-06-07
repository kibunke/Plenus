<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\EtapaMunicipal
 *
 * @ORM\Entity()
 *
 */
class EtapaMunicipal extends Etapa
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
        return "municipal";
    }

    /**
     * get tipoValor
     *
     * @return integer
     */
    public function getTipoValor()
    {
        return 44;
    }
    
    public function getNombreInicial()
    {
        return 'Etapa Municipal';
    }
}
