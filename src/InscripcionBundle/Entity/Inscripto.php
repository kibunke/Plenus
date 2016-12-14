<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Inscripto
 * @ORM\Table(name="services_juegosba_final.Inscripto")
 * @ORM\Entity(repositoryClass="InscripcionBundle\Entity\Repository\InscriptoRepository")
 */
class Inscripto
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
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @var integer $cantidadMasculinos
     *
     * @ORM\Column(name="cantidadMasculinos", type="integer")
     * @Assert\Range( min = 0, minMessage = "Este campo debe ser mayor a 0.",)
     */
    private $cantidadMasculinos;
    
    /**
     * @var integer $cantidadFemeninos
     *
     * @ORM\Column(name="cantidadFemeninos", type="integer")
     * @Assert\Range( min = 0, minMessage = "Este campo debe ser mayor a 0.",)
     */
    private $cantidadFemeninos;
    
    /**
     * @ORM\ManyToOne(targetEntity="Origen", cascade={"persist"})
     * @ORM\JoinColumn(name="origen", referencedColumnName="id")
     */       
    private $origen;    
    
    /**
     * @ORM\ManyToOne(targetEntity="ResultadoBundle\Entity\Evento", inversedBy="inscriptos")
     * @ORM\JoinColumn(name="evento", referencedColumnName="id")
     */       
    private $evento;
    
    /**
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Partido")
     * @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     */       
    private $municipio;
    
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
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
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
        $this->cantidadFemeninos=0;
        $this->cantidadMasculinos=0;
    }

    /**
     * __toString
     */    
    public function __toString()
    {
        return "Inscripto en : ".$this->getMunicipio()->getNombre();
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Inscripto
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
     * @return Inscripto
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
     * @return Inscripto
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
     * Set origen
     *
     * @param \InscripcionBundle\Entity\Origen $origen
     * @return Inscripto
     */
    public function setOrigen(\InscripcionBundle\Entity\Origen $origen = null)
    {
        $this->origen = $origen;

        return $this;
    }

    /**
     * Get origen
     *
     * @return \InscripcionBundle\Entity\Origen 
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set evento
     *
     * @param \ResultadoBundle\Entity\Evento $evento
     * @return Inscripto
     */
    public function setEvento(\ResultadoBundle\Entity\Evento $evento = null)
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get evento
     *
     * @return \ResultadoBundle\Entity\Evento 
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Inscripto
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
     * @return Inscripto
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
     * Set municipio
     *
     * @param \CommonBundle\Entity\Partido $municipio
     * @return Inscripto
     */
    public function setMunicipio(\CommonBundle\Entity\Partido $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return \CommonBundle\Entity\Partido 
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set cantidadMasculinos
     *
     * @param integer $cantidadMasculinos
     * @return Inscripto
     */
    public function setCantidadMasculinos($cantidadMasculinos)
    {
        $this->cantidadMasculinos = $cantidadMasculinos;

        return $this;
    }

    /**
     * Get cantidadMasculinos
     *
     * @return integer 
     */
    public function getCantidadMasculinos()
    {
        return $this->cantidadMasculinos;
    }

    /**
     * Set cantidadFemeninos
     *
     * @param integer $cantidadFemeninos
     * @return Inscripto
     */
    public function setCantidadFemeninos($cantidadFemeninos)
    {
        $this->cantidadFemeninos = $cantidadFemeninos;

        return $this;
    }

    /**
     * Get cantidadFemeninos
     *
     * @return integer 
     */
    public function getCantidadFemeninos()
    {
        return $this->cantidadFemeninos;
    }
}
