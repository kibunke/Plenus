<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TesoreriaBundle\Entity\Fondo
 * @ORM\Table(name="Fondo")
 * @ORM\Entity(repositoryClass="TesoreriaBundle\Entity\Repository\FondoRepository")
 */
class Fondo {

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
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string $abreviatura
     *
     * @ORM\Column(name="abreviatura", type="string", length=6)
     */
    private $abreviatura;

     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $modeloRecibo
     *
     * @ORM\Column(name="modeloRecibo", type="text")
     */
    private $modeloRecibo;

    /**
     * @var float $monto
     *
     * @ORM\Column(name="monto", type="float")
     */
    private $monto;

    /**
     * @var Entidad $entidad
     *
     * @ORM\ManyToOne(targetEntity="Entidad", inversedBy="fondos")
     * @ORM\JoinColumn(name="entidad_id", referencedColumnName="id")
     */
    private $entidad;

    /**
     * @var ArrayCollection $movimientos
     *
     * @ORM\OneToMany(targetEntity="Movimiento", mappedBy="fondo")
     */
    private $movimientos;

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
        $this->movimientos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Fondo
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
     * Get montoDisponible
     *
     * @return float
     */
    public function getMontoDisponible()
    {
        return $this->monto-$this->getMontoReservado()-$this->getMontoUtilizado();
    }

    /**
     * Get montoReservado
     *
     * @return float
     */
    public function getMontoReservado()
    {
        $sum = 0;
        foreach( $this->movimientos as $movimiento){
            if ($movimiento->estaReservado())
                $sum += $movimiento->getMonto();
        }
        return $sum;
    }

    /**
     * Get montoDisponible
     *
     * @return float
     */
    public function getMontoUtilizado()
    {
        $sum = 0;
        foreach( $this->movimientos as $movimiento){
            if ($movimiento->estaCompletado())
                $sum += $movimiento->getMonto();
        }
        return $sum;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Fondo
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
     * @return Fondo
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
     * Set Entidad
     *
     * @param \TesoreriaBundle\Entity\Entidad $entidad
     * @return Movimiento
     */
    public function setEntidad(\TesoreriaBundle\Entity\Entidad $entidad = null)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get Entidad
     *
     * @return \TesoreriaBundle\Entity\Entidad
     */
    public function getEntidad()
    {
        if ($this->entidad)
            return $this->entidad;
        return "(?) Sin entidad";
    }

    /**
     * Add movimientos
     *
     * @param \TesoreriaBundle\Entity\Movimiento $movimientos
     * @return Fondo
     */
    public function addMovimiento(\TesoreriaBundle\Entity\Movimiento $movimientos)
    {
        $this->movimientos[] = $movimientos;

        return $this;
    }

    /**
     * Remove movimientos
     *
     * @param \TesoreriaBundle\Entity\Movimiento $movimientos
     */
    public function removeMovimiento(\TesoreriaBundle\Entity\Movimiento $movimientos)
    {
        $this->movimientos->removeElement($movimientos);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Fondo
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
     * @return Fondo
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
     * Set nombre
     *
     * @param string $nombre
     * @return Fondo
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
     * Get abreviatura
     *
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }

    /**
     * Set abreviatura
     *
     * @param string $abreviatura
     * @return Fondo
     */
    public function setAbreviatura($abreviatura)
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Fondo
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
     * deleteCheck
     *
     * @return boolean
     */
    public function deleteCheck()
    {
        return ($this->getMontoDisponible() == $this->monto);
    }

    /**
     * editCheck
     *
     * @return boolean
     */
    public function editCheck()
    {
        return ($this->monto > $this->getMontoReservado() + $this->getMontoUtilizado());
    }

    /**
     * get MovimientosJson
     *
     * @return array
     */
    public function getMovimientosJson()
    {
        $arr = array(
                //"sinAsignar"=> ["total" => 0,"cantidad" => 0,"percent" => 0],
                "Reservado" => ["total" => 0,"cantidad" => 0,"percent" => 0],
                "Completado"    => ["total" => 0,"cantidad" => 0,"percent" => 0],
                "data"      => array(
                        "movimientos" => [],
                        "destinatarios" => []
                )
            );
        foreach($this->movimientos as $mov){
            if ( array_key_exists($mov->getEstado()->getTxt(),$arr)){
                $arr[$mov->getEstado()->getTxt()]['total'] += $mov->getMonto();
                $arr[$mov->getEstado()->getTxt()]['cantidad'] ++;
            }
            $arr['data']['movimientos'][] = array(
                    "id" => $mov->getId(),
                    "monto" => $mov->getMonto(),
                    "destinatario" => $mov->getDestinatario()->getId(),
                    "tipo" => $mov->getEstado()
            );
            //$arr['data']['destinatarios'][] = $mov->getDestinatario()->toArray(false);
        }
        return $arr;
    }

    /**
     * Set modeloRecibo
     *
     * @param string $modeloRecibo
     * @return Fondo
     */
    public function setModeloRecibo($modeloRecibo)
    {
        $this->modeloRecibo = $modeloRecibo;

        return $this;
    }

    /**
     * Get modeloRecibo
     *
     * @return string
     */
    public function getModeloRecibo()
    {
        return $this->modeloRecibo;
    }
}
