<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CommonBundle\Entity\Persona;
/**
 * ResultadoBundle\Entity\EquiposCompetidores
 *
 * @ORM\Entity()
 * @ORM\Table(name="EquiposCompetidores")
 *
 */
class EquiposCompetidores
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Competidor", inversedBy="competidorEquipos", cascade={"all"})
     * @ORM\JoinColumn(name="competidor_id", referencedColumnName="id")
     * */
    protected $competidor;
    
    /**
     * @ORM\ManyToOne(targetEntity="Equipo", inversedBy="equipoCompetidores", cascade={"all"})
     * @ORM\JoinColumn(name="equipo_id", referencedColumnName="id")
     * */
    protected $equipo;    

    /**
     * @var string $rol
     *
     * @ORM\Column(name="rol", type="string", length=100)
     */
    protected $rol;
    
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rol
     *
     * @param string $rol
     *
     * @return EquiposCompetidores
     */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return string
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set competidor
     *
     * @param \ResultadoBundle\Entity\Competidor $competidor
     *
     * @return EquiposCompetidores
     */
    public function setCompetidor(\ResultadoBundle\Entity\Competidor $competidor = null)
    {
        $this->competidor = $competidor;

        return $this;
    }

    /**
     * Get competidor
     *
     * @return \ResultadoBundle\Entity\Competidor
     */
    public function getCompetidor()
    {
        return $this->competidor;
    }

    /**
     * Set equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     *
     * @return EquiposCompetidores
     */
    public function setEquipo(\ResultadoBundle\Entity\Equipo $equipo = null)
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * Get equipo
     *
     * @return \ResultadoBundle\Entity\Equipo
     */
    public function getEquipo()
    {
        return $this->equipo;
    }
}
