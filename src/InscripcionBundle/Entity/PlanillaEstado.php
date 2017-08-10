<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\PlanillaEstado
 * @ORM\Table(name="PlanillaEstado",indexes={
 *                                      @ORM\Index(name="estado_nombre", columns={"nombre"}),
 *                                      @ORM\Index(name="search_discr", columns={"discr"})}
 *                )
 * @ORM\Entity(repositoryClass="InscripcionBundle\Entity\Repository\PlanillaEstadoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "Cargada"    = "Cargada",
 *                          "Enviada"    = "Enviada",
 *                          "Presentada" = "Presentada",
 *                          "Aprobada"   = "Aprobada",
 *                          "Observada"  = "Observada",
 *                          "EnRevision" = "EnRevision"
 *                      })
 */
abstract class PlanillaEstado
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=150)
     */
    private $nombre;

     /**
     * @var string $observacion
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    private $observacion;

    /**
     * @ORM\OneToOne(targetEntity="Planilla", mappedBy="estado", cascade={"persist","remove"})
     */
    private $planilla;

    /**
     * Estado anterior
     * @ORM\OneToOne(targetEntity="PlanillaEstado")
     * @ORM\JoinColumn(name="estadoAnterior_id", referencedColumnName="id")
     */
    private $estadoAnterior;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */
    private $createdBy;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return PlanillaEstado
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return PlanillaEstado
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return PlanillaEstado
     */
    public function setCreatedBy(\SeguridadBundle\Entity\Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \SeguridadBundle\Entity\Usuario
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set planilla
     *
     * @param \InscripcionBundle\Entity\Planilla $planilla
     *
     * @return PlanillaEstado
     */
    public function setPlanilla(\InscripcionBundle\Entity\Planilla $planilla = null)
    {
        $this->planilla = $planilla;

        return $this;
    }

    /**
     * Get planilla
     *
     * @return \InscripcionBundle\Entity\Planilla
     */
    public function getPlanilla()
    {
        return $this->planilla;
    }

    /**
     * Set estadoAnterior
     *
     * @param \InscripcionBundle\Entity\PlanillaEstado $planillaEstado
     *
     * @return PlanillaEstado
     */
    public function setEstadoAnterior(\InscripcionBundle\Entity\PlanillaEstado $planillaEstado = null)
    {
        $this->estadoAnterior = $planillaEstado;

        return $this;
    }

    /**
     * Get estadoAnterior
     *
     * @return \InscripcionBundle\Entity\PlanillaEstado
     */
    public function getEstadoAnterior()
    {
        return $this->estadoAnterior;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return "Estado ".$this->getNombre();
    }

    /**
     * get NombreRaw
     */
    public function getNombreRaw()
    {
        return "<span title='".$this->getNombre()."' class='".$this->getClass()."'>".$this->getAbr()."</span>";
    }

    /**
     * Get isRemovable
     *
     * @return boolean
     */
    public function isRemovable()
    {
        return false;
    }

    /**
     * Get isEditable
     *
     * @return boolean
     */
    public function isEditable()
    {
        return false;
    }

    /**
     * Get getRoute
     *
     * @return string
     */
    public function getRoute()
    {
    }

    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
        return array();
    }
    /**
     * get icon
     */
    public function getIcon()
    {
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return PlanillaEstado
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    public function isAprobada(){
        return false;
    }

    static function getEstadosPosibles(){
        return  array(
                        "Cargada" => ["nombre" => "Cargada", "nombrePlural" => "Cargadas","cantidad" => 0,"porcentaje" => 0, "color" => "#777"],
                        "En Revisión" => ["nombre" => "En Revisión","nombrePlural" => "En Revisión","cantidad" => 0,"porcentaje" => 0, "color" => "#c9302c"],
                        "Enviada" => ["nombre" => "Enviada", "nombrePlural" => "Enviadas", "cantidad" => 0,"porcentaje" => 0, "color" => "#269abc"],
                        "Observada" => ["nombre" => "Observada","nombrePlural" => "Observadas","cantidad" => 0,"porcentaje" => 0, "color" => "#ec971f"],
                        "Presentada" => ["nombre" => "Presentada","nombrePlural" => "Presentadas","cantidad" => 0,"porcentaje" => 0, "color" => "#204d74"],
                        "Aprobada" => ["nombre" => "Aprobada","nombrePlural" => "Aprobadas","cantidad" => 0,"porcentaje" => 0, "color" => "#5cb85c"]
                    );
    }
}
