<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Individual
 * @ORM\Entity()
 */

class Individual extends Planilla
{
    /**
     * Get equipos
     *
     * @return json
     */
    public function getEquipos()
    {
        $equipos = array();
        for ($x=0; $x < $this->getSegmento()->getMaxEquiposPorPlanilla();$x++){
            $equipos[$x]= array(
                "id" => '',
                "nombre" => '',
                "integrantes" => array()
            );
            for ($i=0; $i<$this->getSegmento()->getMaxIntegrantes();$i++){
                $equipos[$x]['integrantes'][] = array(
                                        'order' => $x +1 ,
                                        'type' => 'inscripto',
                                        'persona' => array(),
                                );
            }
            //No se contemplan los sustitutos para los individuales
        }
        return $equipos;
    }         
}
