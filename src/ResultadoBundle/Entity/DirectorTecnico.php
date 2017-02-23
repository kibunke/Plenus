<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CommonBundle\Entity\Persona;
/**
 * ResultadoBundle\Entity\DirectorTecnico
 *
 * @ORM\Entity()
 *
 */
class DirectorTecnico extends Persona
{
    /**
     * @ORM\OneToMany(targetEntity="Equipo", mappedBy="directorTecnico")
     */
    private $equipos;

    /**
     * Constructor
     */
    public function __construct($user = null)
    {
        $this->equipos = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($user);
    }
    
    public function getJson()
    {
        return array(
                'nombre' => $this->getNombre(),
                'apellido' => $this->getApellido(),
                'tipoDocumento' => $this->getTipoDocumento()->getNombre(),
                'dni' => $this->getDni()
            );
    }
}
