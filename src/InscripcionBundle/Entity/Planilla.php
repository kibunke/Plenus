<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Planilla
 * @ORM\Table(name="Planilla")
 * @ORM\Entity(repositoryClass="InscripcionBundle\Entity\Repository\PlanillaRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "Individual" = "Individual",
 *                          "Equipo"   = "Equipo"
 *                      })
 */
abstract class Planilla
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
     * @ORM\OneToMany(targetEntity="ResultadoBundle\Entity\Equipo", mappedBy="planilla")
     */
    private $competidores;
    
    /**
     * @ORM\ManyToOne(targetEntity="Origen", cascade={"persist"})
     * @ORM\JoinColumn(name="origen", referencedColumnName="id")
     */       
    private $origen;    
    
    /**
     * @ORM\ManyToOne(targetEntity="Segmento", inversedBy="planillas")
     * @ORM\JoinColumn(name="segmento", referencedColumnName="id")
     */       
    private $segmento;
    
    /**
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Municipio")
     * @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     */       
    private $municipio;
    
    /**
     * this is OneToMany because the historial is important
     * @ORM\OneToMany(targetEntity="PlanillaEstado", mappedBy="planilla")
     */
    private $estados;
    
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
    }

    /**
     * __toString
     */    
    public function __toString()
    {
        return "Planilla de ".$this->getMunicipio()->getNombre()." con ".count($this->getParticipantes())." participantes";
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
     *
     * @return Planilla
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
     *
     * @return Planilla
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
     *
     * @return Planilla
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
     * Add competidore
     *
     * @param \ResultadoBundle\Entity\Equipo $competidore
     *
     * @return Planilla
     */
    public function addCompetidore(\ResultadoBundle\Entity\Equipo $competidore)
    {
        $this->competidores[] = $competidore;

        return $this;
    }

    /**
     * Remove competidore
     *
     * @param \ResultadoBundle\Entity\Equipo $competidore
     */
    public function removeCompetidore(\ResultadoBundle\Entity\Equipo $competidore)
    {
        $this->competidores->removeElement($competidore);
    }

    /**
     * Get competidores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetidores()
    {
        return $this->competidores;
    }

    /**
     * Set origen
     *
     * @param \InscripcionBundle\Entity\Origen $origen
     *
     * @return Planilla
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
     * Set segmento
     *
     * @param \InscripcionBundle\Entity\Segmento $segmento
     *
     * @return Planilla
     */
    public function setSegmento(\InscripcionBundle\Entity\Segmento $segmento = null)
    {
        $this->segmento = $segmento;

        return $this;
    }

    /**
     * Get segmento
     *
     * @return \InscripcionBundle\Entity\Segmento
     */
    public function getSegmento()
    {
        return $this->segmento;
    }

    /**
     * Set municipio
     *
     * @param \CommonBundle\Entity\Municipio $municipio
     *
     * @return Planilla
     */
    public function setMunicipio(\CommonBundle\Entity\Municipio $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return \CommonBundle\Entity\Municipio
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Add estado
     *
     * @param \InscripcionBundle\Entity\PlanillaEstado $estado
     *
     * @return Planilla
     */
    public function addEstado(\InscripcionBundle\Entity\PlanillaEstado $estado)
    {
        $this->estados[] = $estado;

        return $this;
    }

    /**
     * Remove estado
     *
     * @param \InscripcionBundle\Entity\PlanillaEstado $estado
     */
    public function removeEstado(\InscripcionBundle\Entity\PlanillaEstado $estado)
    {
        $this->estados->removeElement($estado);
    }

    /**
     * Get estados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstados()
    {
        return $this->estados;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Planilla
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
     *
     * @return Planilla
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
}
