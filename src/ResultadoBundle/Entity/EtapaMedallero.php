<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\EtapaMedallero
 *
 * @ORM\Entity()
 *
 */
class EtapaMedallero extends Etapa
{
    /**
     * Construct
     */
    public function __construct($user = NULL) {
       $this->setCompetencia( new CompetenciaMedallero($user));
       parent::__construct($user);
    }
    
    /**
     * get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return "fa fa-asterisk";
    }
    
    /**
     * get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return "medallero";
    }
    
    /**
     * get tipoValor
     *
     * @return integer
     */
    public function getTipoValor()
    {
        return 10;
    }
    
    public function getNombreInicial()
    {
        return 'Etapa Medallero';
    }
}
