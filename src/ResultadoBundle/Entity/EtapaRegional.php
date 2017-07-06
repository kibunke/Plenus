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
     * Construct
     */
    public function __construct($user = NULL)
    {
       $this->orden     = 1;
       parent::__construct($user);
    }

    /**
     * get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return "fa fa-angle-double-right";
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

    /**
     * isEtapaRegional
     * @return boolean
     */
    public function isEtapaRegional()
    {
        return true;
    }

    /**
     * isEtapaRegional
     * @return boolean
     */
    public function hayGanadorEnRegion($region)
    {
        foreach ($this->getEquipos() as $equipo) {
            if ($equipo->getPlanilla()->getMunicipio()->getRegionDeportiva() == $region){
                //throw new \Exception('Plenus: Este evento ya tiene un ganador en la region '.$region. '-'.$equipo->getPlanilla()->getMunicipio()->getRegionDeportiva().'-'.$equipo->getid(). '-'.$this->getId());
                return true;
            }
        }
        return false;
    }

    public function validarGanadorRegional($equipo)
    {
        $region = $equipo->getPlanilla()->getMunicipio()->getRegionDeportiva();
        if ($this->hayGanadorEnRegion($region)){
            throw new \Exception('Plenus: Este evento ya tiene un ganador en la region '.$region);
        }
    }
}
