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
     * Construct
     */
    public function __construct($user = NULL)
    {
       $this->orden     = 0;
       parent::__construct($user);
    }

    /**
     * get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return "fa fa-angle-right";
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

    /**
     * isEtapaMunicipal
     * @return boolean
     */
    public function isEtapaMunicipal()
    {
        return true;
    }

    public function getNombreInicial()
    {
        return 'Etapa Municipal';
    }

    public function validarGanadorMunicipal($equipo)
    {
        $municipio = $equipo->getPlanilla()->getMunicipio();
        foreach($this->getEquipos() as $eq){
            if ($eq->getPlanilla()->getMunicipio() == $municipio){
                throw new \Exception('Plenus: Este evento ya tiene un ganador de '.$municipio->getNombre());
            }
        }

        if (!$this->getEvento()->getSaltaControlEtapaMunicipal()){
            $equipo->getPlanilla()->getSegmento()->getTorneo()->validarGanadorMunicipal($equipo);
        }
    }
}
