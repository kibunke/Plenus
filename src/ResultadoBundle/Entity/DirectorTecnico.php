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

    public function toArray()
    {
        return array(
                'nombre' => $this->getNombre(),
                'apellido' => $this->getApellido(),
                'tipoDocumento' => $this->getTipoDocumento()->getNombre(),
                'dni' => $this->getDni()
            );
    }

    public function getTipoPersona()
    {
        return 'Director Técnico';
    }

    public function getClass()
    {
        return 'ResultadoBundle:DirectorTecnico';
    }

    /**
     * Add equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     *
     * @return DirectorTecnico
     */
    public function addEquipo(\ResultadoBundle\Entity\Equipo $equipo)
    {
        $this->equipos[] = $equipo;

        return $this;
    }

    /**
     * Remove equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     */
    public function removeEquipo(\ResultadoBundle\Entity\Equipo $equipo)
    {
        $this->equipos->removeElement($equipo);
    }

    /**
     * Get equipos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEquipos()
    {
        return $this->equipos;
    }
}
