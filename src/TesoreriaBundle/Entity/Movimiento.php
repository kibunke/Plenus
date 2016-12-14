<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Movimiento
 * @ORM\Table(name="services_juegosba_final.Movimiento")
 * @ORM\Entity(repositoryClass="TesoreriaBundle\Entity\Repository\MovimientoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "INGRESO"   = "Ingreso",
 *                          "EGRESO"    = "Egreso"
 *                      })
 */
class Movimiento
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
     * @var float $monto
     * 
     * @ORM\Column(name="monto", type="float")
     */
    private $monto;
        
    /**
     * @var Fondo $fondo
     * 
     * @ORM\ManyToOne(targetEntity="Fondo", inversedBy="movimientos")
     * @ORM\JoinColumn(name="fondo_id", referencedColumnName="id")
     */
    private $fondo;
    
    /**
     * @var string $descripcion
     * 
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @ORM\OneToOne(targetEntity="MovimientoEstado", cascade={"persist"})
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     */
    private $estado;

    /**
     * @ORM\OneToOne(targetEntity="Recibo", inversedBy="movimiento", cascade={"persist"})
     * @ORM\JoinColumn(name="recibo_id", referencedColumnName="id")
     */
    private $recibo;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var SeguridadBundle\Entity\Usuario $createdBy
     * 
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */
    private $createdBy;

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
        $this->createdAt = new \DateTime();
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
     * Set monto
     *
     * @param float $monto
     * @return Movimiento
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Movimiento
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Movimiento
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Movimiento
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
     * Set fondo
     *
     * @param \TesoreriaBundle\Entity\Fondo $fondo
     * @return Movimiento
     */
    public function setFondo(\TesoreriaBundle\Entity\Fondo $fondo = null)
    {
        $this->fondo = $fondo;

        return $this;
    }

    /**
     * Get fondo
     *
     * @return \TesoreriaBundle\Entity\Fondo 
     */
    public function getFondo()
    {
        return $this->fondo;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Movimiento
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
     * Set updatedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $updatedBy
     * @return Movimiento
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

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
     * Set estado
     *
     * @param \TesoreriaBundle\Entity\MovimientoEstado $estado
     * @return Movimiento
     */
    public function setEstado(\TesoreriaBundle\Entity\MovimientoEstado $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \TesoreriaBundle\Entity\MovimientoEstado 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set recibo
     *
     * @param \TesoreriaBundle\Entity\Recibo $recibo
     * @return Movimiento
     */
    public function setRecibo(\TesoreriaBundle\Entity\Recibo $recibo = null)
    {
        $this->recibo = $recibo;

        return $this;
    }

    /**
     * Get recibo
     *
     * @return \TesoreriaBundle\Entity\Recibo 
     */
    public function getRecibo()
    {
        return $this->recibo;
    }
    
    /**
     * estaCompletado
     *
     * @return boolean
     */
    public function estaCompletado()
    {
        return $this->getEstado()->estaCompletado();
    }

    /**
     * estaPagado
     *
     * @return boolean
     */
    public function estaPagado()
    {
        return $this->getEstado()->estaCompletado();
    }
    
    /**
     * estaReservado
     *
     * @return boolean
     */
    public function estaReservado()
    {
        return $this->getEstado()->estaReservado();
    }
}
