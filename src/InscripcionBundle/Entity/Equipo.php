<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Individual
 * @ORM\Entity()
 */

class Equipo extends Planilla
{
    public function getTemplate(){
        return "InscripcionBundle:Planilla:newEquipo.html.twig";
    }
    
    /**
     * Get equipos
     *
     * @return json
     */
    public function getJson()
    {
        $equipos = parent::getJson();
        //foreach ($equipos as $key => $equipo){
        //    for ($i=0; $i<$this->getSegmento()->getMaxIntegrantes();$i++){
        //        $equipos[$key]['integrantes'][] = array(
        //                                'order' => $i + 1,
        //                                'type' => 'inscripto',
        //                                'persona' => array(),
        //                        );
        //    }
        //    for ($j=0; $j<$this->getSegmento()->getMaxReemplazos();$j++){
        //        $equipos[$key]['integrantes'][] = array(
        //                                'order' => $j + 1,
        //                                'type' => 'sustituto',
        //                                'persona' => array(),
        //                        );
        //    }
        //}
        return $equipos;
    }

    /**
     * Set responsableMunicipioNombre
     *
     * @param string $responsableMunicipioNombre
     *
     * @return Equipo
     */
    public function setResponsableMunicipioNombre($responsableMunicipioNombre)
    {
        $this->responsableMunicipioNombre = $responsableMunicipioNombre;

        return $this;
    }

    /**
     * Get responsableMunicipioNombre
     *
     * @return string
     */
    public function getResponsableMunicipioNombre()
    {
        return $this->responsableMunicipioNombre;
    }

    /**
     * Set responsableMunicipioApellido
     *
     * @param string $responsableMunicipioApellido
     *
     * @return Equipo
     */
    public function setResponsableMunicipioApellido($responsableMunicipioApellido)
    {
        $this->responsableMunicipioApellido = $responsableMunicipioApellido;

        return $this;
    }

    /**
     * Get responsableMunicipioApellido
     *
     * @return string
     */
    public function getResponsableMunicipioApellido()
    {
        return $this->responsableMunicipioApellido;
    }
}
