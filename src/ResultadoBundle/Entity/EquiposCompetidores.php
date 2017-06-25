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
     * @ORM\ManyToOne(targetEntity="Competidor", inversedBy="competidorEquipos", cascade={"persist"})
     * @ORM\JoinColumn(name="competidor_id", referencedColumnName="id")
     * */
    protected $competidor;

    /**
     * @ORM\ManyToOne(targetEntity="Equipo", inversedBy="equipoCompetidores", cascade={"persist"})
     * @ORM\JoinColumn(name="equipo_id", referencedColumnName="id")
     * */
    protected $equipo;

    /**
     * @ORM\ManyToOne(targetEntity="Equipo")
     * @ORM\JoinColumn(name="exEquipo_id", referencedColumnName="id")
     * */
    protected $exEquipo;

    /**
     * @var string $rol
     *
     * @ORM\Column(name="rol", type="string", length=100)
     */
    protected $rol;

    /**
     * Un competidor en un equipo puede tener una salida por sustitucion.
     * @ORM\OneToOne(targetEntity="EquiposCompetidores", mappedBy="sale", cascade={"persist"})
     */
    private $entra;

    /**
     * Un competidor en un equipo puede tener una entrada por sustitucion.
     * @ORM\OneToOne(targetEntity="EquiposCompetidores", inversedBy="entra")
     * @ORM\JoinColumn(name="equioCompetidor_id", referencedColumnName="id")
     */
    private $sale;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var SeguridadBundle\Entity\Usuario $updatedBy
     *
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */
    private $updatedBy;

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

    /**
     * Set entra
     *
     * @param \ResultadoBundle\Entity\EquiposCompetidores $entra
     *
     * @return EquiposCompetidores
     */
    public function setEntra(\ResultadoBundle\Entity\EquiposCompetidores $entra = null)
    {
        $this->entra = $entra;
        $this->rol = "reemplazado";

        return $this;
    }

    /**
     * Get entra
     *
     * @return \ResultadoBundle\Entity\EquiposCompetidores
     */
    public function getEntra()
    {
        return $this->entra;
    }

    /**
     * Set sale
     *
     * @param \ResultadoBundle\Entity\EquiposCompetidores $sale
     *
     * @return EquiposCompetidores
     */
    public function setSale(\ResultadoBundle\Entity\EquiposCompetidores $sale = null)
    {
        $this->sale = $sale;
        $this->rol = "inscripto";
        return $this;
    }

    /**
     * Get sale
     *
     * @return \ResultadoBundle\Entity\EquiposCompetidores
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return EquiposCompetidores
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $updatedBy
     *
     * @return EquiposCompetidores
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \SeguridadBundle\Entity\Usuario
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set exEquipo
     *
     * @param \ResultadoBundle\Entity\Equipo $exEquipo
     *
     * @return EquiposCompetidores
     */
    public function setExEquipo(\ResultadoBundle\Entity\Equipo $exEquipo = null)
    {
        $this->exEquipo = $exEquipo;

        return $this;
    }

    /**
     * Get exEquipo
     *
     * @return \ResultadoBundle\Entity\Equipo
     */
    public function getExEquipo()
    {
        return $this->exEquipo;
    }
}
