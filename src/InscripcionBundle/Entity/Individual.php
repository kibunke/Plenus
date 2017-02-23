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
    public function getTemplate(){
        return "InscripcionBundle:Planilla:newIndividual.html.twig";
    }
    
    public function getNewEquipo(){
        return new \ResultadoBundle\Entity\Individual();
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
        //    $equipos[$key]['integrantes'][] = array(
        //                            'id' => '',
        //                            'order' => $equipo['orden'],
        //                            'type' => 'inscripto',
        //                            'persona' => array(
        //                                'id' => '',
        //                                'apellido' => '',
        //                                'nombre' => '',
        //                                'documento' => '',
        //                                'nacimiento' => '',
        //                                'domicilio' => '',
        //                                'localidad' => '',
        //                                'email' => ''
        //                            ),
        //                    );
        //    //No se contemplan los sustitutos para los individuales
        //}
        return $equipos;
    }

    /**
     * Set responsableMunicipioNombre
     *
     * @param string $responsableMunicipioNombre
     *
     * @return Individual
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
     * @return Individual
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
